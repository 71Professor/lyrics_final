# 🔓 PREMIUM-SYSTEM INSTALLATION

## ✅ Was wurde implementiert?

**Vollständiges Premium-System mit:**
- ✅ Code-Eingabe im Frontend
- ✅ Backend-Validierung (nicht umgehbar!)
- ✅ Session-basierte Speicherung
- ✅ Serverseitiges Rate-Limiting
- ✅ Premium-Status-Anzeige
- ✅ Automatische UI-Updates

---

## 📦 Neue Dateien

Du hast folgende neue Dateien im Outputs-Ordner:

1. **`check-premium.php`** - Premium-Validierung Backend
2. **`generate-lyrics-v2.php`** - Aktualisierte API mit Rate-Limiting
3. **`script-premium.js`** - Aktualisiertes JavaScript
4. **`premium-ui-snippet.html`** - HTML & CSS für UI
5. **`PREMIUM-INSTALL.md`** - Diese Anleitung

---

## 🚀 Installation (5 Schritte)

### Schritt 1: Neue PHP-Dateien hochladen

Via FTP auf deinen All-Inkl Webspace:

```
api/
├── config.php                 ← Schon vorhanden
├── generate-lyrics.php        ← ERSETZEN durch generate-lyrics-v2.php
└── check-premium.php          ← NEU hochladen
```

**Wichtig:**
- Benenne `generate-lyrics-v2.php` um in `generate-lyrics.php`
- Oder ersetze die alte `generate-lyrics.php` komplett

### Schritt 2: Premium-Codes in config.php eintragen

Öffne `api/config.php` und finde die Zeile (~Line 54):

```php
define('PREMIUM_CODES', [
    'METAL2024-DEMO'  => 'Demo Premium Code',
    'METAL2024-VIP'   => 'VIP Access',
    // Füge hier weitere Codes hinzu
]);
```

**Füge deine eigenen Codes hinzu:**

```php
define('PREMIUM_CODES', [
    'METAL2024-DEMO'   => 'Demo Code',
    'METAL2024-VIP'    => 'VIP Access',
    'PREMIUM-ABC123'   => 'Code für Max Mustermann',
    'GUMROAD-XYZ789'   => 'Gumroad Käufer #1',
    // ... mehr Codes
]);
```

**💡 Tipp:** Generiere sichere Codes mit:
```php
// Online Tool: https://randomkeygen.com/
// Oder in PHP:
echo 'METAL-' . bin2hex(random_bytes(6));
```

### Schritt 3: JavaScript aktualisieren

In deiner `index.html` ganz unten:

**ALT:**
```html
<script src="script.js"></script>
```

**NEU:**
```html
<script src="script-premium.js"></script>
```

**Oder:**
- Lösche die alte `script.js`
- Benenne `script-premium.js` um in `script.js`
- Dann bleibt der Link gleich

### Schritt 4: UI in index.html einfügen

Öffne `premium-ui-snippet.html` und:

1. **Kopiere den HTML-Teil** (Zeile 8-36)
2. **Öffne deine `index.html`**
3. **Suche nach** `</form>` im Generator-Bereich
4. **Füge den HTML-Code DANACH ein**

**Position:**
```html
            </form>  ← Hier endet das Formular
            
            <!-- HIER EINFÜGEN: Premium Code Eingabe -->
            
        </section>  ← Hier endet die Section
```

5. **Kopiere das CSS** (Zeile 42-150 aus `premium-ui-snippet.html`)
6. **Füge es ans Ende deiner `style.css`** ein

### Schritt 5: Testen!

1. Lade alle Dateien hoch
2. Öffne `https://deine-domain.de`
3. **Setze `DEMO_MODE: false`** in `script-premium.js` (Zeile 8)
4. Lade `script-premium.js` erneut hoch
5. Browser-Cache leeren (Strg+F5)
6. Teste:
   - Generiere 5x Lyrics → Sollte Limit zeigen
   - Gib Premium-Code ein (z.B. `METAL2024-DEMO`)
   - Sollte "Premium aktiv" zeigen
   - Generiere unbegrenzt! ✅

---

## 🧪 Test-Codes

Nutze diese Codes zum Testen:

```
METAL2024-DEMO
METAL2024-VIP
```

**⚠️ Wichtig:** Lösche/ändere diese Codes vor dem Launch!

---

## 🔐 Wie funktioniert es?

### Free User (ohne Code):
1. User öffnet die Seite
2. PHP prüft Session → Kein Premium
3. Rate-Limiting: Max 5 Generierungen/Tag
4. Nach 5x → Button disabled
5. Server verhindert weitere API-Calls (429 Error)

### Premium User (mit Code):
1. User gibt Code ein → JavaScript ruft `check-premium.php`
2. PHP validiert Code gegen `PREMIUM_CODES`
3. Wenn gültig → Session-Variable `premium_active = true`
4. Bei API-Calls: PHP prüft Session → Premium erkannt
5. Rate-Limiting wird übersprungen
6. Unbegrenzte Generierungen! ✅

### Warum nicht umgehbar?

- ❌ LocalStorage löschen → Hilft NICHT (Server entscheidet!)
- ❌ Browser-Cookies löschen → Session bleibt auf Server
- ❌ JavaScript manipulieren → Backend prüft trotzdem
- ✅ Nur gültiger Code im Backend öffnet Zugang

---

## 🎨 UI-Anpassungen

### Premium-Button-Farbe ändern

In `style.css`:
```css
.premium-activate-btn {
    background: linear-gradient(135deg, #your-color 0%, #your-dark-color 100%);
}
```

### Premium-Active-Farbe ändern

```css
.premium-active {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    /* Ändere zu deiner Farbe */
}
```

