<?php
//
// TwigPlugin bootstrap file
//

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

require __DIR__ . '/../Vendor/autoload.php';

Configure::write('Exception.renderer', 'TwigPlugin\\Exception\\ExceptionRenderer');

// Override in app/Config/bootstrap.php if needed.
if (!defined('TWIG_CACHE_PATH')) {
	define( 'TWIG_CACHE_PATH', TMP . 'twig' . DS .  'cache' );
}

// Configure Defaults extensions
Configure::write('twig.extensions', array_merge(
    (array) Configure::read('twig.extensions'),
    array(
        'TwigPlugin\Extension\BasicExtension',
        'TwigPlugin\Extension\HtmlExtension',
    )
));
