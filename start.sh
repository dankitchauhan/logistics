#!/bin/bash
if [ "$(uname)" == "Darwin" ]; then

    echo "Mac Detected" # Do something under Mac OS X platform    

    =""    

elif [ "$(expr substr $(uname -s) 1 5)" == "Linux" ]; then

    echo "Linux detected" # Do something under GNU/Linux platform

    CMD_PREFIX=""

elif [ "$(expr substr $(uname -s) 1 10)" == "MINGW32_NT" ]; then

    echo "Windows 32 Bits detected" # Do something under 32 bits Windows NT platform

    CMD_PREFIX='winpty '

elif [ "$(expr substr $(uname -s) 1 10)" == "MINGW64_NT" ]; then

    echo "Windows 64 Bits detected" # Do something under 64 bits Windows NT platform

    CMD_PREFIX='winpty '

fi

echo "${CMD_PREFIX}should be added"
echo "Moving to laradock"
cd laradock
echo "Destroying all container"
docker-compose down
echo "Starting all container"
docker-compose up -d nginx mysql workspace
echo "Retrying all container"
docker-compose up -d nginx mysql workspace
echo "Building php-fmp container"
docker-compose build php-fpm
echo "Retrying all container"
docker-compose up -d nginx mysql workspace
echo "Installing dependency"
$CMD_PREFIX docker-compose exec workspace composer install
echo "Generating Key"
$CMD_PREFIX docker-compose exec workspace php artisan key:generate
echo "Running Migration"
$CMD_PREFIX docker-compose exec workspace php artisan migrate
echo "Running Swagger"
$CMD_PREFIX docker-compose exec workspace php artisan l5-swagger:generate
echo "Clearing config"
$CMD_PREFIX docker-compose exec workspace php artisan config:clear
echo "Running integation test cases"
$CMD_PREFIX docker-compose exec workspace ./vendor/bin/phpunit --testsuite Feature
echo "Running unit test cases"
$CMD_PREFIX docker-compose exec workspace ./vendor/bin/phpunit --testsuite Unit
$SHELL