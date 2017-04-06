2pit.io
=======

Introduction
------------
This is the minimal 2pit application. It is based on a standard ZF2 installation. It is packaged with the core module (\vendor\PpitCore) and the default security manager (\vendor\PpitUser), based on a login with a password input in a form.

Please note that only the 2pit application is under GPL license. Each file under GPL mentions it explicitly. The ZF2 application on which is packaged 2pit has it own license which is not affected by 2pit.

Installation
------------

Clone the repository:

    cd my/project/dir
    git clone git://github.com/2pit-io/2pit.io
    create a database on Mysql (le’t say 2pit_io)

    Adapt this line in config/autoload/global.php according to your mysql settings 
and the name you gave to the database:
    'dsn' => 'mysql:dbname=2pit_io;host=localhost:3306',

    Adapt those lines in config/autoload/local.php according to your mysql user and password:
    return array(
        'db' => array(
            'username' => 'root',
            'password' => 'root',
        ),
    );


Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName 2pit.io.localhost
        DocumentRoot /path/to/2pit.io/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/2pit.io/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>
