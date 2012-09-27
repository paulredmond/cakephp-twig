<?php

namespace TwigPlugin\Extension;

use \TwigView;

class ViewExtension extends \Twig_Extension
{
    /**
     * @var \TwigView
     */
    protected $view;

    public function __construct(TwigView $view)
    {
        $this->view = $view;
    }

    public function getFunctions()
    {
        return array(
            'element' => new \Twig_Function_Method($this, 'element',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
        );
    }

    public function element($name, array $data = array(), array $options = array())
    {
        return $this->view->element($name, $data, $options);
    }

    public function getName()
    {
        return get_class($this);
    }
}