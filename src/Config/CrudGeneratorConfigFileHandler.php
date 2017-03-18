<?php

/**
 * Api to handle the config.json file.
 *
 * @author tesa
 */

namespace CrudGenerator\Config;

class CrudGeneratorConfigFileHandler
{
    /**
     * Stores a decoded json file with the configuration.
     * 
     * @var type 
     */
    public $data;
    
    /**
     * All the configurations options as part as the class.
     * 
     * @var type 
     */
    public $laravelVersion, $modelPath, $controllerPath, $formRequestPath, $viewsPath, $routePath, $blacklist;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $configJson = $this->getFile(dirname(__FILE__)."/config.json");
        
        if ($configJson) {
            $config = $this->jsonDecode($configJson);
            $this->getDataFromConfigJsonFile($config);
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
     * Set the attributes of the class. The variable $attributes needs to be an array where the is the attribute name 
     * and the value the attribute value.
     * 
     * ["laravelVersion" => "5.1"]
     * 
     * @param array $attributes
     */
    public function setAttributtes(array $attributes)
    {
        $classAttributes = $this->getAttributes();
        
        foreach ($attributes as $attribute => $value) {
            
            if (array_key_exists($attribute, $classAttributes)) {
                $functionName = "set".ucfirst($attribute);
                $this->$functionName($value);
            }
        }
    }
    
    /**
     * Set laravel version.
     * 
     * @param type $laraverVersion
     */
    public function setLaravelVersion($laraverVersion)
    {
        $this->laravelVersion = $laraverVersion;        
    }
    
    /**
     * Set model path.
     * 
     * @param type $modelPath
     */
    public function setModelPath($modelPath)
    {
        $this->modelPath = $modelPath;
    }
    
    /**
     * Set controller path.
     * 
     * @param type $controllerPath
     */
    public function setControllerPath($controllerPath)
    {
        $this->controllerPath = $controllerPath;
    }
    
    /**
     * Set fromrequest path.
     * 
     * @param type $formRequestPath
     */
    public function setFormRequestPath($formRequestPath)
    {
        $this->formRequestPath = $formRequestPath;
    }
    
    /**
     * Set views path.
     * 
     * @param type $viewsPath
     */
    public function setViewsPath($viewsPath)
    {
        $this->viewsPath = $viewsPath;
    }
    
    /**
     * Set route path.
     * 
     * @param type $routePaths
     */
    public function setRoutePath($routePaths)
    {
        $this->routePath = $routePaths;
    }
    
    /**
     * Set blacklist.
     * 
     * @param type $blacklist
     */
    public function setBlacklist($blacklist)
    {
        $this->blacklist = $blacklist;
    }
    
    /**
     * Save the actual data into a new config.json file.
     */
    protected function saveDataToConfigJason()
    {
        $attributes = $this->getAttributes();
        $config = $this->jsonEncode($attributes);
        
        $this->saveFile($config, dirname(__FILE__)."/config.json");
    }
    
    /**
     * Save a file.
     * 
     * @param type $configFile
     * @param type $path
     */
    protected function saveFile($configFile, $path)
    {
        $file = fopen($path, 'w+');
        fwrite($file, $fileToSave);
        fclose($file);
    }
    
    /**
     * Get class attibutes as array.
     * 
     * @return type
     */
    public function getAttributes()
    {
        return get_class_vars($this);
    }
    
    /**
     * Set the data accessible into this class.
     * 
     * @param type $config
     */
    protected function getDataFromConfigJsonFile($config)
    {
        $this->data = $config;
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
        return file_get_contents($filePath);
    }
}
