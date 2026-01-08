<?php
/**
 * PREMIUM CODE VALIDATION
 * Validates Premium codes and manages Premium status
 */

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session-security.php';

// ========================================
// IP-BASED RATE LIMITING FOR PREMIUM ACTIVATION
// ========================================

/**
 * Get the real client IP address (considering proxies)
 */
function getClientIP() {
    $ip = '';

    // Check for CloudFlare IP
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    // Check for forwarded IP (behind proxy/load balancer)
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // X-Forwarded-For can contain multiple IPs, take the first one
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }
    // Check for real IP header
    elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    // Fall back to REMOTE_ADDR
    else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    // Validate IP format
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '0.0.0.0';
    }

    return $ip;
}

/**
 * Check if IP is rate limited
 * Returns: ['allowed' => bool, 'remaining' => int, 'reset_at' => timestamp, 'wait_time' => seconds]
 */
function checkRateLimit($ip) {
    $rateLimitFile = __DIR__ . '/rate-limit-attempts.json';
    $maxAttempts = 5;
    $windowDuration = 3600; // 1 hour in seconds
    $now = time();

    // Load existing rate limit data
    $rateLimitData = [];
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $rateLimitData = json_decode($content, true) ?: [];
    }

    // Clean up old entries (older than 2 hours)
    foreach ($rateLimitData as $ipKey => $data) {
        if (isset($data['reset_at']) && $data['reset_at'] < ($now - 3600)) {
            unset($rateLimitData[$ipKey]);
        }
    }

    // Get IP data
    $ipData = $rateLimitData[$ip] ?? [
        'attempts' => 0,
        'first_attempt' => $now,
        'reset_at' => $now + $windowDuration,
        'blocked_until' => 0
    ];

    // Check if IP is currently blocked
    if ($ipData['blocked_until'] > $now) {
        $waitTime = $ipData['blocked_until'] - $now;
        return [
            'allowed' => false,
            'remaining' => 0,
            'reset_at' => $ipData['blocked_until'],
            'wait_time' => $waitTime,
            'attempts' => $ipData['attempts']
        ];
    }

    // Check if time window has expired - reset counter
    if ($ipData['reset_at'] <= $now) {
        $ipData = [
            'attempts' => 0,
            'first_attempt' => $now,
            'reset_at' => $now + $windowDuration,
            'blocked_until' => 0
        ];
    }

    // Check if attempts exceeded
    if ($ipData['attempts'] >= $maxAttempts) {
        // Calculate exponential backoff: 2^(attempts - maxAttempts) * 5 minutes
        $backoffMultiplier = pow(2, min($ipData['attempts'] - $maxAttempts, 5)); // Cap at 2^5 = 32
        $blockDuration = $backoffMultiplier * 300; // 5 minutes base * multiplier
        $blockDuration = min($blockDuration, 7200); // Max 2 hours

        $ipData['blocked_until'] = $now + $blockDuration;
        $rateLimitData[$ip] = $ipData;

        // Save updated data
        file_put_contents($rateLimitFile, json_encode($rateLimitData, JSON_PRETTY_PRINT));

        return [
            'allowed' => false,
            'remaining' => 0,
            'reset_at' => $ipData['blocked_until'],
            'wait_time' => $blockDuration,
            'attempts' => $ipData['attempts']
        ];
    }

    // Rate limit OK
    $remaining = $maxAttempts - $ipData['attempts'];

    return [
        'allowed' => true,
        'remaining' => $remaining,
        'reset_at' => $ipData['reset_at'],
        'wait_time' => 0,
        'attempts' => $ipData['attempts']
    ];
}

/**
 * Record a failed attempt
 */
function recordFailedAttempt($ip, $code) {
    $rateLimitFile = __DIR__ . '/rate-limit-attempts.json';
    $windowDuration = 3600; // 1 hour
    $now = time();

    // Load existing data
    $rateLimitData = [];
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $rateLimitData = json_decode($content, true) ?: [];
    }

    // Get or create IP data
    if (!isset($rateLimitData[$ip])) {
        $rateLimitData[$ip] = [
            'attempts' => 0,
            'first_attempt' => $now,
            'reset_at' => $now + $windowDuration,
            'blocked_until' => 0
        ];
    }

    // Increment attempts
    $rateLimitData[$ip]['attempts']++;
    $rateLimitData[$ip]['last_attempt'] = $now;
    $rateLimitData[$ip]['last_code'] = substr($code, 0, 20); // Store partial code for logging

    // Save
    file_put_contents($rateLimitFile, json_encode($rateLimitData, JSON_PRETTY_PRINT));

    // Log the attempt
    if (ENABLE_LOGGING) {
        $attempts = $rateLimitData[$ip]['attempts'];
        logMessage("Failed premium activation attempt from IP $ip (attempt #$attempts, code: " . substr($code, 0, 10) . "...)", 'warning');
    }
}

