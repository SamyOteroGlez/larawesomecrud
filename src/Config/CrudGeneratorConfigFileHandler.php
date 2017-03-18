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
     * All the configurations options as part as the class.
     * 
     * @var type 
     */
    public $laravelVersion, $modelPath, $controllerPath, $formRequestPath, $viewsPath, $routePath, $blacklist;
    
    /**
     * Constructor.
     * 
     * @param type $path
     * @param type $fileName
     */
    public function __construct($path = null, $fileName = null)
    {
        $path = ($path) ? $path : dirname(__FILE__).'/';
        $fileName = ($fileName) ? $fileName : 'config';
        
        $configJson = $this->getFile($path.$fileName.".json");
        
        if ($configJson) {
            $config = $this->jsonDecode($configJson);
            $this->setAttributesFromConfigFile($config);
        }
    }
    
    /**
     * Return a new instance of the class.
     * 
     * @param type $path
     * @param type $fileName
     * @return \CrudGenerator\Config\CrudGeneratorConfigFileHandler
     */
    static public function newInstance($path = null, $fileName = null)
    {
        return new CrudGeneratorConfigFileHandler($path, $fileName);
    }
    
    /**
     * Get class attibutes as array.
     * 
     * @return type
     */
    public function getAttributes()
    {
        $attributes = [];
        $attributeNames = get_class_vars(self::class);
        
        foreach ($attributeNames as $name => $value) {
            $attributes[$name] = $this->$name;
        }
        
        return $attributes;
    }
    
    /**
     * Set the attributes based on the json config file.
     * 
     * @param type $stdClass
     * @return $this
     */
    protected function setAttributesFromConfigFile($stdClass)
    {
        $stdClassAttributes = get_object_vars($stdClass);
        $attributeNames = get_class_vars(self::class);
        
        foreach ($stdClassAttributes as $attribute => $value) {
            
            if (array_key_exists($attribute, $attributeNames)) {
                
                if ($value instanceof \stdClass) {
                    $value = get_object_vars($value);
                }
                
                $this->$attribute = $value;
            }
        }
        
        return $this;
    }
    
    /**
     * Set the attributes of the class. The variable $attributes needs to be an array where the is the attribute name 
     * and the value the attribute value.
     * 
     * ["laravelVersion" => "5.1"]
     * 
     * @param array $attributes
     * @return $this
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
        
        return $this;
    }
    
    /**
     * Set laravel version.
     * 
     * @param type $laraverVersion
     * @return $this
     */
    public function setLaravelVersion($laraverVersion)
    {
        $this->laravelVersion = $laraverVersion;   
        
        return $this;
    }
    
    /**
     * Set model path.
     * 
     * @param type $modelPath
     * @return $this
     */
    public function setModelPath($modelPath)
    {
        $this->modelPath = $modelPath;
        
        return $this;
    }
    
    /**
     * Set controller path.
     * 
     * @param type $controllerPath
     * @return $this
     */
    public function setControllerPath($controllerPath)
    {
        $this->controllerPath = $controllerPath;
        
        return $this;
    }
    
    /**
     * Set fromrequest path.
     * 
     * @param type $formRequestPath
     * @return $this
     */
    public function setFormRequestPath($formRequestPath)
    {
        $this->formRequestPath = $formRequestPath;
        
        return $this;
    }
    
    /**
     * Set views path.
     * 
     * @param type $viewsPath
     * @return $this
     */
    public function setViewsPath($viewsPath)
    {
        $this->viewsPath = $viewsPath;
        
        return $this;
    }
    
    /**
     * Set route path.
     * 
     * @param type $routePaths
     * @return $this
     */
    public function setRoutePath($routePaths)
    {
        $this->routePath = $routePaths;
        
        return $this;
    }
    
    /**
     * Set blacklist.
     * 
     * @param type $blacklist
     * @return $this
     */
    public function setBlacklist($blacklist)
    {
        $this->blacklist = $blacklist;
        
        return $this;
    }
    
    /**
     * Save the actual data into a new config.json file.
     * 
     * @param type $path
     * @param type $fileName
     * @return $this
     */
    public function saveDataToConfigJason($path = null, $fileName = null)
    {
        $path = ($path) ? $path : dirname(__FILE__).'/';
        $fileName = ($fileName) ? $fileName : 'config';
        
        $attributes = $this->getAttributes();
        $config = $this->jsonEncode($attributes);
        
        $this->saveFile($config, $path.$fileName.".json");
        
        return $this;
    }
    
    /**
     * Save a file.
     * 
     * @param type $configFile
     * @param type $path
     * @return $this
     */
    protected function saveFile($fileToSave, $path)
    {
        $file = fopen($path, 'w+');
        fwrite($file, $fileToSave);
        fclose($file);
        
        return $this;
    }    
    
    /**
     * Encode data into json.
     * 
     * @param type $toJson
     * @return type
     */
    protected function jsonEncode($toJson)
    {
        return json_encode($toJson, JSON_PRETTY_PRINT);
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
