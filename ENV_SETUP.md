# 🔐 Environment Setup - Schritt-für-Schritt Anleitung

## Warum diese Änderung?

**Sicherheitsproblem behoben:** Das Admin-Passwort ist jetzt **nicht mehr im Code hardcodiert**, sondern wird sicher aus einer `.env` Datei geladen und als **Hash** gespeichert.

---

## ⚡ Schnellstart (5 Minuten)

### Schritt 1: .env Datei erstellen

```bash
# Kopiere das Template
cp .env.example .env
```

### Schritt 2: Passwort-Hash generieren

**Option A: Mit unserem Helper-Skript (empfohlen)**
```bash
php generate-password-hash.php
# Folge den Anweisungen
# Kopiere den generierten Hash
```

**Option B: Manuell**
```bash
php -r "echo password_hash('DeinSicheresPasswort123!', PASSWORD_BCRYPT);"
# Kopiere den Output
```

### Schritt 3: .env Datei ausfüllen

Öffne die `.env` Datei und füge deine Werte ein:

```env
# OpenAI API-Key (von https://platform.openai.com/api-keys)
OPENAI_API_KEY=sk-proj-DEIN-ECHTER-KEY

# Admin-Passwort-Hash (generiert mit Schritt 2)
ADMIN_PASSWORD_HASH=$2y$10$abcdefghijklmnopqrstuvwxyz...

# Deine Domain (ohne http://)
ALLOWED_DOMAIN=deine-domain.de
```

### Schritt 4: Testen

1. Öffne `admin-generate-codes.php` im Browser
2. Gib dein Passwort ein (das du in Schritt 2 verwendet hast)
3. ✅ Erfolgreich eingeloggt!

---

## 🔒 Sicherheits-Features

### Was wurde verbessert?

| Vorher (UNSICHER) | Nachher (SICHER) |
|-------------------|------------------|
| ❌ Passwort im Code hardcodiert | ✅ Passwort in .env (nicht in Git) |
| ❌ Klartext-Passwort | ✅ Bcrypt-Hash (nicht umkehrbar) |
| ❌ Jeder mit Code-Zugriff kennt Passwort | ✅ Hash ist nutzlos ohne Klartext |
| ❌ Keine Session-Regeneration | ✅ Session-ID nach Login regeneriert |
| ❌ Keine Login-Logs | ✅ Failed attempts werden geloggt |

### Wie sicher ist das?

- ✅ **Bcrypt-Hash:** Selbst bei Datenbank-Leak nicht umkehrbar
- ✅ **.gitignore:** .env wird niemals in Git committet
- ✅ **.htaccess:** Direkter Zugriff auf .env blockiert
- ✅ **Session-Regeneration:** Schutz vor Session-Fixation
- ✅ **Login-Logging:** Monitoring von Angriffen möglich

---

## 📋 Alle Umgebungsvariablen

### Pflicht-Variablen

```env
# OpenAI API-Key (ERFORDERLICH)
OPENAI_API_KEY=sk-proj-...

# Admin-Passwort-Hash (ERFORDERLICH für Admin-Panel)
ADMIN_PASSWORD_HASH=$2y$10$...

# Domain für CORS-Schutz (ERFORDERLICH)
ALLOWED_DOMAIN=deine-domain.de
```

### Optional-Variablen

```env
# FALLBACK: Klartext-Passwort (NICHT EMPFOHLEN!)
# Nur für erste Tests - sollte durch Hash ersetzt werden
ADMIN_PASSWORD_PLAIN=TemporaryPassword123

# Weitere Einstellungen (können auch in config.php bleiben)
MAX_FREE_GENERATIONS=5
ENABLE_LOGGING=false
DEBUG_MODE=false
```

---

## 🛠️ Troubleshooting

### Problem: "SECURITY ERROR: Kein Admin-Passwort konfiguriert"

**Lösung:**
1. Prüfe ob `.env` Datei existiert
2. Prüfe ob `ADMIN_PASSWORD_HASH` oder `ADMIN_PASSWORD_PLAIN` gesetzt ist
3. Stelle sicher, dass `env-loader.php` die .env korrekt lädt

