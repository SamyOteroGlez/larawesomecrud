<?php

namespace CrudGenerator;

use DB;
use Artisan;
use Faker\Provider\File;
use Illuminate\Console\Command;
use CrudGenerator\CrudGeneratorFileCreator as FileCreator;

class CrudGeneratorService 
{
    private $output = null;
    private $appNamespace = 'App';
    private $modelName = '';
    private $tableName = '';
    private $formRequest = 'Request';
    private $prefix = '';
    private $force = false;
    private $layout = '';
    private $controllerName = '';
    private $existingModel = '';
    private $viewFolderName = '';
    private $fileCreator;
    private $dashboard;

    /**
     * New CrudGeneratorService instance.
     *
     * @param null $output
     * @param string $appNamespace
     * @param string $modelName
     * @param string $tableName
     * @param string $formrequest
     * @param string $prefix
     * @param bool|false $force
     * @param string $layout
     * @param string $controllerName
     * @param string $existingModel
     * @param string $viewFolderName
     */
    public function __construct(
        $output = null,
        $appNamespace = 'App',
        $modelName = '',
        $tableName = '',
        $formrequest = 'Request',
        $prefix = '',
        $force = false,
        $layout = '',
        $controllerName = '',
        $dashboard = false,
        $existingModel = '',
        $viewFolderName = ''        
    )
    {
        $this->output = $output;
        $this->appNamespace = $appNamespace;
        $this->modelName = $modelName;
        $this->tableName = $tableName;
        $this->formRequest = $formrequest;
        $this->prefix = $prefix;
        $this->force = $force;
        $this->layout = $layout;
        $this->controllerName = $controllerName;
        $this->existingModel = $existingModel;
        $this->viewFolderName = $viewFolderName;
        $this->dashboard = $dashboard;

        $this->fileCreator = new FileCreator();
    }

    /**
     * Generate the CRUD.
     */
    public function Generate() 
    {
        $modelname = ucfirst(str_singular($this->modelName));
        $this->viewFolderName = strtolower($this->controllerName);

        $this->output->info('Creating catalogue for table: ' . ($this->tableName ?: strtolower(str_plural($this->modelName))));
        $this->output->info('Model Name: ' . $modelname);

        $options = [
            'model_uc' => $modelname,
            'model_uc_plural' => str_plural($modelname),
            'model_singular' => strtolower($modelname),
            'model_plural' => strtolower(str_plural($modelname)),
            'tablename' => $this->tableName ?: strtolower(str_plural($this->modelName)),
            'prefix' => $this->prefix,
            'custom_master' => $this->layout ?: 'crudgenerator::layouts.master',
            'controller_name' => $this->controllerName,
            'formrequest' => $this->formRequest,
            'view_folder' => $this->viewFolderName,
            'route_path' => $this->viewFolderName,
            'appns' => $this->appNamespace,
        ];

        if(!$this->force) { 
            if(file_exists(app_path().'/'.$modelname.'.php')) { $this->output->info('The model class already exists, use --force to overwrite'); return; }
            if(file_exists(app_path().'/Http/Controllers/'.$this->controllerName.'Controller.php')) { $this->output->info('The controller class already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/_form.blade.php')) { $this->output->info('The _form.blade.php view already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/create.blade.php')) { $this->output->info('The create.blade.php view already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/show.blade.php')) { $this->output->info('The show.blade.php view already exists, use --force to overwrite');  return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/edit.blade.php')) { $this->output->info('The edit.blade.php view already exists, use --force to overwrite');  return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/index.blade.php')) { $this->output->info('The index.blade.php view already exists, use --force to overwrite');  return; }
        }

        $this->deletePreviousFiles($options['tablename']);

        $columns = $this->createModel($modelname, $this->prefix, $this->tableName);

        $options['columns'] = $columns;
        $options['first_column_nonid'] = count($columns) > 1 ? $columns[1]['name'] : '';
        $options['num_columns'] = count($columns);
        
        $this->createViewDirectory();

        $this->prepareFileCreator($options);

        if(false !== $this->formRequest) {
            $options['formrequest'] = $modelname . 'FormRequest';

            $this->generateFormRequestClassFile($modelname);
        }

        $this->generateControllerClassFile();
        $this->generateCreateViewFile();
        $this->generateShowViewFile();
        $this->generateEditViewFile();
        $this->generateFormViewFile();
        $this->generateIndexViewFile();
        $this->addRoutesToRouteFile();
        
        if($this->dashboard) {
            $this->verifyDashboardMenuPartial();
        }        

