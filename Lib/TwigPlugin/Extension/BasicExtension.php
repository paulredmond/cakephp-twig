<?php

namespace TwigPlugin\Extension;

/**
 * CakePHP Basic functions
 *
 * Use: {{ user|debug }}
 * Use: {{ user|pr }}
 * Use: {{ env('HTTP_HOST') }}
 *
 * @author Hiroshi Hoaki <rewish.org@gmail.com>
 */
class BasicExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return array(
            'env'   => new \Twig_Function_Function('env'),
        );
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'debug' => new \Twig_Filter_Function('debug'),
            'pr'    => new \Twig_Filter_Function('pr'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'basic';
    }
}