<?php
/**
 * Lightweight .env loader
 *
 * Reads key/value pairs from a local .env file and sets them as environment
 * variables for the current request. Existing environment variables are not
 * overwritten.
 */

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath) || !is_readable($envPath)) {
    return;
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    $trimmed = trim($line);

    // Skip comments and empty lines
    if ($trimmed === '' || str_starts_with($trimmed, '#')) {
        continue;
    }

    // Only handle KEY=VALUE lines
    if (strpos($trimmed, '=') === false) {
        continue;
    }

    [$key, $value] = explode('=', $trimmed, 2);
    $key = trim($key);
    $value = trim($value);

    // Remove optional quotes
    $value = trim($value, "\"'");

    // Keep existing environment values
    if (getenv($key) !== false) {
        continue;
    }

    putenv("$key=$value");
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}
