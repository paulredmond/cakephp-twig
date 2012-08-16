HtmlExtension
=============================================

This extension wraps method found in the [HtmlHelper](http://api.cakephp.org/class/html-helper) class.

## Functions

#### link

Create an HTML link. Arguments are the same as the [HtmlHelper::link()](http://api.cakephp
.org/class/html-helper#method-HtmlHelperlink) method.

```jinja
{{ link('Title', {'controller': 'articles', 'action': 'index'}, {'class': 'article'}, false) }}
```

--------------------------------------------------

#### link_unless_current

Same options as `link`, but will only return the title if the current page matches the link.

```jinja
{{ link_unless_current('Title', '/link', {}) }}
```

--------------------------------------------------

#### url

Find a url for a specified action. The `url` function has the same arguments as [Helper::url](http://api.cakephp
.org/class/helper#method-Helperurl). Pass `true` as the second argument for a full base url.

```jinja
{{ url({'controller': 'articles, 'action': 'index'}, true) }}
```

--------------------------------------------------

#### css

Creates a link element for CSS stylesheets. Arguments are the same as [HtmlHelper::css()](http://api.cakephp
.org/class/html-helper#method-HtmlHelpercss) method.

```jinja
{{ css('mystyle') }}
{# Path will be /css/mystyle.css #}
```

--------------------------------------------------

#### script

Returns script tag (or many) depending on the number of scripts given. Arguments are the same as the
[HtmlHelper::script()](http://api.cakephp.org/class/html-helper#method-HtmlHelperscript) method.

```jinja
{{ script('jquery') }}
{# <script src="/js/jquery.js"></script> #}
```

As an array:
```jinja
{{ script(['jquery', 'jquery-ui']) }}
```

--------------------------------------------------

Go to the Twig plugin [extension index](index.md) or [docs home](../index.md).