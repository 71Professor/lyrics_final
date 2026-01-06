<?php
/**
 * SECURE SESSION MANAGEMENT
 *
 * Centralized session security to prevent:
 * - Session Hijacking
 * - Session Fixation
 * - CSRF attacks
 * - XSS-based session theft
 *
 * USAGE:
 * Include this file BEFORE any session_start() calls:
 * require_once __DIR__ . '/session-security.php';
 * startSecureSession();
 */

// ========================================
// SECURE SESSION CONFIGURATION
// ========================================

/**
 * Configure secure session parameters
 */
function configureSecureSession() {
    // Prevent session fixation attacks
    ini_set('session.use_strict_mode', '1');

    // Use only cookies for session ID (no URL parameters)
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');

    // Secure cookie settings
    ini_set('session.cookie_httponly', '1'); // Prevent JavaScript access
    ini_set('session.cookie_samesite', 'Strict'); // CSRF protection

    // Enable secure flag for HTTPS (auto-detect or force)
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || $_SERVER['SERVER_PORT'] == 443
               || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

    // In production, ALWAYS use secure flag. In development (localhost), make it optional
    $isLocalhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']);

    if ($isHttps || !$isLocalhost) {
        ini_set('session.cookie_secure', '1');
    }

    // Session lifetime (24 hours for active session, 30 minutes for inactive)
    ini_set('session.gc_maxlifetime', '86400'); // 24 hours
    ini_set('session.cookie_lifetime', '0'); // Session cookie (browser close)

    // Strong session ID
    ini_set('session.entropy_length', '32');
    ini_set('session.hash_function', 'sha256');
    ini_set('session.sid_length', '48');
    ini_set('session.sid_bits_per_character', '6');
}

/**
 * Start secure session with additional validation
 *
 * @param int $inactivityTimeout Timeout in seconds (default: 30 minutes)
 * @param bool $enableFingerprinting Enable IP/User-Agent validation
 */
function startSecureSession($inactivityTimeout = 1800, $enableFingerprinting = true) {
    // Configure session security BEFORE starting
    configureSecureSession();

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ========================================
    // SESSION FINGERPRINTING (Anti-Hijacking)
    // ========================================

    if ($enableFingerprinting) {
        $currentFingerprint = generateSessionFingerprint();

        // First time - set fingerprint
        if (!isset($_SESSION['__security_fingerprint'])) {
            $_SESSION['__security_fingerprint'] = $currentFingerprint;
            $_SESSION['__security_created_at'] = time();
        }
        // Validate fingerprint on subsequent requests
        else {
            if ($_SESSION['__security_fingerprint'] !== $currentFingerprint) {
                // Fingerprint mismatch - possible hijacking attempt
                logSecurityEvent('Session fingerprint mismatch - possible hijacking attempt', [
                    'expected' => $_SESSION['__security_fingerprint'],
                    'received' => $currentFingerprint,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);

                // Destroy compromised session
                destroySession();

                // Throw exception or redirect to login
                http_response_code(401);
                if (isAjaxRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'error' => 'Session Invalid',
                        'message' => 'Your session has been invalidated for security reasons. Please refresh the page.',
                        'code' => 'SESSION_HIJACK_DETECTED'
                    ]);
                } else {
                    header('Location: ' . ($_SERVER['REQUEST_URI'] ?? '/'));
                }
                exit;
            }
        }
    }

    // ========================================
    // SESSION TIMEOUT VALIDATION
    // ========================================

    $currentTime = time();

    // Check for session inactivity timeout
    if (isset($_SESSION['__security_last_activity'])) {
        $inactiveTime = $currentTime - $_SESSION['__security_last_activity'];

        if ($inactiveTime > $inactivityTimeout) {
            logSecurityEvent('Session expired due to inactivity', [
                'inactive_seconds' => $inactiveTime,
                'timeout' => $inactivityTimeout
            ]);

            destroySession();

            // Return timeout response
            if (isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Session Timeout',
                    'message' => 'Your session has expired due to inactivity. Please refresh the page.',
                    'code' => 'SESSION_TIMEOUT'
                ]);
                exit;
            } else {
                // For regular requests, let it continue but clear session
                session_start(); // Start new session
            }
        }
    }

    // Update last activity timestamp
    $_SESSION['__security_last_activity'] = $currentTime;

    // ========================================
    // SESSION ABSOLUTE TIMEOUT (Max 24 hours)
    // ========================================

    $maxSessionLifetime = 86400; // 24 hours

    if (isset($_SESSION['__security_created_at'])) {
        $sessionAge = $currentTime - $_SESSION['__security_created_at'];

        if ($sessionAge > $maxSessionLifetime) {
            logSecurityEvent('Session expired - maximum lifetime reached', [
                'age_seconds' => $sessionAge,
                'max_lifetime' => $maxSessionLifetime
            ]);

            destroySession();

            if (isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Session Expired',
                    'message' => 'Your session has expired. Please refresh the page.',
                    'code' => 'SESSION_EXPIRED'
                ]);
                exit;
            } else {
                session_start(); // Start new session
            }
        }
    }

    // ========================================
    // SESSION REGENERATION (Every 15 minutes)
    // ========================================

    if (!isset($_SESSION['__security_last_regeneration'])) {
        $_SESSION['__security_last_regeneration'] = $currentTime;
    } else {
        $timeSinceRegeneration = $currentTime - $_SESSION['__security_last_regeneration'];

        // Regenerate session ID every 15 minutes
        if ($timeSinceRegeneration > 900) { // 15 minutes
            regenerateSession();
        }
    }
}

