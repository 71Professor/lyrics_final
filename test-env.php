<?php
/**
 * .ENV DEBUG TOOL
 * Zeigt an, ob die .env Datei korrekt geladen wird
 *
 * WICHTIG: Diese Datei nach dem Test LÖSCHEN!
 * Sie zeigt sensible Informationen an!
 */

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>.ENV Debug Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1a1a1a; color: #0f0; }
        h1 { color: #f00; }
        .success { color: #0f0; background: #002200; padding: 10px; margin: 10px 0; }
        .error { color: #f00; background: #220000; padding: 10px; margin: 10px 0; }
        .info { color: #ff0; background: #222200; padding: 10px; margin: 10px 0; }
        .warning { color: #ff0; }
        pre { background: #000; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
<h1>🔍 .ENV DIAGNOSE-TOOL</h1>
<p class='warning'>⚠️ WICHTIG: Diese Datei nach dem Test LÖSCHEN! Sie zeigt sensible Daten an!</p>
";

// Test 1: .env Datei vorhanden?
echo "<h2>📁 Test 1: .env Datei existiert?</h2>";
$envPath = __DIR__ . '/.env';
$envExamplePath = __DIR__ . '/.env.example';

if (file_exists($envPath)) {
    echo "<div class='success'>✅ .env Datei gefunden: $envPath</div>";

    // Dateirechte prüfen
    if (is_readable($envPath)) {
        echo "<div class='success'>✅ .env ist lesbar</div>";
    } else {
        echo "<div class='error'>❌ .env ist NICHT lesbar! Dateiberechtigungen prüfen!</div>";
    }

    // Dateigröße
    $fileSize = filesize($envPath);
    echo "<div class='info'>📏 Dateigröße: $fileSize Bytes</div>";

    if ($fileSize === 0) {
        echo "<div class='error'>❌ .env ist LEER!</div>";
    }
} else {
    echo "<div class='error'>❌ .env Datei NICHT gefunden!</div>";
    echo "<div class='info'>Gesuchter Pfad: $envPath</div>";
}

if (file_exists($envExamplePath)) {
    echo "<div class='info'>ℹ️ .env.example gefunden (Template vorhanden)</div>";
} else {
    echo "<div class='warning'>⚠️ .env.example nicht gefunden</div>";
}

// Test 2: .env Inhalt lesen
echo "<h2>📄 Test 2: .env Inhalt lesen</h2>";
if (file_exists($envPath) && is_readable($envPath)) {
    $envContent = file_get_contents($envPath);
    $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    echo "<div class='info'>Anzahl Zeilen: " . count($envLines) . "</div>";

    echo "<h3>Gefundene Variablen:</h3>";
    echo "<pre>";

    $foundApiKey = false;
    foreach ($envLines as $lineNum => $line) {
        $lineNum++;
        $trimmedLine = trim($line);

        // Kommentare überspringen
        if (strpos($trimmedLine, '#') === 0 || empty($trimmedLine)) {
            echo "Zeile $lineNum: [Kommentar oder leer]\n";
            continue;
        }

        // KEY=VALUE parsen
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Prüfen ob Anführungszeichen
            $hasQuotes = (strpos($value, '"') === 0 || strpos($value, "'") === 0);

            if ($key === 'OPENAI_API_KEY') {
                $foundApiKey = true;
                $valueLength = strlen($value);
                $startsWithSk = (strpos($value, 'sk-') === 0);

                echo "Zeile $lineNum: $key = [" . substr($value, 0, 15) . "..." . substr($value, -5) . "] (Länge: $valueLength)\n";

                if (!$startsWithSk) {
                    echo "         ⚠️ WARNING: Startet NICHT mit 'sk-'!\n";
                }

                if ($hasQuotes) {
                    echo "         ⚠️ WARNING: Enthält Anführungszeichen (werden entfernt beim Laden)\n";
                }

                if ($value === 'sk-proj-YOUR-API-KEY-HERE') {
                    echo "         ❌ ERROR: Platzhalter nicht ersetzt!\n";
                }
            } else {
                echo "Zeile $lineNum: $key = $value\n";
            }
        } else {
            echo "Zeile $lineNum: [Ungültiges Format] $trimmedLine\n";
        }
    }
    echo "</pre>";

    if (!$foundApiKey) {
        echo "<div class='error'>❌ OPENAI_API_KEY wurde NICHT in .env gefunden!</div>";
    }
} else {
    echo "<div class='error'>❌ .env kann nicht gelesen werden!</div>";
}

// Test 3: .env Loader Code simulieren
echo "<h2>🔄 Test 3: .env Laden (wie in config.php)</h2>";
if (file_exists($envPath)) {
    $envFile = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
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
    echo "<div class='success'>✅ .env Laden simuliert</div>";
} else {
    echo "<div class='error'>❌ Keine .env zum Laden</div>";
}

// Test 4: getenv() testen
echo "<h2>🔑 Test 4: API-Key aus Environment lesen</h2>";
$apiKeyFromGetenv = getenv('OPENAI_API_KEY');
$apiKeyFromEnv = $_ENV['OPENAI_API_KEY'] ?? null;
$apiKeyFromServer = $_SERVER['OPENAI_API_KEY'] ?? null;

echo "<pre>";
echo "getenv('OPENAI_API_KEY'):     ";
if ($apiKeyFromGetenv) {
    $keyPreview = substr($apiKeyFromGetenv, 0, 15) . '...' . substr($apiKeyFromGetenv, -5);
    echo "✅ $keyPreview (Länge: " . strlen($apiKeyFromGetenv) . ")\n";

    if ($apiKeyFromGetenv === 'sk-proj-YOUR-API-KEY-HERE') {
        echo "                              ❌ FEHLER: Platzhalter nicht ersetzt!\n";
    }
} else {
    echo "❌ LEER oder FALSE\n";
}

echo "\$_ENV['OPENAI_API_KEY']:      ";
if ($apiKeyFromEnv) {
    echo "✅ " . substr($apiKeyFromEnv, 0, 15) . '...' . substr($apiKeyFromEnv, -5) . "\n";
} else {
    echo "❌ NICHT GESETZT\n";
}

echo "\$_SERVER['OPENAI_API_KEY']:   ";
if ($apiKeyFromServer) {
    echo "✅ " . substr($apiKeyFromServer, 0, 15) . '...' . substr($apiKeyFromServer, -5) . "\n";
} else {
    echo "❌ NICHT GESETZT\n";
}
echo "</pre>";

// Test 5: Was würde config.php verwenden?
echo "<h2>⚙️ Test 5: Welchen Key würde config.php verwenden?</h2>";
$finalKey = getenv('OPENAI_API_KEY') ?: 'sk-proj-YOUR-API-KEY-HERE';
echo "<pre>";
echo "Verwendeter Key: ";
if ($finalKey === 'sk-proj-YOUR-API-KEY-HERE') {
    echo "<span class='error'>❌ FALLBACK (PLATZHALTER)</span>\n";
    echo "\n";
    echo "Das bedeutet: getenv('OPENAI_API_KEY') gibt FALSE oder leer zurück!\n";
} else {
    $keyPreview = substr($finalKey, 0, 15) . '...' . substr($finalKey, -5);
    echo "<span class='success'>✅ $keyPreview</span>\n";
    echo "\n";
    echo "Das sieht gut aus! Der Key würde aus .env geladen.\n";
}
echo "</pre>";

// Zusammenfassung
echo "<h2>📊 ZUSAMMENFASSUNG & LÖSUNGEN</h2>";
echo "<div class='info'>";
if (file_exists($envPath) && $apiKeyFromGetenv && $apiKeyFromGetenv !== 'sk-proj-YOUR-API-KEY-HERE') {
    echo "<h3 style='color: #0f0;'>✅ ALLES OK!</h3>";
    echo "<p>Die .env Datei wird korrekt geladen und der API-Key ist gesetzt.</p>";
    echo "<p><strong>Nächster Schritt:</strong> Teste die Lyrics-Generierung auf deiner Website.</p>";
} else {
    echo "<h3 style='color: #f00;'>❌ PROBLEM GEFUNDEN</h3>";

    if (!file_exists($envPath)) {
        echo "<p><strong>Problem:</strong> .env Datei existiert nicht!</p>";
        echo "<p><strong>Lösung:</strong></p>";
        echo "<ol>";
        echo "<li>Erstelle eine Datei namens <code>.env</code> (mit Punkt am Anfang!)</li>";
        echo "<li>Speichere sie im selben Verzeichnis wie diese Datei</li>";
        echo "<li>Inhalt:<br><code>OPENAI_API_KEY=sk-proj-dein-echter-key</code></li>";
        echo "</ol>";
    } elseif (!$apiKeyFromGetenv) {
        echo "<p><strong>Problem:</strong> .env existiert, aber API-Key wird nicht geladen!</p>";
        echo "<p><strong>Mögliche Ursachen:</strong></p>";
        echo "<ul>";
        echo "<li>Falsches Format in .env (siehe oben bei Test 2)</li>";
        echo "<li>Leerzeichen vor/nach dem Key</li>";
        echo "<li>Anführungszeichen um den Key</li>";
        echo "</ul>";
        echo "<p><strong>Lösung:</strong> .env muss EXAKT so aussehen:<br>";
        echo "<code>OPENAI_API_KEY=sk-proj-abc123xyz...</code></p>";
        echo "<p>KEINE Anführungszeichen, KEINE Leerzeichen!</p>";
    } elseif ($apiKeyFromGetenv === 'sk-proj-YOUR-API-KEY-HERE') {
        echo "<p><strong>Problem:</strong> Platzhalter nicht ersetzt!</p>";
        echo "<p><strong>Lösung:</strong> Ersetze in .env den Platzhalter durch deinen echten OpenAI API-Key</p>";
    }
}
echo "</div>";

echo "<hr>";
echo "<p class='warning'>⚠️ <strong>WICHTIG: Lösche diese test-env.php Datei SOFORT nach dem Test!</strong></p>";
echo "<p>Sie zeigt sensible Informationen an und sollte nicht öffentlich zugänglich sein!</p>";

echo "</body></html>";
?>
