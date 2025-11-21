# 24-Stunden-Premium-Codes System

## Übersicht

Das 24-Stunden-Code-System ermöglicht es, Premium-Zugang über zeitbasierte Codes zu verkaufen. Jeder Code ist **ab erster Aktivierung 24 Stunden lang gültig** und kann in diesem Zeitraum auf beliebig vielen Geräten und von beliebigen IPs verwendet werden. Nach Ablauf der 24 Stunden verfällt der Code automatisch.

## 📦 Paket-Information

- **Preis:** 5,00 EUR
- **Anzahl Codes:** 1 Code pro Paket
- **Gültigkeitsdauer:** 24 Stunden ab erster Aktivierung
- **Typ:** Zeitbasierte Codes (wiederverwendbar während Gültigkeitsdauer)
- **Format:** `METAL-XXXXXXXXXXXX`

## 🚀 Installation

Das System ist bereits konfiguriert und einsatzbereit. Die folgenden Dateien wurden hinzugefügt:

- `disposable_codes.json` - Speichert alle Codes und deren Status
- `generate-disposable-codes.php` - Generiert neue Codes
- `view-code-statistics.php` - Zeigt Code-Statistiken an
- `config.php` - Erweitert um Disposable-Code-Funktionen
- `check-premium.php` - Erweitert um Einweg-Code-Validierung

## 📝 Verwendung

### 1. Codes Generieren

Generiere einen einzelnen Code:

```bash
php generate-disposable-codes.php 1 "Paket #1"
```

Generiere 5 Codes für Tests:

```bash
php generate-disposable-codes.php 5 "Test-Batch"
```

Ohne Parameter wird standardmäßig 1 Code generiert:

```bash
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
    1. METAL-XYZ9ABC3DEF7
```

### 2. Codes Verteilen

Die generierten Codes können auf verschiedene Weisen an Kunden verteilt werden:

#### Option A: Manuelle Verteilung
1. Codes generieren
2. Codes per E-Mail an Kunden senden nach Zahlungseingang
3. Code wird beim ersten Einlösen aktiviert und ist 24 Stunden gültig

#### Option B: Automatisierte Verteilung (PayPal/Gumroad)
1. Codes im Voraus generieren
2. Integration mit Payment-Provider einrichten
3. Codes automatisch nach Zahlung versenden
4. Kunde kann Code sofort aktivieren und 24 Stunden lang nutzen

### 3. Code-Statistiken Anzeigen

Übersicht aller Codes:

```bash
php view-code-statistics.php
```

Detaillierte Ansicht mit allen Codes:

```bash
php view-code-statistics.php --detailed
```

Nur unbenutzte Codes anzeigen:

```bash
php view-code-statistics.php --detailed --unused
```

Nur aktive (noch nicht abgelaufene) Codes anzeigen:

```bash
php view-code-statistics.php --detailed --active
```

Nur abgelaufene Codes anzeigen:

```bash
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

## 🔐 Sicherheit

### Code-Format
- **Präfix:** `METAL-`
- **Länge:** 12 Zeichen (nach Präfix)
- **Zeichen:** Großbuchstaben und Zahlen (ohne verwirrende Zeichen wie I, O, 0, 1)
- **Beispiel:** `METAL-ABC9XYZ3PQR7`

### Schutz vor Missbrauch
- ✅ Jeder Code ist **24 Stunden ab Aktivierung** gültig
- ✅ Codes können während der Gültigkeitsdauer auf mehreren Geräten verwendet werden
- ✅ Nach Ablauf der 24 Stunden wird der Code automatisch ungültig
- ✅ Aktivierungszeitpunkt und IP werden mit Zeitstempel gespeichert
- ✅ Codes werden in JSON-Datei persistent gespeichert
- ✅ Serverseite Validierung (kein Client-Zugriff auf Code-Liste)

### Datenspeicherung
Die Code-Daten werden in `disposable_codes.json` gespeichert:

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

## 🔄 Workflow

### Für Admins:

1. **Code generieren:**
   ```bash
   php generate-disposable-codes.php 1 "Paket #1"
   ```

2. **Code notieren** und sicher speichern

3. **Code verkaufen** (5 EUR für 24-Stunden-Zugang)

4. **Nach Zahlungseingang:** Code per E-Mail an Kunden senden

5. **Statistiken prüfen:**
   ```bash
   php view-code-statistics.php
   ```

### Für Kunden:

1. **Code kaufen** (5 EUR für 24 Stunden Premium-Zugang)

2. **Code erhalten** per E-Mail

3. **Code einlösen** auf der Website:
   - Premium-Bereich öffnen
   - Code eingeben (z.B. `METAL-ABC9XYZ3PQR7`)
   - "Activate Premium" klicken
   - Code ist ab jetzt 24 Stunden gültig

4. **Premium-Zugang nutzen**:
   - Alle Mythologien verfügbar
   - Unbegrenzte Generierungen
   - Erweiterte Strukturen (Long, Epic, Progressive, Concept)
   - Auf allen Geräten nutzbar mit demselben Code

5. **Code bleibt 24 Stunden gültig**:
   - Kann auf mehreren Geräten gleichzeitig verwendet werden
   - Läuft nach 24 Stunden automatisch ab
   - Verbleibende Zeit wird angezeigt

## ⚙️ Konfiguration

Die Einstellungen befinden sich in `config.php`:

```php
// Einweg-Codes aktivieren/deaktivieren
define('ENABLE_DISPOSABLE_CODES', true);

