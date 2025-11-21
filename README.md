# 🎸 Metal Lyrics Generator

Ein KI-gestützter Generator für authentische Metal-Lyrics basierend auf Mythologien aus aller Welt.

## 🚀 Quick Start (Lokal Testen)

### 1. Dateien öffnen
Öffne einfach `index.html` in deinem Browser - fertig! 

Das Projekt läuft im **Demo-Modus** und zeigt vorgefertigte Beispiel-Lyrics.

### 2. Ausprobieren
- Wähle eine Mythologie (z.B. Nordisch)
- Wähle ein Genre (z.B. Thrash Metal)
- Gib ein Thema ein (z.B. "Krieg")
- Klicke auf "LYRICS GENERIEREN"

→ Nach 2 Sekunden siehst du Demo-Lyrics!

---

## 📁 Projekt-Struktur

```
metal-lyrics-generator/
├── index.html          # Haupt-Website
├── style.css          # Metal-Design (Dark Theme)
├── script.js          # JavaScript-Logik
├── .htaccess          # Apache Config (für All-Inkl)
├── .gitignore         # Git Ignore (wichtig!)
├── api/               # PHP Backend
│   ├── generate-lyrics.php  # API-Integration
│   └── config.php           # API-Key (GEHEIM!)
├── README.md          # Diese Datei
└── QUICK-START.md     # Schnellstart-Anleitung
```

---

## 🔧 Von Demo zu Live: OpenAI API-Integration

### Schritt 1: OpenAI API-Key besorgen

1. Gehe zu: https://platform.openai.com/api-keys
2. Registriere dich (kostenlos)
3. "Create new secret key" klicken
4. Kopiere den Key (beginnt mit "sk-proj-...")

**Kosten:** ~$0.01-0.015 pro Generierung (sehr günstig!)

**Empfohlenes Model:** `gpt-4o` (beste Balance aus Qualität/Preis)

### Schritt 2: API-Key in Config eintragen

1. Öffne die Datei `api/config.php`
2. Trage deinen API-Key ein:
   ```php
   define('OPENAI_API_KEY', 'sk-proj-DEIN-KEY-HIER');
   ```
3. Speichern!

⚠️ **WICHTIG:** Committe `config.php` NIEMALS zu Git!

### Schritt 3: Demo-Modus deaktivieren

In `script.js` ändere:
```javascript
const CONFIG = {
    DEMO_MODE: false,  // Von true auf false!
    // ...
};
```

### Schritt 4: Auf All-Inkl hochladen

#### Via FTP (FileZilla):
1. Verbinde dich mit deinem All-Inkl FTP:
   - Host: `deine-domain.de`
   - User: dein FTP-User
   - Passwort: dein FTP-Passwort
   
2. Navigiere zu deinem Web-Verzeichnis (meist `/`)

3. Lade alle Dateien hoch:
   ```
   ✅ index.html
   ✅ style.css
   ✅ script.js
   ✅ .htaccess
   ✅ api/ (ganzer Ordner mit PHP-Dateien)
   ```

4. Setze Berechtigungen für `api/` Ordner:
   - Rechtsklick auf `api/` Ordner
   - Berechtigungen: 755

