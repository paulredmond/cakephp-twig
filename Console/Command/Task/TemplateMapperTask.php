<?php

use Symfony\Component\Finder\Finder;

class TemplateMapperTask extends Shell
{
    private $collection = array();
    
    public function exectute(Finder $finder, $group)
    {
        foreach ($finder as $file) {
            
            $name = array();
            $name[] = $group;
            
            $rel = explode(DS, $file->getRelativePathname());
            
            switch ($group) {
                case 'App':
                    // Plugin overrides in app/View
                    if (preg_match('/^(plugin)$/i', $rel[0])) {
                        array_shift($rel);
                        $name[0] = $rel[0];
                        array_shift($rel);
                    }
                    $name[] = $rel[0];
                    array_shift($rel);
                    $name[] = implode('/', $rel);
                break;
                
                case 'Plugin':
                    $name = array(
                        $rel[0],
                        $rel[2],
                    );
                    $view = array_slice($rel, 3);
                    $name[] = implode('/', $view);
                break;
            }
            
            $name = implode(':', $name);

            if (null === $this->get($name)) {
                $this->set($name, $file->getRealpath());
            }
        }
    }
    
    public function writeCacheFile()
    {
        $out = sprintf('<?php return %s;', var_export($this->getCollection(), true));
        
        return file_put_contents(CACHE . '/templates.php', $out);
    }
    
    public function set($key, $val)
    {
        $this->collection[$key] = $val;
    }
    
    public function get($key)
    {
        if (isset($this->collection[$key])) {
            return $this->collection[$key];
        }
        
        return null;
    }
    
    
    public function getCollection()
    {
        return $this->collection;
    }
}