// Pfad zur Code-Datenbank
define('DISPOSABLE_CODES_FILE', __DIR__ . '/disposable_codes.json');

// Paket-Preis in EUR
define('DISPOSABLE_CODE_PACKAGE_PRICE', 5.00);

// Anzahl Codes pro Paket
define('DISPOSABLE_CODE_PACKAGE_SIZE', 1);

// Gültigkeitsdauer in Stunden
define('DISPOSABLE_CODE_DURATION_HOURS', 24);
```

### Einweg-Codes Deaktivieren

Falls Sie zurück zum alten System wechseln möchten:

```php
define('ENABLE_DISPOSABLE_CODES', false);
```

Das System verwendet dann wieder die regulären Premium-Codes aus `PREMIUM_CODES`.

## 🔧 Technische Details

### Code-Validierung (check-premium.php)

Die Validierung erfolgt in folgender Reihenfolge:

1. **Disposable Code prüfen** (wenn aktiviert)
   - Code in JSON-Datenbank suchen
   - Prüfen ob bereits aktiviert
   - Falls aktiviert: Prüfen ob noch gültig (< 24h)
     - Falls abgelaufen: Fehler zurückgeben
     - Falls noch gültig: Premium aktivieren
   - Falls nicht aktiviert: Code aktivieren und Ablaufzeit setzen

2. **Reguläre Premium Codes prüfen** (Fallback)
   - Code in `PREMIUM_CODES` Array suchen
   - Bei Erfolg: Premium aktivieren (wiederverwendbar)

### Datei-Berechtigungen

Stellen Sie sicher, dass die JSON-Datei beschreibbar ist:

```bash
chmod 644 disposable_codes.json
```

Für die Skripte:

```bash
chmod +x generate-disposable-codes.php
chmod +x view-code-statistics.php
```

## 📊 Business-Modell

### Beispiel-Rechnung:

- **Preis pro Code:** 5,00 EUR
- **Gültigkeitsdauer:** 24 Stunden
- **Preis pro Stunde:** ~0,21 EUR

**Verkaufsszenarien:**

| Verkaufte Codes | Einnahmen | Aktive Nutzer (gleichzeitig) |
|-----------------|-----------|------------------------------|
| 10              | 50 EUR    | 3-5                          |
| 50              | 250 EUR   | 15-20                        |
| 100             | 500 EUR   | 30-40                        |

### Alternative Preismodelle:

**Verschiedene Laufzeiten:**
- 12 Stunden für 3,00 EUR
- 24 Stunden für 5,00 EUR (Standard)
- 48 Stunden für 8,00 EUR
- 7 Tage für 15,00 EUR

**Rabatt-Aktionen:**
- Wochenende-Special: 24h für 3,00 EUR
- Black Friday: 48h für 5,00 EUR

## 🆘 Fehlerbehebung

### Problem: "Could not save codes to file"

**Lösung:** Datei-Berechtigungen prüfen
```bash
chmod 644 disposable_codes.json
chown www-data:www-data disposable_codes.json
```

### Problem: "Code has expired"

**Ursache:** Die 24-Stunden-Frist ist abgelaufen

**Lösung:** Kunden muss einen neuen Code kaufen

### Problem: "Invalid code"

**Ursachen:**
- Tippfehler beim Eingeben
- Code existiert nicht in der Datenbank
- ENABLE_DISPOSABLE_CODES ist false

**Lösung:** Code-Format prüfen (METAL-XXXXXXXXXXXX)

### Problem: Codes werden nicht gespeichert

**Lösung 1:** JSON-Datei erstellen
```bash
touch disposable_codes.json
chmod 644 disposable_codes.json
```

**Lösung 2:** Schreibrechte prüfen
```bash
ls -la disposable_codes.json
```

## 📚 API-Referenz

### Code-Aktivierung

**Endpoint:** `POST check-premium.php`

**Request:**
```json
{
    "action": "activate",
    "code": "METAL-ABC9XYZ3PQR7"
}
```

**Response (Erfolg - Erstaktivierung):**
```json
{
    "success": true,
    "message": "✅ Premium successfully activated! Valid for 24 hours.",
    "isPremium": true,
    "codeType": "disposable",
    "expiresAt": "2025-11-22 15:45:30",
    "remainingHours": 24
}
```

**Response (Erfolg - Bereits aktiviert, noch gültig):**
```json
{
    "success": true,
    "message": "✅ Premium activated! Code is valid for 12.5 more hours.",
    "isPremium": true,
    "codeType": "disposable",
    "expiresAt": "2025-11-22 15:45:30",
    "remainingHours": 12.5
}
```

**Response (Code abgelaufen):**
```json
{
    "success": false,
    "message": "⚠️ This code has expired. Premium codes are valid for 24 hours after first activation."
}
```

**Response (Ungültiger Code):**
```json
{
    "success": false,
    "message": "Invalid code. Please check your entry."
}
```

## 📞 Support

Bei Fragen oder Problemen:

1. Statistiken prüfen: `php view-code-statistics.php --detailed`
2. Log-Dateien prüfen (falls ENABLE_LOGGING aktiviert)
3. Datei-Berechtigungen prüfen
4. GitHub Issues: https://github.com/yourusername/metal-lyrics-generator

## 📄 Lizenz

Dieses System ist Teil des Metal Lyrics Generator Projekts.

---

**Version:** 1.0
**Datum:** 2025-11-21
**Status:** ✅ Production Ready
