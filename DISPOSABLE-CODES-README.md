# Einweg-Premium-Codes System

## Übersicht

Das Einweg-Code-System ermöglicht es, Premium-Zugang über eindeutige Codes zu verkaufen, die nur **einmal aktiviert** werden können. Jeder Code wird nach der ersten Nutzung als "verbraucht" markiert und kann nicht erneut verwendet werden.

## 📦 Paket-Information

- **Preis:** 5,00 EUR
- **Anzahl Codes:** 10 Codes pro Paket
- **Typ:** Einweg-Codes (einmalige Nutzung)
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

Generiere ein Paket mit 10 Codes:

```bash
php generate-disposable-codes.php 10 "Paket #1"
```

Generiere 5 Codes für Tests:

```bash
php generate-disposable-codes.php 5 "Test-Batch"
```

Ohne Parameter werden standardmäßig 10 Codes generiert:

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
   Codes: 10

🔑 GENERATED CODES:
    1. METAL-XYZ9ABC3DEF7
    2. METAL-GHJ8KLM2NQR6
    ...
```

### 2. Codes Verteilen

Die generierten Codes können auf verschiedene Weisen an Kunden verteilt werden:

#### Option A: Manuelle Verteilung
1. Codes generieren
2. Codes per E-Mail an Kunden senden nach Zahlungseingang
3. Code wird beim ersten Einlösen verbraucht

#### Option B: Automatisierte Verteilung (PayPal/Gumroad)
1. Codes im Voraus generieren
2. Integration mit Payment-Provider einrichten
3. Codes automatisch nach Zahlung versenden

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

Nur benutzte Codes anzeigen:

```bash
php view-code-statistics.php --detailed --used
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
   Used Codes:         3 (30.0%)
   Unused Codes:       7
   Package Price:      5.00 EUR
   Codes per Package:  10
   Estimated Revenue:  1.50 EUR
```

## 🔐 Sicherheit

### Code-Format
- **Präfix:** `METAL-`
- **Länge:** 12 Zeichen (nach Präfix)
- **Zeichen:** Großbuchstaben und Zahlen (ohne verwirrende Zeichen wie I, O, 0, 1)
- **Beispiel:** `METAL-ABC9XYZ3PQR7`

### Schutz vor Missbrauch
- ✅ Jeder Code kann nur **einmal** aktiviert werden
- ✅ Verwendete Codes werden mit Zeitstempel und IP gespeichert
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
            "used": true,
            "used_at": "2025-11-21 15:45:30",
            "used_ip": "192.168.1.100"
        }
    },
    "metadata": {
        "last_updated": "2025-11-21 15:45:30",
        "total_codes_generated": 10,
        "total_codes_used": 3
    }
}
```

## 🔄 Workflow

### Für Admins:

1. **Code-Paket generieren:**
   ```bash
   php generate-disposable-codes.php 10 "Paket #1"
   ```

2. **Codes notieren** und sicher speichern

3. **Codes verkaufen** (5 EUR für 10 Codes)

4. **Nach Zahlungseingang:** Code per E-Mail an Kunden senden

5. **Statistiken prüfen:**
   ```bash
   php view-code-statistics.php
   ```

### Für Kunden:

1. **Paket kaufen** (10 Codes für 5 EUR)

2. **Code erhalten** per E-Mail

3. **Code einlösen** auf der Website:
   - Premium-Bereich öffnen
   - Code eingeben (z.B. `METAL-ABC9XYZ3PQR7`)
   - "Activate Premium" klicken

4. **Premium-Zugang nutzen**:
   - Alle Mythologien verfügbar
   - Unbegrenzte Generierungen
   - Erweiterte Strukturen (Long, Epic, Progressive, Concept)

5. **Code kann nicht erneut verwendet werden** - Session bleibt aktiv bis Browser geschlossen wird

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
define('DISPOSABLE_CODE_PACKAGE_SIZE', 10);
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
   - Prüfen ob bereits verwendet
   - Falls verwendet: Fehler zurückgeben
   - Falls unbenutzt: Code als verwendet markieren

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

- **Paketgröße:** 10 Codes
- **Paketpreis:** 5,00 EUR
- **Preis pro Code:** 0,50 EUR

**Verkaufsszenarien:**

| Verkaufte Pakete | Einnahmen | Codes generiert | Codes verwendet |
|------------------|-----------|-----------------|-----------------|
| 10               | 50 EUR    | 100             | ~70-80          |
| 50               | 250 EUR   | 500             | ~350-400        |
| 100              | 500 EUR   | 1000            | ~700-800        |

### Alternative Preismodelle:

**Single Codes:**
- 1 Code für 1,00 EUR (höherer Einzelpreis)

**Bulk Pakete:**
- 50 Codes für 20,00 EUR (0,40 EUR/Code)
- 100 Codes für 35,00 EUR (0,35 EUR/Code)

## 🆘 Fehlerbehebung

### Problem: "Could not save codes to file"

**Lösung:** Datei-Berechtigungen prüfen
```bash
chmod 644 disposable_codes.json
chown www-data:www-data disposable_codes.json
```

### Problem: "Code has already been used"

**Ursache:** Code wurde bereits eingelöst

**Lösung:** Kunden einen neuen Code aus einem unbenutzten Paket geben

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

**Response (Erfolg):**
```json
{
    "success": true,
    "message": "✅ Premium successfully activated! This one-time code has been consumed.",
    "isPremium": true,
    "codeType": "disposable"
}
```

**Response (Code bereits verwendet):**
```json
{
    "success": false,
    "message": "⚠️ This code has already been used and cannot be activated again."
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
