<?php
/**
 * CakePHP view for Twig Template Engine.
 * Requires CakePHP 2 and PHP 5.
 *
 * Learn more about Twig at http://www.twig-project.org/
 *
 * @author Paul Redmond <paulrredmond@gmail.com>
 * @link https://github.com/paulredmond/cakephp-twig Github
 * @link http://goredmonster.com/ Author
 * @package Twig
 * @subpackage Twig.View.Twig
 * @license MIT
 */

use TwigPlugin\Extension\BasicExtension;
use TwigPlugin\Templating\Loader\FilesystemLoader;
use TwigPlugin\Templating\Loader\TemplateLocator;
use TwigPlugin\Templating\TemplateNameParser;

use Symfony\Component\Config\FileLocator;

/**
 * TwigView class for Cakephp 1.3 and PHP 5
 *
 * @package twig
 * @subpackage twig.views.twig
 * @author Paul Redmond <paulrredmond@gmail.com>
 * @link http://api13.cakephp.org/class/view
 */
class TwigView extends View
{


    /**
     * Default configuration options. Override array with Configure::write('Twig', array('ext' => 'twig'))
     */
    private $defaults = array(
        'ext' => '.twig',
        'debug_comments' => 'true', # only matters if Configure::read('debug') value > 0
        'lexer' => array(
            'tag_comment' => array('{#', '#}'),
            'tag_block' => array('{%', '%}'),
            'tag_variable' => array('{{', '}}'),
        ),
    );


    /**
     * Twig debug setting. Debugging is based on Configure::read('debug') value.
     */
    private $debug = false;


    /**
     * Array of paths where twig will look for templates.
     */
    protected $templatePaths = array();


    /**
     * TwigLoader
     *
     * Holds the Twig_Loader_Filesystem object.
     * @access protected
     */
    protected $TwigLoader;

    /**
     * TwigLexer object
     *
     * Allows custom syntax in templates for block delimiters.
     *
     * @access protected
     * @link http://www.twig-project.org/doc/recipes.html#customizing-the-syntax
     */
    protected $TwigLexer;

    /**
     * TwigEnv
     *
     * The Twig_Environment object.
     */
    protected $TwigEnv;


    public function __construct($controller, $register = true)
    {
        parent::__construct($controller, $register);

        $this->debug = (boolean)Configure::read('debug');
        $this->settings = array_merge($this->defaults, (array)Configure::read('Twig'));

        # Set up extension.
        $ext = $this->settings['ext'];
        $this->ext = substr($ext, 0, 1) == '.' ? $ext : ".{$ext}";

        // Paths for this request.
        $this->templatePaths = $this->getPaths($this->plugin);

        # Set up the Twig environment instance.
        $this->TwigLoader = new FilesystemLoader(
            new TemplateLocator(
                new FileLocator($this->templatePaths),
                $this
            ),
            new TemplateNameParser
        );

        // Paths used when parser/locator fall back to Twig_Loader_Filesystem class.
        $this->TwigLoader->setPaths($this->templatePaths);

        $this->TwigEnv = new Twig_Environment($this->TwigLoader, array(
            'cache' => Configure::read('Cache.disable') == true ? false : TWIG_CACHE_PATH,
            'debug' => $this->debug,
            'auto_reload' => $this->debug,
            'autoescape' => true,
        ));

        # Initialize a lexer instance with configured settings.
        $this->TwigLexer = new Twig_Lexer($this->TwigEnv, $this->settings['lexer']);
        $this->TwigEnv->setLexer($this->TwigLexer);

        foreach ((array)Configure::read('twig.extensions') as $ext) {
            $this->TwigEnv->addExtension(new $ext());
        }
    }

    public function getPaths($plugin = null)
    {
        return $this->_paths($plugin);
    }

    /**
     * Main render method.
     *
     * Totally broken right now.
     */
    public function render($view = null, $layout = null)
    {
        if ($this->hasRendered) {
            return true;
        }
        if (!$this->_helpersLoaded) {
            $this->loadHelpers();
        }
        $this->output = null;

        // Get the view filename and the relative path.
        $viewFileName = $this->_getViewFileName($view);
        $relative = str_replace($this->templatePaths, '', $viewFileName);
        $relative = ltrim($relative, '/');

        // Handy reference to this plugin's error layout.
        $this->set('twig_error_layout', 'Twig:Layouts:error.twig');

        // @todo At the moment, not calling beforeLayout/afterLayout callbacks. Might break 3rd party helpers?
        // @todo These are dispatched differently in CakePHP 2.1 -- support both 2.0x & 2.1x.
        $this->Helpers->trigger('beforeRender', array($viewFileName));

        // Render
        try {
            $template = $this->TwigEnv->loadTemplate($relative);
            $this->output = $template->render(array_merge($this->viewVars, array('_view' => $this)));
            $this->hasRendered = true;
        } catch (Twig_Error_Syntax $e) {
            return $this->renderTwigException('Syntax', $e);
        } catch (Twig_Error_Runtime $e) {
            return $this->renderTwigException('Runtime', $e);
        } catch (Twig_Error $e) {
            return $this->renderTwigException('Twig', $e);
        }

        // The only value this provides I guess is that $this->output is fully rendered at this point.
        $this->Helpers->trigger('afterRender', array($viewFileName));

        return $this->output;
    }

    private function renderTwigException($type, Twig_Error $error, $file = 'error')
    {
        $e = $error;
        $template = $this->TwigEnv->loadTemplate("Twig:Errors:{$file}.twig");

        return $template->render(array(
            'type' => $type,
            'error' => array(
                'message' => $e->getMessage(),
                'file' => ltrim(str_replace(ROOT, '', $e->getFile()), DS),
                'line' => $e->getLine(),
            ),
            'trace' => $e->getTrace(),
        ));
    }

