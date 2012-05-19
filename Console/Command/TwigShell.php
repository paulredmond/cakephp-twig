<?php

use Symfony\Component\Finder\Finder;

/**
 * TwigShell for automating Plugin tasks and performance caching.
 */
class TwigShell extends AppShell
{
    public $tasks = array('TemplateMapper');
    
    /**
     * Warmup templates.
     * Used to match a templates logical name and the realpath.
     * 
     * Note: The TemplateMapper task is very rudimentary ATM, and could be improved later on.
     */
    public function warmup()
    {
        $collection = array();
        $paths = array(
            'App'       => App::path("View"),
            'Plugin'    => App::path('Plugin'),
        );
        
        $this->out('Warming up template cache', 1);
        
        foreach ($paths as $group => $locations) {
            // Sometimes this paths don't exist on the filesystem.
            $paths[$group] = $locations = $this->pruneLocations($locations);

            $finder = new Finder();
            $finder
                ->files()
                ->in($locations)
                ->exclude('.DS_Store')
                ->name('*.twig')
            ;
            
            $this->TemplateMapper->exectute($finder, $group, $locations);
        }
        
        $this->TemplateMapper->writeCacheFile();
        $this->out('<info>Cache file templates.php has been updated.</info>', 2);
    }
    
    /**
     * Make a given path is valid, if not unset.
     * 
     * @param $locations (array) Array of paths to verify.
     * @return (array) Array of valid filesystem paths.
     */
    private function pruneLocations(array $locations = array())
    {
        foreach ($locations as $k => $path)
        {
            if (!is_dir($path)) {
                unset($locations[$k]);
            }
        }
        
        return $locations;
    }

}