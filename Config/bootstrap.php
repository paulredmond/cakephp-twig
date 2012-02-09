<?php
//
// TwigPlugin bootstrap file
//

require dirname(__FILE__) . '/../Vendor/.composer/autoload.php';

// Override in app/Config/bootstrap.php if needed.
if (!defined('TWIG_CACHE_PATH')) {
	define( 'TWIG_CACHE_PATH', TMP . 'twig' . DS .  'cache' );
}