    /**
     * Override default View _render method.
     * Uses Twig's exception handling for errors.
     *
     * @param $action file that is going to be rendered.
     * @param $params Data for the view being rendered.
     * @param $loadHelpers Whether or not to load helpers.
     * @param $cached (default: false)  Whether or not to create a cache file. Only applies to .ctp files.
     * @link http://api13.cakephp.org/class/view#method-View_render
     */
    public function _render($__view, $__data = array())
    {
        if (pathinfo($__view, PATHINFO_EXTENSION) == 'ctp') {
            return parent::_render($__view, $__data);
        }

        list($file, $dir) = array(basename($__view), dirname($__view));
        $relative = str_replace($this->TwigLoader->getPaths(), '', $__view);

        # Set up helpers.
        $loadedHelpers = array();
        if ($this->helpers != false && $loadedHelpers === true) {
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
        $timeStart = microtime(true);

        try {
            $e_path = dirname(__FILE__) . DS . 'exceptions'; # View path to exceptions.
            $params = array_merge($__data, (array)$this->loaded);
            $params['this'] =& $this;
            $template = $this->TwigEnv->loadTemplate($relative);
            echo $template->render($params);
            if ($this->debug == true && $this->settings['debug_comments'] == true) {
                echo "\n<!-- Twig rendered {$file} in " . round(microtime(true) - $timeStart, 4) . "s -->";
                echo "\n<!-- Path: {$__view} -->\n";
            }
        }
        catch (Twig_SyntaxError $e) {
            $this->_clearAllBuffers();
            ob_start();
            include($e_path . DS . 'syntax.ctp');
            $this->_twigException('Syntax Error', ob_get_clean(), $action, $e);
        }
        catch (Twig_RuntimeError $e) {
            $this->_clearAllBuffers();
            ob_start();
            include($e_path . DS . 'runtime.ctp');
            $this->_twigException('Runtime Error', ob_get_clean(), $action, $e);
        }
        catch (RuntimeException $e) {
            $this->_clearAllBuffers();
            ob_start();
            include($e_path . DS . 'runtime.ctp');
            $this->_twigException('Runtime Error', ob_get_clean(), $action, $e);
        }
        catch (Twig_Error $e) {
            $this->_clearAllBuffers();
            ob_start();
            include($e_path . DS . 'exception.ctp');
            $this->_twigException('Error', ob_get_clean(), $__view, $e);
        }

        if ($loadedHelpers === true) {
            $this->_triggerHelpers('afterRender');
        }

        return ob_get_clean();
    }


    /**
     * Rework of default element method in Core.
     * Allows for elements with $this->ext and the default .ctp extension
     * like View::_render() method.
     *
     * Requires a little more work to find files now that the method
     * is looping through extensions.
     *
     * @param string $name Name of the element minus the file extension.
     * @param array $params Array of parameters to pass to the element.
     * @return string Rendered element
     * @access public
     */
    public function element($name, $params = array(), $loadHelpers = false)
    {
        $file = $plugin = $key = null;

        if (isset($params['plugin'])) {
            $plugin = $params['plugin'];
        }

        if (isset($this->plugin) && !$plugin) {
            $plugin = $this->plugin;
        }

        if (isset($params['cache'])) {
            $expires = '+1 day';

            if (is_array($params['cache'])) {
                $expires = $params['cache']['time'];
                $key = Inflector::slug($params['cache']['key']);
            } elseif ($params['cache'] !== true) {
                $expires = $params['cache'];
                $key = implode('_', array_keys($params));
            }

            if ($expires) {
                $cacheFile = 'element_' . $key . '_' . $plugin . Inflector::slug($name);
                $cache = cache('views' . DS . $cacheFile, null, $expires);

                if (is_string($cache)) {
                    return $cache;
                }
            }
        }
        $paths = $this->_paths($plugin);

        $exts = array($this->ext);
        if ($this->ext !== '.ctp') {
            array_push($exts, '.ctp');
        }

        foreach ($exts as $ext) {
            foreach ($paths as $path) {
                if (file_exists($path . 'Elements' . DS . $name . $ext)) {
                    $file = $path . 'Elements' . DS . $name . $ext;
                    break;
                }
            }
        }

        if (is_file($file)) {
            $params = array_merge_recursive($params, (array)$this->loaded);
            $element = $this->_render($file, array_merge($this->viewVars, $params), $loadHelpers);
            if (isset($params['cache']) && isset($cacheFile) && isset($expires)) {
                cache('views' . DS . $cacheFile, $element, $expires);
            }
            return $element;
        }

        if (Configure::read() > 0) {
            return "Not Found: " . $file;
        }
    }

    /**
     * Output Twig exceptions
     *
     * Outputs exceptions raised by Twig using the default layout.
     * If debugging is disabled, alternatively logs the exception.
     */
    private function _twigException($type, $content, $filename, Exception $e)
    {
        $type = 'TwigView: ' . $type;
        $this->viewVars['title_for_layout'] = $type;
        if ($this->debug == true) {
            $this->plugin = 'Twig';
            echo $this->renderLayout($content, 'twig_exception');
            exit; # Important!
        } else {
            $this->log("[$type]: " . $e->getMessage());
        }
    }


    /**
     * If an exception is thrown during rendering,
     * this cheesy method ensures all buffers are cleared
     * before outputting debugging info.
     */
    private function _clearAllBuffers()
    {
        foreach (ob_list_handlers() as $buffer) {
            ob_end_clean();
        }
    }
}