        $this->showSuccessMessage($modelname);
    }
    
    protected function verifyDashboardMenuPartial()
    {
        if(!is_dir(base_path() . '/resources/views/dashboard')) {
            $this->createDasboardFolder();
        }
        
        if(!file_exists(base_path().'/resources/views/dashboard/_menu.blade.php')) {
            $this->createDashboardMenuPartial();
        }
        
        $this->addNewLinkToDashboardMenu();
    }
    
    protected function createDasboardFolder()
    {
        $this->output->info('Creating directory: ' . base_path() . '/resources/views/dasboard');
        mkdir(base_path() . '/resources/views/dashboard');
    }
    
    protected function createDashboardMenuPartial()
    {
        $this->createFiles(
            '_menu',
            base_path() . '/resources/views/dashboard/_menu.blade.php'
        );
    }
    
    protected function addNewLinkToDashboardMenu()
    {
        $text = '<li class=""><a href="{{route(\''.$this->viewFolderName.'.index\')}}">'.$this->viewFolderName.' <span class="sr-only">(current)</span></a></li>';
        $this->appendToEndOfFile(
            base_path() . '/resources/views/dashboard/_menu.blade.php',
            "\n".$text,
            0,
            true
        );
    }

    /**
     * Create the views directory if don't exist.
     */
    protected function createViewDirectory()
    {
        if(!is_dir(base_path() . '/resources/views/' . $this->viewFolderName)) {
            $this->output->info('Creating directory: ' . base_path() . '/resources/views/' . $this->viewFolderName);
            mkdir(base_path() . '/resources/views/' . $this->viewFolderName);
        }
    }

    /**
     * Prepare the file creator instance to generate the files.
     *
     * @param $options
     */
    protected function prepareFileCreator($options)
    {
        $this->fileCreator->setOptionsAttribute($options);
        $this->fileCreator->setOutputAttribute($this->output);
    }

    /**
     * Add new routes to the route.php file.
     */
    protected function addRoutesToRouteFile()
    {
        $startText = '//Start routes for ' . $this->viewFolderName;
        $this->addTextToRoutesFile($startText);

        $route = 'Route::resource(\'/'.$this->viewFolderName.'\', \''.$this->controllerName.'Controller\');';
        $this->addTextToRoutesFile($route, true);

        $endText = '//End routes for ' . $this->viewFolderName;
        $this->addTextToRoutesFile($endText);
    }

    /**
     * Append text to the route.php file.
     *
     * @param $addRoute
     * @param bool|false $message
     */
    protected function addTextToRoutesFile($text, $message = false)
    {
        $this->appendToEndOfFile(base_path() . '/app/Http/routes.php', "\n" . $text, 0, true);

        if($message) {
            $this->output->info('Adding Route: ' . $text );
        }
    }

    /**
     * Generate the form request class file.
     *
     * @param $modelName
     */
    protected function generateFormRequestClassFile($modelName)
    {
        $this->createFiles(
            'formrequest',
            app_path().'/Http/Requests/'. $modelName . 'FormRequest.php'
        );

        $this->output->info('FormRequest Name: ' . $modelName . 'FormRequest');
    }

    /**
     * Generate the controller class file.
     */
    protected function generateControllerClassFile()
    {
        $this->createFiles(
            'controller',
            app_path().'/Http/Controllers/'.$this->controllerName.'Controller.php'
        );
    }

    /**
     * Generate the view create.blade.php file.
     */
    protected function generateCreateViewFile()
    {
        $this->createFiles(
            'view.create',
            base_path().'/resources/views/'.$this->viewFolderName.'/create.blade.php'
        );
    }

    /**
     * Generate the view show.blade.php file.
     */
    protected function generateShowViewFile()
    {
        $this->createFiles(
            'view.show',
            base_path().'/resources/views/'.$this->viewFolderName.'/show.blade.php'
        );
    }

    /**
     * Generate the view edit.blade.php file.
     */
    protected function generateEditViewFile()
    {
        $this->createFiles(
            'view.edit',
            base_path().'/resources/views/'.$this->viewFolderName.'/edit.blade.php'
        );
    }

    protected function generateFormViewFile()
    {
        $this->createFiles(
            'view._form',
            base_path().'/resources/views/'.$this->viewFolderName.'/_form.blade.php'
        );
    }

    /**
     * Generates the view index.blade.php file.
     */
    protected function generateIndexViewFile()
    {
        $this->createFiles(
            'view.index',
            base_path().'/resources/views/'.$this->viewFolderName.'/index.blade.php'
        );
    }

    /**
     * Generate the file according the template name and the path.
     *
     * @param $templateName
     * @param $path
     */
    protected function createFiles($templateName, $path)
    {
        $this->fileCreator->setTemplateNameAttribute($templateName);
        $this->fileCreator->setPathAttribute($path);
        $this->fileCreator->Generate();
    }

    /**
     * Get the column name of the table to generate the model. Get the column type to generate the form fields.
     *
     * @param $tablename
     *
     * @return array
     */
    protected function getColumns($tablename)
    {
        $dbType = DB::getDriverName();

        switch ($dbType) {
            case "pgsql":
                $cols = DB::select("select column_name as Field, "
                                . "data_type as Type, "
                                . "is_nullable as Null "
                                . "from INFORMATION_SCHEMA.COLUMNS "
                                . "where table_name = '" . $tablename . "'");
                break;

            default:
                $cols = DB::select("show columns from " . $tablename);
                break;
        }

        $ret = [];

        foreach ($cols as $c) {
            $field = isset($c->Field) ? $c->Field : $c->field;
            $type = isset($c->Type) ? $c->Type : $c->type;
            $cadd = [];
            $cadd['name'] = $field;

            $related = $this->getRelatedObjDataFK($tablename, $cadd['name']);
            $relatedTableName = '';

            if($related) {
                $relatedTableName = $this->getRelatedTableName($related);
            }

            if($cadd['name'] == $relatedTableName) {
                $cadd['type'] = 'related';
                $cadd['relatedName'] = $related->REFERENCED_TABLE_NAME;
                $cadd['display'] = ucwords($related->REFERENCED_TABLE_NAME);
            }
            else {
                $cadd['type'] = $field == 'id' ? 'id' : $this->getTypeFromDBType($type);
                $cadd['display'] = ucwords(str_replace('_', ' ', $field));
            }

            $ret[] = $cadd;
        }

        return $ret;
    }

    /**
     * Get the related table data, the foreingkey table data.
     *
     * @param $tablename
     * @param $fkName
     * @return mixed
     */
    protected function getRelatedObjDataFK($tablename, $fkName)
    {
        $dbname = DB::connection()->getDatabaseName();

        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $dbname)
            ->where('TABLE_NAME', $tablename)
            ->where('CONSTRAINT_NAME', $tablename . '_' . $fkName . '_foreign')
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return reset($result);
    }

    /**
     * Get the related table name and data.
     *
     * @param $tablename
     * @return mixed
     */
    protected function getRelatedObjData($tablename)
    {
        $dbname = DB::connection()->getDatabaseName();

        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $dbname)
            ->where('TABLE_NAME', $tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }

    /**
     * Get the referenced table name and data.
     *
     * @param $tablename
     * @return mixed
     */
    protected function getReferencedObjData($tablename)
    {
        $dbname = DB::connection()->getDatabaseName();

        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $dbname)
            ->where('REFERENCED_TABLE_NAME', $tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }

    /**
     * Get the related table name, the foreingkey.
     *
     * @param $obj
     * @return mixed
     */
    protected function getRelatedTableName($obj)
    {
        $patterns = ['/' . $obj->TABLE_NAME . '_/', '/_foreign/'];
        $replacements = ['', ''];
        $fkTableName = preg_replace($patterns, $replacements, $obj->CONSTRAINT_NAME);

        return $fkTableName;
    }

    /**
     * Get the column type to generate the field type in the form.
     *
     *      [Db type => form type]
     *      varchar => text
     *      int => number
     *      date => date
     *      text => textarea
     *
     * If the column type is other it will return unknown.
     *
     * @param $dbtype
     * @return string
     */
    protected function getTypeFromDBType($dbtype)
    {
        if(str_contains($dbtype, 'varchar')) { return 'text'; }
        if(str_contains($dbtype, 'int') || str_contains($dbtype, 'float')) { return 'number'; }
        if(str_contains($dbtype, 'date')) { return 'date'; }
        if(str_contains($dbtype, 'text')) { return 'textarea'; }

        return 'unknown';
    }

    /**
     * Create the model to generate.
     *
     * @param $modelname
     * @param $prefix
     * @param $table_name
     * @return array
     */
    protected function createModel($modelname, $prefix, $table_name)
    {
        $table_name = strtolower($modelname);
        $columns = $this->getColumns($prefix.$table_name);
        
        Artisan::call('make:model', ['name' => $modelname]);

        $this->appendUseDb(app_path().'/'.$modelname.'.php');        
        $this->output->info('Custom table name: '.$prefix.$table_name);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$table = '".$table_name."';\n\n}", 2, true);
        
        $cc = collect($columns);

        $this->addFillable($modelname, $cc);

        $this->addAppends($modelname, $table_name);

        if(!$cc->contains('name', 'updated_at') || !$cc->contains('name', 'created_at')) { 
            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public \$timestamps = false;\n\n}", 2, true);
        }

        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "", 2, true);

        $dataRelated = $this->getRelatedObjData($table_name);

        if(!empty($dataRelated)) {

            foreach($dataRelated as $obj) {

                if($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $this->appendBelongTo($modelname, $obj->REFERENCED_TABLE_NAME);
                }
            }
        }

        $dataReferenced = $this->getReferencedObjData($table_name);

        if(!empty($dataReferenced)) {

            foreach($dataReferenced as $obj) {

                if($table_name == $obj->REFERENCED_TABLE_NAME) {
                    $this->appendHasMany($modelname, $obj->TABLE_NAME);
                }
            }
        }

        $this->appendAttributes($modelname, $table_name, $cc);

        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n}", 0, false);

        $this->output->info('Model created, columns: '.json_encode($columns));

        return $columns;
    }

    /**
     * Add attributes to the model.
     *
     * @param $modelname
     * @param $table_name
     * @param $columName
     */
    protected function appendAttributes($modelname, $table_name, $columName)
    {
        $dataRelated = $this->getRelatedObjData($table_name);
        $columName = $columName[1]['name'];

        if(!empty($dataRelated)) {

            foreach($dataRelated as $obj) {

                if($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $referencedClassName = ucwords($obj->REFERENCED_TABLE_NAME);
                    $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public function get" . "$referencedClassName" . "ListAttribute()\n    {\n        \$data = DB::table('$obj->REFERENCED_TABLE_NAME')->lists('$columName' , 'id');\n\n        return \$data;\n    }\n", 0, true);
                }
            }
        }
    }

    /**
     * Add appends array to the model.
     *
     * @param $modelname
     * @param $table_name
     */
    protected function addAppends($modelname, $table_name)
    {
        $appends = "\n";
        $dataRelated = $this->getRelatedObjData($table_name);

        if(!empty($dataRelated)) {

            foreach($dataRelated as $obj) {

                if($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $appends .= "                            '". $obj->REFERENCED_TABLE_NAME . 'List' . "',\n";
                }
            }

            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$appends = [$appends                         ];\n\n}", 2, true);
        }
    }

    /**
     * Add fillable array to the model.
     *
     * @param $modelname
     * @param $fields
     */
    protected function addFillable($modelname, $fields)
    {
        $fillables = "\n";

        foreach($fields as $field) {

            if('id' == $field['name']) {
                continue;
            }
            elseif('created_at' == $field['name']) {
                continue;
            }
            elseif('updated_at' == $field['name']) {
                continue;
            }
            elseif('deleted_at' == $field['name']) {
                continue;
            }
            else {
                $fillables .= "                            '". $field['name'] . "',\n";
            }
        }

        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$fillable = [$fillables                          ];\n\n}", 2, true);
    }

    /**
     * Add belongTo to the model.
     *
     * @param $modelname
     * @param $referencedName
     */
    protected function appendBelongTo($modelname, $referencedName)
    {
        $referencedClassName = ucwords($referencedName);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public function $referencedName()\n    {\n        return \$this->belongsTo('App\\$referencedClassName');\n    }\n", 0, true);
    }

    /**
     * Add hasMany relationship to the model.
     *
     * @param $modelname
     * @param $relatedName
     */
    protected function appendHasMany($modelname, $relatedName)
    {
        $relatedClassName = ucwords($relatedName);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public function $relatedName()\n    {\n        return \$this->hasMany('App\\$relatedClassName');\n    }\n", 0, true);
    }

    /**
     * Delete existing files (Controller, index, create, edit, show, _form).
     *
     * @param $name
     */
    protected function deletePreviousFiles($name)
    {
        $todelete = [
                app_path().'/Http/Controllers/'.ucfirst($name).'Controller.php',
                base_path().'/resources/views/'.str_plural($name).'/index.blade.php',
                base_path().'/resources/views/'.str_plural($name).'/create.blade.php',
                base_path().'/resources/views/'.str_plural($name).'/edit.blade.php',
                base_path().'/resources/views/'.str_plural($name).'/show.blade.php',
                base_path().'/resources/views/'.str_plural($name).'/_form.blade.php',
            ];

        $todelete[] = app_path().'/'.ucfirst(str_singular($name)).'.php';

        foreach($todelete as $path) {

            if(file_exists($path)) { 
                unlink($path);    
                //$this->output->info('Deleted: '.$path);
            }   
        }
    }

    /**
     * Append text to the end of a file.
     *
     * @param $path
     * @param $text
     * @param int $remove_last_chars
     * @param bool|false $dont_add_if_exist
     */
    protected function appendToEndOfFile($path, $text, $remove_last_chars = 0, $dont_add_if_exist = false)
    {
        $content = file_get_contents($path);

        if(!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content)-$remove_last_chars).$text;
            file_put_contents($path, $newcontent);    
        }
    }

    /**
     * Append the "use DB" at the top of the class.
     *
     * @param $path
     */
    protected function appendUseDb($path)
    {
        $lines = file($path);
        $lines[2] .= "\n";
        $lines[3] = "use DB;\n";

        file_put_contents($path, implode('', $lines));
    }

    /**
     * Set output.
     *
     * @param $output
     */
    public function setOutputAttribute($output)
    {
        $this->output = $output;
    }

    /**
     * Get output.
     *
     * @return null
     */
    public function getOutputAttribute()
    {
        return $this->output;
    }

    /**
     * Set appNamespace.
     *
     * @param $appNamespace
     */
    public function setAppNamespaceAttribute($appNamespace)
    {
        $this->appNamespace = $appNamespace;
    }

    /**
     * Get appNamespace.
     *
     * @return string
     */
    public function getAppNamespaceAttribute()
    {
        return $this->appNamespace;
    }

    /**
     * Set modelName.
     *
     * @param $modelName
     */
    public function setModelNameAttribute($modelName)
    {
        $this->modelName = $modelName;
    }

    /**
     * Get modelName.
     *
     * @return string
     */
    public function getModelNameAttribute()
    {
        return $this->modelName;
    }

    /**
     * Set tableName.
     *
     * @param $tableName
     */
    public function setTableNameAttribute($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Get tableName.
     *
     * @return string
     */
    public function getTableNameAttribute()
    {
        return $this->tableName;
    }

    /**
     * Set formRequest.
     *
     * @param $formRequest
     */
    public function setFormRequestAttribute($formRequest)
    {
        $this->formRequest = $formRequest;
    }

    /**
     * Get formRequest.
     *
     * @return string
     */
    public function getFormRequestAttribute()
    {
        return $this->formRequest;
    }

    /**
     * Set prefix.
     *
     * @param $prefix
     */
    public function setPrefixAttribute($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Get prefix.
     *
     * @return string
     */
    public function getPrefixAttribute()
    {
        return $this->prefix;
    }

    /**
     * Set force.
     *
     * @param $force
     */
    public function setForceAttribute($force)
    {
        $this->force = $force;
    }

    /**
     * Get force.
     *
     * @return bool|false
     */
    public function getForceAttribute()
    {
        return $this->force;
    }

    /**
     * Set layout.
     *
     * @param $layout
     */
    public function setLauyoutAttribute($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get layout.
     *
     * @return string
     */
    public function getLayoutAttribute()
    {
        return $this->layout;
    }

    /**
     * Set controllerName.
     *
     * @param $controllerName
     */
    public function setControllerNameAttribute($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * Get controllerName.
     *
     * @return string
     */
    public function getControllerNameAttribute()
    {
        return $this->controllerName;
    }

    /**
     * Set existingModel.
     *
     * @param $existingModel
     */
    public function setExistingModelAttribute($existingModel)
    {
        $this->existingModel = $existingModel;
    }

    /**
     * Get existingModel.
     *
     * @return string
     */
    public function getExistingModelAttribute()
    {
        return $this->existingModel;
    }

    /**
     * Set viewFolderName.
     *
     * @param $viewFolderName
     */
    public function setViewFolderNameAttribute($viewFolderName)
    {
        $this->viewFolderName = $viewFolderName;
    }

    /**
     * Get viewFolderName.
     *
     * @return string
     */
    public function getViewFolderNameAttribute()
    {
        return $this->viewFolderName;
    }

    /**
     * Show bye bye message.
     */
    protected function showSuccessMessage($modelname)
    {
        $this->output->info('');
        $this->output->info('****************************************************');
        $this->output->info('* Awesome!                                         *');
        $this->output->info('* The files for ' . $modelname);
        $this->output->info('* were succesfully generated.                      *');
        $this->output->info('*                                                  *');
        $this->output->info('****************************************************');
        $this->output->info('');
    }
}
