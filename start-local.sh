#!/usr/bin/env bash

docker compose up -d
docker compose exec -w /var/www/md-server backend composer install -q

echo "Your server is running at http://localhost:3000/"
