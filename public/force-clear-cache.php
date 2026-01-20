<?php
/**
 * FORCE CLEAR ALL CACHES
 *
 * This script aggressively clears all Laravel caches
 * Access: https://temanbicara.id/force-clear-cache.php
 *
 * DELETE THIS FILE AFTER USE!
 */

// Change to project root
chdir('/home/u162866096/teman-bicara');

echo "<!DOCTYPE html>";
echo "<html><head><title>Force Clear Cache</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .container{background:white;padding:30px;border-radius:10px;max-width:800px;margin:0 auto;} h1{color:#667eea;} .success{color:green;padding:10px;background:#e8f5e9;border-radius:5px;margin:10px 0;} .error{color:red;padding:10px;background:#ffebee;border-radius:5px;margin:10px 0;} .command{background:#263238;color:#aed581;padding:15px;border-radius:5px;font-family:monospace;margin:10px 0;white-space:pre-wrap;}</style>";
echo "</head><body><div class='container'>";
echo "<h1>🧹 Force Clear All Caches</h1>";

$commands = [
    'View Cache' => 'php artisan view:clear',
    'Config Cache' => 'php artisan config:clear',
    'Route Cache' => 'php artisan route:clear',
    'Application Cache' => 'php artisan cache:clear',
    'Optimize Clear' => 'php artisan optimize:clear',
];

$allSuccess = true;

foreach ($commands as $name => $command) {
    echo "<h3>$name</h3>";
    echo "<div class='command'>$ $command</div>";

    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);

    if ($return_var === 0) {
        echo "<div class='success'>✅ Success: " . implode("\n", $output) . "</div>";
    } else {
        echo "<div class='error'>❌ Failed: " . implode("\n", $output) . "</div>";
        $allSuccess = false;
    }
}

if ($allSuccess) {
    echo "<div style='background:#667eea;color:white;padding:20px;border-radius:5px;margin-top:20px;text-align:center;'>";
    echo "<h2 style='color:white;margin:0;'>✅ All Caches Cleared Successfully!</h2>";
    echo "<p style='margin:10px 0 0 0;'>Please refresh your browser (Ctrl+Shift+R or Cmd+Shift+R) to see changes.</p>";
    echo "</div>";
} else {
    echo "<div style='background:#f44336;color:white;padding:20px;border-radius:5px;margin-top:20px;text-align:center;'>";
    echo "<h2 style='color:white;margin:0;'>⚠️ Some Commands Failed</h2>";
    echo "<p style='margin:10px 0 0 0;'>Check error messages above</p>";
    echo "</div>";
}

echo "<div style='margin-top:30px;padding:15px;background:#fff3cd;border-radius:5px;'>";
echo "<h3 style='margin-top:0;'>🔒 Security Warning</h3>";
echo "<p>This file allows anyone to clear your application cache. <strong>DELETE this file after use!</strong></p>";
echo "<p>To delete: Go to cPanel File Manager → public_html → Delete force-clear-cache.php</p>";
echo "</div>";

echo "<div style='margin-top:20px;padding:15px;background:#e3f2fd;border-radius:5px;'>";
echo "<h3 style='margin-top:0;'>📋 Next Steps</h3>";
echo "<ol>";
echo "<li>Refresh your browser with <code>Ctrl+Shift+R</code> (Windows) or <code>Cmd+Shift+R</code> (Mac)</li>";
echo "<li>Visit home page: <a href='https://temanbicara.id'>https://temanbicara.id</a></li>";
echo "<li>Check if 'Admin Panel' menu appears after 'Contact'</li>";
echo "<li><strong>DELETE this force-clear-cache.php file!</strong></li>";
echo "</ol>";
echo "</div>";

echo "</div></body></html>";
?>
