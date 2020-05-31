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

PHPYam doesn't do yet:
- form validation

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHPYAM.

```bash
composer require tbl0605/phpyam:~1.0
```

## Easy way to test the provided demo

```bash
cd demo
php -S localhost:8000 index.php
```

And open [localhost:8000](http://localhost:8000).

For further testings, you can also adjust value of property `\PHPYAM\demo\DemoRouter::$useApplicationAutoLoader`
inside the `index.php` file:

```
class DemoRouter extends \PHPYAM\core\Router
{
    public $useApplicationAutoLoader = true;
    ...
}
```
