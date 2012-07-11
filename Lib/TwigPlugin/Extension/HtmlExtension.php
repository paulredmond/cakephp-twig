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

    public function getName()
    {
        return 'HtmlHelper';
    }
}