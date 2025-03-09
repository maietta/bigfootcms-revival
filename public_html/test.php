<?php
declare(strict_types=1);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/lib/Database.php';
require_once __DIR__ . '/lib/Framework.php';

try {
    $framework = new Framework();
    $db = Database::getInstance();

    // Check if table exists and has content
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='commnetivity_content'")->fetchAll(PDO::FETCH_COLUMN);
    
    // Get all content for debugging
    $allContent = $db->query("SELECT * FROM commnetivity_content")->fetchAll(PDO::FETCH_OBJ);
    
    // Try to get specific content
    $content = $framework->getContent('/index.html');

    // Output as JSON for testing
    header('Content-Type: application/json');
    echo json_encode([
        'content' => $content,
        'database_working' => $content !== null,
        'php_version' => PHP_VERSION,
        'sqlite_version' => $db->query('SELECT sqlite_version()')->fetchColumn(),
        'debug' => [
            'tables_exist' => $tables,
            'all_content' => $allContent,
            'data_dir' => realpath(__DIR__ . '/../data'),
            'db_file_exists' => file_exists(__DIR__ . '/../data/bigfootcms.db'),
            'db_file_permissions' => file_exists(__DIR__ . '/../data/bigfootcms.db') ? 
                substr(sprintf('%o', fileperms(__DIR__ . '/../data/bigfootcms.db')), -4) : null
        ]
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
} 