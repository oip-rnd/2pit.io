2pit.io
=======

Introduction
------------
This is the minimal 2pit.io application. It is based on a standard ZF3 installation. It is packaged with the core module (\vendor\PpitCore) and the default security manager (\vendor\PpitUser), providing a classic authentication mode based on login/password in a form, an account management and customizable data structures named events related to accounts.

Please note that only the 2pit application is under GPL license. Each file under GPL mentions it explicitly. The ZF3 application on which is packaged 2pit.io and incorporated free vendor packages have their own license.

Installation
------------

Pull the repository:

	mkdir /path/to/2pit.io/
    cd /path/to/2pit.io/
	git init    
    git remote add origin git://github.com/2pit-io/2pit.io
    git pull origin master
    
Create a database on Mysql (let's say 2pit_io)
Load in mysql the full and all the incremental sql file in database/

	cd database/
	mysql -u'you sql user' -p'you sql password' 2pit_io < 2pit_io-full.sql
	mysql -u'you sql user' -p'you sql password' 2pit_io < 2pit_io-1.0.05.sql
	...
	
In your database you have a table named 'core_instance' with only one row. Change the fqdn value (which is localhost) to reflect the one of your site. The fqdn is for example of the form www.2pit.io.

The config/application.config.php and config/modules.config.php files are ignored by git since they refer to your local modules and files. Create them by copying from the templates:

	cd ../config/
	cp application.config.template.php application.config.php
	cp modules.config.template.php modules.config.php

Ensure that all files are accessible to your web server (Apache on Linux in this example):
    
    chown -R www-data:www-data /path/to/2pit.io/
    chmod -R 700 /path/to/2pit.io/

The config/autoload/local.php file deals with security and so is ignored by git. Create it by copying from the template and protect it (let's say that www-data is the apache group and user names):

	cd autoload/
	cp local.template.php local.php
	chown www-data:www-data local.php
	chmod 500 local.php

Adapt these three lines in config/autoload/global.php according to your own mysql settings:

    'dsn' => 'mysql:dbname=yourdbname;host=localhost',
    'driver_options' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
    ),
    'username' => 'yourmysqlusername',
    'password' => 'yourmysqlpassword',

Web Server Setup
----------------

### Example Apache Setup

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

Ensure that your server is configured to send emails via SMTP.

Adapt this line in config/autoload/local.php, replacing the value with your email between quotes:

    'mailTo' => 'no-reply@2pit.io', // Overrides the real email if not NULL (for test purposes)
    
Specifying here an email has the result that each email sent by 2pit is routed to this address, regardless the real destination email. This parameter should be set to null in production environment in order to send email to real addresses.

Login
-----

At this stage, you should get the 2pit login page when browsing to your website. The default admin account (for development only!) is admin admin.
