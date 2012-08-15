Installing the plugin
==================================================

This plugin supports [Composer](https://github.com/composer/composer) and [Packagist](http://packagist.org/). [Download composer.phar](http://packagist.org/) and put it in your path.

Download the plugin archive or clone the repository, and place the files in
`app/Plugin/Twig` folder. Install the vendor files from the command line:

```shell
cd path/to/app/Plugin/
git clone https://github.com/paulredmond/cakephp-twig.git Twig
cd ./Twig
php composer.phar install
```

Boostrap the plugin in ```app/Config/bootstrap.php```:

```php
<?php

CakePlugin::load('Twig', array('bootstrap' => true));
```
**Note:** _You must bootstrap this plugin._

Configure the application ```Controller::$view``` property:

```php
<?php

// Preferably in AppController.php - Application-wide Twig views.

public $viewClass = 'Twig.Twig';
```

Twig caches templates, therefore, you need to add this folder and give apache write permissions for this path:

```bash
cd path/to/app
mkdir -p tmp/twig/cache
```

Additionally, you can configure the path in ```app/Config/core.php``` if you'd like:

```php
<?php

if (!defined('TWIG_CACHE_PATH')) {
    define('TWIG_CACHE_PATH', '/path/to/twig/cache');
}
```