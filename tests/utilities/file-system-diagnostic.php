<?php
// tests/utilities/file-system-diagnostic.php
// Comprehensive file system testing for upload functionality

echo "<h1>📁 File System Diagnostic Script</h1>";
echo "<p><em>Testing directory creation, permissions, and file operations</em></p>";

// Get different possible upload directory paths
$possiblePaths = [
    'Method 1: $_SERVER[DOCUMENT_ROOT]' => $_SERVER['DOCUMENT_ROOT'] . '/assets/uploads/',
    'Method 2: __DIR__ relative' => __DIR__ . '/../../public/assets/uploads/',
    'Method 3: Absolute from public' => realpath(__DIR__ . '/../../public') . '/assets/uploads/',
    'Method 4: Current working directory' => getcwd() . '/public/assets/uploads/'
];

echo "<h2>📋 Path Analysis</h2>";
echo "<table style='border-collapse: collapse; width: 100%; border: 1px solid #ddd;'>";
echo "<tr style='background: #f5f5f5;'>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Method</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Path</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Exists</th>";
echo "<th style='border: 1px solid #ddd; padding: 8px;'>Writable</th>";
echo "</tr>";

foreach ($possiblePaths as $method => $path) {
    $exists = file_exists($path);
    $writable = $exists ? is_writable($path) : false;
    
    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>$method</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px; font-family: monospace;'>$path</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($exists ? "✅ Yes" : "❌ No") . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($writable ? "✅ Yes" : "❌ No") . "</td>";
    echo "</tr>";
}
echo "</table>";

// Determine best base path
$baseUploadDir = null;
foreach ($possiblePaths as $method => $path) {
    if (file_exists(dirname($path))) {
        $baseUploadDir = $path;
        echo "<p><strong>🎯 Using base path:</strong> <code>$path</code> (from $method)</p>";
        break;
    }
}

if (!$baseUploadDir) {
    // Try to create in public directory
    $publicDir = realpath(__DIR__ . '/../../public');
    if ($publicDir) {
        $baseUploadDir = $publicDir . '/assets/uploads/';
        echo "<p><strong>🔧 Will attempt to create:</strong> <code>$baseUploadDir</code></p>";
    } else {
        echo "<p><strong>❌ Error:</strong> Cannot determine upload directory location</p>";
        exit;
    }
}

echo "<h2>📁 Directory Creation Test</h2>";

$requiredDirs = [
    'assets',
    'assets/uploads',
    'assets/uploads/logos',
    'assets/uploads/profiles', 
    'assets/uploads/documents'
];

$publicDir = dirname($baseUploadDir, 2); // Go up to public level
echo "<p><strong>Public directory:</strong> <code>$publicDir</code></p>";

foreach ($requiredDirs as $dir) {
    $fullPath = $publicDir . '/' . $dir;
    echo "<h3>Testing: $dir</h3>";
    echo "&nbsp;&nbsp;Full path: <code>$fullPath</code><br>";
    
    if (file_exists($fullPath)) {
        echo "&nbsp;&nbsp;✅ Directory exists<br>";
        
        if (is_readable($fullPath)) {
            echo "&nbsp;&nbsp;✅ Directory is readable<br>";
        } else {
            echo "&nbsp;&nbsp;❌ Directory is NOT readable<br>";
        }
        
        if (is_writable($fullPath)) {
            echo "&nbsp;&nbsp;✅ Directory is writable<br>";
        } else {
            echo "&nbsp;&nbsp;❌ Directory is NOT writable<br>";
        }
        
        // List contents
        $contents = scandir($fullPath);
        $contents = array_diff($contents, ['.', '..']);
        if (!empty($contents)) {
            echo "&nbsp;&nbsp;📄 Contents: " . implode(', ', array_slice($contents, 0, 5));
            if (count($contents) > 5) echo " (and " . (count($contents) - 5) . " more)";
            echo "<br>";
        } else {
            echo "&nbsp;&nbsp;📄 Directory is empty<br>";
        }
        
    } else {
        echo "&nbsp;&nbsp;❌ Directory does NOT exist<br>";
        echo "&nbsp;&nbsp;🔧 Attempting to create...<br>";
        
        try {
            if (mkdir($fullPath, 0755, true)) {
                echo "&nbsp;&nbsp;✅ Successfully created directory<br>";
                
                // Test permissions on newly created directory
                if (is_writable($fullPath)) {
                    echo "&nbsp;&nbsp;✅ New directory is writable<br>";
                } else {
                    echo "&nbsp;&nbsp;❌ New directory is NOT writable<br>";
                }
            } else {
                echo "&nbsp;&nbsp;❌ Failed to create directory<br>";
            }
        } catch (Exception $e) {
            echo "&nbsp;&nbsp;❌ Error creating directory: " . $e->getMessage() . "<br>";
        }
    }
    echo "<br>";
}

