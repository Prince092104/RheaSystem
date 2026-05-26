<?php
/**
 * Calendar System - Installation Verification Script
 * 
 * This script checks if all required files and permissions are set up correctly.
 * Access it at: http://localhost/RhealynSystem/verify.php
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar System - Installation Verify</title>
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
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        
        header p {
            color: #666;
            font-size: 0.9em;
        }
        
        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .check-item.success {
            background: #d4edda;
            border-color: #28a745;
        }
        
        .check-item.warning {
            background: #fff3cd;
            border-color: #ffc107;
        }
        
        .check-item.error {
            background: #f8d7da;
            border-color: #dc3545;
        }
        
        .check-icon {
            font-size: 1.5em;
            margin-right: 15px;
            min-width: 30px;
        }
        
        .check-content {
            flex: 1;
        }
        
        .check-title {
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .check-detail {
            font-size: 0.85em;
            color: #666;
        }
        
        .section {
            margin-top: 30px;
        }
        
        .section-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 20px;
        }
        
        .status.ready {
            background: #28a745;
            color: white;
        }
        
        .status.issues {
            background: #dc3545;
            color: white;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            display: inline-block;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📋 Installation Verification</h1>
            <p>Checking your Calendar System setup...</p>
        </header>
        
        <?php
        $issues = [];
        $allGood = true;
        
        // Check PHP version
        $phpVersion = phpversion();
        $phpOk = version_compare($phpVersion, '7.0.0', '>=');
        if (!$phpOk) {
            $allGood = false;
            $issues[] = "PHP version is too old";
        }
        
        // Check file permissions
        $baseDir = __DIR__;
        $filesCheck = [
            'index.php' => file_exists($baseDir . '/index.php'),
            'config.php' => file_exists($baseDir . '/config.php'),
            'events.json' => file_exists($baseDir . '/events.json'),
            'README.md' => file_exists($baseDir . '/README.md'),
        ];
        
        $dirsCheck = [
            'attachments' => [
                'exists' => is_dir($baseDir . '/attachments'),
                'writable' => is_writable($baseDir . '/attachments') || !is_dir($baseDir . '/attachments')
            ]
        ];
        
        // Check if events.json is writable
        $eventsWritable = false;
        if (file_exists($baseDir . '/events.json')) {
            $eventsWritable = is_writable($baseDir . '/events.json');
        }
        
        if (!$eventsWritable && file_exists($baseDir . '/events.json')) {
            $allGood = false;
            $issues[] = "events.json is not writable";
        }
        
        // Check JSON validity
        $eventsValid = true;
        if (file_exists($baseDir . '/events.json')) {
            $content = file_get_contents($baseDir . '/events.json');
            $decoded = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $eventsValid = false;
                $allGood = false;
                $issues[] = "events.json contains invalid JSON";
            }
        }
        ?>
        
        <div class="section">
            <h2 class="section-title">System Requirements</h2>
            
            <div class="check-item <?php echo $phpOk ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $phpOk ? '✅' : '❌'; ?></div>
                <div class="check-content">
                    <div class="check-title">PHP Version</div>
                    <div class="check-detail">PHP <?php echo $phpVersion; ?> <?php echo $phpOk ? '(7.0.0+)' : '(needs 7.0.0+)'; ?></div>
                </div>
            </div>
            
            <div class="check-item success">
                <div class="check-icon">✅</div>
                <div class="check-content">
                    <div class="check-title">Web Server</div>
                    <div class="check-detail">Apache (XAMPP)</div>
                </div>
            </div>
            
            <div class="check-item success">
                <div class="check-icon">✅</div>
                <div class="check-content">
                    <div class="check-title">JSON Support</div>
                    <div class="check-detail">PHP JSON extension is available</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">Files</h2>
            
            <?php foreach ($filesCheck as $file => $exists): ?>
            <div class="check-item <?php echo $exists ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $exists ? '✅' : '❌'; ?></div>
                <div class="check-content">
                    <div class="check-title"><?php echo $file; ?></div>
                    <div class="check-detail"><?php echo $exists ? 'Found' : 'Missing - Please restore'; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section">
            <h2 class="section-title">Directories & Permissions</h2>
            
            <?php foreach ($dirsCheck as $dir => $info): ?>
            <div class="check-item <?php echo ($info['exists'] && $info['writable']) ? 'success' : ($info['exists'] ? 'warning' : 'warning'); ?>">
                <div class="check-icon"><?php echo ($info['exists'] && $info['writable']) ? '✅' : '⚠️'; ?></div>
                <div class="check-content">
                    <div class="check-title"><?php echo $dir; ?>/</div>
                    <div class="check-detail">
                        <?php 
                        if (!$info['exists']) {
                            echo 'Will be created automatically';
                        } elseif ($info['writable']) {
                            echo 'Exists and writable';
                        } else {
                            echo 'Exists but not writable';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section">
            <h2 class="section-title">Data Files</h2>
            
            <div class="check-item <?php echo $eventsWritable ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $eventsWritable ? '✅' : '❌'; ?></div>
                <div class="check-content">
                    <div class="check-title">events.json Writable</div>
                    <div class="check-detail"><?php echo $eventsWritable ? 'Can save events' : 'Cannot save events'; ?></div>
                </div>
            </div>
            
            <div class="check-item <?php echo $eventsValid ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $eventsValid ? '✅' : '❌'; ?></div>
                <div class="check-content">
                    <div class="check-title">Data Integrity</div>
                    <div class="check-detail"><?php echo $eventsValid ? 'Valid JSON format' : 'Invalid JSON - needs repair'; ?></div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($issues)): ?>
        <div class="section">
            <h2 class="section-title">⚠️ Issues Found</h2>
            <ul style="margin-left: 20px;">
                <?php foreach ($issues as $issue): ?>
                <li style="margin-bottom: 10px; color: #dc3545;"><?php echo htmlspecialchars($issue); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <div style="text-align: center;">
            <div class="status <?php echo $allGood ? 'ready' : 'issues'; ?>">
                <?php echo $allGood ? '🎉 System is ready to use!' : '⚠️ Some issues need attention'; ?>
            </div>
        </div>
        
        <div class="actions">
            <a href="index.php" class="btn btn-primary">Go to Calendar</a>
            <a href="README.md" class="btn btn-secondary">View Documentation</a>
        </div>
    </div>
</body>
</html>