**Test:**
```php
<?php
require_once 'env-loader.php';
var_dump(getenv('ADMIN_PASSWORD_HASH'));
?>
```

### Problem: "Falsches Passwort" obwohl Passwort korrekt

**Ursachen:**
- Hash wurde nicht korrekt kopiert
- Leerzeichen im Hash oder Passwort
- Falsche PHP-Version (< 5.5)

**Lösung:**
1. Hash neu generieren: `php generate-password-hash.php`
2. Komplett kopieren (inkl. `$2y$10$`)
3. Keine Leerzeichen vor/nach Hash in .env

### Problem: "Sicherheitswarnung" im Admin-Panel

**Ursache:** Du verwendest `ADMIN_PASSWORD_PLAIN` statt `ADMIN_PASSWORD_HASH`

**Lösung:**
1. Hash generieren: `php generate-password-hash.php`
2. In .env: `ADMIN_PASSWORD_HASH=...` setzen
3. In .env: `ADMIN_PASSWORD_PLAIN=...` entfernen
4. Speichern & neu laden

### Problem: .env Datei wird nicht geladen

**Lösung:**
```bash
# Prüfe ob env-loader.php existiert
ls -la env-loader.php

# Prüfe Berechtigungen
chmod 644 .env

# Prüfe Pfad in config.php
grep "env-loader" config.php
```

---

## 🔐 Best Practices

### ✅ DO's

- ✅ Verwende starke Passwörter (12+ Zeichen)
- ✅ Passwort-Hash verwenden (nicht Klartext)
- ✅ .env Datei regelmäßig sichern
- ✅ Passwort alle 3-6 Monate ändern
- ✅ Verschiedene Passwörter für Dev/Production
- ✅ API-Keys regelmäßig rotieren

### ❌ DON'Ts

- ❌ NIEMALS .env in Git committen
- ❌ NIEMALS Passwort per Email/Chat teilen
- ❌ NIEMALS gleiche Passwörter für mehrere Services
- ❌ NIEMALS Klartext-Passwort in Produktion
- ❌ NIEMALS .env Datei öffentlich zugänglich machen

---

## 🚀 Deployment-Checkliste

Vor dem Live-Gang:

- [ ] `.env` Datei erstellt und konfiguriert
- [ ] `ADMIN_PASSWORD_HASH` gesetzt (kein Klartext!)
- [ ] `OPENAI_API_KEY` mit echtem Key gesetzt
- [ ] `ALLOWED_DOMAIN` auf Production-Domain gesetzt
- [ ] `.gitignore` enthält `.env`
- [ ] Keine sensiblen Daten in Git-Historie
- [ ] `.htaccess` schützt .env und config.php
- [ ] Berechtigungen gesetzt (`chmod 600 .env`)
- [ ] Backup der .env erstellt
- [ ] Test: Admin-Login funktioniert
- [ ] Test: API-Calls funktionieren

---

## 📚 Weitere Ressourcen

- **Passwort-Generator:** https://www.random.org/passwords/
- **Security-Guide:** [SECURITY.md](SECURITY.md)
- **Vollständige Doku:** [DOKUMENTATION.md](DOKUMENTATION.md)
- **PHP password_hash():** https://www.php.net/manual/de/function.password-hash.php

---

## 💡 Beispiel: Komplette .env

```env
# ========================================
# METAL LYRICS GENERATOR - PRODUCTION
# ========================================

# OpenAI API
OPENAI_API_KEY=sk-proj-abc123xyz789...

# Admin-Zugang
ADMIN_PASSWORD_HASH=$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

# Domain
ALLOWED_DOMAIN=mythtometal.com

# Optional
MAX_FREE_GENERATIONS=5
ENABLE_LOGGING=true
DEBUG_MODE=false
```

---

**🎸 Viel Erfolg mit deinem sicheren Setup!**

*Bei Fragen oder Problemen: Siehe DOKUMENTATION.md oder öffne ein GitHub Issue*