#### Via All-Inkl KAS (File-Manager):
1. Logge dich ins KAS ein (https://kas.all-inkl.com/)
2. Tools → File-Manager
3. Dateien hochladen
4. Fertig!

### Schritt 5: Testen

1. Öffne deine Domain im Browser: `https://deine-domain.de`
2. Wähle Mythologie, Genre, Thema
3. Klick "LYRICS GENERIEREN"
4. Nach ~5 Sekunden siehst du echte KI-Lyrics! 🎉

---

## 🔒 Sicherheit: API-Key schützen

### Option 1: config.php außerhalb des Web-Root (BESTE Lösung)

Viele All-Inkl Pakete haben folgende Struktur:
```
/www/           ← Öffentlich zugänglich
/private/       ← NICHT öffentlich
```

1. Verschiebe `config.php` nach `/private/config.php`
2. In `generate-lyrics.php` ändere:
   ```php
   require_once '../private/config.php';
   ```

### Option 2: .htaccess Schutz (BEREITS ENTHALTEN)

Die `.htaccess` blockiert direkten Zugriff auf `config.php`:
```apache
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

### Option 3: Environment Variables (falls unterstützt)

Manche All-Inkl Pakete unterstützen `php.ini` oder `.user.ini`:
```ini
; .user.ini
auto_prepend_file = "/pfad/zu/config.php"
```

---

## 💰 Monetarisierung: Premium-Features

### Option 1: Gumroad (EINFACHSTE Lösung)

1. Erstelle Produkt auf Gumroad.com:
   - Name: "Metal Lyrics Generator Premium"
   - Preis: 4,99€/Monat oder 49€/Jahr
   - Liefere: Zugangs-Code

2. Füge PHP-Session-Check hinzu:
   ```php
   // api/check-premium.php
   session_start();
   $isPremium = isset($_SESSION['premium_code']) && 
                verify_code($_SESSION['premium_code']);
   ```

3. In `script.js` erweitern:
   ```javascript
   async function checkPremiumStatus() {
       const response = await fetch('api/check-premium.php');
       const data = await response.json();
       return data.isPremium;
   }
   ```

### Option 2: PayPal Integration

All-Inkl unterstützt PHP, also kannst du PayPal direkt integrieren:
- PayPal SDK: https://developer.paypal.com/sdk/js/
- Subscription Buttons

### Option 3: Einfacher Lizenzschlüssel

Für den Start am einfachsten:
```php
// In config.php
$PREMIUM_CODES = [
    'METAL2024-ABC123',
    'METAL2024-XYZ789'
];

// User gibt Code ein → wird gespeichert → Features freigeschaltet
```

---

## 🎨 Customization: Anpassen

### Farben ändern (style.css)
```css
:root {
    --color-accent: #c41e3a;     /* Dein Rot */
    --color-accent-bright: #ff6b6b; /* Dein helles Rot */
}
```

### Logo ändern (index.html)
```html
<h1 class="logo">⚡ DEIN TITEL HIER</h1>
```

### Mehr Mythologien hinzufügen (script.js)
```javascript
const MYTHOLOGY_DATA = {
    // ... bestehende
    aztec: {
        name: "Aztekisch",
        keywords: ["Quetzalcoatl", "Tezcatlipoca", ...],
        tone: "brutal, ritualistisch"
    }
};
```

---

## 📊 Analytics: Nutzer-Tracking (Optional)

### Google Analytics einbinden

In `index.html` vor `</head>`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

---

## 🚀 Marketing: Erste User gewinnen

### Reddit Posts
- r/Metal
- r/metalmusicians
- r/WeAreTheMusicMakers

**Beispiel-Post:**
```
[Tool] I built an AI-powered Metal Lyrics Generator 🤘

Free tool for generating Metal lyrics using AI.
Based on Norse, Celtic, Greek, and Occult mythology.

Try it: [your-link]

Free: 5 generations/day
Premium: Unlimited

Feedback welcome!
```

### Social Media
- Instagram: Poste generierte Lyrics als Grafiken
- TikTok: "Making Of"-Videos
- YouTube: Tutorial "AI Metal Lyrics"

### SEO Keywords
- "metal lyrics generator"
- "ai metal lyrics"
- "death metal text generator"
- "black metal lyrics creator"

---

## 🐛 Troubleshooting

### Problem: "API Key nicht gefunden"
**Lösung:** Prüfe `api/config.php` - ist der Key korrekt eingetragen?

### Problem: "500 Internal Server Error"
**Lösung:** 
1. Prüfe PHP Error Log (im All-Inkl KAS unter "Logs")
2. Stelle sicher, dass cURL in PHP aktiviert ist
3. Teste: Erstelle `api/test.php` mit `<?php phpinfo(); ?>`

### Problem: "Lyrics werden nicht generiert"
**Lösung:**
1. Öffne Browser-Konsole (F12)
2. Schaue nach Fehlermeldungen
3. Prüfe: Ist `DEMO_MODE: false` in script.js?

### Problem: "CORS Error"
**Lösung:** In `generate-lyrics.php` ist bereits `Access-Control-Allow-Origin: *` gesetzt. Falls Problem bleibt, kontaktiere All-Inkl Support.

### Problem: "Zu langsam / Timeout"
**Lösung:** 
- Erhöhe PHP Timeout in `.user.ini`:
  ```ini
  max_execution_time = 60
  ```
- Oder nutze `set_time_limit(60);` in PHP

---

## 💡 All-Inkl Spezifische Tipps

### PHP Version wählen
1. Gehe zu All-Inkl KAS
2. Domain → Einstellungen → PHP-Einstellungen
3. Wähle PHP 8.1 oder höher (empfohlen)

### Cronjobs für Cleanup (optional)
Erstelle Cronjob für tägliches Löschen alter Logs:
```bash
# Täglich um 3 Uhr
0 3 * * * /usr/bin/php /pfad/zu/cleanup.php
```

### SSL/HTTPS aktivieren
1. All-Inkl KAS → SSL/TLS
2. "Let's Encrypt" aktivieren (kostenlos!)
3. Auto-Renewal aktivieren

### Backup
All-Inkl macht automatisch Backups, aber:
- Erstelle regelmäßig manuelle Backups via FTP
- Besonders wichtig: `api/config.php` separat sichern!

---

## 📈 Performance-Optimierung

### PHP OpCache aktivieren
In `.user.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
```

### Gzip Compression
In `.htaccess` (bereits enthalten):
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript
</IfModule>
```

### Caching für API-Calls
```php
// Speichere bereits generierte Lyrics in Session
session_start();
$cacheKey = md5($mythology . $genre . $theme);

if (isset($_SESSION['lyrics_cache'][$cacheKey])) {
    echo json_encode($_SESSION['lyrics_cache'][$cacheKey]);
    exit;
}
```

---

## 🆘 Support

**All-Inkl Support:**
- https://all-inkl.com/kontakt/
- Sehr guter deutscher Support!

**OpenAI Support:**
- https://help.openai.com/

**Projekt-Fragen:**
- GitHub Issues: [your-repo]
- Email: contact@metal-lyrics-ai.com

---

## 📜 Lizenz

MIT License - Du kannst den Code frei nutzen, anpassen, verkaufen.

**Wichtig:** OpenAI API hat eigene Terms of Service!

---

## 🎸 Credits

- **Hosting:** All-Inkl.com
- **AI:** OpenAI ChatGPT
- **Design:** Metal-Ästhetik
- **Fonts:** Google Fonts

---

**Built with 🤘 for All-Inkl users!**

*Rock on!*