/**
 * Record a successful attempt (resets counter)
 */
function recordSuccessfulAttempt($ip, $code) {
    $rateLimitFile = __DIR__ . '/rate-limit-attempts.json';

    // Load existing data
    $rateLimitData = [];
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $rateLimitData = json_decode($content, true) ?: [];
    }

    // Reset IP counter on successful activation
    if (isset($rateLimitData[$ip])) {
        unset($rateLimitData[$ip]);
        file_put_contents($rateLimitFile, json_encode($rateLimitData, JSON_PRETTY_PRINT));
    }

    // Log success
    if (ENABLE_LOGGING) {
        logMessage("Successful premium activation from IP $ip (code: " . substr($code, 0, 10) . "...)", 'info');
    }
}

// ========================================
// SECURE CORS CONFIGURATION
// ========================================
// Get allowed domain from environment
$allowedDomain = getenv('ALLOWED_DOMAIN') ?: 'localhost';

// Validate Origin header
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'http://' . $allowedDomain,
    'https://' . $allowedDomain,
    'http://localhost',
    'http://localhost:8000',
    'http://localhost:3000',
    'http://127.0.0.1',
    'https://localhost',
];

// Check if origin is allowed
$isOriginAllowed = false;
foreach ($allowedOrigins as $allowedOrigin) {
    if (strpos($origin, $allowedOrigin) === 0) {
        $isOriginAllowed = true;
        header('Access-Control-Allow-Origin: ' . $origin);
        break;
    }
}

// If no valid origin, block CORS (but allow same-origin requests)
if (!$isOriginAllowed && !empty($origin)) {
    // Referer check as additional security layer
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $refererValid = false;

    foreach ($allowedOrigins as $allowedOrigin) {
        if (strpos($referer, $allowedOrigin) === 0) {
            $refererValid = true;
            break;
        }
    }

    if (!$refererValid) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => 'Forbidden',
            'message' => 'Origin not allowed'
        ]);
        exit;
    }
}

// CORS headers (only if origin is allowed)
if ($isOriginAllowed) {
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Session starten mit Sicherheitsmaßnahmen
startSecureSession();

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// GET: Query Premium status
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $isPremium = isset($_SESSION['premium_active']) && $_SESSION['premium_active'] === true;

    // Check if premium is still valid (for time-based codes)
    if ($isPremium && isset($_SESSION['premium_code']) && isset($_SESSION['premium_type'])) {
        if ($_SESSION['premium_type'] === 'disposable') {
            $codeCheck = checkDisposableCode($_SESSION['premium_code']);

            // If code is expired, deactivate premium
            if ($codeCheck['valid'] && $codeCheck['activated'] && $codeCheck['expired']) {
                unset($_SESSION['premium_active']);
                unset($_SESSION['premium_code']);
                unset($_SESSION['premium_activated_at']);
                unset($_SESSION['premium_type']);
                unset($_SESSION['premium_expires_at']);
                $isPremium = false;
            }
        }
    }

    echo json_encode([
        'isPremium' => $isPremium,
        'code' => $_SESSION['premium_code'] ?? null,
        'activatedAt' => $_SESSION['premium_activated_at'] ?? null,
        'expiresAt' => $_SESSION['premium_expires_at'] ?? null,
        'premiumType' => $_SESSION['premium_type'] ?? null
    ]);
    exit;
}

