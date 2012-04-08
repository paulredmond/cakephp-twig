<?php

namespace TwigPlugin\Exception;

use \Exception as Exception;

interface ExceptionRendererInterface {
    public function __construct(Exception $exception);
    public function render();
}