/**
 * Generate session fingerprint based on user characteristics
 *
 * @return string Hashed fingerprint
 */
function generateSessionFingerprint() {
    $components = [
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        // Don't include Accept-Language or other frequently changing headers
    ];

    return hash('sha256', implode('|', $components));
}

/**
 * Regenerate session ID safely
 */
function regenerateSession() {
    // Store important data before regeneration
    $oldSessionData = $_SESSION;

    // Regenerate session ID
    session_regenerate_id(true);

    // Restore session data
    $_SESSION = $oldSessionData;

    // Update regeneration timestamp
    $_SESSION['__security_last_regeneration'] = time();

    logSecurityEvent('Session ID regenerated', [
        'session_id' => session_id()
    ]);
}

/**
 * Safely regenerate session ID after privilege escalation
 * Call this after login or premium activation
 */
function regenerateSessionAfterLogin() {
    regenerateSession();

    // Reset fingerprint after login
    $_SESSION['__security_fingerprint'] = generateSessionFingerprint();
    $_SESSION['__security_created_at'] = time();
    $_SESSION['__security_last_activity'] = time();

    logSecurityEvent('Session regenerated after authentication');
}

/**
 * Destroy session completely
 */
function destroySession() {
    $_SESSION = [];

    // Delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Check if current request is AJAX
 *
 * @return bool
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
           && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Log security events
 *
 * @param string $message
 * @param array $context
 */
function logSecurityEvent($message, $context = []) {
    // Only log if ENABLE_LOGGING is defined and true
    if (!defined('ENABLE_LOGGING') || !ENABLE_LOGGING) {
        return;
    }

    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event' => $message,
        'session_id' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'context' => $context
    ];

    $logLine = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";

    // Create logs directory if it doesn't exist
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }

    // Write to security log
    @error_log($logLine, 3, $logDir . '/security.log');
}

/**
 * Validate session for admin areas
 * Throws exception if not authenticated
 */
function requireAdminSession() {
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        http_response_code(401);

        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Unauthorized',
                'message' => 'Admin authentication required'
            ]);
        } else {
            // Redirect to login or show error
            header('Location: ' . $_SERVER['PHP_SELF']);
        }
        exit;
    }
}

/**
 * Get session security status for debugging
 * Only use in development!
 *
 * @return array
 */
function getSessionSecurityStatus() {
    return [
        'session_id' => session_id(),
        'created_at' => $_SESSION['__security_created_at'] ?? null,
        'last_activity' => $_SESSION['__security_last_activity'] ?? null,
        'last_regeneration' => $_SESSION['__security_last_regeneration'] ?? null,
        'fingerprint_set' => isset($_SESSION['__security_fingerprint']),
        'session_age' => isset($_SESSION['__security_created_at'])
            ? (time() - $_SESSION['__security_created_at'])
            : null,
        'inactive_time' => isset($_SESSION['__security_last_activity'])
            ? (time() - $_SESSION['__security_last_activity'])
            : null,
    ];
}
