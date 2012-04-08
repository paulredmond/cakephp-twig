<?php

namespace TwigPlugin\Exception;

\App::uses('ExceptionRenderer', 'Error');

use \ExceptionRenderer as BaseExceptionRenderer;

class ExceptionRenderer extends BaseExceptionRenderer
{
    /**
     * Overloaded _outputMessage to set the view path to Twig plugin.
     * That way the plugin can render error templates from the plugin
     * without requiring the user to move them to app or create them.
     *
     * @param $template Error template name being rendered.
     */
    protected function _outputMessage($template)
    {
        $this->controller->plugin = 'Twig';
        $this->controller->set('trace', $this->error->getTrace());

        $this->controller->render($template);
        $this->controller->afterFilter();
        $this->controller->response->send();
    }
}