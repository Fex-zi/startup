<?php
/**
 * Local Assets Setup Script
 * Run this to set up local asset directories and download CDN resources
 * 
 * Following Critical Path Rule - Complete asset setup before moving to next component
 */

echo "<h1>🔧 Local Assets Setup - Critical Path Component</h1>";
echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>Setting Up Production-Ready Local Assets</h3>";
echo "<p><strong>Component:</strong> Local Asset Management</p>";
echo "<p><strong>Priority:</strong> Bootstrap → Font Awesome → Custom Assets</p>";
echo "</div>";

$baseDir = __DIR__ . '/../../';
$publicDir = $baseDir . 'public/';

// Step 1: Create Directory Structure
echo "<h2>📁 Step 1: Creating Asset Directory Structure</h2>";

$assetDirectories = [
    'public/assets',
    'public/assets/css',
    'public/assets/js', 
    'public/assets/fonts',
    'public/assets/images',
    'public/assets/vendor',
    'public/assets/vendor/bootstrap',
    'public/assets/vendor/fontawesome',
    'public/assets/custom'
];

foreach ($assetDirectories as $dir) {
    $fullPath = $baseDir . $dir;
    if (!file_exists($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created: $dir<br>";
        } else {
            echo "❌ Failed to create: $dir<br>";
        }
    } else {
        echo "✅ Already exists: $dir<br>";
    }
}

// Step 2: Download Instructions & URLs
echo "<h2>⬇️ Step 2: Download URLs (Critical Path Order)</h2>";

$downloadInstructions = [
    [
        'name' => 'Bootstrap 5.1.3 CSS',
        'priority' => 'CRITICAL',
        'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css',
        'local_path' => 'public/assets/vendor/bootstrap/bootstrap.min.css',
        'why' => 'Entire UI framework depends on this'
    ],
    [
        'name' => 'Bootstrap 5.1.3 JS Bundle', 
        'priority' => 'CRITICAL',
        'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js',
        'local_path' => 'public/assets/vendor/bootstrap/bootstrap.bundle.min.js',
        'why' => 'Mobile menu, dropdowns, modals won\'t work without this'
    ],
    [
        'name' => 'Font Awesome 6.0.0 CSS',
        'priority' => 'HIGH',
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
        'local_path' => 'public/assets/vendor/fontawesome/all.min.css',
        'why' => 'Icons throughout the interface'
    ],
    [
        'name' => 'Font Awesome Webfonts',
        'priority' => 'HIGH', 
        'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/webfonts/',
        'local_path' => 'public/assets/vendor/fontawesome/webfonts/',
        'why' => 'Font files for icons'
    ]
];

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
echo "<thead><tr style='background: #f8f9fa;'>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Asset</th>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Priority</th>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Download URL</th>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Local Path</th>";
echo "</tr></thead><tbody>";

foreach ($downloadInstructions as $asset) {
    $priorityColor = $asset['priority'] === 'CRITICAL' ? '#dc3545' : '#fd7e14';
    echo "<tr>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; font-weight: bold;'>{$asset['name']}</td>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; color: $priorityColor; font-weight: bold;'>{$asset['priority']}</td>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; font-family: monospace; font-size: 0.9em;'>";
    echo "<a href='{$asset['url']}' target='_blank'>{$asset['url']}</a>";
    echo "</td>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; font-family: monospace; font-size: 0.9em;'>{$asset['local_path']}</td>";
    echo "</tr>";
}

echo "</tbody></table>";

// Step 3: Automated Download Function
echo "<h2>🤖 Step 3: Automated Download (Optional)</h2>";

