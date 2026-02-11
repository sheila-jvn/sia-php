#!/bin/bash
# Start Docker environment for sia-php

set -e

echo "Starting sia-php Docker environment..."

# Create Docker config directory structure if not exists
mkdir -p docker/config/lib

# Start Docker environment
docker-compose up -d

echo ""
echo "✓ Application: http://localhost:8000"
echo "✓ Database: localhost:3306 (root/root)"
echo ""
echo "Note: Database initializes on first run (may take 10-20 seconds)"
echo ""
echo "Use ./docker-logs.sh to view container logs"
