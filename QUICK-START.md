# 🚀 QUICK START - In 5 Minuten loslegen!

## 🎯 Sofort loslegen (DEMO-Modus)

### Schritt 1: Dateien öffnen
1. Entpacke den `metal-lyrics-generator` Ordner
2. Öffne `index.html` in deinem Browser
   - Windows: Doppelklick auf die Datei
   - Mac: Rechtsklick → Öffnen mit → Browser
   - Linux: `firefox index.html` oder `chromium index.html`

### Schritt 2: Ausprobieren! 🤘
Der Generator läuft sofort im **DEMO-MODUS**:
- Wähle Mythologie (z.B. "Nordisch")
- Wähle Genre (z.B. "Thrash Metal")
- Gib Thema ein (z.B. "Krieg")
- Klick "LYRICS GENERIEREN"

→ Du siehst sofort Beispiel-Lyrics!

**Hinweis:** Im Demo-Modus werden vorgefertigte Beispiel-Lyrics gezeigt, keine echten KI-Generierungen.

---

## ⚡ Von Demo zu Live (All-Inkl Hosting)

### 📋 Was du brauchst:
- ✅ All-Inkl Webspace (Privat Plus oder höher empfohlen)
- ✅ FTP-Zugang (bekommst du von All-Inkl)
- ✅ OpenAI API Key (siehe unten)

---

## 🔑 Schritt 1: OpenAI API-Key besorgen (5 Min)

1. Gehe zu: **https://platform.openai.com/api-keys**
2. Registriere dich / Logge dich ein
3. Klicke auf **"Create new secret key"**
4. Gib einen Namen ein (z.B. "Metal Lyrics Generator")
5. Kopiere den Key (beginnt mit `sk-proj-...`)

**💰 Kosten:**
- ~$0.01 pro Generierung
- Bei 100 Generierungen = ~1€
- **Sehr günstig!**

**💡 Tipp:** OpenAI gibt neuen Accounts oft $5 Startguthaben!

---

## 🔧 Schritt 2: API-Key eintragen (2 Min)

1. Öffne die Datei **`api/config.php`** mit einem Texteditor
2. Finde die Zeile:
   ```php
   define('OPENAI_API_KEY', 'sk-proj-DEIN-KEY-HIER');
   ```
3. Ersetze `'sk-proj-DEIN-KEY-HIER'` mit deinem echten Key:
   ```php
   define('OPENAI_API_KEY', 'sk-proj-abc123xyz...');
   ```
4. Speichern!

⚠️ **WICHTIG:** Diese Datei NIEMALS öffentlich teilen oder zu Git hochladen!

---

## 📤 Schritt 3: Auf All-Inkl hochladen (10 Min)

### Option A: Via FTP (FileZilla - EMPFOHLEN)

1. **FileZilla installieren:** https://filezilla-project.org/

2. **Mit All-Inkl verbinden:**
   - Host: `deine-domain.de` (oder FTP-Server aus KAS)
   - Benutzer: dein FTP-User (z.B. `w12345`)
   - Passwort: dein FTP-Passwort
   - Port: `21`
   - Klick "Verbinden"

3. **Dateien hochladen:**
   - Links: Dein lokaler Ordner (metal-lyrics-generator)
   - Rechts: Dein Webserver (meist `/` oder `/html`)
   
   Lade hoch:
   ```
   ✅ index.html
   ✅ style.css
   ✅ script.js
   ✅ .htaccess
   ✅ api/ (ganzer Ordner!)
   ```

4. **Berechtigungen setzen:**
   - Rechtsklick auf `api/` Ordner
   - Dateiattribute/Berechtigungen
   - Setze auf: `755` (rwxr-xr-x)

### Option B: Via All-Inkl KAS (Web-Interface)

1. Gehe zu: **https://kas.all-inkl.com/**
2. Einloggen mit deinen All-Inkl Zugangsdaten
3. Klicke auf **"Tools"** → **"File-Manager"**
4. Navigiere zu deinem Web-Verzeichnis (meist `/`)
5. Klicke **"Hochladen"** und wähle alle Dateien aus
6. Fertig!

---

## ⚙️ Schritt 4: Demo-Modus deaktivieren (1 Min)

1. Öffne **`script.js`** mit einem Texteditor
2. Finde ganz oben (Zeile 7):
   ```javascript
   const CONFIG = {
       DEMO_MODE: true,  // ← Hier!
   ```
