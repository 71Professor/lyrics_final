#!/usr/bin/env php
<?php
/**
 * PASSWORT-HASH GENERATOR
 *
 * Dieses Skript hilft dir, einen sicheren Passwort-Hash für die Admin-Authentifizierung zu erstellen.
 *
 * Verwendung:
 *   php generate-password-hash.php
 *   oder
 *   php generate-password-hash.php "MeinPasswort"
 */

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   PASSWORT-HASH GENERATOR                 ║\n";
echo "║   Metal Lyrics Generator                  ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";

// Passwort aus Kommandozeile oder interaktiv erfragen
if (isset($argv[1])) {
    $password = $argv[1];
} else {
    echo "Bitte gib dein gewünschtes Admin-Passwort ein:\n";
    echo "> ";

    // Passwort-Eingabe (versteckt bei Unix-Systemen)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows: Normale Eingabe
        $password = trim(fgets(STDIN));
    } else {
        // Unix/Linux/Mac: Versteckte Eingabe
        system('stty -echo');
        $password = trim(fgets(STDIN));
        system('stty echo');
        echo "\n";
    }
}

// Validierung
if (empty($password)) {
    echo "❌ FEHLER: Passwort darf nicht leer sein!\n\n";
    exit(1);
}

if (strlen($password) < 8) {
    echo "⚠️  WARNUNG: Passwort ist zu kurz (< 8 Zeichen)\n";
    echo "   Empfohlen: Mindestens 12 Zeichen mit Groß-/Kleinbuchstaben, Zahlen und Sonderzeichen\n\n";
}

// Hash generieren
echo "🔐 Generiere sicheren Hash...\n\n";

$hash = password_hash($password, PASSWORD_BCRYPT);

if ($hash === false) {
    echo "❌ FEHLER: Hash konnte nicht generiert werden!\n\n";
    exit(1);
}

// Ausgabe
echo "✅ Hash erfolgreich generiert!\n\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   DEIN PASSWORT-HASH                      ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";
echo $hash . "\n";
echo "\n";

echo "📝 NÄCHSTE SCHRITTE:\n";
echo "   ═══════════════════════════════════════\n";
echo "   1. Kopiere den Hash oben (die lange Zeichenkette)\n";
echo "   2. Öffne die .env Datei\n";
echo "   3. Setze: ADMIN_PASSWORD_HASH=" . $hash . "\n";
echo "   4. Entferne ADMIN_PASSWORD_PLAIN (falls vorhanden)\n";
echo "   5. Speichern & Fertig!\n";
echo "\n";

echo "⚠️  WICHTIG:\n";
echo "   - Speichere den Hash SICHER in der .env Datei\n";
echo "   - NIEMALS den Hash in Git committen!\n";
echo "   - Die .env Datei ist bereits in .gitignore\n";
echo "\n";

echo "🔒 SICHERHEITS-TIPPS:\n";
echo "   - Verwende ein starkes Passwort (12+ Zeichen)\n";
echo "   - Mix aus Groß-/Kleinbuchstaben, Zahlen, Sonderzeichen\n";
echo "   - Nicht das gleiche Passwort wie anderswo\n";
echo "   - Passwort regelmäßig ändern (alle 3-6 Monate)\n";
echo "\n";

echo "✨ Done!\n";
echo "\n";

exit(0);
