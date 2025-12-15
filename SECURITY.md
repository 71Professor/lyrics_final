# 🔐 Sicherheitsanleitung - OpenAI API-Schlüssel

Diese Anleitung zeigt dir, wie du deinen OpenAI API-Schlüssel **sicher** in diesem Projekt verwendest.

## ⚠️ Warum ist das wichtig?

Dein OpenAI API-Schlüssel ist wie ein Passwort zu deinem OpenAI-Konto. Wenn jemand anderes deinen Schlüssel bekommt, kann er:
- Auf deine Kosten API-Anfragen stellen
- Dein Guthaben aufbrauchen
- Zugang zu deinen Daten erhalten

**NIEMALS** den API-Schlüssel:
- ❌ In Git committen
- ❌ Öffentlich teilen
- ❌ Im Frontend-Code (JavaScript) speichern
- ❌ In Screenshots zeigen

## ✅ Empfohlene Methode: `.env` Datei

### Schritt 1: `.env` Datei erstellen

Kopiere die Template-Datei:

```bash
cp .env.example .env
```

Oder erstelle manuell eine neue Datei namens `.env` im Hauptverzeichnis.

### Schritt 2: API-Schlüssel eintragen

Öffne die `.env` Datei und trage deinen echten API-Schlüssel ein:

```env
# Ändere "YOUR-API-KEY-HERE" zu deinem echten Schlüssel
OPENAI_API_KEY=sk-proj-abc123xyz...
```

### Schritt 3: Fertig!

Die `.env` Datei wird **automatisch** von `.gitignore` ausgeschlossen und niemals in Git committet.

Die `config.php` liest den Schlüssel automatisch aus der `.env` Datei.

## 🔄 Alternative Methode: Direkt in `config.php`

Falls du keine `.env` Datei verwenden möchtest:

### Schritt 1: `config.php` erstellen

```bash
cp config.example.php config.php
```

### Schritt 2: API-Schlüssel eintragen

Öffne `config.php` und suche nach dieser Zeile (ca. Zeile 73):

```php
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: 'sk-proj-YOUR-API-KEY-HERE');
```

Ersetze `'sk-proj-YOUR-API-KEY-HERE'` mit deinem echten Schlüssel:

```php
define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: 'sk-proj-abc123xyz...');
```

### Schritt 3: Fertig!

Die `config.php` ist bereits in `.gitignore` und wird nicht committet.

## 🆚 Vergleich der Methoden

| Methode | Vorteile | Nachteile |
|---------|----------|-----------|
| **`.env` Datei** ✅ | ✅ Beste Sicherheit<br>✅ Einfach zu verwalten<br>✅ Standard in modernen Projekten<br>✅ Mehrere Umgebungen möglich | ⚠️ Benötigt 2 Dateien |
| **`config.php`** | ✅ Einfach<br>✅ Nur 1 Datei | ⚠️ Weniger flexibel<br>⚠️ Schwieriger für mehrere Umgebungen |

## 📝 Wo bekomme ich einen API-Schlüssel?

1. Gehe zu [OpenAI Platform](https://platform.openai.com/api-keys)
2. Melde dich an oder erstelle einen Account
3. Klicke auf **"Create new secret key"**
4. Kopiere den Schlüssel (beginnt mit `sk-proj-...`)
5. Speichere ihn sicher (du kannst ihn nur einmal sehen!)

## 🔍 Überprüfung

### Ist mein API-Schlüssel sicher?

Stelle sicher, dass diese Dateien in `.gitignore` stehen:

```bash
cat .gitignore
```

Du solltest sehen:
```
config.php
.env
```

### Ist mein Schlüssel aktiv?

Prüfe mit diesem Befehl:

```bash
git ls-files | grep -E "(config.php|\.env)$"
```

**Ergebnis sollte LEER sein!** Falls Dateien angezeigt werden:

```bash
# Entferne sie aus Git (falls sie dort sind)
git rm --cached config.php .env
git commit -m "Remove sensitive config files"
```

## 🚨 Was tun bei versehentlichem Commit?

Falls du deinen API-Schlüssel versehentlich committet hast:

### 1. Schlüssel SOFORT ungültig machen

Gehe zu [OpenAI API Keys](https://platform.openai.com/api-keys) und lösche den Schlüssel.

### 2. Neuen Schlüssel erstellen

Erstelle einen neuen API-Schlüssel und trage ihn in `.env` oder `config.php` ein.

### 3. Git-Historie bereinigen (optional)

```bash
# Datei aus Git-Historie entfernen
git filter-branch --force --index-filter \
  "git rm --cached --ignore-unmatch config.php .env" \
  --prune-empty --tag-name-filter cat -- --all

# Force-Push (VORSICHT!)
git push origin --force --all
```

**WICHTIG:** Force-Push kann Probleme verursachen, falls andere mit dem Repository arbeiten!

## 🛡️ Zusätzliche Sicherheitsmaßnahmen

### 1. `.htaccess` Schutz (Apache)

Die Datei `.htaccess` im Projekt blockiert bereits den direkten Zugriff auf `config.php`:

```apache
<Files "config.php">
    Require all denied
</Files>
```

### 2. Nginx Konfiguration

Falls du Nginx verwendest, füge zur Server-Konfiguration hinzu:

```nginx
location ~* (config\.php|\.env) {
    deny all;
    return 404;
}
```

### 3. Dateiberechtigungen setzen

```bash
# Nur Besitzer kann lesen/schreiben
chmod 600 .env
chmod 600 config.php

# Nur Besitzer kann lesen
chmod 400 .env
chmod 400 config.php
```

### 4. Außerhalb des Web-Root (Fortgeschritten)

Verschiebe sensible Dateien außerhalb des öffentlichen Webverzeichnisses:

```
/home/user/
├── public_html/              ← Web-Root
│   ├── index.html
│   ├── script.js
│   └── generate-lyrics.php
└── config/                   ← Außerhalb!
    ├── config.php
    └── .env
```

Dann in PHP:

```php
require_once __DIR__ . '/../config/config.php';
```

## 📚 Best Practices

1. ✅ **Verwende `.env` Dateien** für Entwicklung und Produktion
2. ✅ **Nutze Umgebungsvariablen** auf Produktionsservern
3. ✅ **Rotiere Schlüssel regelmäßig** (alle 3-6 Monate)
4. ✅ **Überwache API-Nutzung** im OpenAI Dashboard
5. ✅ **Setze API-Limits** um Missbrauch zu vermeiden
6. ✅ **Verwende verschiedene Schlüssel** für Entwicklung/Produktion
7. ✅ **Logge verdächtige Aktivitäten**

## 🆘 Support

Falls du Fragen hast oder Hilfe brauchst:

- 📖 Lies die [README.md](README.md)
- 📖 Lies die [DOKUMENTATION.md](DOKUMENTATION.md)
- 🐛 Erstelle ein Issue auf GitHub
- 💬 Kontaktiere den Support

## 📋 Checkliste

- [ ] `.env` Datei erstellt (oder `config.php`)
- [ ] OpenAI API-Schlüssel eingetragen
- [ ] Überprüft, dass `.env` und `config.php` in `.gitignore` sind
- [ ] Überprüft mit `git status` - keine sensiblen Dateien staged
- [ ] `.htaccess` oder Nginx-Konfiguration geprüft
- [ ] Dateiberechtigungen gesetzt (optional)
- [ ] API-Schlüssel im OpenAI Dashboard getestet

---

**🔒 Denke daran: Sicherheit ist kein Zustand, sondern ein Prozess!**
