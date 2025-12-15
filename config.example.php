<?php
/**
 * METAL LYRICS GENERATOR - CONFIGURATION TEMPLATE
 *
 * ⚠️ IMPORTANT: This is a template file!
 *
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to "config.php"
 * 2. Enter your OpenAI API Key
 * 3. Add your Premium Codes
 * 4. Save and upload to your server
 *
 * NEVER share config.php publicly or upload to Git!
 */

// ========================================
// LOAD ENVIRONMENT VARIABLES (RECOMMENDED)
// ========================================

/**
 * Load .env file if it exists (RECOMMENDED METHOD)
 *
 * This is the most secure way to manage API keys:
 * 1. Create a .env file (copy from .env.example)
 * 2. Add your API key there: OPENAI_API_KEY=sk-proj-...
 * 3. The .env file is automatically ignored by Git
 *
 * If no .env file exists, the config below will be used as fallback.
 */
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        // Skip comments and empty lines
        if (strpos(trim($line), '#') === 0 || empty(trim($line))) {
            continue;
        }

        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = trim($value, '"\'');

            // Set as environment variable
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// ========================================
// OPENAI API SETTINGS
// ========================================

/**
 * OpenAI API Key
 *
 * RECOMMENDED: Use .env file (see above)
 * ALTERNATIVE: Enter key directly here
 *
 * Where to get the key?
 * 1. Go to: https://platform.openai.com/api-keys
 * 2. Create a new API Key
 * 3a. RECOMMENDED: Add to .env file: OPENAI_API_KEY=sk-proj-...
 * 3b. ALTERNATIVE: Enter here (starts with "sk-proj-...")
 *
 * IMPORTANT: Only enter the key here, NOT in other files!
 */
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: 'sk-proj-YOUR-API-KEY-HERE');

/**
 * OpenAI Model Selection
 *
 * Available Models:
 * - 'gpt-4o'           → Recommended! Best balance (quality/price)
 * - 'gpt-4-turbo'      → Very good, but more expensive
 * - 'gpt-3.5-turbo'    → Cheaper, but lower quality
 *
 * Cost per 1000 Tokens (approx. 750 words):
 * - gpt-4o:        Input $2.50  | Output $10.00
 * - gpt-4-turbo:   Input $10.00 | Output $30.00
 * - gpt-3.5-turbo: Input $0.50  | Output $1.50
 *
 * Average lyrics = ~600 Tokens
 * → gpt-4o: ~$0.01 per generation ✅
 */
define('OPENAI_MODEL', getenv('OPENAI_MODEL') ?: 'gpt-4o');

// ========================================
// PREMIUM & RATE LIMITING
// ========================================

/**
 * Maximum free generations per day
 * (used in script.js)
 */
define('MAX_FREE_GENERATIONS', 5);

/**
 * Enable rate limiting?
 * Prevents too many API calls from individual IPs
 *
 * Requires: Session or database for tracking
 */
define('ENABLE_RATE_LIMITING', getenv('ENABLE_RATE_LIMITING') === 'true' ? true : false);

/**
 * Premium Codes
 * Users can enter these codes to unlock Premium features
 *
 * Format: 'CODE' => 'Description'
 */
define('PREMIUM_CODES', [
    'METAL2024-DEMO'  => 'Demo Premium Code',
    // Add your codes here:
    // 'METAL2024-VIP'   => 'VIP Access',
    // 'METAL2024-ABC123' => 'User: John Doe',
]);

/**
 * Disposable Codes Settings
 * 24-Stunden-Codes: Codes sind ab erster Aktivierung 24 Stunden gültig
 */
define('ENABLE_DISPOSABLE_CODES', true);
define('DISPOSABLE_CODES_FILE', __DIR__ . '/disposable_codes.json');
define('DISPOSABLE_CODE_PACKAGE_PRICE', 5.00); // EUR
define('DISPOSABLE_CODE_PACKAGE_SIZE', 1); // 1 Code pro Paket
define('DISPOSABLE_CODE_DURATION_HOURS', 24); // Gültigkeitsdauer in Stunden

// ========================================
// LOGGING & DEBUGGING
// ========================================

/**
 * Enable logging?
 * Saves generations in logfile for statistics
 *
 * Logfile: api/logs/generation.log
 *
 * IMPORTANT: Create the "logs" folder in the api/ directory!
 * And set permissions to 755
 */
define('ENABLE_LOGGING', getenv('ENABLE_LOGGING') === 'true' ? true : false);

/**
 * Debug mode
 * Shows detailed error messages
 *
 * ⚠️ Only enable during development!
 * ALWAYS false on live server!
 */
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' ? true : false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ========================================
// SECURITY
// ========================================

/**
 * Allowed domains for CORS
 * List of domains that are allowed to access the API
 *
 * Examples:
 * - 'https://your-domain.com'
 * - 'https://www.your-domain.com'
 *
 * Leave empty = all domains allowed (*)
 */
define('ALLOWED_ORIGINS', [
    // 'https://your-domain.com',
    // 'https://www.your-domain.com',
]);

/**
 * IP Blacklist
 * IPs that should be blocked
 *
 * Example: ['192.168.1.100', '10.0.0.50']
 */
define('IP_BLACKLIST', [
    // Add blocked IPs here
]);

// ========================================
// ADVANCED: CUSTOM PROMPTS
// ========================================

/**
 * System Prompt for ChatGPT
 * Defines the AI's behavior
 *
 * Can be customized for better results
 */
