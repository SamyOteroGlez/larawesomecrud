<?php

namespace CrudGenerator\Config;

use App;
use CrudGeneratorConfigFileHandler;

/*
 * Api to interact with the config.php file
 */

/**
 * Description of CrudConfigHandler
 *
 * @author tesa
 */
class CrudGeneratorConfigHandler
{
    protected $config;
    
    public function __construct(CrudGeneratorConfigFileHandler $config)
    {
        $this->config = $config->newInstance();
    }
    
    protected function setConfig(array $config)
    {
        $this->config = $config;
    }
    
    public function setLaravelVersion($version = null)
    {
        if (is_null($version)) {
            $version = App::version();
        }
    }
    
    public function getLaravelVersion()
    {
        return $this->config->laravelVersion;
    }
    
    public function setPaths(array $paths)
    {
        
    }
    
    public function getPaths()
    {
        return [
            'modelPath' => $this->config->modelPath,
            'controllerPath' => $this->config->controllerPath,
            'formRequestPath' => $this->configformRequestPath,
            'viewsPath' => $this->config->viewsPath,
            'routePath' => $this->config->routPath
        ];
    }
    
    public function setModelPath($modelPath)
    {
        
    }
    
    public function getModelPath()
    {
        return $this->config->modelPath;
    }
    
    public function setControllerPath($controllerPaths)
    {
        
    }
    
    public function getControllerPath()
    {
        return $this->config->controllerPath;
    }
    
    public function setFormRequestPath($formRequestPath)
    {
        
    }
    
    public function getFormRequestPath()
    {
        return $this->config->formRequestPath;
    }
    
    public function setViewsPath($viewsPath)
    {
        
    }
    
    public function getViewsPath()
    {
        return $this->config->viewsPath;
    }
    
    public function setRoutePath($routePath)
    {
        
    }
    
    public function getRoutePath()
    {
        return $this->config->routePath;
    }
    
    public function setBlacklist(array $blacklist)
    {
        
    }
    
    public function getBlacklist()
    {
        return $this->config->blacklist;
    }
}
