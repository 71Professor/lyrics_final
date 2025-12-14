<?php
/**
 * ADMIN: DISPOSABLE CODE GENERATOR (WEB VERSION)
 *
 * ⚠️ SECURITY: Set a secure password below!
 * This page generates premium codes - protect it!
 */

// ========================================
// SECURITY: SET YOUR PASSWORD HERE!
// ========================================
define('ADMIN_PASSWORD', 'IhrSicheresPasswort123!'); // ⚠️ ÄNDERN SIE DIES!

// ========================================
// AUTHENTICATION
// ========================================

session_start();

// Load config early for download handler
require_once __DIR__ . '/config.php';

// ========================================
// HANDLE DOWNLOAD
// ========================================

if (isset($_GET['download']) && isset($_SESSION['last_generated_codes'])) {
    $codes = $_SESSION['last_generated_codes'];
    $packageName = $_SESSION['last_package_name'] ?? 'Premium Codes';
    $packagePrice = $_SESSION['last_package_price'] ?? DISPOSABLE_CODE_PACKAGE_PRICE;
    $timestamp = date('Y-m-d_H-i-s');

    // Prepare download content
    $content = "========================================\n";
    $content .= "METAL LYRICS GENERATOR - PREMIUM CODES\n";
    $content .= "========================================\n\n";
    $content .= "Paket: " . $packageName . "\n";
    $content .= "Generiert am: " . date('d.m.Y H:i:s') . "\n";
    $content .= "Anzahl Codes: " . count($codes) . "\n";
    $content .= "Preis pro Code: " . number_format($packagePrice, 2) . " EUR\n";
    $content .= "Gültigkeit: 24 Stunden ab Aktivierung\n\n";
    $content .= "========================================\n";
    $content .= "CODES\n";
    $content .= "========================================\n\n";

    foreach ($codes as $i => $code) {
        $content .= str_pad($i + 1, 3, '0', STR_PAD_LEFT) . ". " . $code . "\n";
    }

    $content .= "\n========================================\n";
    $content .= "HINWEISE\n";
    $content .= "========================================\n\n";
    $content .= "- Jeder Code kann nur einmal aktiviert werden\n";
    $content .= "- Nach Aktivierung ist der Code 24 Stunden gültig\n";
    $content .= "- Geben Sie Codes nur nach erfolgter Zahlung heraus\n";
    $content .= "- Bewahren Sie diese Datei sicher auf\n\n";

    // Send download headers
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="premium-codes_' . $timestamp . '.txt"');
    header('Content-Length: ' . strlen($content));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $content;
    exit;
}

