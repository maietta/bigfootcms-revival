<?php
declare(strict_types=1);

require_once __DIR__ . '/../public_html/lib/Database.php';

try {
    // Create data directory if it doesn't exist
    $dataDir = __DIR__ . '/../data';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }

    $db = Database::getInstance();
    
    // Drop existing tables
    $tables = [
        'commnetivity_components',
        'commnetivity_content',
        'commnetivity_content_hist',
        'commnetivity_dynamics',
        'commnetivity_media',
        'commnetivity_mimetypes',
        'commnetivity_navigation',
        'commnetivity_overrides',
        'commnetivity_presentation',
        'commnetivity_redirects'
    ];
    
    foreach ($tables as $table) {
        $db->exec("DROP TABLE IF EXISTS $table");
    }
    
    // Read schema
    $schema = file_get_contents(__DIR__ . '/../schema.sqlite.sql');
    if ($schema === false) {
        throw new RuntimeException('Could not read schema file');
    }
    
    // Split into statements, handling triggers specially
    $statements = [];
    $currentStatement = '';
    $inTrigger = false;
    
    foreach (explode("\n", $schema) as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }
        
        // Check if we're starting a trigger
        if (strpos($line, 'CREATE TRIGGER') === 0) {
            $inTrigger = true;
        }
        
        $currentStatement .= ' ' . $line;
        
        // If we're in a trigger, look for END;
        if ($inTrigger && strpos($line, 'END;') === 0) {
            $statements[] = trim($currentStatement);
            $currentStatement = '';
            $inTrigger = false;
            continue;
        }
        
        // For non-trigger statements, look for semicolon
        if (!$inTrigger && substr($line, -1) === ';') {
            $statements[] = trim($currentStatement);
            $currentStatement = '';
        }
    }
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $db->exec($statement);
            } catch (Exception $e) {
                echo "Error executing statement: " . $statement . "\n";
                throw $e;
            }
        }
    }
    
    // Sample content structure
    $content = [
        [
            'virtual_path' => '/index.html',
            'page_title' => 'Welcome to BigfootCMS',
            'nav_title' => 'Home',
            'content' => '<h1>Welcome to BigfootCMS</h1>
                         <p>Your site is ready! This is a modern revival of the classic CMS.</p>
                         <h2>Features</h2>
                         <ul>
                             <li>Simple and fast</li>
                             <li>SQLite database</li>
                             <li>PHP 8.3 compatible</li>
                             <li>Modern architecture</li>
                         </ul>'
        ],
        [
            'virtual_path' => '/about/index.html',
            'page_title' => 'About Us',
            'nav_title' => 'About',
            'content' => '<h1>About BigfootCMS</h1>
                         <p>A lightweight CMS focused on simplicity and speed.</p>'
        ],
        [
            'virtual_path' => '/contact/index.html',
            'page_title' => 'Contact Us',
            'nav_title' => 'Contact',
            'content' => '<h1>Contact Us</h1>
                         <p>Get in touch with us for any questions or feedback.</p>'
        ]
    ];
    
    // Insert content
    $contentStmt = $db->prepare(
        "INSERT INTO commnetivity_content 
         (virtual_path, page_title, nav_title, encoded_content, weight) 
         VALUES (?, ?, ?, ?, ?)"
    );
    
    // Insert navigation
    $navStmt = $db->prepare(
        "INSERT INTO commnetivity_navigation 
         (virtual_path, weight, position) 
         VALUES (?, ?, ?)"
    );
    
    foreach ($content as $index => $page) {
        // Insert content
        $contentStmt->execute([
            $page['virtual_path'],
            $page['page_title'],
            $page['nav_title'],
            base64_encode($page['content']),
            $index * 10
        ]);
        
        // Add to navigation
        $navStmt->execute([
            $page['virtual_path'],
            $index * 10,
            'top'  // Add to top navigation
        ]);
    }
    
    echo "Database initialized successfully with sample content\n";
    
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
} 