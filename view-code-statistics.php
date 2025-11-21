#!/usr/bin/env php
<?php
/**
 * CODE STATISTICS VIEWER
 *
 * View statistics and details about disposable premium codes
 *
 * Usage:
 *   php view-code-statistics.php [--detailed] [--unused] [--used]
 *
 * Options:
 *   --detailed    Show detailed information for each code
 *   --unused      Show only unused codes
 *   --used        Show only used codes
 *
 * Examples:
 *   php view-code-statistics.php
 *   php view-code-statistics.php --detailed
 *   php view-code-statistics.php --unused
 */

require_once __DIR__ . '/config.php';

// ========================================
// PARSE COMMAND LINE OPTIONS
// ========================================

$detailed = in_array('--detailed', $argv);
$showUnused = in_array('--unused', $argv);
$showUsed = in_array('--used', $argv);

// If neither filter is set, show all
$showAll = !$showUnused && !$showUsed;

// ========================================
// LOAD DATA
// ========================================

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║   CODE STATISTICS VIEWER                  ║\n";
echo "║   Metal Lyrics Generator                  ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";

// Check if disposable codes are enabled
if (!ENABLE_DISPOSABLE_CODES) {
    echo "❌ Disposable codes are disabled in config.php\n";
    exit(1);
}

echo "📂 Loading code database...\n";
$data = loadDisposableCodes();

if (empty($data['codes'])) {
    echo "   ⚠️  No codes found in database\n";
    echo "\n💡 Generate codes with: php generate-disposable-codes.php 10\n";
    echo "\n";
    exit(0);
}

// ========================================
// CALCULATE STATISTICS
// ========================================

$totalCodes = count($data['codes']);
$usedCodes = 0;
$unusedCodes = 0;
$packages = [];

foreach ($data['codes'] as $code => $info) {
    if (isset($info['used']) && $info['used'] === true) {
        $usedCodes++;
    } else {
        $unusedCodes++;
    }

    // Group by package
    $packageName = $info['package_name'] ?? 'Unknown';
    if (!isset($packages[$packageName])) {
        $packages[$packageName] = [
            'total' => 0,
            'used' => 0,
            'unused' => 0,
            'batch_ids' => []
        ];
    }

    $packages[$packageName]['total']++;

    if (isset($info['used']) && $info['used'] === true) {
        $packages[$packageName]['used']++;
    } else {
        $packages[$packageName]['unused']++;
    }

    if (isset($info['batch_id']) && !in_array($info['batch_id'], $packages[$packageName]['batch_ids'])) {
        $packages[$packageName]['batch_ids'][] = $info['batch_id'];
    }
}

$usagePercent = $totalCodes > 0 ? round(($usedCodes / $totalCodes) * 100, 1) : 0;
$revenue = $usedCodes * DISPOSABLE_CODE_PACKAGE_PRICE / DISPOSABLE_CODE_PACKAGE_SIZE;

// ========================================
// OUTPUT STATISTICS
// ========================================

echo "\n";
echo "📊 OVERALL STATISTICS:\n";
echo "   ═══════════════════════════════════════\n";
echo "   Total Codes:        $totalCodes\n";
echo "   Used Codes:         $usedCodes (" . $usagePercent . "%)\n";
echo "   Unused Codes:       $unusedCodes\n";
echo "   Package Price:      " . number_format(DISPOSABLE_CODE_PACKAGE_PRICE, 2) . " EUR\n";
echo "   Codes per Package:  " . DISPOSABLE_CODE_PACKAGE_SIZE . "\n";
echo "   Estimated Revenue:  " . number_format($revenue, 2) . " EUR\n";
echo "\n";

// ========================================
// PACKAGES BREAKDOWN
// ========================================

if (count($packages) > 0) {
    echo "📦 PACKAGES BREAKDOWN:\n";
    echo "   ═══════════════════════════════════════\n";

    foreach ($packages as $packageName => $stats) {
        echo "\n   Package: $packageName\n";
        echo "   ───────────────────────────────────────\n";
        echo "   Total:    " . $stats['total'] . " codes\n";
        echo "   Used:     " . $stats['used'] . " codes\n";
        echo "   Unused:   " . $stats['unused'] . " codes\n";
        echo "   Batches:  " . count($stats['batch_ids']) . "\n";
    }

    echo "\n";
}

// ========================================
// DETAILED CODE LIST
// ========================================

if ($detailed) {
    echo "📋 DETAILED CODE LIST:\n";
    echo "   ═══════════════════════════════════════\n";

    $displayedCount = 0;

    foreach ($data['codes'] as $code => $info) {
        $isUsed = isset($info['used']) && $info['used'] === true;

        // Filter based on options
        if ($showUnused && $isUsed) continue;
        if ($showUsed && !$isUsed) continue;

        $displayedCount++;

        echo "\n";
        echo "   Code #$displayedCount: $code\n";
        echo "   ───────────────────────────────────────\n";
        echo "   Status:       " . ($isUsed ? "🔴 USED" : "🟢 AVAILABLE") . "\n";
        echo "   Package:      " . ($info['package_name'] ?? 'Unknown') . "\n";
        echo "   Batch ID:     " . ($info['batch_id'] ?? 'Unknown') . "\n";
        echo "   Created:      " . ($info['created_at'] ?? 'Unknown') . "\n";

        if ($isUsed) {
            echo "   Used At:      " . ($info['used_at'] ?? 'Unknown') . "\n";
            echo "   Used By IP:   " . ($info['used_ip'] ?? 'Unknown') . "\n";
        }
    }

    if ($displayedCount === 0) {
        echo "\n   No codes match the selected filter.\n";
    }

    echo "\n";
}

// ========================================
// QUICK ACTIONS
// ========================================

echo "💡 QUICK ACTIONS:\n";
echo "   ═══════════════════════════════════════\n";
echo "   Generate more codes:\n";
echo "   $ php generate-disposable-codes.php 10 \"Package Name\"\n";
echo "\n";
echo "   View only unused codes:\n";
echo "   $ php view-code-statistics.php --detailed --unused\n";
echo "\n";
echo "   View only used codes:\n";
echo "   $ php view-code-statistics.php --detailed --used\n";
echo "\n";

// ========================================
// METADATA
// ========================================

if (isset($data['metadata'])) {
    echo "ℹ️  METADATA:\n";
    echo "   ═══════════════════════════════════════\n";
    echo "   Last Updated:       " . ($data['metadata']['last_updated'] ?? 'Unknown') . "\n";
    echo "   Total Generated:    " . ($data['metadata']['total_codes_generated'] ?? 0) . "\n";
    echo "   Total Used:         " . ($data['metadata']['total_codes_used'] ?? 0) . "\n";
    echo "\n";
}

echo "✨ Done!\n";
echo "\n";

exit(0);
?>