function downloadAsset($url, $localPath, $baseDir) {
    $fullPath = $baseDir . $localPath;
    $directory = dirname($fullPath);
    
    // Ensure directory exists
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }
    
    // Download using cURL or file_get_contents
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $data !== false) {
            file_put_contents($fullPath, $data);
            return true;
        }
    } else {
        // Fallback to file_get_contents
        $context = stream_context_create([
            'http' => [
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
        
        $data = file_get_contents($url, false, $context);
        if ($data !== false) {
            file_put_contents($fullPath, $data);
            return true;
        }
    }
    
    return false;
}

// Auto-download if requested
if (isset($_GET['auto_download']) && $_GET['auto_download'] === 'true') {
    echo "<h3>🔄 Auto-downloading assets...</h3>";
    
    foreach ($downloadInstructions as $asset) {
        if ($asset['name'] !== 'Font Awesome Webfonts') { // Skip the folder URL
            echo "Downloading {$asset['name']}... ";
            if (downloadAsset($asset['url'], $asset['local_path'], $baseDir)) {
                echo "<span style='color: #28a745;'>✅ SUCCESS</span><br>";
            } else {
                echo "<span style='color: #dc3545;'>❌ FAILED</span><br>";
            }
        }
    }
} else {
    echo "<p><a href='?auto_download=true' class='btn btn-primary'>🚀 Auto-Download All Assets</a></p>";
    echo "<p><em>Or download manually using the URLs above</em></p>";
}

// Step 4: Manual Download Instructions
echo "<h2>📝 Step 4: Manual Download Instructions</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>For Bootstrap:</h4>";
echo "<ol>";
echo "<li>Go to <a href='https://getbootstrap.com/docs/5.1/getting-started/download/' target='_blank'>Bootstrap Download Page</a></li>";
echo "<li>Download 'Compiled CSS and JS' version</li>";
echo "<li>Extract and copy:</li>";
echo "<ul>";
echo "<li><code>bootstrap.min.css</code> → <code>public/assets/vendor/bootstrap/</code></li>";
echo "<li><code>bootstrap.bundle.min.js</code> → <code>public/assets/vendor/bootstrap/</code></li>";
echo "</ul>";
echo "</ol>";

echo "<h4>For Font Awesome:</h4>";
echo "<ol>";
echo "<li>Go to <a href='https://fontawesome.com/download' target='_blank'>Font Awesome Download</a></li>";
echo "<li>Download 'Free for Web' version</li>";
echo "<li>Extract and copy:</li>";
echo "<ul>";
echo "<li><code>css/all.min.css</code> → <code>public/assets/vendor/fontawesome/</code></li>";
echo "<li><code>webfonts/</code> folder → <code>public/assets/vendor/fontawesome/webfonts/</code></li>";
echo "</ul>";
echo "</ol>";
echo "</div>";

// Step 5: Update Layout File
echo "<h2>🔧 Step 5: Update Layout Files</h2>";

$updatedLayoutCode = '<!-- Replace CDN links with local assets -->
<link href="<?= asset(\'vendor/bootstrap/bootstrap.min.css\') ?>" rel="stylesheet">
<link href="<?= asset(\'vendor/fontawesome/all.min.css\') ?>" rel="stylesheet">

<!-- At the bottom of your layout -->
<script src="<?= asset(\'vendor/bootstrap/bootstrap.bundle.min.js\') ?>"></script>';

echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;'>";
echo htmlspecialchars($updatedLayoutCode);
echo "</pre>";

// Step 6: Helper Function for Asset URLs
echo "<h2>🛠️ Step 6: Asset Helper Function</h2>";

$helperFunction = '<?php
// Add this to src/Utils/helpers.php if not already present

if (!function_exists(\'asset\')) {
    /**
     * Generate URL for local assets
     * @param string $path
     * @return string
     */
    function asset($path) {
        $baseUrl = $_SERVER[\'REQUEST_SCHEME\'] . \'://\' . $_SERVER[\'HTTP_HOST\'];
        $scriptDir = dirname($_SERVER[\'SCRIPT_NAME\']);
        $assetBase = rtrim($baseUrl . $scriptDir, \'/\') . \'/assets/\';
        return $assetBase . ltrim($path, \'/\');
    }
}';

echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 8px; overflow-x: auto;'>";
echo htmlspecialchars($helperFunction);
echo "</pre>";

// Step 7: Performance Benefits
echo "<h2>📈 Step 7: Performance Benefits</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>✅ Benefits of Local Assets:</h4>";
echo "<ul>";
echo "<li><strong>Faster Loading:</strong> No external DNS lookups</li>";
echo "<li><strong>Reliability:</strong> Won't break if CDN goes down</li>";
echo "<li><strong>Privacy:</strong> No tracking from external CDNs</li>";
echo "<li><strong>Offline Development:</strong> Works without internet</li>";
echo "<li><strong>Caching Control:</strong> You control cache headers</li>";
echo "<li><strong>Security:</strong> No dependency on external resources</li>";
echo "</ul>";
echo "</div>";

// Step 8: File Size Information
echo "<h2>💾 Step 8: File Sizes</h2>";
echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
echo "<thead><tr style='background: #f8f9fa;'>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>File</th>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Approximate Size</th>";
echo "<th style='padding: 12px; border: 1px solid #dee2e6;'>Impact</th>";
echo "</tr></thead><tbody>";

$fileSizes = [
    ['file' => 'bootstrap.min.css', 'size' => '~160KB', 'impact' => 'Critical - UI framework'],
    ['file' => 'bootstrap.bundle.min.js', 'size' => '~220KB', 'impact' => 'Critical - Interactive components'],
    ['file' => 'fontawesome all.min.css', 'size' => '~75KB', 'impact' => 'High - Icon styles'],
    ['file' => 'fontawesome webfonts', 'size' => '~400KB total', 'impact' => 'High - Icon fonts'],
];

foreach ($fileSizes as $file) {
    echo "<tr>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; font-family: monospace;'>{$file['file']}</td>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6; text-align: center;'>{$file['size']}</td>";
    echo "<td style='padding: 12px; border: 1px solid #dee2e6;'>{$file['impact']}</td>";
    echo "</tr>";
}
echo "</tbody></table>";

// Step 9: Next Steps
echo "<h2>🎯 Step 9: Critical Path Completion</h2>";
echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4>After completing this component:</h4>";
echo "<ol>";
echo "<li>✅ Download Bootstrap CSS & JS (CRITICAL)</li>";
echo "<li>✅ Download Font Awesome CSS & Fonts (HIGH)</li>";
echo "<li>✅ Update layout file to use local assets</li>";
echo "<li>✅ Test that all UI components work</li>";
echo "<li>✅ Verify mobile navigation still functions</li>";
echo "<li>➡️ Move to next major component in Critical Path</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🧪 Verification Test</h2>";
echo "<p>After setup, verify by checking:</p>";
echo "<ul>";
echo "<li>Dashboard loads with proper styling</li>";
echo "<li>Mobile menu toggle works</li>";
echo "<li>Icons display correctly</li>";
echo "<li>No console errors about missing resources</li>";
echo "</ul>";

echo "<p><strong>✅ Component Complete when all assets load locally!</strong></p>";

?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    margin: 20px;
    background: #f8f9fa;
}

h1 {
    color: #2d3748;
    border-bottom: 3px solid #667eea;
    padding-bottom: 10px;
}

h2 {
    color: #4a5568;
    margin-top: 30px;
    padding: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    margin: 10px 0;
}

.btn:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    color: white;
}

table {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

tr:nth-child(even) {
    background: #f8f9fa;
}

tr:hover {
    background: #e9ecef;
}

pre {
    font-family: 'Consolas', 'Monaco', monospace;
}
</style>