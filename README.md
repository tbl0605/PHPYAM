# PHPYAM

Really simple, yet easily extendable, MVC framework for PHP developers.

Based on Mini, rewritten to add following features:
- URL forwarding support
- ajax requests support
- protection against multiple form submissions
- error logging using log4php
- define the client's charset encoding
- htaccess support
- session support
- output buffering support
- user authentication support
- internationalization of the PHPYAM error messages
- customization of PHPYAM's settings storage (since v2.0)

PHPYam doesn't do yet:
- form validation

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHPYAM:

```bash
composer require tbl0605/phpyam:~2.0
```

## Easy way to test the provided demo

#### Use php built-in web server

```bash
cd demo
php -S localhost:8000 index.php
```

And open [localhost:8000](http://localhost:8000).

#### Use Apache

Add the `demo/` path to the `httpd.conf` file or to some dedicated virtual host configuration file:

```
<Directory "/path/to/your/PHPYAM/demo">
	AllowOverride All
	Options FollowSymLinks
	RewriteEngine On
</Directory>
```

#### Additional notes

For further testings, you can play with constants `YAM_DEMO_USE_AUTOLOADER` and `YAM_DEMO_APP_DIR`
inside the `demo/server-conf.php` file:

```
define('YAM_DEMO_USE_AUTOLOADER', true);
define('YAM_DEMO_APP_DIR', 'application-autoloader');
```

or

```
define('YAM_DEMO_USE_AUTOLOADER', false);
define('YAM_DEMO_APP_DIR', 'application-noautoloader');
```
