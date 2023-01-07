#!/bin/bash

BRANCH=${1:-$(git branch --show-current)}

deployStart() {
    set -e
    echo "Deployment started ... ..."
    (php artisan down) || true
    export NODE_OPTIONS=--max-old-space-size=8192
}

deployGit() {
    git fetch --all
    git fetch --tags
    git checkout $BRANCH
    git reset --hard
    git pull origin $BRANCH
}

deployComposer() {
    rm composer.lock
    composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
    composer update --no-dev --no-interaction --prefer-dist --optimize-autoloader
}

deployArtisanCommands() {
    php artisan clear-compiled
    php artisan config:cache
    php artisan optimize
}

deployMigrationsAndSeeds() {
    php artisan migrate --force
    php artisan db:seed
}

deployFrontEnd() {
    rm package-lock.json
    npm install
    npm update
    npm run clean
    npm run lint
    npm run optimize
    npm run build
}

deployFinish() {
    php artisan up
    echo "Deployment finished Successfully!"
}

deploy() {
    deployStart
    deployGit
    deployComposer
    deployArtisanCommands
    deployMigrationsAndSeeds
    deployFrontEnd
    deployFinish
}

deploy
