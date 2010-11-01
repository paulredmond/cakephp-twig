<?php

App::import('Vendor', 'Twig.Twig/Autoloader');

/**
 * TwigView class for Cakephp.
 */
class TwigView extends View {
	
	private 
	$tmpName = 'twig',
	$cacheName = 'cache',
	$debug = false,
	$tmpPath,
	$cachePath,
	$TwigLoader,
	$TwigEnv;
	
	const DEFAULT_EXTENSION = '.twg';
	
	public function __construct( $controller, $register=true )
	{
		parent::__construct($controller, $register);
		
		Twig_Autoloader::register();
		$this->tmpFolderSetup();
		
		// Set up the Twig environment instance.
		$this->TwigLoader = new Twig_Loader_Filesystem(VIEWS); // Have to load a path. Not really useful yet.
		$this->TwigEnv = new Twig_Environment( $this->TwigLoader, array(
			'cache' => Configure::read('Cache.disable') == true ? false : $this->cachePath,
			'debug' => $this->debug
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
		
		list($file, $dir) = array( basename( $action ), dirname( $action ) );
		$this->TwigLoader->setPaths( $dir );

		if ( $loadHelpers == true) {
			$helpers = $this->loadHelpers();
			$params = array_merge( $params, $helpers);
		}
		
		$params['this'] = $this;
		
		$template = $this->TwigEnv->loadTemplate($file);
		$this->debug = true;
		$timeStart = getMicrotime();
		$out = $template->render( $params );
		
		if ( $this->debug == true ) {
			$out = $out . "\n<!-- Twig rendered {$file} in " . round(getMicrotime() - $timeStart, 4) . "s -->";
			$out = $out . "\n<!-- Path: {$action} -->\n";
		}
		
		return $out;
	}
	
	private function loadHelpers() {
		$helpers = array();
		if ( $this->helpers != false ) {
			$helpers = $this->_loadHelpers( $helpers, $this->helpers );
			foreach( $helpers as $var => $obj ) {
				$varName = Inflector::variable( $var );
				$this->loaded[$varName] = $obj;
			}
			return $this->loaded;
		}
		return array();
	}
	
	/**
	 * Makes sure that temp folders are set up for twig.
	 */
	private function tmpFolderSetup() {
		$tmp = TMP . $this->tmpName;
		$cache = $tmp . DS . $this->cacheName;
		
		if( ! is_dir( $tmp )) {
			mkdir( $tmp );
		}
		
		if( ! is_dir( $cache ) ) {
			mkdir( $cache );
		}
		
		$this->tmpPath = $tmp;
		$this->cachePath = $cache;
	}
}
