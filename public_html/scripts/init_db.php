<?php
declare(strict_types=1);

require_once __DIR__ . '/../lib/Database.php';

try {
    $db = Database::getInstance();
    
    // Read and execute the schema
    $schema = file_get_contents(__DIR__ . '/../../schema.sqlite.sql');
    if ($schema === false) {
        throw new RuntimeException('Could not read schema file');
    }
    
    // Split the schema into individual statements
    $statements = array_filter(
        array_map(
            'trim',
            explode(';', $schema)
        )
    );
    
    // Execute each statement
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }
    
    echo "Database initialized successfully\n";
    
} catch (Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
} 