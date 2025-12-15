#!/usr/bin/env php
<?php
/**
 * DISPOSABLE CODE GENERATOR
 *
 * Generates secure, unique disposable premium codes
 * Each code can only be activated once
 *
 * Usage:
 *   php generate-disposable-codes.php [count] [package-name]
 *
 * Examples:
 *   php generate-disposable-codes.php 10 "Paket #1"
 *   php generate-disposable-codes.php 5 "Test Batch"
 *   php generate-disposable-codes.php (generates 10 codes by default)
 */

require_once __DIR__ . '/env-loader.php';
require_once __DIR__ . '/config.php';

// ========================================
// CONFIGURATION
// ========================================

// Get parameters from command line
$count = isset($argv[1]) && is_numeric($argv[1]) ? (int)$argv[1] : DISPOSABLE_CODE_PACKAGE_SIZE;
$packageName = $argv[2] ?? 'Generated Package';

// Validate count
if ($count < 1 || $count > 100) {
    echo "❌ ERROR: Count must be between 1 and 100\n";
    exit(1);
}

// ========================================
// FUNCTIONS
// ========================================

/**
 * Generate a secure random code
 * Format: METAL-XXXXXXXXXXXX (12 random chars)
 */
function generateSecureCode() {
    // Generate 12 random characters (uppercase letters and numbers)
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Removed confusing chars: I, O, 0, 1
    $code = 'METAL-';

    for ($i = 0; $i < 12; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $code;
}

/**
 * Check if code already exists
 */
function codeExists($code, $data) {
    return isset($data['codes'][$code]);
}

/**
 * Generate unique codes
 */
function generateUniqueCodes($count, $existingData) {
    $codes = [];
    $attempts = 0;
    $maxAttempts = $count * 10; // Prevent infinite loop

    while (count($codes) < $count && $attempts < $maxAttempts) {
        $code = generateSecureCode();

        // Check if code is unique
        if (!in_array($code, $codes) && !codeExists($code, $existingData)) {
            $codes[] = $code;
        }

        $attempts++;
    }

    if (count($codes) < $count) {
        echo "⚠️  WARNING: Could only generate " . count($codes) . " unique codes (requested: $count)\n";
    }

    return $codes;
}

// ========================================
// MAIN SCRIPT
// ========================================

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   DISPOSABLE CODE GENERATOR               ║\n";
echo "║   Metal Lyrics Generator                  ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";

// Check if disposable codes are enabled
if (!ENABLE_DISPOSABLE_CODES) {
    echo "❌ ERROR: Disposable codes are disabled in config.php\n";
    echo "   Set ENABLE_DISPOSABLE_CODES to true\n";
    exit(1);
}

// Load existing data
echo "📂 Loading existing codes...\n";
$data = loadDisposableCodes();

$existingCount = count($data['codes']);
echo "   Found $existingCount existing codes\n";

// Generate new codes
echo "\n🔐 Generating $count new secure codes...\n";
$newCodes = generateUniqueCodes($count, $data);

if (empty($newCodes)) {
    echo "❌ ERROR: Could not generate any codes\n";
    exit(1);
}

// Prepare batch info
$batchId = date('Ymd-His');
$timestamp = date('Y-m-d H:i:s');

echo "   Generated " . count($newCodes) . " codes\n";
echo "   Batch ID: $batchId\n";

// Add codes to data structure
echo "\n💾 Saving codes to database...\n";

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

if (!$success) {
    echo "❌ ERROR: Could not save codes to file\n";
    echo "   Check file permissions for: " . DISPOSABLE_CODES_FILE . "\n";
    exit(1);
}

echo "   ✅ Saved successfully!\n";

// ========================================
// OUTPUT
// ========================================

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   SUCCESS! ✅                              ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";

echo "📦 PACKAGE INFORMATION:\n";
echo "   Package: $packageName\n";
echo "   Price: " . number_format(DISPOSABLE_CODE_PACKAGE_PRICE, 2) . " EUR\n";
echo "   Codes: " . count($newCodes) . "\n";
echo "   Duration: " . DISPOSABLE_CODE_DURATION_HOURS . " hours per code\n";
echo "   Batch ID: $batchId\n";
echo "\n";

echo "🔑 GENERATED CODES:\n";
echo "   (Each code is valid for 24 hours after first activation)\n";
echo "\n";

foreach ($newCodes as $i => $code) {
    echo "   " . str_pad($i + 1, 2, ' ', STR_PAD_LEFT) . ". $code\n";
}

echo "\n";

echo "📊 STATISTICS:\n";
echo "   Total codes in database: " . count($data['codes']) . "\n";
echo "   Total codes generated: " . $data['metadata']['total_codes_generated'] . "\n";
echo "   Total codes activated: " . $data['metadata']['total_codes_activated'] . "\n";
echo "   Total codes expired: " . $data['metadata']['total_codes_expired'] . "\n";
echo "   Available codes: " . (count($data['codes']) - $data['metadata']['total_codes_activated']) . "\n";
echo "\n";

echo "💡 NEXT STEPS:\n";
echo "   1. Save these codes securely\n";
echo "   2. Distribute codes to customers after payment\n";
echo "   3. Each code is valid for 24 hours after first activation\n";
echo "   4. Codes can be used on multiple devices during the 24-hour period\n";
echo "   5. Monitor usage with: php view-code-statistics.php\n";
echo "\n";

echo "✨ Done!\n";
echo "\n";

exit(0);
?>
