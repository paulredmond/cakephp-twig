<?php

namespace TwigPlugin\Templating;

use Symfony\Component\Templating\TemplateNameParser as BaseTemplateNameParser;

class TemplateNameParser extends BaseTemplateNameParser
{
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            return $name;
        } else if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        
        $parts = explode(':', $name);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid (format is "Plugin:Folder:template.engine")', $name));
        }
        
        $elements = explode('.', $parts[2]);
        if (2 > count($elements)) {
            throw new \InvalidArgumentException(sprintf('Template name "%s" is not valid (format is "Plugin:Folder:template.engine")'));
        }
        
        $engine = array_pop($elements);
        
        $template = new TemplateReference($parts[0], $parts[1], implode('.', $elements), $engine);
        
        return $this->cache[$name] = $template;
    }
}