### Text anpassen

In `index.html`:
```html
<p>Hast du einen Premium-Code? Gib ihn hier ein:</p>
<!-- Ändere den Text nach deinen Wünschen -->
```

---

## 💰 Premium-Codes verkaufen

### Option 1: Manuell (EINFACH)

1. **Codes generieren:**
   ```php
   <?php
   for ($i = 0; $i < 10; $i++) {
       echo 'METAL-' . bin2hex(random_bytes(6)) . "\n";
   }
   ?>
   ```

2. **In `config.php` eintragen**

3. **Per Email verschicken:**
   ```
   Vielen Dank für deinen Kauf!
   
   Dein Premium-Code:
   METAL-abc123def456
   
   So aktivierst du ihn:
   1. Gehe zu https://deine-domain.de
   2. Scrolle zum Premium-Bereich
   3. Gib den Code ein
   4. Fertig! Unbegrenzte Lyrics 🎸
   ```

### Option 2: Gumroad (AUTOMATISCH)

1. **Produkt auf Gumroad erstellen**
   - Preis: 4,99€/Monat
   - Liefere: Premium-Code

2. **Webhook einrichten** (Advanced):
   - Gumroad sendet bei Kauf Webhook
   - PHP generiert automatisch Code
   - Email wird automatisch versendet

**Tutorial:** Siehe `GUMROAD-INTEGRATION.md` (erstelle ich auf Anfrage!)

### Option 3: PayPal (MITTEL)

1. PayPal-Button erstellen
2. Nach Zahlung: User bekommt Code per Email
3. Code manuell in `config.php` eintragen

---

## 📊 Statistiken & Logging

### Aktiviere Logging in config.php:

```php
define('ENABLE_LOGGING', true);
```

Dann wird geloggt:
- Jede Generierung
- Free vs. Premium
- Verwendete Mythologien/Genres
- Token-Nutzung

**Logfile:** `api/logs/generation.log`

**Analyse:**
```bash
# Wie viele Premium-User?
grep "Premium" api/logs/generation.log | wc -l

# Welche Mythologie am beliebtesten?
grep "Mythology:" api/logs/generation.log | sort | uniq -c | sort -nr
```

---

## 🐛 Troubleshooting

### "Premium-Code funktioniert nicht"

**Checkliste:**
- [ ] Code in `config.php` eingetragen?
- [ ] Genau geschrieben (case-sensitive)?
- [ ] `check-premium.php` hochgeladen?
- [ ] Browser-Konsole (F12) - Fehler?

**Test:**
```bash
# Test-Aufruf in Browser:
https://deine-domain.de/api/check-premium.php
# Sollte JSON zurückgeben
```

### "Limit wird nicht durchgesetzt"

**Lösung:**
1. Stelle sicher `generate-lyrics-v2.php` wird verwendet
2. Check ob Session funktioniert:
   ```php
   <?php
   session_start();
   var_dump($_SESSION);
   ?>
   ```
3. PHP-Sessions aktiviert? (meist Standard bei All-Inkl)

### "Premium aktiviert, aber Limit bleibt"

**Lösung:**
- Session löschen:
  ```php
  <?php
  session_start();
  session_destroy();
  ?>
  ```
- Browser-Cookies löschen
- Neu einloggen mit Code

### "UI zeigt nicht korrekt"

**Lösung:**
- Browser-Cache leeren (Strg+Shift+R)
- CSS in `style.css` eingefügt?
- HTML in `index.html` eingefügt?
- `script-premium.js` korrekt verlinkt?

---

## 🔄 Updates & Wartung

### Neue Codes hinzufügen

Einfach in `config.php` ergänzen:
```php
'NEUER-CODE-2024' => 'Beschreibung',
```

Keine Neuinstallation nötig!

### Codes widerrufen

Einfach aus `config.php` löschen:
```php
// 'ALTER-CODE' => 'Gesperrt',  ← Auskommentieren
```

User mit diesem Code verlieren sofort Zugang.

### Preis ändern

Nur in der UI (index.html):
```html
<h3>Nur 4,99€/Monat oder 49€/Jahr</h3>
<!-- Auf 9,99€ erhöhen etc. -->
```

---

## ✅ Checkliste: Installation komplett?

- [ ] `check-premium.php` hochgeladen (in `api/`)
- [ ] `generate-lyrics-v2.php` als `generate-lyrics.php` hochgeladen
- [ ] Premium-Codes in `config.php` eingetragen
- [ ] `script-premium.js` verlinkt in `index.html`
- [ ] Premium-UI HTML eingefügt in `index.html`
- [ ] Premium-UI CSS eingefügt in `style.css`
- [ ] `DEMO_MODE: false` gesetzt
- [ ] Getestet: 5x generiert → Limit?
- [ ] Getestet: Code eingegeben → Premium aktiv?
- [ ] Getestet: Unbegrenzt generieren möglich?

---

## 🎉 Fertig!

Du hast jetzt ein **vollständiges Premium-System**:

- ✅ Echtes serverseitiges Rate-Limiting
- ✅ Nicht umgehbar
- ✅ Code-basierte Freischaltung
- ✅ Session-Verwaltung
- ✅ Professionelle UI

**Nächste Schritte:**
1. Premium-Codes generieren
2. Verkaufsplattform wählen (Gumroad/PayPal)
3. Marketing starten
4. Profit! 💰

**Viel Erfolg! 🤘🔥**

---

## 📞 Support

**Fragen zum Premium-System?**
- Email: contact@metal-lyrics-ai.com
- Erstelle GitHub Issue
- Check README.md

**All-Inkl Support (PHP/Sessions):**
- 📞 +49 (0)6207 9396-0
- 📧 support@all-inkl.com
