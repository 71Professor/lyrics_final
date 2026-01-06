<?php
/**
 * METAL LYRICS GENERATOR V2 - API BACKEND
 * OpenAI ChatGPT Integration mit erweiterten Mythologien & Strukturen
 */

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session-security.php';

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
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

// Nur POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// ===== RATE LIMITING (BACKEND) =====
$isPremium = isset($_SESSION['premium_active']) && $_SESSION['premium_active'] === true;

if (!$isPremium) {
    $today = date('Y-m-d');

    if (!isset($_SESSION['usage_data'])) {
        $_SESSION['usage_data'] = [
            'date' => $today,
            'count' => 0
        ];
    }

    if ($_SESSION['usage_data']['date'] !== $today) {
        $_SESSION['usage_data'] = [
            'date' => $today,
            'count' => 0
        ];
    }

    if ($_SESSION['usage_data']['count'] >= MAX_FREE_GENERATIONS) {
        http_response_code(429);
        echo json_encode([
            'error' => 'Rate Limit Exceeded',
            'message' => 'You have reached your daily limit of ' . MAX_FREE_GENERATIONS . ' generations. Upgrade to Premium for unlimited usage!',
            'limit' => MAX_FREE_GENERATIONS,
            'remaining' => 0
        ]);
        exit;
    }
}

// ===== REQUEST VALIDATION =====
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['prompt']) || empty($input['prompt'])) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Bad Request',
        'message' => 'Prompt is required'
    ]);
    exit;
}

$prompt = $input['prompt'];
$mythology = $input['mythology'] ?? 'unknown';
$genre = $input['genre'] ?? 'unknown';
$theme = $input['theme'] ?? 'unknown';
$structure = $input['structure'] ?? 'medium';
$options = $input['options'] ?? [];

// ===== TOKEN BERECHNUNG BASIEREND AUF STRUKTUR =====
$tokenLimits = [
    'short' => 600,
    'medium' => 800,
    'long' => 1000,
    'epic' => 1200,
    'progressive' => 1400,
    'concept' => 1600
];

$maxTokens = $tokenLimits[$structure] ?? 800;

// ===== TEMPERATURE BASIEREND AUF INTENSITÄT =====
$intensity = $options['intensity'] ?? 'high';
$temperature = [
    'moderate' => 0.7,
    'high' => 0.8,
    'extreme' => 0.9
][$intensity] ?? 0.8;

// ===== ERWEITERTE SYSTEM PROMPTS FÜR VERSCHIEDENE STRUKTUREN =====
$systemPrompts = [
    'short' => 'You are an expert Metal lyricist. Create concise, powerful Metal lyrics with strong imagery. Keep it focused and impactful.',
    
    'medium' => 'You are an expert Metal lyricist specializing in mythological themes. Create authentic, powerful Metal lyrics that sound like they could be performed by real Metal bands.',
    
    'long' => 'You are a master Metal lyricist with deep knowledge of mythology and metal music. Create detailed, atmospheric lyrics with strong narrative elements and vivid imagery.',
    
    'epic' => 'You are an elite Metal songwriter capable of crafting epic, multi-section compositions. Create a complete Metal epic with intro, verses, pre-choruses, choruses, bridge, solo section, and outro. Each section should flow naturally into the next while building intensity. Use dramatic imagery and mythological references throughout.',
    
    'progressive' => 'You are a Progressive Metal composer specializing in complex, multi-part compositions. Create a progressive metal piece with distinct parts (I, II, III, IV), interludes, and instrumental breaks. Each part should have its own character while contributing to the overall narrative. Use varying tempos, complex metaphors, and philosophical themes.',
    
    'concept' => 'You are a concept album writer for Metal bands. Create a story-based song structured in three acts (Beginning, Journey/Struggle, Resolution). Each act should advance the narrative while maintaining metal authenticity. Use recurring themes and motifs. Create a complete story arc with introduction, rising action, climax, and resolution/epilogue.'
];

$systemPrompt = $systemPrompts[$structure] ?? SYSTEM_PROMPT;