// Check if password submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['admin_authenticated'] = true;
    } else {
        $error = 'Falsches Passwort!';
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Check authentication
if (!isset($_SESSION['admin_authenticated'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Login</title>
        <style>
            body { font-family: Arial; background: #1a1a1a; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .login-box { background: #2a2a2a; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(255,0,0,0.3); }
            input { padding: 10px; margin: 10px 0; width: 250px; border: 1px solid #666; background: #333; color: #fff; border-radius: 5px; }
            button { padding: 10px 20px; background: #c00; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
            button:hover { background: #f00; }
            .error { color: #f00; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>🔐 Admin Login</h2>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <input type="password" name="password" placeholder="Admin Passwort" required>
                <br>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ========================================
// LOAD CONFIGURATION
// ========================================

// Config already loaded at top for download handler

// Check if disposable codes are enabled
if (!ENABLE_DISPOSABLE_CODES) {
    die('❌ ERROR: Disposable codes are disabled in config.php');
}

// ========================================
// HANDLE CODE GENERATION
// ========================================

$generated = false;
$newCodes = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $count = (int)($_POST['count'] ?? 10);
    $packageName = $_POST['package_name'] ?? 'Generated Package';

    // Validate
    if ($count < 1 || $count > 100) {
        $error = 'Anzahl muss zwischen 1 und 100 liegen';
    } else {
        // Load existing data
        $data = loadDisposableCodes();

        // Generate codes
        $newCodes = generateUniqueCodes($count, $data);

        if (!empty($newCodes)) {
            // Prepare batch info
            $batchId = date('Ymd-His');
            $timestamp = date('Y-m-d H:i:s');

            // Add codes to data structure
            foreach ($newCodes as $code) {
                $data['codes'][$code] = [
                    'created_at' => $timestamp,
                    'batch_id' => $batchId,
                    'package_name' => $packageName,
                    'package_price' => DISPOSABLE_CODE_PACKAGE_PRICE,
                    'activated_at' => null,
                    'expires_at' => null,
                    'activation_ip' => null
                ];
            }

            // Update metadata
            if (!isset($data['metadata'])) {
                $data['metadata'] = [];
            }

            $data['metadata']['last_updated'] = $timestamp;
            $data['metadata']['total_codes_generated'] = ($data['metadata']['total_codes_generated'] ?? 0) + count($newCodes);

            if (!isset($data['metadata']['total_codes_activated'])) {
                $data['metadata']['total_codes_activated'] = 0;
            }

            if (!isset($data['metadata']['total_codes_expired'])) {
                $data['metadata']['total_codes_expired'] = 0;
            }

            // Save to file
            $success = saveDisposableCodes($data);

            if ($success) {
                $generated = true;
                // Save codes to session for download
                $_SESSION['last_generated_codes'] = $newCodes;
                $_SESSION['last_package_name'] = $packageName;
                $_SESSION['last_package_price'] = DISPOSABLE_CODE_PACKAGE_PRICE;
            } else {
                $error = 'Fehler beim Speichern. Prüfen Sie die Dateiberechtigungen.';
            }
        } else {
            $error = 'Keine Codes generiert';
        }
    }
}

// Helper function: Generate secure code
function generateSecureCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = 'METAL-';
    for ($i = 0; $i < 12; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Helper function: Check if code exists
function codeExists($code, $data) {
    return isset($data['codes'][$code]);
}

// Helper function: Generate unique codes
function generateUniqueCodes($count, $existingData) {
    $codes = [];
    $attempts = 0;
    $maxAttempts = $count * 10;

    while (count($codes) < $count && $attempts < $maxAttempts) {
        $code = generateSecureCode();
        if (!in_array($code, $codes) && !codeExists($code, $existingData)) {
            $codes[] = $code;
        }
        $attempts++;
    }

    return $codes;
}

// Load statistics
$data = loadDisposableCodes();
$totalCodes = count($data['codes']);
$totalActivated = $data['metadata']['total_codes_activated'] ?? 0;
$totalExpired = $data['metadata']['total_codes_expired'] ?? 0;
$availableCodes = $totalCodes - $totalActivated;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Generator - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d1f1f 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .header {
            background: linear-gradient(135deg, #c00 0%, #800 100%);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(255,0,0,0.3);
        }
        .header h1 { font-size: 28px; margin-bottom: 5px; }
        .header p { opacity: 0.9; }
        .logout { float: right; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px; text-decoration: none; color: #fff; }
        .logout:hover { background: rgba(255,255,255,0.3); }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #c00;
        }
        .stat-box h3 { font-size: 14px; color: #888; margin-bottom: 10px; text-transform: uppercase; }
        .stat-box .value { font-size: 32px; font-weight: bold; color: #c00; }
        .form-box {
            background: #2a2a2a;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #aaa; font-weight: 500; }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            background: #1a1a1a;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input:focus { outline: none; border-color: #c00; }
        .btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #c00 0%, #800 100%);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
        }
        .btn:hover { background: linear-gradient(135deg, #f00 0%, #a00 100%); }
        .success {
            background: linear-gradient(135deg, #0a0 0%, #080 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .error {
            background: linear-gradient(135deg, #c00 0%, #800 100%);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .codes-list {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
        }
        .code-item {
            padding: 10px;
            background: #2a2a2a;
            margin-bottom: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .code-item .number { color: #666; margin-right: 10px; }
        .code-item .code { color: #0f0; font-weight: bold; }
        .copy-btn {
            padding: 5px 10px;
            background: #444;
            border: none;
            border-radius: 3px;
            color: #fff;
            cursor: pointer;
            font-size: 12px;
        }
        .copy-btn:hover { background: #666; }
        .download-btn {
            padding: 12px 30px;
            background: linear-gradient(135deg, #0a0 0%, #080 100%);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .download-btn:hover { background: linear-gradient(135deg, #0d0 0%, #0a0 100%); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="?logout" class="logout">🔓 Logout</a>
            <h1>🔐 Disposable Code Generator</h1>
            <p>Metal Lyrics Generator - Admin Panel</p>
        </div>

        <div class="stats">
            <div class="stat-box">
                <h3>Total Codes</h3>
                <div class="value"><?php echo $totalCodes; ?></div>
            </div>
            <div class="stat-box">
                <h3>Aktiviert</h3>
                <div class="value"><?php echo $totalActivated; ?></div>
            </div>
            <div class="stat-box">
                <h3>Verfügbar</h3>
                <div class="value"><?php echo $availableCodes; ?></div>
            </div>
            <div class="stat-box">
                <h3>Preis</h3>
                <div class="value"><?php echo number_format(DISPOSABLE_CODE_PACKAGE_PRICE, 2); ?>€</div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error">
                ❌ <strong>Fehler:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($generated): ?>
            <div class="success">
                ✅ <strong>Erfolgreich!</strong> <?php echo count($newCodes); ?> Codes wurden generiert
            </div>

            <a href="?download" class="download-btn">📥 Alle Codes als .txt herunterladen</a>

            <div class="codes-list">
                <h3 style="margin-bottom: 15px;">🔑 Generierte Codes:</h3>
                <?php foreach ($newCodes as $i => $code): ?>
                    <div class="code-item">
                        <div>
                            <span class="number"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?>.</span>
                            <span class="code"><?php echo htmlspecialchars($code); ?></span>
                        </div>
                        <button class="copy-btn" onclick="copyCode('<?php echo htmlspecialchars($code); ?>')">Kopieren</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-box">
            <h2 style="margin-bottom: 20px;">Neue Codes generieren</h2>
            <form method="post">
                <div class="form-group">
                    <label>Anzahl Codes (1-100)</label>
                    <input type="number" name="count" value="10" min="1" max="100" required>
                </div>
                <div class="form-group">
                    <label>Paket Name (optional)</label>
                    <input type="text" name="package_name" value="<?php echo date('d.m.Y'); ?> Paket" placeholder="z.B. Weihnachts-Aktion">
                </div>
                <button type="submit" name="generate" class="btn">🎲 Codes generieren</button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
            <p>💡 Jeder Code ist 24 Stunden nach Aktivierung gültig</p>
            <p>🔒 Geben Sie Codes nur nach erfolgter Zahlung heraus</p>
        </div>
    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Code kopiert: ' + code);
            }).catch(() => {
                // Fallback
                const el = document.createElement('textarea');
                el.value = code;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                alert('Code kopiert: ' + code);
            });
        }
    </script>
</body>
</html>
