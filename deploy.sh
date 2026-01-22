#!/bin/bash
set -e

echo "Starting Deployment Script..."

# Set PHP Path as requested
PHP_BIN="/usr/php82/usr/bin/php"

# Verify PHP exists
if [ ! -x "$PHP_BIN" ]; then
    echo "PHP binary not found at $PHP_BIN"
    exit 1
fi

echo "Using PHP Binary: $PHP_BIN"

# Navigate to script directory (project root)
cd "$(dirname "$0")"
echo "Current Directory: $(pwd)"

# Create Backup Script
echo "Creating backup script..."
cat <<'EOF' > deploy_backup.php
<?php
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    try {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        $config = config('database.connections.mysql');
        // Fallback to env if config is null (rare)
        if (!$config) {
            echo "Could not load database config.\n";
            exit(1);
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $user = $config['username'];
        $pass = $config['password'];
        $db = $config['database'];
        
        $dir = __DIR__ . '/storage/backups';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $fileName = 'backup_' . date('Ymd_His') . '.sql';
        $filePath = $dir . '/' . $fileName;
        
        echo "Backing up database '$db' to '$filePath'...\n";
        
        // Construct command
        // --no-tablespaces requires PROCESS privilege, usually better to include it if possible, 
        // but if it fails, we assume standard user access. 
        // Using --no-tablespaces is safer for shared hosting.
        $cmd = "mysqldump -h '{$host}' -P '{$port}' -u '{$user}' -p'{$pass}' --no-tablespaces '{$db}' > '{$filePath}' 2>&1";
        
        exec($cmd, $output, $return);
        
        if ($return === 0) {
            echo "✅ Backup successful!\n";
        } else {
            echo "❌ Backup failed (Return Code: $return).\n";
            echo "Command Output:\n";
            print_r($output);
            
            // Fallback: Try without single quotes for host/port (sometimes shell parsing issues)
            echo "Retrying locally...\n";
            $cmd2 = "mysqldump -u '{$user}' -p'{$pass}' --no-tablespaces '{$db}' > '{$filePath}' 2>&1";
            exec($cmd2, $output2, $return2);
            if ($return2 === 0) {
                echo "✅ Retry successful!\n";
            } else {
                 echo "❌ Retry failed too.\n";
                 print_r($output2);
            }
        }
        
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Vendor autoload not found. Skipping backup.\n";
}
EOF

# Execute Backup
$PHP_BIN deploy_backup.php
rm -f deploy_backup.php

# Run Artisan commands
echo "Running Artisan commands..."
$PHP_BIN artisan migrate --force
$PHP_BIN artisan optimize:clear
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache
$PHP_BIN artisan storage:link
echo "Deployment Complete."
