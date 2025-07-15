<?php
echo "<h1>Font Awesome Complete Setup</h1>";

$baseDir = __DIR__ . '/../../';
$fontDir = $baseDir . 'public/assets/vendor/webfonts/';

// Create webfonts directory
if (!file_exists($fontDir)) {
    mkdir($fontDir, 0755, true);
    echo "✅ Created webfonts directory<br>";
}

// Font files to download
$fonts = [
    'fa-brands-400.woff2',
    'fa-regular-400.woff2', 
    'fa-solid-900.woff2',
    'fa-v4compatibility.woff2'
];

echo "<h2>Downloading Font Files...</h2>";

foreach ($fonts as $font) {
    $url = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/webfonts/$font";
    $savePath = $fontDir . $font;
    
    echo "Downloading $font... ";
    
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $data = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $data !== false) {
            file_put_contents($savePath, $data);
            echo "<span style='color: green;'>✅ SUCCESS (" . number_format(strlen($data)/1024, 1) . " KB)</span><br>";
        } else {
            echo "<span style='color: red;'>❌ FAILED</span><br>";
        }
    } else {
        echo "<span style='color: orange;'>⚠️ cURL not available - download manually</span><br>";
    }
}

echo "<h2>✅ Font Awesome Setup Complete!</h2>";
echo "<p>Refresh your dashboard - icons should now display properly!</p>";
?>