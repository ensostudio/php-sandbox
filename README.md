## PHP Sandbox

[![Latest Stable Version](https://img.shields.io/packagist/v/ensostudio/php-sandbox.svg)](https://packagist.org/packages/ensostudio/php-sandbox)
[![Total Downloads](https://img.shields.io/packagist/dt/ensostudio/php-sandbox.svg)](https://packagist.org/packages/ensostudio/php-sandbox)

The sandbox to test your PHP code in Web browser.

- Extended autocomplete for constants, functions and classes/interfaces
- Helper functions to dump data and reflect functions/classes in readeable format
- Format code in [PSR-12](https://www.php-fig.org/psr/psr-12/)

![screenshot](https://user-images.githubusercontent.com/3521094/186710160-e55c5fc6-3a9b-40ac-a952-d421c95de992.png)

REQUIREMENTS
------------

The minimum requirement by this project that your server supports PHP 7.4 with JSON extension.

The sandbox apllication base on [Slim framework](https://www.slimframework.com) and requires:
- [Symfony PHP polyfills](https://github.com/symfony/polyfill) to write modern code
- [Kint](https://kint-php.github.io/kint/) to dump variables
- [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) to format code style

The GUI bases on [Bootstrap 5](https://getbootstrap.com/docs/5.1/) and [Ace editor](https://ace.c9.io).

INSTALLATION
------------

If you do not have Composer, you may install it by following the instructions at
[getcomposer.org](https://getcomposer.org/doc/00-intro.md#introduction).

You can then install this project template using the following command:

```shell
composer create-project ensostudio/php-sandbox sandbox
```

Now you should be able to access the application through the following URL, assuming `sandbox` is the directory
directly under the Web root and start build-in HTTP server:

```shell
composer run start
```

CUSTOMIZATION
-------------

Run [Phing](https://www.phing.info) to re-build public assets:

```shell
composer run build
```

Update [bootstrap.php](src/bootstrap.php) to change Kint options or add extra functions.
