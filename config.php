<?php
/**
 * METAL LYRICS GENERATOR - CONFIGURATION
 * 
 * ⚠️ WICHTIG: Diese Datei enthält deinen API-Key!
 * NIEMALS öffentlich teilen oder zu Git hochladen!
 * 
 * Füge diese Datei zur .gitignore hinzu:
 * echo "api/config.php" >> .gitignore
 */

// ========================================
// OPENAI API EINSTELLUNGEN
// ========================================

/**
 * OpenAI API Key
 * 
 * Wo bekomme ich den Key?
 * 1. Gehe zu: https://platform.openai.com/api-keys
 * 2. Erstelle einen neuen API Key
 * 3. Kopiere den Key hierher (beginnt mit "sk-proj-...")
 * 
 * WICHTIG: Trage den Key NUR hier ein, NICHT in anderen Dateien!
 */
define('OPENAI_API_KEY', 'sk-proj-DEIN-API-KEY-HIER');

/**
 * OpenAI Model Auswahl
 * 
 * Verfügbare Models:
 * - 'gpt-4o'           → Empfohlen! Beste Balance (Qualität/Preis)
 * - 'gpt-4-turbo'      → Sehr gut, aber teurer
 * - 'gpt-3.5-turbo'    → Günstiger, aber schlechtere Qualität
 * 
 * Kosten pro 1000 Tokens (ca. 750 Wörter):
 * - gpt-4o:        Input $2.50  | Output $10.00
 * - gpt-4-turbo:   Input $10.00 | Output $30.00
 * - gpt-3.5-turbo: Input $0.50  | Output $1.50
 * 
 * Durchschnittliche Lyrics = ~600 Tokens
 * → gpt-4o: ~$0.01 pro Generierung ✅
 */
define('OPENAI_MODEL', 'gpt-4o');

// ========================================
// PREMIUM & RATE LIMITING
// ========================================

/**
 * Maximale kostenlose Generierungen pro Tag
 * (wird in script.js verwendet)
 */
define('MAX_FREE_GENERATIONS', 5);

/**
 * Rate Limiting aktivieren?
 * Verhindert zu viele API-Calls von einzelnen IPs
 * 
 * Benötigt: Session oder Datenbank für Tracking
 */
define('ENABLE_RATE_LIMITING', false);

/**
 * Premium Codes
 * User können diese Codes eingeben um Premium-Features freizuschalten
 * 
 * Format: 'CODE' => 'Beschreibung'
 */
define('PREMIUM_CODES', [
    'METAL2024-DEMO'  => 'Demo Premium Code',
    'METAL2024-VIP'   => 'VIP Access',
    // Füge hier weitere Codes hinzu
    // 'METAL2024-ABC123' => 'User: Max Mustermann',
]);

// ========================================
// LOGGING & DEBUGGING
// ========================================

/**
 * Logging aktivieren?
 * Speichert Generierungen in Logfile für Statistiken
 * 
 * Logfile: api/logs/generation.log
 * 
 * WICHTIG: Erstelle den Ordner "logs" im api/ Verzeichnis!
 * Und setze Berechtigungen auf 755
 */
define('ENABLE_LOGGING', false);

/**
 * Debug-Modus
 * Zeigt detaillierte Fehlermeldungen
 * 
 * ⚠️ Nur während Entwicklung aktivieren!
 * Auf Live-Server IMMER false!
 */
define('DEBUG_MODE', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ========================================
// SICHERHEIT
// ========================================

/**
 * Erlaubte Domains für CORS
 * Liste von Domains, die auf die API zugreifen dürfen
 * 
 * Beispiele:
 * - 'https://deine-domain.de'
 * - 'https://www.deine-domain.de'
 * 
 * Leer lassen = alle Domains erlaubt (*)
 */
define('ALLOWED_ORIGINS', [
    // 'https://deine-domain.de',
    // 'https://www.deine-domain.de',
]);

/**
 * IP-Blacklist
 * IPs die blockiert werden sollen
 * 
 * Beispiel: ['192.168.1.100', '10.0.0.50']
 */
define('IP_BLACKLIST', [
    // Füge hier blockierte IPs hinzu
]);

// ========================================
// ADVANCED: CUSTOM PROMPTS
// ========================================

/**
 * System Prompt für ChatGPT
 * Definiert das Verhalten der AI
 * 
 * Kann angepasst werden für bessere Ergebnisse
 */
define('SYSTEM_PROMPT', 
    'You are an expert Metal lyricist specializing in mythological themes. ' .
    'Create authentic, powerful Metal lyrics that sound like they could be ' .
    'performed by real Metal bands. Use proper song structure with ' .
    '[Verse], [Chorus], [Bridge] markers. Be creative but stay true to ' .
    'the Metal genre and mythological themes.'
);

/**
 * Prompt-Templates für verschiedene Genres
 * Verbessert die Qualität für spezifische Genres
 * 
 * Wird in generate-lyrics.php NICHT verwendet (optional)
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
// VALIDIERUNG
// ========================================

/**
 * Prüfe ob API Key gesetzt ist
 */
if (OPENAI_API_KEY === 'sk-proj-DEIN-API-KEY-HIER' || empty(OPENAI_API_KEY)) {
    if (DEBUG_MODE) {
        die('❌ ERROR: Bitte trage deinen OpenAI API Key in config.php ein!');
    }
}

/**
 * Prüfe ob API Key das richtige Format hat
 */
if (!preg_match('/^sk-(proj-)?[a-zA-Z0-9]{20,}/', OPENAI_API_KEY)) {
    if (DEBUG_MODE) {
        die('❌ ERROR: API Key hat ungültiges Format! Sollte mit "sk-proj-" beginnen.');
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Prüfe ob ein Premium Code gültig ist
 * 
 * @param string $code Der eingegebene Code
 * @return bool True wenn gültig
 */
function verifyPremiumCode($code) {
    $codes = PREMIUM_CODES;
    return isset($codes[$code]);
}

/**
 * Prüfe ob IP geblockt ist
 * 
 * @return bool True wenn geblockt
 */
function isIPBlocked() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    $blacklist = IP_BLACKLIST;
    return in_array($clientIP, $blacklist);
}

/**
 * Logging Funktion
 * 
 * @param string $message Die Log-Nachricht
 * @param string $type Der Log-Typ (info, error, warning)
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

// ========================================
// DONE! ✅
// ========================================

/**
 * Configuration erfolgreich geladen!
 * 
 * Nächste Schritte:
 * 1. Trage deinen OpenAI API Key ein (siehe oben)
 * 2. Speichere diese Datei
 * 3. Lade sie auf deinen All-Inkl Webspace hoch
 * 4. Teste die API: https://deine-domain.de/api/generate-lyrics.php
 * 
 * Support:
 * - README.md für Details
 * - QUICK-START.md für Anleitung
 * - Email: contact@metal-lyrics-ai.com
 */
?>
