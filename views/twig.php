<?php

App::import('Vendor', 'Twig.Twig/Autoloader');

if ( ! defined( 'TWIG_CACHE_PATH' ) ) {
	define( 'TWIG_CACHE_PATH', TMP . 'twig' . DS .  'cache' );
}

/**
 * TwigView class for Cakephp.
 */
class TwigView extends View {
	
	private 
	$tmpName = 'twig',
	$cacheName = 'cache',
	$debug = false,
	$tmpPath,
	$error_view_path,
	$cachePath,
	$TwigLoader,
	$TwigEnv;
	
	const DEFAULT_EXTENSION = '.twg';
	
	public function __construct( $controller, $register=true )
	{
		parent::__construct($controller, $register);
		
		Twig_Autoloader::register();
		
		$this->debug = (boolean) Configure::read('debug');
		
		// Set up the Twig environment instance.
		$this->TwigLoader = new Twig_Loader_Filesystem( VIEWS ); # Have to load a path. Not really useful yet.
		$this->TwigEnv = new Twig_Environment( $this->TwigLoader, array(
			'cache' => Configure::read('Cache.disable') == true ? false : TWIG_CACHE_PATH,
			'debug' => $this->debug,
			'auto_reload' => $this->debug
		));
		
		$this->ext = self::DEFAULT_EXTENSION;
		
		if( isset( $controller->viewExt ) && !empty( $controller->viewExt ) ) {
			$ext = $controller->viewExt;
			$this->ext = substr($ext, 0, 1) == '.' ? $ext : ".{$ext}";
		}
	}
	
	public function _render($action, $params, $loadHelpers = true, $cached = false) {
		if (pathinfo( $action, PATHINFO_EXTENSION ) == 'ctp' ) {
			return parent::_render( $action, $params, $loadHelpers, $cached );
		}
		
		# Set the twig path to the current filename path.
		list($file, $dir) = array( basename( $action ), dirname( $action ) );
		$this->TwigLoader->setPaths( $dir );

		# Set up helpers.
		$loadedHelpers = array();
		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);
			$helpers = array_keys($loadedHelpers);
			$helperNames = array_map(array('Inflector', 'variable'), $helpers);

			for ($i = count($helpers) - 1; $i >= 0; $i--) {
				$name = $helperNames[$i];
				$helper =& $loadedHelpers[$helpers[$i]];

				if (!isset($___dataForView[$name])) {
					${$name} =& $helper;
				}
				$this->loaded[$helperNames[$i]] =& $helper;
				$this->{$helpers[$i]} =& $helper;
			}
			$this->_triggerHelpers('beforeRender');
			unset($name, $loadedHelpers, $helpers, $i, $helperNames);
		}
		
		# Render template
		ob_start();
		$timeStart = getMicrotime();
		try {
			$e_path = dirname(__FILE__) . DS . 'exceptions'; # View path to exceptions.
			$params = array_merge( $params, $this->loaded );
			$template = $this->TwigEnv->loadTemplate($file);
			echo $template->render( $params );		
			if ( $this->debug == true ) {
				echo "\n<!-- Twig rendered {$file} in " . round(getMicrotime() - $timeStart, 4) . "s -->";
				echo "\n<!-- Path: {$action} -->\n";
			}
		}
		catch( Twig_SyntaxError $e ) {
			include( $e_path . DS . 'exception.ctp' );
			$this->_twigException('Syntax Error', ob_get_clean(), $action, $e);
		}
		catch( Twig_RuntimeError $e ) {
			include( $e_path . DS . 'exception.ctp' );
			$this->_twigException('Runtime Error', ob_get_clean(), $action, $e);
		}
		catch( RuntimeException $e) {
			include( $e_path . DS . 'exception.ctp' );
			$this->_twigException('Runtime Error', ob_get_clean(), $action, $e);
		}
		catch( Twig_Error $e ) {
			include( $e_path . DS . 'exception.ctp' );
			$this->_twigException('Error', ob_get_clean(), $action, $e);
		}
		
		if ($loadHelpers === true) {
			$this->_triggerHelpers('afterRender');
		}
		
		return ob_get_clean();
	}
	
	/**
	 * Output Twig exceptions
	 * 
	 * Outputs exceptions raised by Twig using the default layout.
	 * If debugging is disabled, alternatively logs the exception. 
	 */
	private function _twigException( $type, $content, $filename, Exception $e ) {
		$type = 'TwigView: ' . $type;
		$this->viewVars['title_for_layout'] = $type;
		if($this->debug == true) {
			$this->plugin = 'twig';
			echo $this->renderLayout( $content, 'twig_exception' );
			exit; # Important!
		} else {
			$this->log( "[$type]: " . $e->getMessage() );
		}
	}
}
