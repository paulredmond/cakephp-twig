# Twig Plugin for CakePHP 2.

*This is still a work in progress*

This Twig plugin replaces the CakePHP2 view renderer with conventions more familiar to Symfony2.
I've taken some liberties, such as >= PHP 5.3.x only, namespaces, and auto-loading.

Instead of relying on 2-pass rendering, Twig plugin relies on the powerful ```extends``` construct of
the Twig templating library.
----------

## Basics
Templates can now extend views and layouts more elegantly using ```extends``` and ```block```:

```
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

## Helpers

The aim of the twig plugin is to provide a more consistent template API. While this plugin relies
on the existing CakePHP helpers (which fit nicely within the CakePHP framework), the plugin
tries to reduce template helper updates when the underlying CakePHP API changes:

```
{{ link_to('Link Text', '/', {'class': 'my-link'}) }}
```

*This is a stub, provide link to full template API*