<?php

namespace TwigPlugin\Extension;

class HtmlExtension extends \Twig_Extension
{
    /**
     * @var \HtmlHelper
     */
    protected $htmlHelper;

    public function __construct($view)
    {
        \App::import('Helper', 'Html');
        $this->htmlHelper = new \HtmlHelper($view);
    }

    public function getFunctions()
    {
        return array(
            'link_to' => new \Twig_Function_Method($this, 'linkTo',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'url' => new \Twig_Function_Method($this, 'url',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'css' => new \Twig_Function_Method($this, 'css',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'script' => new \Twig_Function_Method($this, 'script',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
        );
    }

    /**
     * Provides link_to function which wraps HtmlHelper::link().
     *
     * @param $title
     * @param $url
     * @param array $options
     * @param bool $confirmMessage
     * @return string Html link.
     */
    public function linkTo($title, $url, $options = array(), $confirmMessage = false)
    {
        return $this->htmlHelper->link($title, $url, $options, $confirmMessage);
    }

    public function url($path, $full = false)
    {
        return $this->htmlHelper->url($path, $full);
    }


    public function script($url, $options = array())
    {
        return $this->htmlHelper->script($url, $options);
    }

    public function css($path, $rel = null, $options = array())
    {
        return $this->htmlHelper->css($path, $rel, $options);
    }

    public function getName()
    {
        return 'HtmlHelper';
    }
}