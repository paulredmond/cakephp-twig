<?php

namespace TwigPlugin\Templating\Loader;

use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\Storage\FileStorage;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\FileLocator;

use \App as App;
use \Cache as Cache;

/**
 * Extends the default Twig filesystem loader
 * to work with CakePHP paths.
 */
class FilesystemLoader extends \Twig_Loader_Filesystem
{
    protected $cache = array();
    
    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser)
    {
        parent::__construct(array());

        $this->locator = $locator;
        $this->parser = $parser;
    }

    public function findTemplate($template)
    {
        $logicalName = (string) $template;

        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }
        
        if (is_string($template) && $this->isAbsolutePath($template) && file_exists($template)) {
            return new FileStorage($template);
        }

        $file = null;
        $previous = null;
        try {
            $template = $this->parser->parse($template);
            try {
                $file = $this->locator->locate($template);
            } catch (\InvalidArgumentException $e) {
                $previous = $e;
            }
        } catch (\Exception $e) {
            try {
                $file = parent::findTemplate($template);
            } catch (\Twig_Error_Loader $e) {
                $previous = $e;
            }
        }

        if (false === $file || null === $file) {
            throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $logicalName), -1, null, $previous);
        }

        return $this->cache[$logicalName] = $file;
    }
    
    /**
     * Returns true if the file is an existing absolute path.
     *
     * @param string $file A path
     *
     * @return true if the path exists and is absolute, false otherwise
     */
    private function isAbsolutePath($file)
    {
        if ($file[0] == '/' || $file[0] == '\\'
            || (strlen($file) > 3 && ctype_alpha($file[0])
                && $file[1] == ':'
                && ($file[2] == '\\' || $file[2] == '/')
            )
        ) {
            return true;
        }

        return false;
    }
}