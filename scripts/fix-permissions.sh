#!/bin/bash

# Get the directory of the script
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
WEB_ROOT="$SCRIPT_DIR/../public_html"
DATA_DIR="$WEB_ROOT/data"

# Create data directory if it doesn't exist
mkdir -p "$DATA_DIR"

# Set directory permissions
find "$WEB_ROOT" -type d -exec chmod 755 {} \;

# Set file permissions
find "$WEB_ROOT" -type f -exec chmod 644 {} \;

# Make data directory writable by web server
chmod 775 "$DATA_DIR"

# Set specific permissions for sensitive directories
chmod 755 "$WEB_ROOT/lib"
chmod 755 "$WEB_ROOT/app"

# Make script files executable
chmod 755 "$SCRIPT_DIR"/*.sh
chmod 755 "$WEB_ROOT/scripts"/*.php

echo "Permissions have been set correctly" 