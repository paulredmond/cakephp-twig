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
use TwigPlugin\Templating\TemplateReference;

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
     * Array of paths where twig will look for templates.
     */
    protected $templatePaths = array();


    /**
     * TwigLoader
     *
     * Holds the Twig_Loader_Filesystem object.
     * @var \TwigPlugin\Templating\Loader\FilesystemLoader
     * @access protected
     */
    protected $TwigLoader;

    /**
     * TwigLexer object
     *
     * Allows custom syntax in templates for block delimiters.
     *
     * @var \Twig_Lexer
     * @access protected
     * @link http://www.twig-project.org/doc/recipes.html#customizing-the-syntax
     */
    protected $TwigLexer;

    /**
     * TwigEnv
     *
     * @var \Twig_Environment object.
     */
    protected $TwigEnv;

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
     * Twig debug setting.
     *
     * Debugging is based on Configure::read('debug') value.
     */
    private $debug = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($controller, $register = true)
    {
        parent::__construct($controller, $register);

        $this->debug = (boolean)Configure::read('debug');
        $this->settings = array_merge($this->defaults, (array)Configure::read('Twig'));

        # Set up extension.
        $ext = $this->settings['ext'];
        $this->ext = substr($ext, 0, 1) == '.' ? $ext : ".{$ext}";

        // Paths for this request.
        $this->templatePaths = $this->getPaths($controller->plugin);

        # Set up the Twig environment instance.
        $this->TwigLoader = new FilesystemLoader(
            new TemplateLocator(
                new FileLocator($this->templatePaths),
                $this
            ),
            new TemplateNameParser
        );

        // Set valid paths for this request that actually exist on the filesystem.
        // CakePHP has various paths that can be used, but might not exist in reality.
        foreach ($this->templatePaths as $path) {
            try {
                $this->TwigLoader->addPath($path);
            } catch (Exception $e) {
                // Skip this path, its configured in CakePHP, but doesn't actually exist.
            }
        }

        // Really silly, but might as well update to only the paths that are valid.
        $this->TwigLoader->locator->locator = new FileLocator($this->templatePaths);

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
     * Render -- single-pass rendering using Twig's template inheritance.
     *
     * @param null $view View filename to render.
     * @param null $layout Layout used to render the view. Twig inheritance used instead.
     * @return bool|string Return the rendered output.
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

        try {
            $parsed = $this->TwigLoader->parser->parse($view);
        } catch (\Exception $e) {
            $parsed = false;
        }

        // Try to create a template reference based on the view object properties.
        if (false === $parsed) {
            $parsed = $this->_getViewFileName($view);
        }

        // Handy reference to this plugin's error layout.
        $this->set('twig_error_layout', 'Twig:Layouts:error.twig');

        // @todo At the moment, not calling beforeLayout/afterLayout callbacks. Might break 3rd party helpers?
        // @todo These are dispatched differently in CakePHP 2.1 -- support both 2.0x & 2.1x.
        $this->Helpers->trigger('beforeRender', array($parsed));

        // Render
        try {
            $template = $this->TwigEnv->loadTemplate($parsed);
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
        $this->Helpers->trigger('afterRender', array($parsed));

        return $this->output;
    }

    protected function _getViewFileName($name = null)
    {
        if ($name === null) {
            $name = $this->view;
        }

        $name = str_replace('/', DS, $name);
        $engine = str_replace('.', '', $this->ext);
        $plugin = (null === $this->plugin) ? 'App' : $this->plugin;
        $format = (null === $this->layoutPath) ? 'html' : $this->layoutPath;

        // Gets a little dirty here :/
        if (false === strpos($this->viewPath, DS)) {
            $controller = $this->viewPath;
        } else {
            $tmp = explode(DS, $this->viewPath);
            $controller = $tmp[0];
        }

        if (null !== $this->subDir) {
            $controller .= '/' . $this->subDir;
        }

        return new TemplateReference($plugin, $controller, $name, $format, $engine);
    }

    /**
     * Render various Twig exception objects for developer feedback.
     *
     * @param $type Type of exception being rendered (ex. 'Syntax').
     * @param Twig_Error $error Exception object
     * @param string $file Exception view that will be used to render the exception in debug mode.
     * @return string returns the rendered exception HTML.
     */
    private function renderTwigException($type, Twig_Error $error, $file = 'error')
    {
        $e = $error;
        $template = $this->TwigEnv->loadTemplate("Twig:Errors:{$file}.html.twig");

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
}
