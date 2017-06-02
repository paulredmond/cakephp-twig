<?php
//
// TwigPlugin bootstrap file
//

if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

Configure::write('Exception.renderer', 'TwigPlugin\\Exception\\ExceptionRenderer');

// Override in app/Config/bootstrap.php if needed.
if (!defined('TWIG_CACHE_PATH')) {
	define( 'TWIG_CACHE_PATH', TMP . 'twig' . DS .  'cache' );
}

// Configure additional extensions
Configure::write('twig.extensions', array_merge(
    array(
    ),
    (array) Configure::read('twig.extensions')
));
