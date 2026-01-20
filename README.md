# 🎸 Metal Lyrics Generator

> **KI-gestützter Generator für authentische Metal-Lyrics basierend auf Mythologien aus aller Welt**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://www.php.net/)
[![OpenAI](https://img.shields.io/badge/OpenAI-GPT--4o-412991.svg)](https://openai.com/)

---

## 📖 Über das Projekt

Der **Metal Lyrics Generator** nutzt künstliche Intelligenz (OpenAI GPT-4o), um authentische Metal-Lyrics zu erstellen. Basierend auf 12+ Mythologien (Norse, Greek, Japanese, Lovecraft, etc.) und verschiedenen Metal-Genres (Thrash, Death, Black, Power Metal, etc.) generiert die App kreative und thematisch passende Songtexte.

### ✨ Features

#### Kostenlose Version
- ✅ **5 Generierungen pro Tag**
- ✅ **4 Mythologien:** Nordisch, Keltisch, Griechisch, Slawisch
- ✅ **6 Genres:** Thrash, Death, Black, Power, Doom, Folk Metal
- ✅ **Flexible Song-Strukturen:** Short & Medium
- ✅ **Export als TXT**

#### Premium Features
- 🔓 **Unbegrenzte Generierungen**
- 🌍 **12+ Mythologien:** Japanisch, Chinesisch, Hindu, Aztekisch, Maya, Afrikanisch, Ägyptisch, Mesopotamisch, Occult, Lovecraft, Gothic Horror
- 🎭 **Erweiterte Strukturen:** Epic, Progressive, Concept Songs
- 🎵 **Zusätzliche Genres:** Heavy Metal, Metalcore, Gothic Metal
- 📄 **Export:** TXT, PDF (geplant)

---

## 🚀 Quick Start

### Voraussetzungen

- **Webserver:** Apache/Nginx mit PHP 8.0+
- **PHP-Extensions:** cURL, JSON, Sessions
- **OpenAI API-Key:** Von [platform.openai.com](https://platform.openai.com/api-keys)

### Installation

1. **Repository klonen**
   ```bash
   git clone https://github.com/71Professor/lyrics_final.git
   cd lyrics_final
   ```

2. **Dateien auf Webserver hochladen**
   ```
   Via FTP oder direkt auf Webspace kopieren
   ```

3. **API-Key konfigurieren**

   Öffne `config.php` und füge deinen OpenAI API-Key ein:
   ```php
   define('OPENAI_API_KEY', 'sk-proj-DEIN-API-KEY-HIER');
   ```

4. **Demo-Modus deaktivieren**

   Öffne `script.js` (Zeile 9):
   ```javascript
   DEMO_MODE: false,  // Von true auf false ändern!
   ```

5. **Berechtigungen setzen**
   ```bash
   chmod 644 *.php
   chmod 644 disposable_codes.json
   chmod 755 .
   ```

6. **Testen**

   Öffne die Website in deinem Browser und generiere deine ersten Lyrics! 🤘

---

## 🛠️ Technologie-Stack

| Komponente | Technologie |
|------------|-------------|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Backend** | PHP 8.0+ |
| **AI-Engine** | OpenAI GPT-4o API |
| **Datenbank** | JSON (für Premium-Codes) |
| **Hosting** | Beliebiger PHP-Hoster (All-Inkl empfohlen) |

---

## 📂 Projektstruktur

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
├── DOKUMENTATION.md                # Vollständige Dokumentation
└── README.md                       # Diese Datei
```

---

## 🔓 Premium-System

Das Projekt verwendet **24-Stunden-Premium-Codes:**

- Gültig für 24 Stunden ab Aktivierung
- Unbegrenzte Generierungen während der 24h
- Verwaltung über JSON-Datenbank
- Preis: $6.00 USD pro Code

#### Codes generieren

```bash
# Einzelnen Code generieren
php generate-disposable-codes.php 1 "Beschreibung"

# 10 Codes generieren
php generate-disposable-codes.php 10 "Verkaufs-Batch"

# Statistiken anzeigen
php view-code-statistics.php
```

---

## ⚙️ Konfiguration

Alle wichtigen Einstellungen findest du in `config.php`:

```php
// OpenAI API
define('OPENAI_API_KEY', 'sk-proj-...');
define('OPENAI_MODEL', 'gpt-4o');

// Rate Limiting
define('MAX_FREE_GENERATIONS', 5);

// 24h Premium-Codes
define('ENABLE_DISPOSABLE_CODES', true);
define('DISPOSABLE_CODE_DURATION_HOURS', 24);
define('DISPOSABLE_CODE_PACKAGE_PRICE', 6.00);
```

---

## 💰 Monetarisierung

### Preismodell

- **24h Premium-Code:** $6.00 USD
- **Alle Premium-Features:** Unbegrenzte Generierungen, 12+ Mythologien, erweiterte Strukturen

### Verkaufsplattformen

- **Gumroad** (empfohlen) - 5% Gebühr
- **PayPal** - IPN-Webhook
- **Stripe** - Vollautomatisch
- **Manuell** - Per Email

### Beispiel-Rechnung

Bei 100 verkauften Codes/Monat:
- Einnahmen: $600.00 USD
- Gumroad-Gebühr: -$30.00 USD
- OpenAI API-Kosten: ~$30.00 USD
- **Netto-Gewinn: ~$540.00 USD** 💰

---

## 📊 Verwendungsbeispiele

### Nordische Mythologie × Thrash Metal
```
🪓 "Blades of Valhalla"

Through storms of steel and thunder's rage
The Allfather calls from beyond the grave
Valkyries scream, their wings of fire
We march to die in Odin's pyre
```

### Japanische Mythologie × Death Metal
```
⚔️ "Blades of the Ronin"

Seven swords in crimson rain
Bushido carved in endless pain
Honor bound to death's embrace
Samurai fall without disgrace
```

---

## 🐛 Troubleshooting

### "API Key invalid"
- Prüfe den API-Key in `config.php`
- Stelle sicher, dass keine Leerzeichen vorhanden sind
- Teste den Key auf [platform.openai.com](https://platform.openai.com)

### "500 Internal Server Error"
- Prüfe PHP-Version (min. 8.0)
- Stelle sicher, dass cURL aktiviert ist
- Prüfe Error-Logs des Servers

### Premium-Code funktioniert nicht
- Groß-/Kleinschreibung beachten
- `ENABLE_DISPOSABLE_CODES = true` in `config.php`?
- Browser-Cache leeren (Ctrl+F5)

**Mehr Infos:** Siehe [DOKUMENTATION.md](DOKUMENTATION.md) für ausführliche Troubleshooting-Guides

---

## 📄 Dokumentation

Für detaillierte Informationen zu Installation, Konfiguration, Premium-System, Marketing und mehr siehe:

👉 **[DOKUMENTATION.md](DOKUMENTATION.md)**

---

## 🤝 Mitwirken

Contributions sind willkommen! Bitte:

1. Fork das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/AmazingFeature`)
3. Commit deine Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Push zum Branch (`git push origin feature/AmazingFeature`)
5. Öffne einen Pull Request

---

## 📜 Lizenz

Dieses Projekt ist unter der MIT-Lizenz lizenziert - siehe [LICENSE](LICENSE) für Details.

---

## 📞 Kontakt & Support

- **GitHub Issues:** [Issues öffnen](https://github.com/71Professor/lyrics_final/issues)
- **Email:** contact@metal-lyrics-ai.com

---

## 🎸 Credits

- **KI-Engine:** [OpenAI GPT-4o](https://openai.com/)
- **Fonts:** [Google Fonts](https://fonts.google.com/) (Metal Mania, Roboto Condensed)
- **Inspiration:** Die weltweite Metal-Community 🤘

---

## 🔮 Roadmap

- [ ] PDF-Export für Lyrics
- [ ] Multi-Language Support (EN, DE, ES)
- [ ] Spotify-Integration (Lyrics zu Songs)
- [ ] Mobile App (iOS/Android)
- [ ] Community-Voting für beste Lyrics
- [ ] API für Entwickler

---

**Made with 🎸 and 🔥 for Metal fans worldwide!**

*Last updated: 2025-11-21*
