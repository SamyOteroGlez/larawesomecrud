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
    protected $file;
    
    /**
     * Constructor.
     * 
     * @param type $path
     * @param type $fileName
     */
    public function __construct($path = null, $fileName = null)
    {
        $this->file = CrudGeneratorConfigFileHandler::newInstance($path, $fileName);
    }
    
    /**
     * Returns a new instance of the class.
     * 
     * @param type $path
     * @param type $fileName
     * @return \CrudGenerator\Config\CrudGeneratorConfigHandler
     */
    static public function newInstance($path = null, $fileName = null)
    {
        return new CrudGeneratorConfigHandler($path, $fileName);
    }
    
    /**
     * Create a new empty config.json file.
     * 
     * @param type $path
     * @param type $fileName
     * @return $this
     */
    public function createConfigFile($path, $fileName)
    {
        $this->file->createNewConfigurationFile($path, $fileName);
        
        return $this;
    }
    
    /**
     * Save the configuration value.
     * 
     * @param array $confiParameters
     * @param type $path
     * @param type $fileName
     * @return $this
     */
    public function saveConfigParameters(array $confiParameters, $path = null, $fileName = null)
    {
        if (is_array($confiParameters)) {
            $this->file->setAttributtes($confiParameters)
                ->saveDataToConfigJason($path, $fileName);
        }
        
        return $this;
    }
    
    /**
     * Get the configuration parameters.
     * 
     * @return type
     */
    public function getConfigParameters()
    {
        return $this->file->getAttributes();
    }


    /**
     * Set laravel version. By default will store the current laravel version.
     * 
     * @param type $version
     * @return $this
     */
    public function setLaravelVersion($laraverVersion = null)
    {
        if (is_null($laraverVersion)) {
            $laraverVersion = App::version();
        }
        
        $this->file->setLaravelVersion($laraverVersion);
        
        return $this;
    }
    
    /**
     * Get the laravel version stored in the configuration file.
     * 
     * @return type
     */
    public function getLaravelVersion()
    {
        return $this->file->laravelVersion;
    }
    
    /**
     * Set all the paths (model, controller, formrequest, views, route).
     * 
     * @param array $paths
     * @return $this
     */
    public function setPaths(array $paths)
    {
        $this->setConfigParameters($paths);
        
        return $this;
    }
    
    /**
     * Get all the paths (model, controller, formrequest, views, route) as an array.
     * 
     * @return type
     */
    public function getPaths()
    {
        return [
            'modelPath' => $this->file->modelPath,
            'controllerPath' => $this->file->controllerPath,
            'formRequestPath' => $this->file->formRequestPath,
            'viewsPath' => $this->file->viewsPath,
            'routePath' => $this->file->routPath
        ];
    }
    
    /**
     * Set the model path.
     * 
     * @param type $modelPath
     * @return $this
     */
    public function setModelPath($modelPath)
    {
        $this->file->setModelPath($modelPath);
        
        return $this;
    }
    
    /**
     * Get the model path.
     * 
     * @return type
     */
    public function getModelPath()
    {
        return $this->file->modelPath;
    }
    
    /**
     * Set the controller path.
     * 
     * @param type $controllerPaths
     * @return $this
     */
    public function setControllerPath($controllerPaths)
    {
        $this->file->setFormRequestPath($formRequestPath);
        
        return $this;
    }
    
    /**
     * Get the controller path.
     * 
     * @return type
     */
    public function getControllerPath()
    {
        return $this->file->controllerPath;
    }
    
    /**
     * Set the formrequest path.
     * 
     * @param type $formRequestPath
     * @return $this
     */
    public function setFormRequestPath($formRequestPath)
    {
        $this->file->setFormRequestPath($formRequestPath);
        
        return $this;
    }
    
    /**
     * Get the formrequest path.
     * 
     * @return type
     */
    public function getFormRequestPath()
    {
        return $this->file->formRequestPath;
    }
    
    /**
     * Set the views path.
     * 
     * @param type $viewsPath
     * @return $this
     */
    public function setViewsPath($viewsPath)
    {
        $this->file->setViewsPath($viewsPath);
        
        return $this;
    }
    
    /**
     * Get the views path.
     * 
     * @return type
     */
    public function getViewsPath()
    {
        return $this->file->viewsPath;
    }
    
    /**
     * Set the route path.
     * 
     * @param type $routePath
     * @return $this
     */
    public function setRoutePath($routePath)
    {
        $this->file->setRoutePath($routePaths);
        
        return $this;
    }
    
    /**
     * Get the route path.
     * 
     * @return type
     */
    public function getRoutePath()
    {
        return $this->file->routePath;
    }
    
    /**
     * Set the blacklist.
     * 
     * @param array $blacklist
     * @return $this
     */
    public function setBlacklist(array $blacklist)
    {
        $this->file->setBlacklist($blacklist);
        
        return $this;
    }
    
    /**
     * Get the blacklist.
     * 
     * @return type
     */
    public function getBlacklist()
    {
        return $this->file->blacklist;
    }
}
