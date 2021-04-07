#!/bin/bash

cd phpmyadmin-launcher
composer install
cd public
git clone https://github.com/phpmyadmin/phpmyadmin
cd phpmyadmin
git checkout STABLE
composer install
yarn install --production
cd ../../config
cp app.sample.php app.php