BasicExtension API
==================================================

This extension wraps useful functions defined in (basics.php)[http://api.cakephp.org/file/Cake/basics.php].

## Functions

#### env

Gets an environment variable from available sources, and provides emulation
for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
environment information.

```
{{ env('HTTP_HOST') }}
```

## Filters

#### debug

Prints out debug information about given variable.

```
{{ myVar|debug }}
```

#### pr

A convenience filter for PHP's `print_r` function.

```
{{ myVar|pr }}
```

