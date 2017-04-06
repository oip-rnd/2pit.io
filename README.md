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

    Adapt this line in config/autoload/global.php according to your own mysql settings:
    'dsn' => 'mysql:dbname=2pit_io;host=localhost:3306',

    Adapt those lines in config/autoload/local.php according to your mysql user and password:
    return array(
        'db' => array(
            'username' => ‘yourusername’,
            'password' => ‘yourpassword’,
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

Receiving emails
----------------

Ensure that your server is configured to send emails via SMTP 
Adapt this line in config/autoload/2pit.global.php, replacing the null value with your email between quotes:
    'mailTo' => null, // Overrides the real email if not NULL (for test purposes)
Specifying here an email has the result that each email sent by 2pit is routed to this address, regardless the real destination email (depending on the context). This parameter should be reset to null in production environment.

Login
-----

At this stage, you should get the 2pit login page when browsing to your website. Click on Lost password and type « admin » as an identifier then Confirm.
You should receive an email with a link. By following it, you get the New password form. Type « Admin » ans twice your new password, then Confirm.

You can now login as admin.
