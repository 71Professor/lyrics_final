<?php
/**
 * PREMIUM CODE VALIDATION
 * Validates Premium codes and manages Premium status
 */

require_once 'config.php';

// CORS & Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Session starten für Premium-Status
session_start();

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

        // Check for disposable codes first
        if (ENABLE_DISPOSABLE_CODES) {
            $disposableCheck = checkDisposableCode($code);

            if ($disposableCheck['valid']) {
                // Code exists in disposable codes

                // Check if already activated and not expired - allow re-activation
                if ($disposableCheck['activated']) {
                    if ($disposableCheck['expired']) {
                        // Code has expired
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
            // Code invalid
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