define('SYSTEM_PROMPT',
    'You are an expert Metal lyricist specializing in mythological themes. ' .
    'Create authentic, powerful Metal lyrics that sound like they could be ' .
    'performed by real Metal bands. Use proper song structure with ' .
    '[Verse], [Chorus], [Bridge] markers. Be creative but stay true to ' .
    'the Metal genre and mythological themes.'
);

/**
 * Prompt templates for different genres
 * Improves quality for specific genres
 *
 * NOT used in generate-lyrics.php (optional)
 */
define('GENRE_PROMPTS', [
    'thrash' => 'Fast, aggressive, precise lyrics with powerful imagery',
    'death' => 'Brutal, technical, dark lyrics with complex metaphors',
    'black' => 'Atmospheric, cold, raw lyrics with nature imagery',
    'power' => 'Epic, melodic, heroic lyrics with grand narratives',
    'doom' => 'Slow, heavy, melancholic lyrics with doom-laden themes',
    'folk' => 'Nature-connected, traditional lyrics with storytelling'
]);

// ========================================
// VALIDATION
// ========================================

/**
 * Check if API Key is set
 */
if (OPENAI_API_KEY === 'sk-proj-YOUR-API-KEY-HERE' || empty(OPENAI_API_KEY)) {
    if (DEBUG_MODE) {
        die('❌ ERROR: Please enter your OpenAI API Key in config.php!');
    }
}

/**
 * Check if API Key has the correct format
 */
if (!preg_match('/^sk-(proj-)?[a-zA-Z0-9]{20,}/', OPENAI_API_KEY)) {
    if (DEBUG_MODE) {
        die('❌ ERROR: API Key has invalid format! Should start with "sk-proj-".');
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Check if a Premium code is valid
 *
 * @param string $code The entered code
 * @return bool True if valid
 */
function verifyPremiumCode($code) {
    $codes = PREMIUM_CODES;
    return isset($codes[$code]);
}

/**
 * Check if IP is blocked
 *
 * @return bool True if blocked
 */
function isIPBlocked() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    $blacklist = IP_BLACKLIST;
    return in_array($clientIP, $blacklist);
}

/**
 * Logging function
 *
 * @param string $message The log message
 * @param string $type The log type (info, error, warning)
 */
function logMessage($message, $type = 'info') {
    if (!ENABLE_LOGGING) return;

    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";

    error_log($logEntry, 3, $logDir . '/app.log');
}

/**
 * Load disposable codes from JSON file
 *
 * @return array The codes data
 */
function loadDisposableCodes() {
    $file = DISPOSABLE_CODES_FILE;
    if (!file_exists($file)) {
        return ['codes' => [], 'metadata' => []];
    }

    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return $data ?: ['codes' => [], 'metadata' => []];
}

/**
 * Save disposable codes to JSON file
 *
 * @param array $data The codes data
 * @return bool Success status
 */
function saveDisposableCodes($data) {
    $file = DISPOSABLE_CODES_FILE;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($file, $json) !== false;
}

/**
 * Check if a disposable code is valid and not expired
 *
 * @param string $code The code to check
 * @return array ['valid' => bool, 'activated' => bool, 'expired' => bool, 'data' => array, 'remaining_hours' => float]
 */
function checkDisposableCode($code) {
    if (!ENABLE_DISPOSABLE_CODES) {
        return ['valid' => false, 'activated' => false, 'expired' => false, 'data' => null, 'remaining_hours' => 0];
    }

    $data = loadDisposableCodes();

    if (!isset($data['codes'][$code])) {
        return ['valid' => false, 'activated' => false, 'expired' => false, 'data' => null, 'remaining_hours' => 0];
    }

    $codeData = $data['codes'][$code];
    $isActivated = !empty($codeData['activated_at']);
    $isExpired = false;
    $remainingHours = 0;

    if ($isActivated && !empty($codeData['expires_at'])) {
        $expiresAt = strtotime($codeData['expires_at']);
        $now = time();
        $isExpired = ($now >= $expiresAt);

        if (!$isExpired) {
            $remainingSeconds = $expiresAt - $now;
            $remainingHours = $remainingSeconds / 3600;
        }
    }

    return [
        'valid' => true,
        'activated' => $isActivated,
        'expired' => $isExpired,
        'data' => $codeData,
        'remaining_hours' => $remainingHours
    ];
}

/**
 * Activate a disposable code (sets activation time and expiration time)
 *
 * @param string $code The code to activate
 * @return bool Success status
 */
function activateDisposableCode($code) {
    if (!ENABLE_DISPOSABLE_CODES) {
        return false;
    }

    $data = loadDisposableCodes();

    if (!isset($data['codes'][$code])) {
        return false;
    }

    $now = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d H:i:s', strtotime('+' . DISPOSABLE_CODE_DURATION_HOURS . ' hours'));

    // Activate code
    $data['codes'][$code]['activated_at'] = $now;
    $data['codes'][$code]['expires_at'] = $expiresAt;
    $data['codes'][$code]['activation_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Update metadata
    if (!isset($data['metadata']['total_codes_activated'])) {
        $data['metadata']['total_codes_activated'] = 0;
    }
    $data['metadata']['total_codes_activated']++;
    $data['metadata']['last_updated'] = $now;

    return saveDisposableCodes($data);
}

// ========================================
// DONE! ✅
// ========================================

/**
 * Configuration successfully loaded!
 *
 * Next steps:
 * 1. Enter your OpenAI API Key (see above)
 * 2. Save this file
 * 3. Upload it to your web hosting
 * 4. Test the API: https://your-domain.com/api/generate-lyrics.php
 *
 * Support:
 * - README.md for details
 * - QUICK-START.md for instructions
 * - Email: contact@metal-lyrics-ai.com
 */
?>
