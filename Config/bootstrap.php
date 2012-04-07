<?php
//
// TwigPlugin bootstrap file
//

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

require __DIR__ . '/../Vendor/.composer/autoload.php';

// Override in app/Config/bootstrap.php if needed.
if (!defined('TWIG_CACHE_PATH')) {
	define( 'TWIG_CACHE_PATH', TMP . 'twig' . DS .  'cache' );
}

// Configure Defaults extensions
Configure::write('twig.extensions', array(
    'TwigPlugin\Extension\BasicExtension'
));