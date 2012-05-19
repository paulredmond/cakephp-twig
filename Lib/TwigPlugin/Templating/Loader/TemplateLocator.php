<?php

namespace TwigPlugin\Templating\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

class TemplateLocator implements FileLocatorInterface
{
    
    public function __construct(FileLocatorInterface $locator, \View $view)
    {
        if (file_exists($cache = CACHE . '/templates.php')) {
            $this->cache = require $cache;
        }

        $this->locator = $locator;
        $this->view = $view;
    }
    
    function locate($template, $currentPath = null, $first = true)
    {
        $plugin = $this->view->plugin;
        
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException("The template must be an instance of TemplateReferenceInterface.");
        }
        $key = $template->getLogicalName();

        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        
        // Check special paths if plugin for this template is different from the current one.
        // @todo caching
        // @todo reducing the # of paths possibly?
        if ('App' !== $template->get('plugin') && $plugin !== $template->get('plugin')) {
            $paths = $this->view->getPaths($template->get('plugin'));
            $locator = new FileLocator($paths);
            $this->cache[$key] = $locator->locate($template->getPath());
            
            return $this->cache[$key];
        }

        // Default locator using currently configured view paths
        try {
            return $this->cache[$key] = $this->locator->locate($template->getPath());
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('Unable to find template "%s".', $template), 0, $e);
        }
    }
}