// POST: Validate Premium code
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';

    // ACTIVATE CODE
    if ($action === 'activate') {
        $code = trim($input['code'] ?? '');

        if (empty($code)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Please enter a code'
            ]);
            exit;
        }

        // ========================================
        // RATE LIMITING CHECK
        // ========================================
        $clientIP = getClientIP();
        $rateLimit = checkRateLimit($clientIP);

        if (!$rateLimit['allowed']) {
            // IP is rate limited
            $waitMinutes = ceil($rateLimit['wait_time'] / 60);

            http_response_code(429); // Too Many Requests
            echo json_encode([
                'success' => false,
                'message' => "🚫 Too many failed attempts. Please try again in $waitMinutes minutes.",
                'rateLimited' => true,
                'waitTime' => $rateLimit['wait_time'],
                'resetAt' => $rateLimit['reset_at'],
                'attempts' => $rateLimit['attempts']
            ]);

            // Log rate limit violation
            if (ENABLE_LOGGING) {
                logMessage("Rate limit exceeded for IP $clientIP (attempts: {$rateLimit['attempts']}, blocked for $waitMinutes minutes)", 'warning');
            }

            exit;
        }

        // Check for disposable codes first
        if (ENABLE_DISPOSABLE_CODES) {
            $disposableCheck = checkDisposableCode($code);

            if ($disposableCheck['valid']) {
                // Code exists in disposable codes

                // Check if already activated and not expired - allow re-activation
                if ($disposableCheck['activated']) {
                    if ($disposableCheck['expired']) {
                        // Code has expired - record as failed attempt
                        recordFailedAttempt($clientIP, $code);

                        http_response_code(400);
                        echo json_encode([
                            'success' => false,
                            'message' => '⚠️ This code has expired. Premium codes are valid for 24 hours after first activation.'
                        ]);
                        exit;
                    } else {
                        // Code is still valid - allow login from different device
                        $remainingHours = round($disposableCheck['remaining_hours'], 1);

                        $_SESSION['premium_active'] = true;
                        $_SESSION['premium_code'] = $code;
                        $_SESSION['premium_activated_at'] = $disposableCheck['data']['activated_at'];
                        $_SESSION['premium_expires_at'] = $disposableCheck['data']['expires_at'];
                        $_SESSION['premium_type'] = 'disposable';

                        // Regenerate session after privilege escalation (anti-session-fixation)
                        regenerateSessionAfterLogin();

                        // Record successful re-activation
                        recordSuccessfulAttempt($clientIP, $code);

                        echo json_encode([
                            'success' => true,
                            'message' => "✅ Premium activated! Code is valid for {$remainingHours} more hours.",
                            'isPremium' => true,
                            'codeType' => 'disposable',
                            'expiresAt' => $disposableCheck['data']['expires_at'],
                            'remainingHours' => $remainingHours
                        ]);
                        exit;
                    }
                }

                // Code is valid and not activated yet - activate it!
                activateDisposableCode($code);

                $now = date('Y-m-d H:i:s');
                $expiresAt = date('Y-m-d H:i:s', strtotime('+' . DISPOSABLE_CODE_DURATION_HOURS . ' hours'));

                $_SESSION['premium_active'] = true;
                $_SESSION['premium_code'] = $code;
                $_SESSION['premium_activated_at'] = $now;
                $_SESSION['premium_expires_at'] = $expiresAt;
                $_SESSION['premium_type'] = 'disposable';

                // Regenerate session after privilege escalation (anti-session-fixation)
                regenerateSessionAfterLogin();

                // Record successful activation
                recordSuccessfulAttempt($clientIP, $code);

                // Optional: Logging
                if (ENABLE_LOGGING) {
                    logMessage("Disposable premium code activated: $code (expires: $expiresAt)", 'info');
                }

                echo json_encode([
                    'success' => true,
                    'message' => '✅ Premium successfully activated! Valid for 24 hours.',
                    'isPremium' => true,
                    'codeType' => 'disposable',
                    'expiresAt' => $expiresAt,
                    'remainingHours' => DISPOSABLE_CODE_DURATION_HOURS
                ]);
                exit;
            }
        }

        // Check if code is valid in regular premium codes
        $premiumCodes = PREMIUM_CODES;

        if (isset($premiumCodes[$code])) {
            // Code is valid!
            $_SESSION['premium_active'] = true;
            $_SESSION['premium_code'] = $code;
            $_SESSION['premium_activated_at'] = date('Y-m-d H:i:s');
            $_SESSION['premium_type'] = 'regular';

            // Regenerate session after privilege escalation (anti-session-fixation)
            regenerateSessionAfterLogin();

            // Record successful activation
            recordSuccessfulAttempt($clientIP, $code);

            // Optional: Logging
            if (ENABLE_LOGGING) {
                logMessage("Premium activated: $code", 'info');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Premium successfully activated! 🎉',
                'isPremium' => true,
                'codeType' => 'regular'
            ]);
            exit;
        } else {
            // Code invalid - record failed attempt
            recordFailedAttempt($clientIP, $code);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid code. Please check your entry.'
            ]);
            exit;
        }
    }

    // DEACTIVATE CODE
    if ($action === 'deactivate') {
        unset($_SESSION['premium_active']);
        unset($_SESSION['premium_code']);
        unset($_SESSION['premium_activated_at']);

        echo json_encode([
            'success' => true,
            'message' => 'Premium deactivated',
            'isPremium' => false
        ]);
        exit;
    }

    // Unknown action
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action'
    ]);
    exit;
}

// Other methods not allowed
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Method Not Allowed'
]);
?>
