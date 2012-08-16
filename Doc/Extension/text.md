TextExtension
=============================================

This extension wraps method found in the [TextHelper](http://api.cakephp.org/class/text-helper) class.

## Filters

#### truncate

Truncate text.

```jinja
{{ 'The quick brown fox jumps over the lazy dog'|truncate(20, {'exact': false, 'html': true}) }}
```

```jinja
{{ myVar|truncate(100) }}
```

See the [TextHelper::truncate()](http://api.cakephp.org/class/text-helper#method-TextHelpertruncate) method for
arguments.

--------------------------------------------------

#### highlight

Highlights the needle text using formatting options.

```jinja
{{ 'Hello World!'|highlight('Hello', {'format': '<span class="highlight">\1</span>') }}
```

See the [TextHelper::highlight()](http://api.cakephp.org/class/text-helper#method-TextHelperhighlight) method for
arguments.

#### autoLink

Convert all links and email addresses to HTML links.

```jinja
{{ 'Contact info@mysite.com for details, or visit http://mysite.com.'|autoLink }}
```

Will output:
```html
Contact <a href="mailto:info@mysite.com">info@mysite.com</a> for details, or visit <a href="http://mysite
.com">http://mysite.com</a>.
```

--------------------------------------------------

#### autoLinkUrls

Convert all links to HTML links.

```jinja
{{ 'Visit http://mysite.com.'|autoLinkUrls }}
```

--------------------------------------------------

#### autoLinkEmails

Convert all emails to HTML links.

```jinja
{{ 'Send an email to info@mysite.com.'|autoLinkEmails }}
```

--------------------------------------------------

#### stripLinks

Strip HTML links from text.

```jinja
{{ set text = "Contact <a href="mailto:info@mysite.com">info@mysite.com</a> for details." }}
{{ text|stripLinks }}
```

--------------------------------------------------

Go to the Twig plugin [extension index](index.md) or [docs home](../index.md).