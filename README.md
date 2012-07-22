# Twig Plugin for CakePHP 2.

*This is still a work in progress*

This Twig plugin replaces the CakePHP2 view renderer with conventions more familiar to Symfony2.
I've taken some liberties, such as PHP 5.3 only, namespaces, and autoloading for the plugin's classes.

The plugin provides a custom set of classes such as ```TemplateNameParser``` and ```TemplateReference``` to parse and reference views.
The ```FileSystemLoader``` class is also helpful in locating view templates.

Instead of relying on 2-pass rendering, Twig plugin relies on the powerful ```extends``` tag of
the Twig templating library.

Lastly, the plugin provides a console to find all .twig templates in an effort to cache file paths 
and reduce filesystem lookups. The goal is to make template rendering faster.

--------------------------------------------------

### Installation

@todo - Provide application vendor installation instructions using composer.

CakePHP 2 plugin supports [Composer](https://github.com/composer/composer) and [Packagist](http://packagist.org/). [Download composer.phar](http://packagist.org/) and put it in your path.

From the ```app/Plugin``` folder run the following:

```bash
git clone https://github.com/paulredmond/cakephp-twig.git Twig
cd ./Twig
php composer.phar install
```

Boostrap the plugin in ```app/Config/bootstrap.php```:

```php
CakePlugin::load('Twig', array('bootstrap' => true));
```
**You must bootstrap this plugin.**

Twig caches templates, therefore, you need to add this folder and give apache write permissions for this path:

```bash
cd path/to/app
mkdir -p tmp/twig/cache
```

Additionally, you can configure the path in ```app/Config/core.php``` if you'd like:

```php
if (!defined('TWIG_CACHE_PATH')) {
    define('TWIG_CACHE_PATH', '/path/to/twig/cache');
}
```

--------------------------------------------------

### Basics
Templates can now extend views and layouts more elegantly using ```extends``` and ```block```:

```php
{% extends 'App:Layouts:default.html.twig' %}

{% block content %}
Hi {{ name }}! This is the content of your view.
{% endblock %}
```

Also use Symfony2-like syntax in controllers:

```php

<?php

// ...Controller method

return $this->render(':Articles:index.html.twig'); // Matches App/View/Articles/index.html.twig
```
--------------------------------------------------

### Helpers

You can still you CakePHP helpers directly, but you have to either a) disable auto-escaping in the configuration,
or b) use Twig's built-in ```|raw``` filter for helpers that produce HTML output:

```
{{ _view.Html.link('test', '/')|raw }}
```
--------------------------------------------------

# Extensions

Info about extensions will go here.

*@todo Information about adding extensions once API has been created for that.*

### HtmlExtension
Wraps HtmlHelper::link() method:

```
{{ link('Link Text', '/', {'class': 'my-link'}) }}
```

Only return a link if not the current url with ```link_unless_current```:

```
{{ link_unless_current('Link Text', '/', {'class': 'my-link'}) }}
```

*This is a stub, provide link to full template API*