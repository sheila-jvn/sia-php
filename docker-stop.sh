#!/usr/bin/env fish
# Stop Docker environment for sia-php

echo "Stopping sia-php Docker environment..."

# Stop Docker environment
docker compose down

echo ""
echo "✓ Docker environment stopped"