echo "<h2>✍️ File Write Test</h2>";

$testDirs = ['logos', 'profiles', 'documents'];

foreach ($testDirs as $testDir) {
    $testPath = $baseUploadDir . $testDir . '/';
    echo "<h3>Testing file operations in: $testDir</h3>";
    
    if (!file_exists($testPath)) {
        echo "&nbsp;&nbsp;❌ Directory doesn't exist, skipping file test<br>";
        continue;
    }
    
    // Test file creation
    $testFile = $testPath . 'test_' . time() . '.txt';
    $testContent = "File write test - " . date('Y-m-d H:i:s');
    
    echo "&nbsp;&nbsp;📝 Attempting to write test file...<br>";
    echo "&nbsp;&nbsp;File path: <code>$testFile</code><br>";
    
    try {
        if (file_put_contents($testFile, $testContent) !== false) {
            echo "&nbsp;&nbsp;✅ Successfully wrote test file<br>";
            
            // Test file reading
            if (file_exists($testFile) && is_readable($testFile)) {
                $readContent = file_get_contents($testFile);
                if ($readContent === $testContent) {
                    echo "&nbsp;&nbsp;✅ Successfully read test file<br>";
                    echo "&nbsp;&nbsp;📄 Content matches: " . htmlspecialchars($readContent) . "<br>";
                } else {
                    echo "&nbsp;&nbsp;❌ File content doesn't match<br>";
                }
            } else {
                echo "&nbsp;&nbsp;❌ Cannot read test file<br>";
            }
            
            // Clean up test file
            if (unlink($testFile)) {
                echo "&nbsp;&nbsp;✅ Successfully deleted test file<br>";
            } else {
                echo "&nbsp;&nbsp;⚠️ Could not delete test file<br>";
            }
            
        } else {
            echo "&nbsp;&nbsp;❌ Failed to write test file<br>";
        }
    } catch (Exception $e) {
        echo "&nbsp;&nbsp;❌ Error during file test: " . $e->getMessage() . "<br>";
    }
    echo "<br>";
}

echo "<h2>🖼️ Image Upload Simulation Test</h2>";

$logoDir = $baseUploadDir . 'logos/';
if (file_exists($logoDir) && is_writable($logoDir)) {
    echo "<p>Testing image file creation in logos directory...</p>";
    
    // Create a simple test image
    $testImagePath = $logoDir . 'test_image_' . time() . '.png';
    
    try {
        // Create a simple 1x1 pixel PNG
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChAGA60e6kgAAAABJRU5ErkJggg==');
        
        if (file_put_contents($testImagePath, $imageData) !== false) {
            echo "&nbsp;&nbsp;✅ Successfully created test image<br>";
            echo "&nbsp;&nbsp;📁 Image path: <code>$testImagePath</code><br>";
            echo "&nbsp;&nbsp;📏 File size: " . filesize($testImagePath) . " bytes<br>";
            
            // Test if image is accessible via web
            $webPath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $testImagePath);
            $webUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $webPath;
            echo "&nbsp;&nbsp;🌐 Web URL: <a href='$webUrl' target='_blank'>$webUrl</a><br>";
            
            // Clean up
            if (unlink($testImagePath)) {
                echo "&nbsp;&nbsp;✅ Test image cleaned up<br>";
            }
        } else {
            echo "&nbsp;&nbsp;❌ Failed to create test image<br>";
        }
    } catch (Exception $e) {
        echo "&nbsp;&nbsp;❌ Image test error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<p>❌ Cannot test image upload - logos directory not writable</p>";
}

