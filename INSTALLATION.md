# 🎸 INSTALLATION - Metal Lyrics Generator mit Premium

## 📦 Was ist im FINAL-Ordner?

Alle Dateien sind **fertig und einsatzbereit** - keine Snippets mehr einfügen!

```
FINAL/
├── index.html              ✅ Premium-UI bereits eingefügt
├── style.css               ✅ Premium-CSS bereits eingefügt
├── script.js               ✅ Premium-Logik komplett
├── .htaccess               ✅ Sicherheit konfiguriert
├── .gitignore              ✅ Sensible Dateien geschützt
│
├── api/                    ✅ Backend komplett fertig
│   ├── generate-lyrics.php   ← OpenAI + Rate-Limiting
│   ├── check-premium.php     ← Code-Validierung
│   └── config.php            ← API-Key hier eintragen!
│
├── README.md               📚 Vollständige Dokumentation
├── QUICK-START.md          🚀 5-Minuten-Anleitung
├── PREMIUM-INSTALL.md      🔓 Premium-System Details
└── PREMIUM-OVERVIEW.md     📊 Übersicht

```

---

## ⚡ QUICK INSTALLATION (10 Minuten)

### 1️⃣ OpenAI API-Key holen (5 Min)

1. Gehe zu: https://platform.openai.com/api-keys
2. "Create new secret key"
3. Kopiere den Key (beginnt mit `sk-proj-...`)

**Kosten:** ~$0.01 pro Generierung (~10€/Monat bei normalem Traffic)

---

### 2️⃣ API-Key eintragen (2 Min)

**WICHTIG:** Öffne `api/config.php`

**Zeile 26 ändern:**
```php
define('OPENAI_API_KEY', 'sk-proj-DEIN-KEY-HIER');
```

**Ersetze** `'sk-proj-DEIN-KEY-HIER'` durch deinen echten Key!

**Speichern!**

---

### 3️⃣ Premium-Codes eintragen (1 Min)

**Optional:** Füge Test-Codes hinzu

**In `api/config.php` Zeile 54:**
```php
define('PREMIUM_CODES', [
    'METAL2024-DEMO'  => 'Test Code',
    'DEIN-CODE-123'   => 'Kunde 1',
]);
```

---

### 4️⃣ Dateien hochladen via FTP (5 Min)

**Mit FileZilla oder All-Inkl KAS:**

```
deine-domain.de/
├── index.html              ← Hochladen
├── style.css               ← Hochladen
├── script.js               ← Hochladen
├── .htaccess               ← Hochladen
└── api/                    ← Ganzen Ordner hochladen!
    ├── generate-lyrics.php
    ├── check-premium.php
    └── config.php
```

**Berechtigungen setzen:**
- `api/` Ordner: 755

---

### 5️⃣ Demo-Modus ausschalten (1 Min)

**Öffne `script.js` (auf deinem PC, BEVOR du hochlädst)**

**Zeile 8 ändern:**
```javascript
DEMO_MODE: false,  // Von true auf false!
```

**Neu hochladen!**

---

### 6️⃣ Testen! 🎉

1. Öffne `https://deine-domain.de`
2. Wähle Mythologie, Genre, Thema
3. Klick "LYRICS GENERIEREN"
4. Nach ~5 Sekunden → Echte KI-Lyrics! ✅

**Premium testen:**
1. Scrolle runter zur Premium-Eingabe
2. Gib Code ein: `METAL2024-DEMO`
3. Klick "Aktivieren"
4. Status: "✅ Premium Aktiv"
5. Generiere unbegrenzt! 🔥

---

## 📁 Datei-für-Datei Anleitung

### **index.html**
- **Was ist drin?** Premium-Code-Eingabe bereits eingefügt
- **Wo hochladen?** Root-Verzeichnis deines Webspace
- **Ändern?** Nein, fertig!

### **style.css**
- **Was ist drin?** Premium-Styling bereits eingefügt
- **Wo hochladen?** Root-Verzeichnis
- **Ändern?** Optional: Farben anpassen (Zeile 20-22)

### **script.js**
- **Was ist drin?** Premium-Logik, Code-Validierung, Rate-Limiting
- **Wo hochladen?** Root-Verzeichnis
- **Ändern?** Ja! Zeile 8: `DEMO_MODE: false`

### **api/generate-lyrics.php**
- **Was ist drin?** OpenAI API-Call + serverseitiges Rate-Limiting
- **Wo hochladen?** `api/` Ordner
- **Ändern?** Nein, fertig!

