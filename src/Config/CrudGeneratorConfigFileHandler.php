<?php

namespace CrudGenerator\Config;

/**
 * Description of CrudGeneratorConfigFileHandler
 *
 * @author tesa
 */
class CrudGeneratorConfigFileHandler
{
    public $laravelVersion, $modelPath, $controllerPath, $formRequestPath, $viewsPath, $routePath, $blacklist;
    
    public function __construct()
    {
        $config = require './config.php';
        
        $this->laravelVersion = $config['laravelVersion'];
        $this->modelPath = $config['modelPath'];
        $this->controllerPath = $config['controllerPath'];
        $this->formRequestPath = $config['formRequestPath'];
        $this->viewsPath = $config['viewsPath'];
        $this->routePath = $config['routePath'];
        $this->blacklist = $config['blacklist'];
    }
    
    public function newInstance()
    {
        return new CrudGeneratorConfigFileHandler;
    }
}
