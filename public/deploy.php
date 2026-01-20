<?php
/**
 * Auto Deploy Script - Pull Latest Changes from GitHub
 *
 * IMPORTANT SECURITY NOTE:
 * Delete this file after use or add password protection!
 * This script executes git pull and artisan commands.
 *
 * Access: https://temanbicara.id/deploy.php
 */

// Simple password protection (CHANGE THIS!)
$DEPLOY_PASSWORD = 'TemanbIcara2025!Deploy';

// Check password
if (!isset($_GET['key']) || $_GET['key'] !== $DEPLOY_PASSWORD) {
    http_response_code(403);
    die('❌ Unauthorized - Invalid deployment key');
}

// Start output
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Deploy - Teman Bicara</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .output {
            background: #1e1e1e;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
        }
        .success {
            color: #00ff00;
        }
        .error {
            color: #ff4444;
        }
        .warning {
            color: #ffaa00;
        }
        .info {
            color: #00aaff;
        }
        .summary {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .summary h2 {
            color: #4caf50;
            margin-bottom: 15px;
        }
        .summary ul {
            list-style: none;
            padding-left: 0;
        }
        .summary li {
            padding: 8px 0;
            border-bottom: 1px solid #c8e6c9;
        }
        .summary li:last-child {
            border-bottom: none;
        }
        .timestamp {
            text-align: center;
            color: #999;
            padding: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Auto Deploy Script</h1>
            <p>Teman Bicara - Mental Health Platform</p>
        </div>
        <div class="content">
<?php

// Configuration
$PROJECT_PATH = '/home/u162866096/teman-bicara';
$commands = [
    'git_status' => [
        'title' => '📊 Checking Git Status',
        'command' => "cd $PROJECT_PATH && git status",
    ],
    'git_pull' => [
        'title' => '⬇️ Pulling Latest Changes from GitHub',
        'command' => "cd $PROJECT_PATH && git pull origin main 2>&1",
    ],
    'composer_install' => [
        'title' => '📦 Installing/Updating Dependencies',
        'command' => "cd $PROJECT_PATH && composer install --no-dev --optimize-autoloader 2>&1",
    ],
    'clear_cache' => [
        'title' => '🧹 Clearing All Caches',
        'command' => "cd $PROJECT_PATH && php artisan view:clear && php artisan cache:clear && php artisan config:clear && php artisan route:clear 2>&1",
    ],
    'optimize' => [
        'title' => '⚡ Optimizing for Production',
        'command' => "cd $PROJECT_PATH && php artisan config:cache && php artisan route:cache && php artisan view:cache 2>&1",
    ],
    'permissions' => [
        'title' => '🔐 Fixing File Permissions',
        'command' => "cd $PROJECT_PATH && chmod -R 775 storage bootstrap/cache 2>&1",
    ],
];

$results = [];
$hasErrors = false;

// Execute commands
foreach ($commands as $key => $config) {
    echo '<div class="step">';
    echo '<h3>' . htmlspecialchars($config['title']) . '</h3>';

    $output = '';
    $return_var = 0;

    exec($config['command'], $output, $return_var);

    $outputStr = implode("\n", $output);
    $results[$key] = [
        'title' => $config['title'],
        'success' => $return_var === 0,
        'output' => $outputStr,
    ];

    if ($return_var === 0) {
        echo '<div class="output success">✅ Success</div>';
        echo '<div class="output">' . htmlspecialchars($outputStr) . '</div>';
    } else {
        $hasErrors = true;
        echo '<div class="output error">❌ Error (Exit Code: ' . $return_var . ')</div>';
        echo '<div class="output error">' . htmlspecialchars($outputStr) . '</div>';
    }

    echo '</div>';

    // Flush output for real-time display
    if (ob_get_level() > 0) {
        ob_flush();
    }
    flush();
}

// Summary
echo '<div class="summary">';
if ($hasErrors) {
    echo '<h2>⚠️ Deployment Completed with Warnings</h2>';
} else {
    echo '<h2>✅ Deployment Successful!</h2>';
}
echo '<ul>';
foreach ($results as $result) {
    $icon = $result['success'] ? '✅' : '❌';
    echo '<li>' . $icon . ' ' . htmlspecialchars($result['title']) . '</li>';
}
echo '</ul>';
echo '</div>';

// Instructions
echo '<div class="step">';
echo '<h3>📋 Next Steps</h3>';
echo '<div class="output info">';
echo "1. Test your website: https://temanbicara.id\n";
echo "2. Test admin panel: https://temanbicara.id/admin/dashboard\n";
echo "3. Check for any errors in Laravel logs\n";
echo "4. For security, DELETE this deploy.php file after use!\n\n";
echo "<span class='warning'>⚠️ SECURITY WARNING:</span>\n";
echo "This file allows anyone with the key to deploy code.\n";
echo "Delete it or move it outside public folder after deployment!\n";
echo "</div>";
echo '</div>';

?>
        </div>
        <div class="timestamp">
            Deployed at: <?php echo date('Y-m-d H:i:s'); ?> WIB
        </div>
    </div>
</body>
</html>
