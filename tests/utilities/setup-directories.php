<?php

echo "<h1>Setting Up Directory Structure</h1>";
echo "<p><em>Location: tests/utilities/setup-directories.php</em></p>";

$baseDir = __DIR__ . '/../../';

$directories = [
    'storage',
    'storage/cache',
    'storage/logs',
    'storage/sessions',
    'storage/temp',
    'public/uploads',
    'public/uploads/documents',
    'public/uploads/documents/pitch-decks',
    'public/uploads/documents/financials',
    'public/uploads/documents/legal-docs',
    'public/uploads/avatars',
    'public/uploads/company-logos',
    'tests',
    'tests/integration',
    'tests/unit',
    'tests/unit/ModelTests',
    'tests/unit/ControllerTests',
    'tests/unit/ServiceTests',
    'tests/utilities',
    'tests/fixtures',
    'tests/fixtures/sample-data',
    'tests/fixtures/test-configs'
];

$files = [
    'storage/logs/.gitkeep' => '',
    'storage/cache/.gitkeep' => '',
    'storage/temp/.gitkeep' => '',
    'public/uploads/.gitkeep' => '',
    'tests/integration/.gitkeep' => '',
    'tests/unit/.gitkeep' => '',
    'tests/utilities/.gitkeep' => '',
    'tests/fixtures/.gitkeep' => '',
    'src/Views/dashboard/investor.php' => getInvestorDashboardContent(),
    'src/Views/dashboard/startup.php' => getStartupDashboardContent()
];

echo "<h2>Creating Directories...</h2>";
foreach ($directories as $dir) {
    $fullPath = $baseDir . $dir;
    if (!file_exists($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created directory: $dir<br>";
        } else {
            echo "❌ Failed to create directory: $dir<br>";
        }
    } else {
        echo "✅ Directory already exists: $dir<br>";
    }
}

echo "<h2>Creating Essential Files...</h2>";
foreach ($files as $file => $content) {
    $fullPath = $baseDir . $file;
    if (!file_exists($fullPath)) {
        $dir = dirname($fullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        if (file_put_contents($fullPath, $content) !== false) {
            echo "✅ Created file: $file<br>";
        } else {
            echo "❌ Failed to create file: $file<br>";
        }
    } else {
        echo "✅ File already exists: $file<br>";
    }
}

echo "<h2>Setting Permissions...</h2>";
$writableDirectories = ['storage', 'public/uploads'];
foreach ($writableDirectories as $dir) {
    $fullPath = $baseDir . $dir;
    if (file_exists($fullPath)) {
        if (chmod($fullPath, 0755)) {
            echo "✅ Set permissions for: $dir<br>";
        } else {
            echo "⚠️ Could not set permissions for: $dir<br>";
        }
    }
}

echo "<h2>✅ Directory Setup Complete!</h2>";

echo "<h2>Navigation</h2>";
echo "<p>";
echo "<a href='" . __DIR__ . "/debug.php'>Run Debug Script</a> | ";
echo "<a href='" . __DIR__ . "/database-diagnostic.php'>Database Diagnostic</a> | ";
echo "<a href='" . __DIR__ . "/../../dashboard'>Go to Dashboard</a>";
echo "</p>";

function getInvestorDashboardContent() {
    return '<?php
// This file was auto-generated by setup script
// You can customize it as needed

// If this file loads, it means the investor dashboard view is available
if (!isset($user)) {
    echo "<div class=\"alert alert-danger\">User data not available</div>";
    return;
}
?>

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Welcome back, <?= htmlspecialchars($user[\'first_name\'] ?? \'Investor\') ?>!</h2>
                <p class="card-text">Here\'s your investment dashboard overview.</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Matches</h5>
                <h3><?= $stats[\'total_matches\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Active Conversations</h5>
                <h3><?= $stats[\'active_conversations\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Saved Startups</h5>
                <h3><?= $stats[\'saved_startups\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Pending Reviews</h5>
                <h3><?= $stats[\'pending_reviews\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Matches -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Startup Matches</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentMatches)): ?>
                    <p class="text-muted">No matches found yet. <a href="/search/startups">Browse startups</a> to find potential investments.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Industry</th>
                                    <th>Stage</th>
                                    <th>Funding Goal</th>
                                    <th>Match Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMatches as $match): ?>
                                <tr>
                                    <td><?= htmlspecialchars($match[\'company_name\']) ?></td>
                                    <td><?= htmlspecialchars($match[\'industry_name\']) ?></td>
                                    <td><?= htmlspecialchars($match[\'funding_stage\']) ?></td>
                                    <td>$<?= number_format($match[\'funding_goal\']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $match[\'score\'] >= 80 ? \'success\' : ($match[\'score\'] >= 60 ? \'warning\' : \'secondary\') ?>">
                                            <?= $match[\'score\'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/startup/<?= $match[\'startup_slug\'] ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>';
}

function getStartupDashboardContent() {
    return '<?php
// This file was auto-generated by setup script
// You can customize it as needed

// If this file loads, it means the startup dashboard view is available
if (!isset($user)) {
    echo "<div class=\"alert alert-danger\">User data not available</div>";
    return;
}
?>

<!-- Welcome Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Welcome back, <?= htmlspecialchars($user[\'first_name\'] ?? \'Founder\') ?>!</h2>
                <p class="card-text">Here\'s your startup dashboard overview.</p>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Investor Matches</h5>
                <h3><?= $stats[\'total_matches\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Profile Views</h5>
                <h3><?= $stats[\'profile_views\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title">Interested Investors</h5>
                <h3><?= $stats[\'interested_investors\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Messages</h5>
                <h3><?= $stats[\'unread_messages\'] ?? 0 ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Recent Matches -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Investor Matches</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentMatches)): ?>
                    <p class="text-muted">No matches found yet. <a href="/search/investors">Browse investors</a> to find potential funding sources.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Investor</th>
                                    <th>Focus Industries</th>
                                    <th>Investment Range</th>
                                    <th>Portfolio Size</th>
                                    <th>Match Score</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentMatches as $match): ?>
                                <tr>
                                    <td><?= htmlspecialchars($match[\'investor_name\']) ?></td>
                                    <td><?= htmlspecialchars($match[\'focus_industries\']) ?></td>
                                    <td>$<?= number_format($match[\'min_investment\']) ?> - $<?= number_format($match[\'max_investment\']) ?></td>
                                    <td><?= $match[\'portfolio_size\'] ?? \'N/A\' ?></td>
                                    <td>
                                        <span class="badge badge-<?= $match[\'score\'] >= 80 ? \'success\' : ($match[\'score\'] >= 60 ? \'warning\' : \'secondary\') ?>">
                                            <?= $match[\'score\'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/investor/<?= $match[\'investor_slug\'] ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>';
}
?>