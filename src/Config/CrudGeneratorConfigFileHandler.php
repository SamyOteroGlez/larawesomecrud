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
        $configJson = $this->getFile("config.json");
        
        if ($configJson) {
            $config = $this->jsonDecode($configJson);
            $this->setDataFromConfigJsonFile($config);
        }
    }
    
    /**
     * Return a new instance of the class.
     * 
     * @return \CrudGenerator\Config\CrudGeneratorConfigFileHandler
     */
    static public function newInstance()
    {
        return new CrudGeneratorConfigFileHandler;
    }
    
    /**
     * Set the data accesible into this class.
     * 
     * @param type $config
     */
    protected function setDataFromConfigJsonFile($config)
    {
        $this->laravelVersion = $config['laravelVersion'];
        $this->modelPath = $config['modelPath'];
        $this->controllerPath = $config['controllerPath'];
        $this->formRequestPath = $config['formRequestPath'];
        $this->viewsPath = $config['viewsPath'];
        $this->routePath = $config['routePath'];
        $this->blacklist = $config['blacklist'];
    }
    
    /**
     * Encode data into json.
     * 
     * @param type $toJson
     */
    protected function jsonEncode($toJson)
    {
        return json_encode($toJson);
    }
    
    /**
     * Decode a json file.
     * 
     * @param type $json
     * @return type
     */
    protected function jsonDecode($json)
    {
        return json_decode($json);
    }
    
    /**
     * Get the file.
     * 
     * @param type $jsonFilePath
     * @return type
     */
    protected function getFile($filePath)
    {
        if (file_exists($filePath)) {
            $file = file_get_contents($filePath);
            
            return $file;
        }
        
        return null;
    }
}
