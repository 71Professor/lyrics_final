# 🎸 Metal Lyrics Generator - Vollständige Dokumentation

**Version:** 2.0 (mit 24-Stunden-Premium-Codes)
**Stand:** November 2025
**Status:** ✅ Production Ready

---

## 📋 Inhaltsverzeichnis

1. [Projektübersicht](#projektübersicht)
2. [Features](#features)
3. [Installation](#installation)
4. [Konfiguration](#konfiguration)
5. [Premium-System](#premium-system)
6. [Nutzung](#nutzung)
7. [Technische Details](#technische-details)
8. [Monetarisierung](#monetarisierung)
9. [Troubleshooting](#troubleshooting)
10. [Marketing & Launch](#marketing--launch)

---

## 📖 Projektübersicht

Der **Metal Lyrics Generator** ist ein KI-gestützter Generator für authentische Metal-Lyrics basierend auf Mythologien aus aller Welt. Die App nutzt OpenAI's GPT-4o API und bietet sowohl kostenlose als auch Premium-Features.

### Technologie-Stack

- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP 8.0+
- **API:** OpenAI GPT-4o
- **Hosting:** Optimiert für All-Inkl.com (aber universell einsetzbar)
- **Premium:** Zeitbasierte Codes (24h) + Permanente Codes

### Projektstruktur

```
lyrics_final/
├── index.html                      # Hauptseite
├── style.css                       # Basis-Styling
├── style-extended.css              # Erweiterte Styles
├── script.js                       # Frontend-Logik
├── config.php                      # Konfiguration (API-Key!)
├── generate-lyrics.php             # OpenAI API Integration
├── check-premium.php               # Premium-Code-Validierung
├── generate-disposable-codes.php   # Code-Generator
├── view-code-statistics.php        # Statistik-Tool
├── disposable_codes.json           # 24h-Codes-Datenbank
└── *.md                            # Dokumentation
```

---

## ✨ Features

### Kostenlose Features (Free Tier)

- ✅ **5 Generierungen pro Tag**
- ✅ **4 Mythologien:** Nordisch, Keltisch, Griechisch, Slawisch
- ✅ **6 Genres:** Thrash, Death, Black, Power, Doom, Folk Metal
- ✅ **2 Song-Strukturen:** Short (Verse + Chorus), Medium (2 Verses + Chorus)
- ✅ **Themen-Eingabe:** Freie Eingabe (z.B. "War", "Revenge", "Apocalypse")
- ✅ **Copy-to-Clipboard:** Lyrics direkt kopieren
- ✅ **Export als TXT:** Download als Textdatei

### Premium Features

- 🔓 **Unbegrenzte Generierungen**
- 🌍 **12+ Mythologien:**
  - **Asien:** Japanisch, Chinesisch, Hindu
  - **Amerika/Afrika:** Aztekisch, Maya, Afrikanisch
  - **Antike:** Ägyptisch, Mesopotamisch
  - **Okkult:** Occult, Lovecraft, Gothic Horror
- 🎭 **Erweiterte Strukturen:**
  - Long (3 Verses + Bridge + Chorus)
  - Epic (Intro → Verses → Bridge → Solo → Outro)
  - Progressive (Multi-Part, komplex)
  - Concept (Story-basiert, 3 Akte)
- 🎵 **Zusätzliche Genres:** Heavy Metal, Metalcore, Gothic Metal
- 🎨 **Anpassungsoptionen:**
  - Intensität (Moderate, High, Extreme)
  - Sprachstil (Modern, Archaic/Poetic, Brutal/Direct)
- 📄 **Export-Optionen:** TXT, PDF (geplant)

---

## 🚀 Installation

### Voraussetzungen

- **Webserver:** Apache/Nginx mit PHP 8.0+
- **PHP-Extensions:** cURL, JSON, Sessions
- **OpenAI API-Key:** Von https://platform.openai.com/api-keys
- **Hosting:** All-Inkl (empfohlen) oder beliebiger PHP-Hoster

### Schritt 1: Dateien hochladen

Via FTP (FileZilla) oder All-Inkl KAS:

```
Hochladen:
✅ index.html
✅ style.css
✅ style-extended.css
✅ script.js
✅ config.php
✅ generate-lyrics.php
✅ check-premium.php
✅ generate-disposable-codes.php
✅ view-code-statistics.php
✅ disposable_codes.json
```

**Berechtigungen setzen:**
- PHP-Dateien: `644`
- JSON-Datei: `644` (muss beschreibbar sein)
- Verzeichnis: `755`

### Schritt 2: OpenAI API-Key konfigurieren

**Öffne `config.php` (Zeile 26):**

```php
define('OPENAI_API_KEY', 'sk-proj-DEIN-ECHTER-KEY-HIER');
```

**API-Key besorgen:**
1. Gehe zu https://platform.openai.com/api-keys
2. Registriere dich (kostenlos, oft $5 Startguthaben)
3. "Create new secret key" klicken
4. Key kopieren (beginnt mit `sk-proj-...`)

**Kosten:**
- Model: `gpt-4o` (bereits in config.php gesetzt)
- Durchschnitt: ~$0.01 pro Generierung
- Bei 100 Generierungen/Tag: ~$30/Monat

### Schritt 3: Demo-Modus deaktivieren

**Öffne `script.js` (Zeile 9):**

```javascript
DEMO_MODE: false,  // Von true auf false ändern!
```

### Schritt 4: Testen

1. Öffne `https://deine-domain.de`
2. Wähle Mythologie, Genre, Thema
3. Klick "LYRICS GENERIEREN"
4. Nach ~5 Sekunden → Echte KI-Lyrics! ✅

---

## ⚙️ Konfiguration

### config.php - Wichtige Einstellungen

```php
// ===== OPENAI =====
define('OPENAI_API_KEY', 'sk-proj-...');  // WICHTIG!
define('OPENAI_MODEL', 'gpt-4o');         // Empfohlen

// ===== RATE LIMITING =====
define('MAX_FREE_GENERATIONS', 5);        // Free User Limit
define('ENABLE_RATE_LIMITING', false);    // Backend-Limiting

// ===== REGULÄRE PREMIUM-CODES (Dauerhaft) =====
define('PREMIUM_CODES', [
    'METAL2024-DEMO'  => 'Demo Premium Code',
    'METAL2024-VIP'   => 'VIP Access',
    // Weitere Codes hinzufügen...
]);

// ===== 24-STUNDEN-CODES (Disposable) =====
define('ENABLE_DISPOSABLE_CODES', true);
define('DISPOSABLE_CODES_FILE', __DIR__ . '/disposable_codes.json');
define('DISPOSABLE_CODE_PACKAGE_PRICE', 5.00);      // EUR
define('DISPOSABLE_CODE_PACKAGE_SIZE', 1);          // Codes pro Paket
define('DISPOSABLE_CODE_DURATION_HOURS', 24);       // Gültigkeit

// ===== LOGGING =====
define('ENABLE_LOGGING', false);  // Auf true für Statistiken
define('DEBUG_MODE', false);      // NUR in Entwicklung!
```

### Sicherheit

**Wichtig:** `config.php` NIEMALS öffentlich teilen!

Die Datei ist durch `.htaccess` geschützt (falls vorhanden):

```apache
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

**Zusätzlicher Schutz (optional):**
- Verschiebe `config.php` außerhalb des Web-Root
- Nutze Environment Variables (falls vom Hoster unterstützt)

---

## 🔓 Premium-System

Die aktuelle Version unterstützt **zwei Arten von Premium-Codes:**

### 1. Reguläre Premium-Codes (Dauerhaft)

**Eigenschaften:**
- ✅ Unbegrenzte Gültigkeit
- ✅ Kann auf allen Geräten verwendet werden
- ✅ Bleibt in der Session aktiv
- ✅ Ideal für Lifetime-Käufe

**Verwaltung in `config.php`:**

```php
define('PREMIUM_CODES', [
    'METAL2024-ABC123' => 'Kunde: Max Mustermann',
    'GUMROAD-XYZ789'   => 'Gumroad Kauf #1',
]);
```

**Hinzufügen:**
- Einfach neuen Code in Array einfügen
- Speichern → Sofort aktiv!

**Entfernen:**
- Zeile auskommentieren oder löschen
- User verliert sofort Zugang

### 2. Disposable 24-Stunden-Codes

**Eigenschaften:**
- ⏱️ **Gültig für 24 Stunden ab erster Aktivierung**
- 🔄 **Kann während der 24h auf mehreren Geräten verwendet werden**
- 💰 **Preis:** 5,00 EUR pro Code
- 📊 **Tracking:** Aktivierungszeit, IP, Ablaufzeit
- 🗄️ **Speicherung:** JSON-Datei (`disposable_codes.json`)

#### Codes generieren

**Via PHP-Skript:**

```bash
# Einzelnen Code generieren
php generate-disposable-codes.php 1 "Paket #1"

# 10 Codes für Verkauf generieren
php generate-disposable-codes.php 10 "Verkaufs-Batch November"

# Standard (1 Code)
php generate-disposable-codes.php
```

**Ausgabe:**

```
╔════════════════════════════════════════════╗
║   DISPOSABLE CODE GENERATOR               ║
║   Metal Lyrics Generator                  ║
╚════════════════════════════════════════════╝

📦 PACKAGE INFORMATION:
   Package: Paket #1
   Price: 5.00 EUR
   Codes: 1
   Duration: 24 hours per code

🔑 GENERATED CODES:
   (Each code is valid for 24 hours after first activation)
    1. METAL-ABC9XYZ3PQR7
```

**Code-Format:**
- Präfix: `METAL-`
- Länge: 12 Zeichen (Großbuchstaben + Zahlen)
- Ohne verwirrende Zeichen (I, O, 0, 1)
- Beispiel: `METAL-ABC9XYZ3PQR7`

#### Code-Statistiken anzeigen

```bash
# Übersicht
php view-code-statistics.php

# Detailliert mit allen Codes
php view-code-statistics.php --detailed

# Nur unbenutzte Codes
php view-code-statistics.php --detailed --unused

# Nur aktive Codes (noch nicht abgelaufen)
php view-code-statistics.php --detailed --active

# Nur abgelaufene Codes
php view-code-statistics.php --detailed --expired
```

**Ausgabe:**

```
╔════════════════════════════════════════════╗
║   CODE STATISTICS VIEWER                  ║
║   Metal Lyrics Generator                  ║
╚════════════════════════════════════════════╝

📊 OVERALL STATISTICS:
   ═══════════════════════════════════════
   Total Codes:        10
   Activated Codes:    3 (30.0%)
   Unused Codes:       7
   Active Codes:       2 (not expired)
   Expired Codes:      1
   Package Price:      5.00 EUR
   Code Duration:      24 hours
   Total Revenue:      15.00 EUR
```

#### Code-Aktivierung (User-Sicht)

**Frontend-Flow:**

1. User öffnet die Website
2. Scrollt zum Premium-Bereich
3. Gibt Code ein: `METAL-ABC9XYZ3PQR7`
4. Klickt "🔓 Activate"
5. **Erstaktivierung:**
   - ✅ Code wird aktiviert
   - ⏱️ Ablaufzeit wird gesetzt (24h ab jetzt)
   - 🎉 Meldung: "Premium successfully activated! Valid for 24 hours."
6. **Wiederverwendung (anderes Gerät):**
   - ✅ Code funktioniert weiterhin
   - ⏱️ Zeigt verbleibende Zeit: "Code is valid for 12.5 more hours."
7. **Nach Ablauf (>24h):**
   - ❌ Code funktioniert nicht mehr
   - 💰 User muss neuen Code kaufen

#### Backend-Validierung

**In `check-premium.php`:**

```php
// 1. Prüfe Disposable Codes (wenn aktiviert)
if (ENABLE_DISPOSABLE_CODES) {
    $check = checkDisposableCode($code);

    if ($check['valid']) {
        if ($check['activated'] && $check['expired']) {
            // Code abgelaufen
            return error: "Code has expired"
        }

        if ($check['activated'] && !$check['expired']) {
            // Noch gültig - Re-Aktivierung erlauben
            return success: "Premium activated! {remainingHours} hours left"
        }

        // Noch nicht aktiviert - aktivieren!
        activateDisposableCode($code);
        return success: "Premium activated! Valid for 24 hours"
    }
}

// 2. Prüfe reguläre Premium-Codes (Fallback)
if (isset(PREMIUM_CODES[$code])) {
    return success: "Premium activated (permanent)"
}

// 3. Code ungültig
return error: "Invalid code"
```

**Wichtig:** Validierung erfolgt **serverseitig** → nicht umgehbar!

---

## 📱 Nutzung

### Free User

1. **Generator öffnen**
2. **Mythologie wählen:** Z.B. "Nordisch"
3. **Genre wählen:** Z.B. "Thrash Metal"
4. **Thema eingeben:** Z.B. "War"
5. **Struktur wählen:** "Medium" (Standard)
6. **"LYRICS GENERIEREN" klicken**
7. **Warten:** ~5 Sekunden
8. **Ergebnis:** Authentische Metal-Lyrics! 🎉

**Limitierung:**
- Max. 5 Generierungen pro Tag
- Reset um Mitternacht (serverseitig)
- Nach Erreichen: "Upgrade to Premium" Meldung

### Premium User

**Code aktivieren:**

1. Scrolle zum Premium-Bereich (unter dem Generator)
2. Gib Code ein: `METAL-ABC9XYZ3PQR7`
3. Klick "🔓 Activate"
4. Status ändert sich zu: "✅ Premium Aktiv"

**Premium-Vorteile nutzen:**

- 🔓 Unbegrenzte Generierungen
- 🌍 Alle 12+ Mythologien auswählen
- 🎭 Erweiterte Strukturen wählen:
  - Long, Epic, Progressive, Concept
- 🎨 Zusätzliche Optionen:
  - Intensität (Moderate/High/Extreme)
  - Sprachstil (Modern/Archaic/Brutal)
- 📄 Export als TXT

**Bei 24h-Codes:**
- ⏱️ Verbleibende Zeit wird angezeigt
- 🔄 Code funktioniert auf allen Geräten (innerhalb 24h)
- ⚠️ Nach Ablauf: Neuen Code kaufen

---

## 🔧 Technische Details

### Frontend (script.js)

**Hauptfunktionen:**

```javascript
// Premium-Status laden
async function loadPremiumStatus()

// Premium-Code aktivieren
async function activatePremiumCode()

// Lyrics generieren
async function generateLyrics(formData)

// UI-Updates
function updatePremiumUI()
```

**Config:**

```javascript
const CONFIG = {
    DEMO_MODE: false,        // true = Demo-Daten
    API_BASE: '',            // Leer = selber Server
};
```

**Demo-Modus:**
- Wenn `DEMO_MODE: true`:
  - Keine echten API-Calls
  - Vorgefertigte Beispiel-Lyrics
  - Für lokales Testen ohne API-Key

### Backend (PHP)

**generate-lyrics.php:**

```php
// 1. Session starten
session_start();

// 2. Premium-Status prüfen
$isPremium = $_SESSION['premium_active'] ?? false;

// 3. Rate-Limiting (wenn nicht Premium)
if (!$isPremium) {
    // Limit erreicht? → 429 Error
}

// 4. OpenAI API-Call
$response = callOpenAI($prompt, $maxTokens, $temperature);

// 5. Response parsen und zurückgeben
return json_encode(['lyrics' => $lyrics, 'title' => $title]);
```

**check-premium.php:**

```php
// GET: Status abfragen
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    return ['isPremium' => $isPremium, 'expiresAt' => ...];
}

// POST: Code aktivieren
if ($action === 'activate') {
    // 1. Disposable Code prüfen
    // 2. Regulären Code prüfen
    // 3. Session setzen
    // 4. Response zurückgeben
}
```

### Datenbank (disposable_codes.json)

**Struktur:**

```json
{
    "codes": {
        "METAL-ABC9XYZ3PQR7": {
            "created_at": "2025-11-21 14:30:00",
            "batch_id": "20251121-143000",
            "package_name": "Paket #1",
            "package_price": 5.00,
            "activated_at": "2025-11-21 15:45:30",
            "expires_at": "2025-11-22 15:45:30",
            "activation_ip": "192.168.1.100"
        }
    },
    "metadata": {
        "last_updated": "2025-11-21 15:45:30",
        "total_codes_generated": 10,
        "total_codes_activated": 3,
        "total_codes_expired": 1
    }
}
```

### Sicherheit

**Implementierte Maßnahmen:**

- ✅ **API-Key-Schutz:** In config.php, nicht im Frontend
- ✅ **Serverseitige Validierung:** Alle Checks im Backend
- ✅ **Session-basiert:** Nicht durch LocalStorage/Cookies umgehbar
- ✅ **CORS-Header:** Nur erlaubte Domains
- ✅ **Input-Validierung:** XSS-Schutz, max-length
- ✅ **Rate-Limiting:** IP-basiert (optional)
- ✅ **Logging:** Optional für Monitoring

**Wichtig:**
- ❌ Keine sensiblen Daten im Frontend
- ❌ Keine API-Keys in JavaScript
- ❌ Keine Code-Listen im Client-Code

---

## 💰 Monetarisierung

### Preismodell (Aktuell)

**24-Stunden-Code:**
- **Preis:** 5,00 EUR
- **Gültigkeitsdauer:** 24 Stunden ab Aktivierung
- **Features:** Alle Premium-Features
- **Geräte:** Beliebig viele (während der 24h)

**Alternative Preismodelle:**

| Laufzeit | Preis | Preis/Stunde |
|----------|-------|--------------|
| 12h      | 3,00€ | 0,25€        |
| 24h      | 5,00€ | 0,21€        |
| 48h      | 8,00€ | 0,17€        |
| 7 Tage   | 15,00€| 0,09€        |
| 30 Tage  | 29,00€| 0,04€        |
| Lifetime | 99,00€| -            |

### Verkaufsplattformen

#### Option 1: Manuell (Start)

**Prozess:**
1. Codes generieren: `php generate-disposable-codes.php 10`
2. Codes notieren
3. Zahlung empfangen (PayPal, Überweisung, etc.)
4. Code per Email versenden

**Vorteile:**
- ✅ Einfach zu starten
- ✅ Keine Gebühren
- ✅ Volle Kontrolle

**Nachteile:**
- ❌ Manueller Aufwand
- ❌ Nicht skalierbar

#### Option 2: Gumroad (Empfohlen)

**Einrichtung:**
1. Account auf Gumroad.com erstellen
2. Produkt erstellen:
   - Name: "Metal Lyrics Generator - 24h Premium"
   - Preis: 5,00 EUR
   - Delivery: Email mit Code
3. Im "Content" Feld:
   ```
   Dein Premium-Code: METAL-XXXXXXXXXXXX

   So aktivierst du ihn:
   1. Gehe zu https://deine-domain.de
   2. Scrolle zum Premium-Bereich
   3. Gib den Code ein
   4. Fertig! 🎉
   ```
4. Codes vorab generieren und händisch einfügen

**Vorteile:**
- ✅ Semi-automatisch
- ✅ Payment Processing integriert
- ✅ Kundenmanagement
- ✅ Nur 5% Gebühr

**Nachteile:**
- ❌ Codes müssen vorab generiert werden
- ❌ Manuelles Update bei jedem Verkauf

#### Option 3: PayPal (Mittel)

**Einrichtung:**
1. PayPal-Button erstellen
2. Nach Zahlung: IPN (Instant Payment Notification)
3. PHP-Webhook empfängt IPN
4. Automatisch Code generieren & per Email senden

**Vorteile:**
- ✅ Vollautomatisch
- ✅ Keine Drittanbieter-Gebühren (außer PayPal)
- ✅ Eigene Kontrolle

**Nachteile:**
- ❌ Technischer Aufwand
- ❌ Webhook-Setup erforderlich

#### Option 4: Stripe (Advanced)

**Einrichtung:**
1. Stripe-Account erstellen
2. Payment Links oder Checkout Sessions
3. Webhook für `checkout.session.completed`
4. Automatische Code-Generierung & Email

**Vorteile:**
- ✅ Vollautomatisch
- ✅ Professionell
- ✅ Subscription möglich
- ✅ Gute Dokumentation

**Nachteile:**
- ❌ Höherer Setup-Aufwand
- ❌ 1,5% + 0,25€ Gebühr pro Transaktion

### Beispiel-Rechnung

**Szenario: 100 verkaufte Codes/Monat**

| Position | Details | Einnahmen |
|----------|---------|-----------|
| Verkaufte Codes | 100 × 5,00€ | 500,00€ |
| Gumroad-Gebühr | 5% | -25,00€ |
| OpenAI API-Kosten | ~3.000 Generierungen × $0.01 | -30,00€ |
| **Netto-Gewinn** | | **445,00€** |

**ROI-Berechnung:**
- Initiale Kosten: ~0€ (nur Zeit)
- Monatliche Fixkosten: Hosting (~5€) + Domain (~1€)
- Break-Even: 2 verkaufte Codes/Monat
- Profit-Marge: ~89%

### Rabatt-Aktionen

**Ideen:**

| Aktion | Preis | Rabatt | Zeitraum |
|--------|-------|--------|----------|
| Black Friday | 3,00€ | 40% | 1 Tag |
| Wochenende-Special | 4,00€ | 20% | Fr-So |
| Bundle: 3 Codes | 12,00€ | 20% | Permanent |
| Erste 100 Kunden | 3,50€ | 30% | Launch |

---

## 🐛 Troubleshooting

### Problem: "API Key invalid"

**Ursachen:**
- API-Key falsch eingegeben
- Key deaktiviert auf OpenAI Platform
- Leerzeichen im Key

**Lösungen:**
1. `config.php` öffnen
2. Key prüfen (copy-paste erneut)
3. Auf OpenAI Platform prüfen ob Key aktiv
4. Neu hochladen

**Test:**
```bash
curl https://api.openai.com/v1/models \
  -H "Authorization: Bearer YOUR_API_KEY"
```

### Problem: "500 Internal Server Error"

**Ursachen:**
- PHP-Version < 8.0
- cURL nicht aktiviert
- Berechtigungsfehler

**Lösungen:**

**1. PHP-Version prüfen:**
```bash
php -v
```
Mindestens PHP 8.0 erforderlich!

**Bei All-Inkl:**
- KAS → Domain → Einstellungen → PHP-Einstellungen
- PHP 8.1 oder höher wählen

**2. cURL prüfen:**
```php
<?php phpinfo(); ?>
```
Suche nach "cURL" → sollte enabled sein

**3. Error-Log checken:**
- All-Inkl KAS → Tools → Logs → Error Log
- Zeigt genaue Fehlermeldung

**4. Berechtigungen:**
```bash
chmod 644 *.php
chmod 644 disposable_codes.json
chmod 755 .
```

### Problem: "Premium-Code funktioniert nicht"

**Checkliste:**

- [ ] Code richtig geschrieben? (Groß-/Kleinschreibung!)
- [ ] `ENABLE_DISPOSABLE_CODES = true` in config.php?
- [ ] `check-premium.php` hochgeladen?
- [ ] `disposable_codes.json` vorhanden?
- [ ] Browser-Cache geleert? (Ctrl+F5)
- [ ] Browser-Konsole checken (F12 → Console)

**Test:**
```bash
# Direkt testen:
curl https://deine-domain.de/check-premium.php
# Sollte JSON zurückgeben
```

### Problem: "Code has expired"

**Ursache:**
- 24 Stunden sind abgelaufen

**Lösung:**
- User muss neuen Code kaufen
- Oder: Gültigkeitsdauer erhöhen in `config.php`:
  ```php
  define('DISPOSABLE_CODE_DURATION_HOURS', 48); // Jetzt 48h
  ```

**Prüfen:**
```bash
php view-code-statistics.php --detailed --expired
# Zeigt alle abgelaufenen Codes
```

### Problem: "Limit wird nicht durchgesetzt"

**Ursachen:**
- Sessions funktionieren nicht
- Browser löscht Cookies
- Premium irrtümlich aktiviert

**Lösungen:**

**1. Sessions testen:**
```php
<?php
session_start();
var_dump($_SESSION);
?>
```
Speichern als `test-session.php` und aufrufen.

**2. Session-Pfad prüfen:**
```php
<?php
echo session_save_path();
?>
```
Sollte beschreibbar sein!

**3. Premium-Session löschen:**
```php
<?php
session_start();
unset($_SESSION['premium_active']);
session_destroy();
echo "Session gelöscht!";
?>
```

### Problem: "CSS/Design fehlt"

**Ursachen:**
- CSS-Datei nicht hochgeladen
- Falscher Pfad
- Browser-Cache

**Lösungen:**
1. Prüfe ob `style.css` und `style-extended.css` vorhanden
2. Öffne im Browser: `https://deine-domain.de/style.css`
   - Sollte CSS zeigen, nicht 404
3. Browser-Cache leeren:
   - Chrome/Firefox: Ctrl+Shift+R
   - Safari: Cmd+Shift+R
4. Prüfe in `index.html`:
   ```html
   <link rel="stylesheet" href="style.css">
   <link rel="stylesheet" href="style-extended.css">
   ```

### Problem: "Codes werden nicht gespeichert"

**Ursachen:**
- `disposable_codes.json` nicht beschreibbar
- Datei existiert nicht
- Falscher Pfad

**Lösungen:**

**1. Datei erstellen:**
```bash
touch disposable_codes.json
chmod 644 disposable_codes.json
```

**2. Berechtigungen prüfen:**
```bash
ls -la disposable_codes.json
# Sollte: -rw-r--r-- zeigen
```

**3. Schreibtest:**
```php
<?php
$file = 'disposable_codes.json';
$test = file_put_contents($file, '{"test": true}');
echo $test ? "Schreibbar!" : "FEHLER: Nicht schreibbar!";
?>
```

**4. Pfad prüfen:**
In `config.php`:
```php
define('DISPOSABLE_CODES_FILE', __DIR__ . '/disposable_codes.json');
echo DISPOSABLE_CODES_FILE; // Zeigt absoluten Pfad
```

### Problem: "Zu langsam / Timeout"

**Ursachen:**
- Standard PHP-Timeout zu kurz (30s)
- OpenAI API langsam
- Zu viele Tokens angefordert

**Lösungen:**

**1. Timeout erhöhen:**

Erstelle `.user.ini` im Root:
```ini
max_execution_time = 60
```

Oder in PHP direkt (in `generate-lyrics.php`):
```php
set_time_limit(60);
```

**2. Tokens reduzieren:**
In `generate-lyrics.php`:
```php
$tokenLimits = [
    'short' => 400,   // Statt 600
    'medium' => 600,  // Statt 800
    // ...
];
```

**3. Caching implementieren:**
```php
$cacheKey = md5($prompt);
if (isset($_SESSION['lyrics_cache'][$cacheKey])) {
    return $_SESSION['lyrics_cache'][$cacheKey];
}
```

---

## 🚀 Marketing & Launch

### Pre-Launch Checkliste

- [ ] API-Key konfiguriert & getestet
- [ ] DEMO_MODE auf `false`
- [ ] Premium-Codes generiert
- [ ] Alle Dateien hochgeladen
- [ ] SSL/HTTPS aktiviert (Let's Encrypt)
- [ ] Google Analytics eingebunden (optional)
- [ ] Impressum & Datenschutz erstellt (Pflicht in DE/EU!)
- [ ] Payment-System eingerichtet
- [ ] Test-Käufe durchgeführt
- [ ] Email-Template für Code-Versand

### Launch-Strategie

**Tag 1-7: Soft Launch**

1. **Reddit Posts:**
   - r/Metal (1,1M Members)
   - r/metalmusicians
   - r/WeAreTheMusicMakers

   **Beispiel-Post:**
   ```markdown
   [Tool] I built an AI-powered Metal Lyrics Generator 🤘

   Free tool for generating Metal lyrics using AI.
   Based on 12+ mythologies (Norse, Greek, Lovecraft, etc.)

   Try it: https://your-domain.com

   Free: 5 generations/day
   Premium: 24h unlimited access for 5€

   Feedback welcome!
   ```

2. **Social Media:**
   - Instagram: Poste generierte Lyrics als Grafiken
   - TikTok: "AI generiert Metal-Lyrics" Videos (15-30s)
   - Twitter/X: Thread mit Beispielen
   - Facebook: Metal-Gruppen

3. **Metal-Communities:**
   - metal-archives.com Forum
   - ultimate-guitar.com Forum
   - Lokale Metal-Communities

**Tag 8-30: Growth Phase**

1. **Influencer Outreach:**
   - Kontaktiere Metal-YouTuber
   - Biete kostenlose Premium-Codes
   - Bitte um Review/Mention

2. **Content Marketing:**
   - Blog-Posts: "How to write Metal lyrics"
   - YouTube Tutorials
   - Case Studies: "Band XY nutzt unseren Generator"

3. **Paid Ads (optional):**
   - Facebook Ads: Targeting Metal-Fans
   - Google Ads: Keywords wie "metal lyrics generator"
   - Reddit Ads: r/Metal targeting

4. **Partnerships:**
   - Metal-Bands kontaktieren
   - Recording Studios
   - Metal-Magazinen/-Blogs

### SEO-Optimierung

**Keywords:**
- metal lyrics generator
- ai metal lyrics
- death metal lyrics generator
- black metal text creator
- mythology metal lyrics

**Meta-Tags in `index.html`:**
```html
<title>Metal Lyrics Generator - AI-Powered from Mythology</title>
<meta name="description" content="Generate authentic Metal lyrics based on 12+ mythologies. Perfect for Thrash, Death, Black, Power Metal. Free & Premium.">
<meta name="keywords" content="metal lyrics, ai generator, mythology, thrash metal, death metal">
```

**Backlink-Strategie:**
- Guest Posts auf Metal-Blogs
- Tool-Verzeichnisse (Product Hunt, etc.)
- Metal-Wiki-Einträge

### Erste 100 Kunden gewinnen

**Strategie:**

1. **Launch-Rabatt:** Erste 100 Codes für 3,50€ (statt 5€)
2. **Referral-Programm:** "Empfiehl einen Freund → 1€ Rabatt"
3. **Contest:** "Beste Lyrics gewinnen Premium-Code"
4. **Email-Liste:** Newsletter mit exklusiven Tipps
5. **Free Samples:** Influencer bekommen kostenlose Codes

**Conversion-Optimierung:**

- A/B-Test: 5€ vs. 4,50€
- Urgency: "Nur noch 50 Codes zum Sonderpreis!"
- Social Proof: "Bereits 234 zufriedene Metal-Fans!"
- Money-Back-Guarantee: "Nicht zufrieden? Geld zurück!" (nur bei Lifetime)

### Retention & Upselling

**Nach 24h-Code Ablauf:**

1. **Email-Reminder (23h nach Aktivierung):**
   ```
   Dein Premium-Code läuft in 1 Stunde ab! 🕐

   Sichere dir jetzt einen neuen Code:
   → 7-Tage-Code für nur 12€ (statt 15€)
   → Lifetime-Access für 79€ (statt 99€)

   [Jetzt upgraden]
   ```

2. **Loyalty-Programm:**
   - 3. Kauf: 10% Rabatt
   - 5. Kauf: 20% Rabatt
   - 10. Kauf: 1 Code gratis

3. **Bundles:**
   - "Weekend Warriors": 3 × 24h für 12€
   - "Metal Month": 10 × 24h für 35€

---

## 📊 Erfolgsmessung

### KPIs (Key Performance Indicators)

**Metriken:**

1. **Traffic:**
   - Unique Visitors/Tag
   - Pageviews
   - Bounce Rate
   - Durchschnittliche Session-Dauer

2. **Conversions:**
   - Free → Premium Conversion Rate
   - Kosten pro Akquisition (CPA)
   - Customer Lifetime Value (CLV)

3. **Revenue:**
   - Täglicher/Monatlicher Umsatz
   - Durchschnittlicher Warenkorbwert
   - Refund-Rate

4. **Engagement:**
   - Generierungen pro User
   - Premium-Nutzungsdauer
   - Return-User Rate

### Analytics Setup

**Google Analytics 4:**

In `index.html` vor `</head>`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');

  // Custom Events
  function trackGeneration(mythology, genre) {
    gtag('event', 'generate_lyrics', {
      'mythology': mythology,
      'genre': genre,
      'premium': isPremiumUser ? 'yes' : 'no'
    });
  }

  function trackPremiumActivation(code) {
    gtag('event', 'premium_activation', {
      'code_type': 'disposable',
      'value': 5.00,
      'currency': 'EUR'
    });
  }
</script>
```

**Custom Logging (optional):**

In `config.php`:
```php
define('ENABLE_LOGGING', true);
```

Log-Format in `api/logs/generation.log`:
```
[2025-11-21 15:45:30] [info] Generation: Norse + Thrash + War | Premium: yes | Tokens: 850
[2025-11-21 15:46:12] [info] Premium activated: METAL-ABC9XYZ3PQR7 (expires: 2025-11-22 15:46:12)
```

**Analyse:**
```bash
# Beliebtest Mythology
grep "Generation:" generation.log | cut -d'+' -f1 | sort | uniq -c | sort -nr

# Premium vs. Free
grep "Premium: yes" generation.log | wc -l
grep "Premium: no" generation.log | wc -l

# Durchschnittliche Tokens
grep "Tokens:" generation.log | awk '{print $NF}' | awk '{s+=$1; n++} END {print s/n}'
```

---

## 📄 Anhang

### Nützliche Links

**OpenAI:**
- API-Keys: https://platform.openai.com/api-keys
- Dokumentation: https://platform.openai.com/docs
- Pricing: https://openai.com/pricing
- Community: https://community.openai.com/

**Payment-Provider:**
- Gumroad: https://gumroad.com
- PayPal: https://developer.paypal.com
- Stripe: https://stripe.com/docs

**Hosting (All-Inkl):**
- KAS: https://kas.all-inkl.com/
- Support: +49 (0)6207 9396-0
- Email: support@all-inkl.com

**Tools:**
- FileZilla (FTP): https://filezilla-project.org/
- Random Code Generator: https://randomkeygen.com/

### Frequently Asked Questions (FAQ)

**Q: Wie viel kostet die OpenAI API?**
A: Durchschnittlich ~$0.01 pro Generierung mit GPT-4o. Bei 100 Generierungen/Tag = ~$30/Monat.

**Q: Kann ich die App offline nutzen?**
A: Nein, die App benötigt eine Verbindung zur OpenAI API.

**Q: Sind die Lyrics urheberrechtlich geschützt?**
A: Laut OpenAI ToS gehören die generierten Texte dem User. Aber: Prüfe die aktuelle OpenAI Policy!

**Q: Kann ich das Projekt weiterverkaufen?**
A: Ja, unter MIT-Lizenz darfst du den Code verkaufen/anpassen. Aber: OpenAI API-ToS beachten!

**Q: Wie lange dauert eine Generierung?**
A: Durchschnittlich 3-8 Sekunden, abhängig von Struktur und API-Last.

**Q: Kann ich mehrere Domains mit einem API-Key betreiben?**
A: Ja, ein OpenAI API-Key funktioniert für beliebig viele Domains.

**Q: Was passiert wenn mein API-Key geklaut wird?**
A: OpenAI erkennt ungewöhnliche Nutzung. Setze Spending-Limits im Dashboard! Bei Verdacht: Key sofort deaktivieren.

**Q: Kann ich die Mythologien selbst erweitern?**
A: Ja! In `script.js` im `MYTHOLOGY_DATA` Objekt hinzufügen.

**Q: Muss ich Steuern auf die Einnahmen zahlen?**
A: Ja, in Deutschland/EU sind Einnahmen steuerpflichtig. Gewerbeanmeldung empfohlen ab regelmäßigen Einnahmen.

### Änderungshistorie

**Version 2.0 (2025-11-21):**
- ✅ 24-Stunden-Disposable-Codes implementiert
- ✅ Code-Generierungs-Script (`generate-disposable-codes.php`)
- ✅ Statistik-Tool (`view-code-statistics.php`)
- ✅ JSON-basierte Code-Datenbank
- ✅ Multi-Device-Support für Codes
- ✅ Automatisches Ablaufen nach 24h
- ✅ Session-basiertes Premium-Management
- ✅ Erweiterte Mythologien (12+)
- ✅ Neue Strukturen (Epic, Progressive, Concept)
- ✅ Neue Genres (Heavy, Metalcore, Gothic)

**Version 1.0 (2024-XX-XX):**
- Basis-Implementierung
- OpenAI GPT-4o Integration
- 4 Mythologien (Norse, Celtic, Greek, Slavic)
- 6 Genres (Thrash, Death, Black, Power, Doom, Folk)
- Permanente Premium-Codes
- Rate-Limiting (5/Tag)
- Demo-Modus

### Support & Kontakt

**Technischer Support:**
- Email: contact@metal-lyrics-ai.com
- GitHub Issues: [Repository-Link]

**All-Inkl Hosting-Support:**
- Telefon: +49 (0)6207 9396-0
- Email: support@all-inkl.com
- Live-Chat: Im KAS verfügbar

**OpenAI API-Support:**
- Help Center: https://help.openai.com/
- Community: https://community.openai.com/

---

## 🎸 Schlusswort

Du hast jetzt ein **vollständig funktionierendes, monetarisierbares Metal Lyrics Generator System**!

**Nächste Schritte:**

1. ✅ API-Key einrichten
2. ✅ Premium-Codes generieren
3. ✅ Payment-System wählen
4. ✅ Testen, testen, testen!
5. ✅ Launchen & Marketing starten
6. 💰 **Profit!**

**Viel Erfolg mit deinem Projekt! 🤘🔥**

---

**Made with 🎸 for Metal fans worldwide!**

*Last updated: 2025-11-21*
