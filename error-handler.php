<?php
/**
 * METAL LYRICS GENERATOR - SECURE ERROR HANDLER
 *
 * Zentrale Fehlerbehandlung mit folgenden Sicherheitsfeatures:
 * - Generische Fehlermeldungen für Benutzer
 * - Detaillierte Fehler nur im Server-Log
 * - Verhindert Information Disclosure
 * - Schutz vor Log Injection
 */

// ========================================
// PHP ERROR CONFIGURATION
// ========================================

// Production Mode: Keine Fehler anzeigen
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Alle Fehler loggen (aber nicht anzeigen)
error_reporting(E_ALL);
ini_set('log_errors', '1');

// Log-Datei setzen (falls nicht in php.ini konfiguriert)
$logDir = __DIR__ . '/logs';
if (!file_exists($logDir)) {
    @mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php-errors.log');

// ========================================
// SECURITY: Expose PHP Off (serverseitig)
// ========================================
// Verhindert dass PHP-Version in Headern erscheint
ini_set('expose_php', '0');

// ========================================
// CUSTOM ERROR HANDLER
// ========================================

/**
 * Custom Error Handler
 * Fängt PHP-Fehler ab und loggt sie sicher
 */
function secureErrorHandler($errno, $errstr, $errfile, $errline) {
    // Nur Fehler behandeln, die in error_reporting enthalten sind
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Fehlertyp bestimmen
    $errorType = match($errno) {
        E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => 'ERROR',
        E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING => 'WARNING',
        E_NOTICE, E_USER_NOTICE => 'NOTICE',
        E_DEPRECATED, E_USER_DEPRECATED => 'DEPRECATED',
        E_STRICT => 'STRICT',
        default => 'UNKNOWN'
    };

    // Detaillierte Fehlermeldung für Log
    $logMessage = sprintf(
        "[%s] %s: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $errorType,
        sanitizeLogString($errstr),
        sanitizeLogString($errfile),
        $errline
    );

    // In Server-Log schreiben
    error_log($logMessage);

    // Bei kritischen Fehlern: Generische Fehlermeldung ausgeben
    if ($errno === E_ERROR || $errno === E_CORE_ERROR || $errno === E_COMPILE_ERROR || $errno === E_USER_ERROR) {
        http_response_code(500);
        sendJsonError('Internal Server Error', 'An unexpected error occurred. Please try again later.');
        exit;
    }

    // Erlaubt PHP die normale Fehlerbehandlung fortzusetzen
    return false;
}

/**
 * Custom Exception Handler
 * Fängt unbehandelte Exceptions ab
 */
function secureExceptionHandler($exception) {
    // Detaillierte Informationen für Log
    $logMessage = sprintf(
        "[%s] EXCEPTION: %s in %s:%d\nStack trace:\n%s",
        date('Y-m-d H:i:s'),
        sanitizeLogString($exception->getMessage()),
        sanitizeLogString($exception->getFile()),
        $exception->getLine(),
        sanitizeLogString($exception->getTraceAsString())
    );

    // In Server-Log schreiben
    error_log($logMessage);

    // Generische Fehlermeldung für Benutzer
    http_response_code(500);
    sendJsonError('Internal Server Error', 'An unexpected error occurred. Please try again later.');
    exit;
}

/**
 * Shutdown Handler für fatale Fehler
 */
function secureShutdownHandler() {
    $error = error_get_last();

    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Detaillierte Informationen für Log
        $logMessage = sprintf(
            "[%s] FATAL ERROR: %s in %s on line %d",
            date('Y-m-d H:i:s'),
            sanitizeLogString($error['message']),
            sanitizeLogString($error['file']),
            $error['line']
        );

        error_log($logMessage);

        // Generische Fehlermeldung für Benutzer
        if (!headers_sent()) {
            http_response_code(500);
            sendJsonError('Internal Server Error', 'An unexpected error occurred. Please try again later.');
        }
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Sanitize strings für Logs (verhindert Log Injection)
 */
function sanitizeLogString($str) {
    // Entferne Control Characters (inklusive Newlines)
    $sanitized = preg_replace('/[\x00-\x1F\x7F]/', '', (string)$str);

    // Limitiere Länge um Log-Dateien nicht zu überlasten
    if (strlen($sanitized) > 1000) {
        $sanitized = substr($sanitized, 0, 1000) . '... (truncated)';
    }

    return $sanitized;
}

/**
 * Sende generische JSON-Fehlermeldung
 */
function sendJsonError($error, $message, $httpCode = 500) {
    if (!headers_sent()) {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode([
        'error' => $error,
        'message' => $message
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * Sichere Fehlerbehandlung für externe API-Calls
 * Verhindert dass API-Fehlerdetails an den Client weitergegeben werden
 */
function handleExternalApiError($httpCode, $errorResponse = null) {
    // Detaillierte Informationen für Server-Log
    $logMessage = sprintf(
        "[%s] External API Error: HTTP %d",
        date('Y-m-d H:i:s'),
        $httpCode
    );

    if ($errorResponse) {
        $logMessage .= "\nResponse: " . sanitizeLogString(json_encode($errorResponse));
    }

    error_log($logMessage);

    // Generische Fehlermeldungen basierend auf HTTP-Code
    $userMessage = match(true) {
        $httpCode >= 500 => 'The service is temporarily unavailable. Please try again later.',
        $httpCode === 429 => 'Rate limit exceeded. Please try again in a moment.',
        $httpCode === 401 || $httpCode === 403 => 'Authentication failed. Please contact support.',
        $httpCode >= 400 => 'Invalid request. Please check your input.',
        default => 'An unexpected error occurred. Please try again later.'
    };

    sendJsonError('Service Error', $userMessage, $httpCode >= 500 ? 503 : 500);
}

/**
 * Sichere Fehlerbehandlung für Dateioperationen
 */
function handleFileOperationError($operation, $filepath) {
    // Detailliert für Log (aber Pfad anonymisieren)
    $sanitizedPath = basename($filepath); // Nur Dateiname, kein vollständiger Pfad
    $logMessage = sprintf(
        "[%s] File Operation Error: %s failed for %s",
        date('Y-m-d H:i:s'),
        $operation,
        $sanitizedPath
    );

    error_log($logMessage);

    // Generische Meldung für Benutzer
    sendJsonError('System Error', 'A system error occurred. Please try again later.', 500);
}

/**
 * Validiere und sichere JSON-Eingabe
 */
function secureJsonDecode($jsonString, $associative = true) {
    $data = json_decode($jsonString, $associative);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $errorMsg = json_last_error_msg();
        error_log(sprintf(
            "[%s] JSON Parse Error: %s",
            date('Y-m-d H:i:s'),
            sanitizeLogString($errorMsg)
        ));

        http_response_code(400);
        sendJsonError('Bad Request', 'Invalid JSON format');
        exit;
    }

    return $data;
}

// ========================================
// REGISTER HANDLERS
// ========================================
set_error_handler('secureErrorHandler');
set_exception_handler('secureExceptionHandler');
register_shutdown_function('secureShutdownHandler');

// ========================================
// LOGGING HELPER (für explizites Logging)
// ========================================

/**
 * Sichere Log-Funktion für Application-Logs
 *
 * @param string $message Die Log-Nachricht
 * @param string $level Log-Level: 'info', 'warning', 'error'
 * @param array $context Zusätzlicher Kontext (wird sanitized)
 */
function secureLog($message, $level = 'info', $context = []) {
    $logDir = __DIR__ . '/logs';
    $logFile = $logDir . '/application.log';

    // Sanitize message
    $message = sanitizeLogString($message);

    // Format context
    $contextStr = '';
    if (!empty($context)) {
        $contextStr = ' | Context: ' . sanitizeLogString(json_encode($context));
    }

    // Build log entry
    $logEntry = sprintf(
        "[%s] [%s] %s%s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message,
        $contextStr
    );

    // Write to log file
    @error_log($logEntry, 3, $logFile);
}

?>
