<?php
/**
 * PREMIUM CODE VALIDATION
 * Validiert Premium-Codes und verwaltet Premium-Status
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

// GET: Premium-Status abfragen
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $isPremium = isset($_SESSION['premium_active']) && $_SESSION['premium_active'] === true;
    
    echo json_encode([
        'isPremium' => $isPremium,
        'code' => $_SESSION['premium_code'] ?? null,
        'activatedAt' => $_SESSION['premium_activated_at'] ?? null
    ]);
    exit;
}

// POST: Premium-Code validieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    // CODE AKTIVIEREN
    if ($action === 'activate') {
        $code = trim($input['code'] ?? '');
        
        if (empty($code)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Bitte gib einen Code ein'
            ]);
            exit;
        }
        
        // Prüfe ob Code gültig ist
        $premiumCodes = PREMIUM_CODES;
        
        if (isset($premiumCodes[$code])) {
            // Code ist gültig!
            $_SESSION['premium_active'] = true;
            $_SESSION['premium_code'] = $code;
            $_SESSION['premium_activated_at'] = date('Y-m-d H:i:s');
            
            // Optional: Logging
            if (ENABLE_LOGGING) {
                logMessage("Premium aktiviert: $code", 'info');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Premium erfolgreich aktiviert! 🎉',
                'isPremium' => true
            ]);
            exit;
        } else {
            // Code ungültig
            echo json_encode([
                'success' => false,
                'message' => 'Ungültiger Code. Bitte überprüfe deine Eingabe.'
            ]);
            exit;
        }
    }
    
    // CODE DEAKTIVIEREN
    if ($action === 'deactivate') {
        unset($_SESSION['premium_active']);
        unset($_SESSION['premium_code']);
        unset($_SESSION['premium_activated_at']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Premium deaktiviert',
            'isPremium' => false
        ]);
        exit;
    }
    
    // Unbekannte Action
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Unbekannte Aktion'
    ]);
    exit;
}

// Andere Methods nicht erlaubt
http_response_code(405);
echo json_encode([
    'success' => false,
    'message' => 'Method Not Allowed'
]);
?>
