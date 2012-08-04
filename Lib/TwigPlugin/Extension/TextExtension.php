<?php

namespace TwigPlugin\Extension;

class TextExtension extends \Twig_Extension
{
    /**
     * @var \TextHelper
     */
    protected $textHelper;

    public function __construct($view)
    {
        \App::import('Helper', 'Text');
        $this->textHelper = new \TextHelper($view);
        $this->request = $this->textHelper->request;
        $this->response = $this->textHelper->response;
    }

    public function getFilters()
    {
        return array(
            'truncate' => new \Twig_Filter_Method($this, 'truncate',
                array(
                    'is_safe'       => array('html'),
                )
            ),
        );
    }

    /**
     * Provides link_to function which wraps HtmlHelper::link().
     *
     * @param $var      String  String to be truncated.
     * @param $length   Integer Length to truncate.
     * @param $options  Array   Options
     *
     * Default options: 'ending' => '...', 'exact' => true, 'html' => false
     *
     * Set 'html' option to true if you want truncate
     * to handle HTML in the string correctly.
     *
     */
    public function truncate($var, $length = 100, array $options = array())
    {
        return $this->textHelper->truncate($var, $length, $options);
    }

    public function getName()
    {
        return 'TextHelper';
    }
}