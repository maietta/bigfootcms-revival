#!/bin/bash

# Get the directory of the script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$SCRIPT_DIR/.."

# Initialize the database if it doesn't exist
if [ ! -f "$PROJECT_ROOT/data/bigfootcms.db" ]; then
    echo "Initializing database..."
    php "$SCRIPT_DIR/init-db.php"
fi

# Start PHP development server with router script
echo "Starting PHP development server..."
cd "$PROJECT_ROOT/public_html" && php -S localhost:8080 index.php 