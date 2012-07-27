<?php

namespace TwigPlugin\Extension;

class FormExtension extends \Twig_Extension
{
    /**
     * @var \FormHelper
     */
    protected $FormHelper;

    public function __construct($view)
    {
        \App::import('Helper', 'Form');
        $this->FormHelper = new \FormHelper($view);

        $this->request = $this->FormHelper->request;
        $this->response = $this->FormHelper->response;
    }

    public function getFunctions()
    {
        return array(
            'form' => new \Twig_Function_Method($this, 'formTag',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'input' => new \Twig_Function_Method($this, 'input',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'button' => new \Twig_Function_Method($this, 'button',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'submit' => new \Twig_Function_Method($this, 'submit',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
            'form_end' => new \Twig_Function_Method($this, 'formEnd',
                array(
                    'pre_escape'    => 'html',
                    'is_safe'       => array('html'),
                )
            ),
        );
    }

    public function formTag($model = null, array $options = array())
    {
        return $this->FormHelper->create($model, $options);
    }

    public function input($field, array $options = array())
    {
        return $this->FormHelper->input($field, $options);
    }

    public function button($title = 'Submit', array $options = array())
    {
        return $this->FormHelper->button($title, $options);
    }

    public function submit($caption = null, array $options = array())
    {
        return $this->FormHelper->submit($caption, $options);
    }

    public function formEnd($options = null)
    {
        return $this->FormHelper->end($options);
    }

    public function getName()
    {
        return 'FormHelper';
    }
}