### **api/check-premium.php**
- **Was ist drin?** Premium-Code Validierung
- **Wo hochladen?** `api/` Ordner
- **Ändern?** Nein, fertig!

### **api/config.php** ⭐⭐⭐
- **Was ist drin?** API-Key, Premium-Codes, Einstellungen
- **Wo hochladen?** `api/` Ordner
- **Ändern?** JA! API-Key eintragen (Zeile 26)
- **⚠️ NIEMALS zu Git hochladen!**

### **.htaccess**
- **Was ist drin?** Sicherheit, Caching, config.php Schutz
- **Wo hochladen?** Root-Verzeichnis
- **Ändern?** Nein, fertig!

### **.gitignore**
- **Was ist drin?** Schützt sensible Dateien bei Git
- **Wo hochladen?** Root-Verzeichnis (nur wenn du Git nutzt)
- **Ändern?** Nein, fertig!

---

## ✅ Checkliste: Ist alles richtig?

**Vor dem Upload:**
- [ ] `api/config.php` → OpenAI API-Key eingetragen?
- [ ] `api/config.php` → Premium-Codes eingetragen?
- [ ] `script.js` → DEMO_MODE auf false?

**Nach dem Upload:**
- [ ] Alle Dateien hochgeladen?
- [ ] `api/` Ordner vorhanden?
- [ ] Berechtigungen: `api/` = 755?
- [ ] Browser-Cache geleert (Ctrl+F5)?

**Funktions-Test:**
- [ ] Generator lädt ohne Fehler?
- [ ] 5x Generieren → Limit-Meldung?
- [ ] Code `METAL2024-DEMO` eingeben → Premium aktiv?
- [ ] Unbegrenzt generieren möglich?

---

## 🐛 Häufige Fehler

### "Seite lädt nicht" / "500 Error"
**Lösung:**
1. All-Inkl KAS → Tools → Logs → Error Log
2. PHP-Version prüfen (min. PHP 8.0)
3. Berechtigungen: `api/` = 755

### "API Key invalid"
**Lösung:**
1. `config.php` öffnen
2. Key prüfen (kein Leerzeichen!)
3. Neu hochladen

### "Premium-Code funktioniert nicht"
**Lösung:**
1. Code genau so geschrieben? (Groß-/Kleinschreibung!)
2. `check-premium.php` hochgeladen?
3. Browser-Konsole (F12) → Fehler?

### "CSS/Design fehlt"
**Lösung:**
1. `style.css` hochgeladen?
2. Browser-Cache (Ctrl+Shift+R)
3. Richtige Datei hochgeladen?

### "Limit wird nicht durchgesetzt"
**Lösung:**
1. Richtige `generate-lyrics.php` hochgeladen?
2. `DEMO_MODE: false` gesetzt?
3. PHP-Sessions aktiviert? (Standard bei All-Inkl)

---

## 📊 Nach der Installation

### **Statistiken tracken:**
In `config.php` Zeile 74:
```php
define('ENABLE_LOGGING', true);
```
→ Logs in `api/logs/generation.log`

### **Premium-Codes generieren:**
```php
<?php
for ($i = 0; $i < 10; $i++) {
    echo 'METAL-' . bin2hex(random_bytes(6)) . "\n";
}
?>
```

### **Codes verkaufen:**
- **Gumroad:** Automatisch (empfohlen)
- **PayPal:** Halb-automatisch
- **Manuell:** Per Email

**Mehr Details:** Siehe `PREMIUM-INSTALL.md`

---

## 📚 Dokumentation

**Lies diese Dateien:**

1. **QUICK-START.md** → Schnelleinstieg
2. **README.md** → Vollständige Anleitung
3. **PREMIUM-INSTALL.md** → Premium-Details
4. **PREMIUM-OVERVIEW.md** → Übersicht

---

## 🎉 Fertig!

Alle Dateien sind **komplett fertig** und **einsatzbereit**!

**Nächste Schritte:**
1. ✅ API-Key eintragen
2. ✅ DEMO_MODE: false
3. ✅ Hochladen
4. ✅ Testen
5. ✅ Premium-Codes verkaufen
6. ✅ Marketing starten
7. 💰 **Profit!**

---

## 📞 Support

**Installation:**
→ PREMIUM-INSTALL.md

**All-Inkl:**
→ Tel: +49 (0)6207 9396-0

**OpenAI:**
→ https://help.openai.com/

**Projekt:**
→ Email: contact@metal-lyrics-ai.com

---

**Viel Erfolg! 🤘🔥**

*Built with 🎸 for All-Inkl users*
