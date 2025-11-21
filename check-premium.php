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

    echo json_encode([
        'isPremium' => $isPremium,
        'code' => $_SESSION['premium_code'] ?? null,
        'activatedAt' => $_SESSION['premium_activated_at'] ?? null
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

        // Check if code is valid
        $premiumCodes = PREMIUM_CODES;

        if (isset($premiumCodes[$code])) {
            // Code is valid!
            $_SESSION['premium_active'] = true;
            $_SESSION['premium_code'] = $code;
            $_SESSION['premium_activated_at'] = date('Y-m-d H:i:s');

            // Optional: Logging
            if (ENABLE_LOGGING) {
                logMessage("Premium activated: $code", 'info');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Premium successfully activated! 🎉',
                'isPremium' => true
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
