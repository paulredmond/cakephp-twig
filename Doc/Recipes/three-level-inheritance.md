## Three Level Inheritance

Three-level interitance is a good way to provide a common template that other layouts can extend from. It consists of:

 * base.html.twig
 * default.html.twig
 * Individual views for each page.


#### base.html.twig

```jinja
{# app/View/Layouts/base.html.twig #}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{% block title}My App{% endblock %}</title>
    {% block stylesheets %}
        {{ css('styles') }}
    {% endblock %}
    {% block javascripts %}
        {{ script('jquery') }}
    {% endblock %}
</head>
<body>
    {% block body %}{% endblock %}
    {% block footerScripts %}
        {# ie. Google analytics code. #}
    {% endblock %}
</body>
</html>
```

### default.html.twig

Layout-specific code can go here to reduce duplication in the individual views.

```jinja
{# app/View/Layouts/default.html.twig #}
{% extends ':Layouts:base.html.twig' %}

{% block body %}
    <div class="content">
    {% block content %}{% endblock %}
    </div>

    <div class="sidebar">
    {% block sidebar %}
        <p>This is the default sidebar</p>
    {% endblock %}
    </div>
{% endblock %}
```


### article.html.twig

This is simply an example, but an `articles.html.twig` layout with a `stylesheets` block might
be better than the example below if you have multiple views that require the same styles.

The example below shows you how to decorate the parent view by appending styles.

```jinja
{# app/View/Articles/index.html.twig #}
{% extends ':Layouts:default.html.twig' %}
{% block stylesheets %}
    {{ parent() }}
    {{ css('articles') }}
{% endblock %}
{% block content %}
    <p>Hello {{ name }}!</p>
{% endblock %}
```