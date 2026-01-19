<?php

// Setup database and run migrations
chdir('c:\Users\melch\Antonio_MidtermsExam\twitter-like-app');

// Load Laravel
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);

// Run migrations
echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);

echo "\nDone!\n";