// ===== OPENAI API CALL =====
$apiData = [
    'model' => OPENAI_MODEL,
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ],
        [
            'role' => 'user',
            'content' => $prompt
        ]
    ],
    'temperature' => $temperature,
    'max_tokens' => $maxTokens,
    'top_p' => 1.0,
    'frequency_penalty' => 0.3,
    'presence_penalty' => 0.3
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($apiData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ],
    CURLOPT_TIMEOUT => 60, // Erhöht für längere Generierungen
    CURLOPT_SSL_VERIFYPEER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Fehlerbehandlung
if ($curlError) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Connection Error',
        'message' => 'Could not connect to OpenAI API'
    ]);
    exit;
}

if ($httpCode !== 200) {
    $errorResponse = json_decode($response, true);
    http_response_code($httpCode);
    echo json_encode([
        'error' => 'OpenAI API Error',
        'message' => $errorResponse['error']['message'] ?? 'Unknown error',
        'code' => $httpCode
    ]);
    exit;
}

// ===== RESPONSE PARSEN =====
$result = json_decode($response, true);

if (!isset($result['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid Response',
        'message' => 'OpenAI returned invalid response'
    ]);
    exit;
}

$lyricsText = $result['choices'][0]['message']['content'];

// ===== TITEL EXTRAHIEREN =====
$lines = explode("\n", $lyricsText);
$titleLine = null;
$title = 'Untitled';

foreach (array_slice($lines, 0, 5) as $line) {
    $line = trim($line);
    
    if (preg_match('/^Title:\s*(.+)$/i', $line, $matches)) {
        $title = trim($matches[1]);
        $titleLine = $line;
        break;
    } elseif (preg_match('/^#+\s*(.+)$/', $line, $matches)) {
        $title = trim($matches[1]);
        $titleLine = $line;
        break;
    } elseif (preg_match('/^\*\*(.+)\*\*$/', $line, $matches)) {
        $title = trim($matches[1]);
        $titleLine = $line;
        break;
    }
}

$lyrics = $lyricsText;
if ($titleLine !== null) {
    $lyrics = str_replace($titleLine, '', $lyricsText);
    $lyrics = trim($lyrics);
}

$lyrics = preg_replace('/\n{3,}/', "\n\n", $lyrics);

// ===== USAGE TRACKING =====
if (!$isPremium) {
    $_SESSION['usage_data']['count']++;
}

// ===== ERWEITERTE STATISTIKEN =====
$stats = [
    'timestamp' => date('Y-m-d H:i:s'),
    'user_type' => $isPremium ? 'Premium' : 'Free',
    'mythology' => $mythology,
    'genre' => $genre,
    'theme' => $theme,
    'structure' => $structure,
    'intensity' => $intensity,
    'language_style' => $options['languageStyle'] ?? 'archaic',
    'tokens_used' => $result['usage']['total_tokens'] ?? 0,
    'model' => $result['model'] ?? OPENAI_MODEL
];

// ===== LOGGING =====
if (ENABLE_LOGGING) {
    $logEntry = date('Y-m-d H:i:s') . " | " . 
                "User: " . ($isPremium ? 'Premium' : 'Free') . " | " .
                "Mythology: $mythology | " . 
                "Genre: $genre | " . 
                "Structure: $structure | " .
                "Theme: $theme | " . 
                "Tokens: " . ($result['usage']['total_tokens'] ?? 0) . "\n";
    
    error_log($logEntry, 3, __DIR__ . '/logs/generation.log');
}

// ===== SUCCESS RESPONSE =====
http_response_code(200);
echo json_encode([
    'title' => $title,
    'lyrics' => $lyrics,
    'metadata' => [
        'mythology' => $mythology,
        'genre' => $genre,
        'theme' => $theme,
        'structure' => $structure,
        'tokens_used' => $result['usage']['total_tokens'] ?? 0,
        'model' => $result['model'] ?? OPENAI_MODEL,
        'is_premium' => $isPremium,
        'remaining_free' => $isPremium ? 999999 : (MAX_FREE_GENERATIONS - $_SESSION['usage_data']['count']),
        'stats' => $stats
    ]
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
