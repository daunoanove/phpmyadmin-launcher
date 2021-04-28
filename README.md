# PhpMyAdmin Launcher

With PhpMyAdmin Launcher you can quickly access your databases via Single Sign-On.
It's a tool only for development environments because there is no authentication system, only a check on the ip address.
Tested on Linux environment.


## Prerequisites
- Git
- Composer
- Node.js
- Yarn


## Installation
```sh
git clone https://github.com/malvik-lab/phpmyadmin-launcher.git
cd phpmyadmin-launcher
bash install.sh
```


## Configuration
Edit config/app.php
- By default you can access the application only from localhost but if you need you can disable the check or add more ip addresses.
- Add as many instances as needed.
```php
<?php

return [
    'useRestrictIpAddress' => true,
    'allowedIpAddresses' => [
        '::1',
        '127.0.0.1'
    ],
    'instances' => [
        [
            'host' => 'host',
            'port' => 3306,
            'user' => 'user',
            'password' => 'password'
        ],
    ]
];
```


## Launch
```sh
php -S localhost:8010 -t public
```