echo "<h2>⚙️ System Information</h2>";
echo "<ul>";
echo "<li><strong>Current working directory:</strong> " . getcwd() . "</li>";
echo "<li><strong>Script location:</strong> " . __FILE__ . "</li>";
echo "<li><strong>DOCUMENT_ROOT:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "<li><strong>Script name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</li>";
echo "<li><strong>PHP version:</strong> " . PHP_VERSION . "</li>";
echo "<li><strong>Operating system:</strong> " . PHP_OS . "</li>";
echo "<li><strong>Upload max filesize:</strong> " . ini_get('upload_max_filesize') . "</li>";
echo "<li><strong>Post max size:</strong> " . ini_get('post_max_size') . "</li>";
echo "<li><strong>Memory limit:</strong> " . ini_get('memory_limit') . "</li>";
echo "</ul>";

// Check for common permission issues
echo "<h2>🔍 Common Issues Check</h2>";

// Check if running on Windows vs Linux
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "<p>✅ Running on Windows - directory permissions usually work differently</p>";
} else {
    echo "<p>ℹ️ Running on Unix/Linux - check file ownership and permissions</p>";
    
    // Check directory permissions in octal
    if (file_exists($baseUploadDir)) {
        $perms = fileperms($baseUploadDir);
        $octal = substr(sprintf('%o', $perms), -4);
        echo "<p>📊 Upload directory permissions: $octal</p>";
        
        if ($octal === '0755' || $octal === '0775' || $octal === '0777') {
            echo "<p>✅ Directory permissions look good</p>";
        } else {
            echo "<p>⚠️ Directory permissions might be restrictive. Try: <code>chmod 755 " . $baseUploadDir . "</code></p>";
        }
    }
}

// Check for .htaccess files that might block uploads
$htaccessPath = $baseUploadDir . '.htaccess';
if (file_exists($htaccessPath)) {
    echo "<p>⚠️ .htaccess file found in upload directory - this might affect access</p>";
    echo "<p>Content preview:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo htmlspecialchars(substr(file_get_contents($htaccessPath), 0, 500));
    echo "</pre>";
} else {
    echo "<p>✅ No .htaccess file in upload directory</p>";
}

echo "<h2>💡 Recommendations</h2>";

if (file_exists($baseUploadDir) && is_writable($baseUploadDir)) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✅ File System Status: Ready</h3>";
    echo "<p>Upload directories exist and are writable. File upload should work!</p>";
    echo "<p><strong>Use this base path in your ProfileController:</strong></p>";
    echo "<code>$baseUploadDir</code>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ File System Status: Issues Found</h3>";
    echo "<p>Some directories are missing or not writable. Here's how to fix:</p>";
    echo "<ol>";
    echo "<li>Create the upload directories manually</li>";
    echo "<li>Set proper permissions (755 or 775)</li>";
    echo "<li>Ensure web server user has write access</li>";
    echo "</ol>";
    
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        echo "<p><strong>Linux/Mac commands:</strong></p>";
        echo "<pre style='background: #f5f5f5; padding: 10px;'>";
        echo "mkdir -p " . $baseUploadDir . "{logos,profiles,documents}\n";
        echo "chmod -R 755 " . $baseUploadDir . "\n";
        echo "# If needed: chown -R www-data:www-data " . $baseUploadDir;
        echo "</pre>";
    }
    echo "</div>";
}

echo "<h2>🧪 Next Steps</h2>";
echo "<p>After fixing any directory issues:</p>";
echo "<ol>";
echo "<li>Update your ProfileController to use the correct upload path</li>";
echo "<li>Test file upload through the profile edit form</li>";
echo "<li>Check that files appear in the upload directories</li>";
echo "<li>Verify files are accessible via web browser</li>";
echo "</ol>";

echo "<p><a href='../integration/ProfileSystemTest.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 Run Profile Test Again</a></p>";
?>