3. Ändere zu:
   ```javascript
   const CONFIG = {
       DEMO_MODE: false,  // ← Jetzt false!
   ```
4. Speichern und neu hochladen (nur `script.js`)

---

## 🎉 Schritt 5: Testen!

1. Öffne deinen Browser
2. Gehe zu: **`https://deine-domain.de`**
3. Wähle Mythologie, Genre, Thema
4. Klick **"LYRICS GENERIEREN"**
5. Warte ~5 Sekunden
6. **BOOM! Echte KI-Lyrics!** 🔥

---

## 📂 Datei-Struktur Übersicht

```
deine-domain.de/
│
├── index.html              ← Hauptseite
├── style.css              ← Design
├── script.js              ← Logik (DEMO_MODE hier!)
├── .htaccess              ← Apache Config
│
└── api/                   ← Backend (PHP)
    ├── generate-lyrics.php  ← API-Call
    └── config.php           ← API-Key (GEHEIM!)
```

---

## 🔒 SICHERHEIT - WICHTIG!

### ❌ Diese Dateien NIEMALS teilen:
- `api/config.php` (enthält API-Key!)
- Backup-Ordner mit sensiblen Daten

### ✅ Geschützt durch .htaccess:
Die `.htaccess` blockiert direkten Zugriff auf `config.php`.

**Test:** Versuche `https://deine-domain.de/api/config.php` aufzurufen
→ Sollte **403 Forbidden** zeigen! ✅

### 💡 Zusätzlicher Schutz (Optional):

**Verschiebe config.php nach außerhalb:**
```
/www/              ← Öffentlich
/private/          ← NICHT öffentlich
    └── config.php ← Hier sicherer!
```

Dann in `generate-lyrics.php` ändern:
```php
require_once '../private/config.php';
```

---

## 🐛 Häufige Probleme & Lösungen

### ❌ "500 Internal Server Error"

**Lösung 1:** PHP-Version prüfen
1. All-Inkl KAS → Domain → Einstellungen
2. PHP-Einstellungen → mindestens PHP 8.0
3. Speichern

**Lösung 2:** Berechtigungen prüfen
- `api/` Ordner: 755
- PHP-Dateien: 644

**Lösung 3:** Error Log checken
1. KAS → Tools → Logs
2. Schaue nach PHP-Fehlern

### ❌ "Lyrics werden nicht generiert"

**Checkliste:**
- [ ] `DEMO_MODE: false` in script.js?
- [ ] API-Key in config.php eingetragen?
- [ ] Browser-Konsole (F12) checken - Fehlermeldung?
- [ ] `api/generate-lyrics.php` existiert?

**Schnell-Test:**
Öffne: `https://deine-domain.de/api/generate-lyrics.php`
→ Sollte NICHT 404 zeigen!

### ❌ "API Key invalid"

**Lösung:**
1. Prüfe ob Key wirklich kopiert (kein Leerzeichen!)
2. Gehe zu OpenAI Platform → API Keys
3. Prüfe ob Key aktiv ist
4. Eventuell neuen Key erstellen

### ❌ "Timeout" / "Zu langsam"

**Lösung:** Timeout erhöhen

Erstelle `.user.ini` im Root:
```ini
max_execution_time = 60
```

Oder kontaktiere All-Inkl Support für höheren Timeout.

---

## 💰 Premium-Features aktivieren (Optional)

### Einfachste Methode: Lizenzschlüssel

1. **Erstelle Codes** in `api/config.php`:
   ```php
   $PREMIUM_CODES = [
       'METAL2024-ABC123',
       'METAL2024-XYZ789'
   ];
   ```

2. **Verkaufe Codes** über:
   - Gumroad.com (5% Gebühr)
   - PayPal.me Links
   - Stripe Payment Links

3. **User gibt Code ein** → Features freigeschaltet!

**Preisvorschlag:** 4,99€/Monat oder 49€/Jahr

---

## 🎨 Design anpassen

### Farben ändern

Öffne **`style.css`**, finde (Zeile 16-20):
```css
:root {
    --color-accent: #c41e3a;     /* ← Deine Hauptfarbe */
    --color-accent-bright: #ff6b6b; /* ← Helle Version */
}
```

### Logo/Titel ändern

Öffne **`index.html`**, finde (Zeile 21):
```html
<h1 class="logo">⚡ METAL LYRICS GENERATOR</h1>
```
→ Ändere Text!

