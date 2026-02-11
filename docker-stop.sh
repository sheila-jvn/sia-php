#!/bin/bash
# Stop Docker environment for sia-php

set -e

echo "Stopping sia-php Docker environment..."

# Stop Docker environment
docker-compose down

echo ""
echo "✓ Docker environment stopped"
