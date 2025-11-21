#!/usr/bin/env php
<?php
/**
 * CODE STATISTICS VIEWER
 *
 * View statistics and details about disposable premium codes
 *
 * Usage:
 *   php view-code-statistics.php [--detailed] [--unused] [--active] [--expired]
 *
 * Options:
 *   --detailed    Show detailed information for each code
 *   --unused      Show only unused codes
 *   --active      Show only active (non-expired) codes
 *   --expired     Show only expired codes
 *
 * Examples:
 *   php view-code-statistics.php
 *   php view-code-statistics.php --detailed
 *   php view-code-statistics.php --active
 */

require_once __DIR__ . '/config.php';

// ========================================
// PARSE COMMAND LINE OPTIONS
// ========================================

$detailed = in_array('--detailed', $argv);
$showUnused = in_array('--unused', $argv);
$showActive = in_array('--active', $argv);
$showExpired = in_array('--expired', $argv);

// If no filter is set, show all
$showAll = !$showUnused && !$showActive && !$showExpired;

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
$activatedCodes = 0;
$unusedCodes = 0;
$activeCodes = 0;
$expiredCodes = 0;
$packages = [];
$now = time();

foreach ($data['codes'] as $code => $info) {
    $isActivated = !empty($info['activated_at']);
    $isExpired = false;

    if ($isActivated) {
        $activatedCodes++;

        if (!empty($info['expires_at'])) {
            $expiresAt = strtotime($info['expires_at']);
            $isExpired = ($now >= $expiresAt);

            if ($isExpired) {
                $expiredCodes++;
            } else {
                $activeCodes++;
            }
        }
    } else {
        $unusedCodes++;
    }

    // Group by package
    $packageName = $info['package_name'] ?? 'Unknown';
    if (!isset($packages[$packageName])) {
        $packages[$packageName] = [
            'total' => 0,
            'activated' => 0,
            'unused' => 0,
            'active' => 0,
            'expired' => 0,
            'batch_ids' => []
        ];
    }

    $packages[$packageName]['total']++;

    if ($isActivated) {
        $packages[$packageName]['activated']++;
        if ($isExpired) {
            $packages[$packageName]['expired']++;
        } else {
            $packages[$packageName]['active']++;
        }
    } else {
        $packages[$packageName]['unused']++;
    }

    if (isset($info['batch_id']) && !in_array($info['batch_id'], $packages[$packageName]['batch_ids'])) {
        $packages[$packageName]['batch_ids'][] = $info['batch_id'];
    }
}

$usagePercent = $totalCodes > 0 ? round(($activatedCodes / $totalCodes) * 100, 1) : 0;
$revenue = $activatedCodes * DISPOSABLE_CODE_PACKAGE_PRICE;

// ========================================
// OUTPUT STATISTICS
// ========================================

echo "\n";
echo "📊 OVERALL STATISTICS:\n";
echo "   ═══════════════════════════════════════\n";
echo "   Total Codes:        $totalCodes\n";
echo "   Activated Codes:    $activatedCodes (" . $usagePercent . "%)\n";
echo "   Unused Codes:       $unusedCodes\n";
echo "   Active Codes:       $activeCodes (not expired)\n";
echo "   Expired Codes:      $expiredCodes\n";
echo "   Package Price:      " . number_format(DISPOSABLE_CODE_PACKAGE_PRICE, 2) . " EUR\n";
echo "   Code Duration:      " . DISPOSABLE_CODE_DURATION_HOURS . " hours\n";
echo "   Total Revenue:      " . number_format($revenue, 2) . " EUR\n";
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
        echo "   Total:      " . $stats['total'] . " codes\n";
        echo "   Activated:  " . $stats['activated'] . " codes\n";
        echo "   Unused:     " . $stats['unused'] . " codes\n";
        echo "   Active:     " . $stats['active'] . " codes (not expired)\n";
        echo "   Expired:    " . $stats['expired'] . " codes\n";
        echo "   Batches:    " . count($stats['batch_ids']) . "\n";
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
        $isActivated = !empty($info['activated_at']);
        $isExpired = false;
        $remainingTime = '';

        if ($isActivated && !empty($info['expires_at'])) {
            $expiresAt = strtotime($info['expires_at']);
            $isExpired = ($now >= $expiresAt);

            if (!$isExpired) {
                $remainingSeconds = $expiresAt - $now;
                $remainingHours = round($remainingSeconds / 3600, 1);
                $remainingTime = " ({$remainingHours}h remaining)";
            }
        }

        // Filter based on options
        if ($showUnused && $isActivated) continue;
        if ($showActive && (!$isActivated || $isExpired)) continue;
        if ($showExpired && !$isExpired) continue;

        $displayedCount++;

        // Determine status
        $status = "🟢 UNUSED";
        if ($isActivated) {
            $status = $isExpired ? "🔴 EXPIRED" : "🟡 ACTIVE" . $remainingTime;
        }

        echo "\n";
        echo "   Code #$displayedCount: $code\n";
        echo "   ───────────────────────────────────────\n";
        echo "   Status:       $status\n";
        echo "   Package:      " . ($info['package_name'] ?? 'Unknown') . "\n";
        echo "   Batch ID:     " . ($info['batch_id'] ?? 'Unknown') . "\n";
        echo "   Created:      " . ($info['created_at'] ?? 'Unknown') . "\n";

        if ($isActivated) {
            echo "   Activated:    " . ($info['activated_at'] ?? 'Unknown') . "\n";
            echo "   Expires:      " . ($info['expires_at'] ?? 'Unknown') . "\n";
            echo "   Activated IP: " . ($info['activation_ip'] ?? 'Unknown') . "\n";
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
echo "   $ php generate-disposable-codes.php 1 \"Package Name\"\n";
echo "\n";
echo "   View only unused codes:\n";
echo "   $ php view-code-statistics.php --detailed --unused\n";
echo "\n";
echo "   View only active codes:\n";
echo "   $ php view-code-statistics.php --detailed --active\n";
echo "\n";
echo "   View only expired codes:\n";
echo "   $ php view-code-statistics.php --detailed --expired\n";
echo "\n";

// ========================================
// METADATA
// ========================================

if (isset($data['metadata'])) {
    echo "ℹ️  METADATA:\n";
    echo "   ═══════════════════════════════════════\n";
    echo "   Last Updated:       " . ($data['metadata']['last_updated'] ?? 'Unknown') . "\n";
    echo "   Total Generated:    " . ($data['metadata']['total_codes_generated'] ?? 0) . "\n";
    echo "   Total Activated:    " . ($data['metadata']['total_codes_activated'] ?? 0) . "\n";
    echo "   Total Expired:      " . ($data['metadata']['total_codes_expired'] ?? 0) . "\n";
    echo "\n";
}

echo "✨ Done!\n";
echo "\n";

exit(0);
?>