### Mehr Mythologien hinzufügen

Öffne **`script.js`**, finde `MYTHOLOGY_DATA` und füge hinzu:
```javascript
aztec: {
    name: "Aztekisch",
    keywords: ["Quetzalcoatl", "Tezcatlipoca", ...],
    tone: "brutal, ritualistisch"
}
```

---

## 📊 Erfolg messen (Optional)

### Google Analytics einbinden

1. Erstelle GA4 Property: https://analytics.google.com/
2. Füge in `index.html` vor `</head>` ein:
   ```html
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
   <script>
     window.dataLayer = window.dataLayer || [];
     function gtag(){dataLayer.push(arguments);}
     gtag('js', new Date());
     gtag('config', 'G-XXXXXXXXXX');
   </script>
   ```
3. Neu hochladen

**Tracken:** Generierungen, beliebte Mythologien, etc.

---

## 🚀 Marketing - Erste User gewinnen

### 1. Reddit Posts (KOSTENLOS!)
- r/Metal (1.1M Members)
- r/metalmusicians
- r/WeAreTheMusicMakers

**Posting-Regeln beachten!**

### 2. Social Media
- **Instagram:** Lyrics als Grafik posten
- **TikTok:** "AI generiert Metal-Lyrics" Videos
- **YouTube Shorts:** Schnelle Demos

### 3. Metal-Foren
- metal-archives.com Forum
- ultimate-guitar.com Forum
- Lokale Metal-Communities

### 4. Direkt an Bands
- Schreibe lokale Metal-Bands an
- Biete kostenlose Premium-Accounts
- Bitte um Erwähnung in Social Media

---

## ✅ Checkliste: Bereit für Launch?

- [ ] OpenAI API-Key eingetragen
- [ ] DEMO_MODE auf false
- [ ] Alle Dateien auf All-Inkl hochgeladen
- [ ] Generator getestet (echte Lyrics generiert)
- [ ] SSL/HTTPS aktiviert (empfohlen)
- [ ] Google Analytics eingebunden (optional)
- [ ] Impressum/Datenschutz erstellt (Pflicht in DE!)
- [ ] Premium-System geplant (optional)
- [ ] Marketing-Plan erstellt

---

## 📞 Hilfe benötigt?

### All-Inkl Support (SEHR GUT!)
- **Telefon:** +49 (0)6207 9396-0
- **Email:** support@all-inkl.com
- **Live-Chat:** Im KAS verfügbar
- **Deutsch & kompetent!** ✅

### OpenAI Support
- https://help.openai.com/
- Community Forum: https://community.openai.com/

### Projekt-Fragen
- Email: contact@metal-lyrics-ai.com
- GitHub: [your-repo]

---

## 💡 Pro-Tipps

### 1. Backup erstellen
Lade regelmäßig alles via FTP runter als Backup!

### 2. Traffic überwachen
All-Inkl KAS → Statistiken → Schaue Besucherzahlen

### 3. Kosten im Blick
OpenAI Dashboard → Usage → Setze Spending Limits!

### 4. Community aufbauen
- Discord Server erstellen
- Facebook-Gruppe
- Newsletter (Mailchimp Free)

### 5. SEO optimieren
- Füge Meta-Tags in index.html hinzu
- Erstelle Blog mit Metal-Themen
- Backlinks aufbauen

---

## 🎸 Du bist bereit!

**In 5 Schritten zum eigenen AI Metal Lyrics Generator:**

1. ✅ Demo lokal getestet
2. ✅ OpenAI API-Key geholt
3. ✅ Auf All-Inkl hochgeladen
4. ✅ Live getestet
5. ✅ Marketing gestartet

**Let's Rock! 🤘🔥**

---

## 📈 Nächste Schritte

### Woche 1-2: Launch
- [ ] Generator live schalten
- [ ] Reddit Posts machen
- [ ] 10 Personen zum Testen einladen

### Woche 3-4: Optimieren
- [ ] Feedback sammeln
- [ ] Bugs fixen
- [ ] Premium-System implementieren

### Monat 2-3: Wachstum
- [ ] Mehr Mythologien hinzufügen
- [ ] Export-Features (PDF, TXT)
- [ ] Kooperationen mit Bands

---

**Viel Erfolg! 🎸🔥**

*Made with 🤘 for All-Inkl users*
