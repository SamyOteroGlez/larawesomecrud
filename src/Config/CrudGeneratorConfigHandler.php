<?php

/**
 * Api to interact with the config.json file
 *
 * @author tesa
 */

namespace CrudGenerator\Config;

use App;
use CrudGenerator\Config\CrudGeneratorConfigFileHandler;

class CrudGeneratorConfigHandler
{
    /**
     * Stores an instance of CrudGeneratorConfigFileHandler class. Through this class we have access and can manage 
     * the configuration parameters.
     * 
     * @var type 
     */
    protected $config;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = CrudGeneratorConfigFileHandler::newInstance();
    }
    
    /**
     * Returns a new instance of the class.
     * 
     * @return \CrudGenerator\Config\CrudGeneratorConfigHandler
     */
    static public function newInstance()
    {
        return new CrudGeneratorConfigHandler;
    }
    
    /**
     * Set the configuration value.
     * 
     * @param array $config
     */
    protected function setConfig(array $config)
    {
        //$this->config = $config;
    }
    
    /**
     * Set laravel version. By default will store the current laravel version.
     * 
     * @param type $version
     */
    public function setLaravelVersion($version = null)
    {
        if (is_null($version)) {
            $version = App::version();
        }
    }
    
    /**
     * Get the laravel version stored in the configuration file.
     * 
     * @return type
     */
    public function getLaravelVersion()
    {
        return $this->config->data->laravelVersion;
    }
    
    /**
     * Set all the paths (model, controller, formrequest, views, route).
     * 
     * @param array $paths
     */
    public function setPaths(array $paths)
    {
        
    }
    
    /**
     * Get all the paths (model, controller, formrequest, views, route) as an array.
     * 
     * @return type
     */
    public function getPaths()
    {
        return [
            'modelPath' => $this->config->data->modelPath,
            'controllerPath' => $this->config->data->controllerPath,
            'formRequestPath' => $this->config->data->formRequestPath,
            'viewsPath' => $this->config->data->viewsPath,
            'routePath' => $this->config->data->routPath
        ];
    }
    
    /**
     * Set the model path.
     * 
     * @param type $modelPath
     */
    public function setModelPath($modelPath)
    {
        
    }
    
    /**
     * Get the model path.
     * 
     * @return type
     */
    public function getModelPath()
    {
        return $this->config->data->modelPath;
    }
    
    /**
     * Set the controller path.
     * 
     * @param type $controllerPaths
     */
    public function setControllerPath($controllerPaths)
    {
        
    }
    
    /**
     * Get the controller path.
     * 
     * @return type
     */
    public function getControllerPath()
    {
        return $this->config->data->controllerPath;
    }
    
    /**
     * Set the formrequest path.
     * 
     * @param type $formRequestPath
     */
    public function setFormRequestPath($formRequestPath)
    {
        
    }
    
    /**
     * Get the formrequest path.
     * 
     * @return type
     */
    public function getFormRequestPath()
    {
        return $this->config->data->formRequestPath;
    }
    
    /**
     * Set the views path.
     * 
     * @param type $viewsPath
     */
    public function setViewsPath($viewsPath)
    {
        
    }
    
    /**
     * Get the views path.
     * 
     * @return type
     */
    public function getViewsPath()
    {
        return $this->config->data->viewsPath;
    }
    
    /**
     * Set the route path.
     * 
     * @param type $routePath
     */
    public function setRoutePath($routePath)
    {
        
    }
    
    /**
     * Get the route path.
     * 
     * @return type
     */
    public function getRoutePath()
    {
        return $this->config->data->routePath;
    }
    
    /**
     * Set the blacklist.
     * 
     * @param array $blacklist
     */
    public function setBlacklist(array $blacklist)
    {
        
    }
    
    /**
     * Get the blacklist.
     * 
     * @return type
     */
    public function getBlacklist()
    {
        return $this->config->data->blacklist;
    }
}
