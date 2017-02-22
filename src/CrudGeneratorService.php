<?php
/* TODO Change generate function
 * The Generate command is too monolithic
 * The way it is now you can not do only views, only api, only controller, etc
 *
 * */
namespace CrudGenerator;

use DB;
use Artisan;
use Illuminate\Console\Command;
use CrudGenerator\CrudGeneratorFileCreator as FileCreator;

//require '/vendor/smarty/smarty/libs/Smarty.class.php';

class CrudGeneratorService
{
    private $commandObject = null;
    private $appNamespace = 'App';
    private $modelName = '';
    private $tableName = '';
    private $formRequest = 'Request';
    private $prefix = '';
    private $force = false;
    private $layout = '';
    private $controllerName = '';
    private $apiControllerName = '';

    private $existingModel = '';
    private $viewFolderName = '';
    private $fileCreator;
    private $dashboard;
    private $templateFolder = null;
    private $options = null;

    /**
     * New CrudGeneratorService instance.
     *
     * @param null $cmdObject
     * @param string $appNamespace
     * @param string $modelName
     * @param string $tableName
     * @param string $formrequest
     * @param string $prefix
     * @param bool|false $force
     * @param string $layout
     * @param string $controllerName
     * @param string $apiControllerName
     * @param string $existingModel
     * @param string $viewFolderName
     */
    public function __construct(
      $cmdObject = null,
      $appNamespace = 'App',
      $modelName = '',
      $tableName = '',
      $formrequest = 'Request',
      $prefix = '',
      $force = false,
      $layout = '',
      $controllerName = '',
      $apiControllerName = '',
      $dashboard = false,
      $existingModel = '',
      $viewFolderName = '',
      $templateFolder = null

    ) {
        $this->commandObject = $cmdObject;
        $this->appNamespace = $appNamespace;
        $this->modelName = $modelName;
        $this->tableName = $tableName;
        $this->formRequest = $formrequest;
        $this->prefix = $prefix;
        $this->force = $force;
        $this->layout = $layout;
        $this->controllerName = $controllerName;
        $this->apiControllerName = $apiControllerName;
        $this->existingModel = $existingModel;
        $this->viewFolderName = $viewFolderName;
        $this->dashboard = $dashboard;
        $this->templateFolder = $templateFolder;

        $this->fileCreator = new FileCreator([], $cmdObject, '', '', $force, $templateFolder);
//        $this->fileCreator = new FileCreator([], $cmdObject, '', '', $force, $templateFolder);
        //$this->commandObject->info('Created with generate from folder: '.$this->generateFromFolder);
    }

    /**
     * @return null|string
     */
    public function getTemplateFolder()
    {
        return $this->templateFolder;
    }

    /**
     * @param null|string $templateFolder
     */
    public function setTemplateFolder($templateFolder)
    {
        $this->templateFolder = $templateFolder;
    }

    /**
     * Generate the CRUD.
     */
    public function Generate()
    {

        $modelname = ucfirst(str_singular($this->modelName));
        $this->viewFolderName = strtolower($this->controllerName);

        $this->commandObject->info('Creating catalogue for table: ' . ($this->tableName ?: strtolower(str_plural($this->modelName))));
        $this->commandObject->info('Model Name: ' . $modelname);

        if (!$this->force) {
            if (file_exists(app_path() . '/' . $modelname . '.php')) {
                $this->commandObject->info('The model class already exists, use --force to overwrite');
                return;
            }
            if (file_exists(app_path() . '/Http/Controllers/' . $this->controllerName . 'Controller.php')) {
                $this->commandObject->info('The controller class already exists, use --force to overwrite');
                return;
            }
            if (file_exists(app_path() . '/Http/Controllers/Api/' . $this->apiControllerName . 'Controller.php')) {
                $this->commandObject->info('The api controller class already exists, use --force to overwrite');
                return;
            }
            if (file_exists(base_path() . '/resources/views/' . $this->viewFolderName . '/_form.blade.php')) {
                $this->commandObject->info('The _form.blade.php view already exists, use --force to overwrite');
                return;
            }
            if (file_exists(base_path() . '/resources/views/' . $this->viewFolderName . '/create.blade.php')) {
                $this->commandObject->info('The create.blade.php view already exists, use --force to overwrite');
                return;
            }
            if (file_exists(base_path() . '/resources/views/' . $this->viewFolderName . '/show.blade.php')) {
                $this->commandObject->info('The show.blade.php view already exists, use --force to overwrite');
                return;
            }
            if (file_exists(base_path() . '/resources/views/' . $this->viewFolderName . '/edit.blade.php')) {
                $this->commandObject->info('The edit.blade.php view already exists, use --force to overwrite');
                return;
            }
            if (file_exists(base_path() . '/resources/views/' . $this->viewFolderName . '/index.blade.php')) {
                $this->commandObject->info('The index.blade.php view already exists, use --force to overwrite');
                return;
            }
        }
        $options = [
          'model_uc' => $modelname,
          'model_uc_plural' => str_plural($modelname),
          'model_singular' => strtolower($modelname),
          'model_plural' => strtolower(str_plural($modelname)),
          'tablename' => $this->tableName ?: strtolower(str_plural($this->modelName)),
          'prefix' => $this->prefix,
          'custom_master' => $this->layout ?: 'crudgenerator::layouts.master',
          'controller_name' => $this->controllerName,
          'api_controller_name' => $this->apiControllerName,
          'formrequest' => $this->formRequest,
          'view_folder' => $this->viewFolderName,
          'route_path' => $this->viewFolderName,
          'appns' => $this->appNamespace,
        ];

        $this->deletePreviousFiles($options['tablename']);

        $columns = $this->createModel($modelname, $this->prefix, $this->tableName);

        $options['columns'] = $columns;
        $options['first_column_nonid'] = count($columns) > 1 ? $columns[1]['name'] : '';
        $options['num_columns'] = count($columns);


        $this->createViewDirectory();
        $this->createApiDirectory();
        $this->options = $options;
        $this->commandObject->info('--->About to prepare FC');
        $this->prepareFileCreator($this->options);
        $this->commandObject->info('-->About To Gen First File');


        if (false !== $this->formRequest) {
            $options['formrequest'] = $modelname . 'FormRequest';
            $this->generateFormRequestClassFile($modelname);
        }

        $this->generateApiControllerClassFile();
        $this->addApiRoutesToRouteFile();
        $this->generateControllerClassFile();
        $this->addRoutesToRouteFile();
        $this->generateCreateViewFile();
        $this->generateShowViewFile();
        $this->generateEditViewFile();
        $this->generateFormViewFile();
        $this->generateIndexViewFile();


        if ($this->dashboard) {
            $this->verifyDashboardMenuPartial();
        }

        $this->showSuccessMessage($modelname);
    }

    protected function verifyDashboardMenuPartial()
    {
        $this->createDasboardFolder();

        if (!file_exists(base_path() . '/resources/views/dashboard/_menu.blade.php')) {
            $this->createDashboardMenuPartial();
        }

        $this->addNewLinkToDashboardMenu();
    }

    protected function createDasboardFolder()
    {
        $this->createDirectoryIfnotexist(base_path() . '/resources/views/dashboard');
    }

    protected function createDashboardMenuPartial()
    {
        $this->createFiles(
          '_menu', base_path() . '/resources/views/dashboard/_menu.blade.php', $this->templateFolder
        );
    }

    protected function addNewLinkToDashboardMenu()
    {
        $text = '<li class=""><a href="{{route(\'' . $this->viewFolderName . '.index\')}}">' . $this->viewFolderName . ' <span class="sr-only">(current)</span></a></li>';
        $this->appendToEndOfFile(
          base_path() . '/resources/views/dashboard/_menu.blade.php',
          "\n" . $text,
          0,
          true
        );
    }

    /**
     * Create the views directory if don't exist.
     */
    protected function createViewDirectory()
    {
        $this->createDirectoryIfnotexist(base_path() . '/resources/views/' . $this->viewFolderName);
    }

    /**
     * Create the api directory if don't exist.
     */
    protected function createApiDirectory()
    {
        $this->createDirectoryIfnotexist(base_path() . '/app/Http/Controllers/Api/');
    }

    /**
     * @param $directory
     */
    protected function createDirectoryIfnotexist($directory)
    {
        if (!empty($directory)) {
            if (!is_dir($directory)) {
                $this->commandObject->info('Creating directory: ' . $directory);
                mkdir($directory);
            }
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
        $this->fileCreator->setOutputAttribute($this->commandObject);
        //$this->commandObject->info('Prepare-- Folder: '.$this->generateFromFolder);
        $this->fileCreator->setTemplateFolder($this->templateFolder);

    }

    /**
     * Add new routes to the route.php file.
     */
    protected function addApiRoutesToRouteFile()
    {
        /*      $startText = '//Start routes for ' . $this->viewFolderName;
              $this->addTextToRoutesFile($startText);

              $route = 'Route::resource(\'/' . $this->viewFolderName . '\', \'' . $this->controllerName . 'Controller\');';
              $this->addTextToRoutesFile($route, true);

              $endText = '//End routes for ' . $this->viewFolderName;
              $this->addTextToRoutesFile($endText);
        */
    }

    /**
     * Add new routes to the route.php file.
     */
    protected function addRoutesToRouteFile()
    {
        $startText = '//Start routes for ' . $this->viewFolderName;
        $this->addTextToRoutesFile($startText);

        $route = 'Route::resource(\'/' . $this->viewFolderName . '\', \'' . $this->controllerName . 'Controller\');';
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
        //$this->appendToEndOfFile(base_path() . '/app/Http/routes.php', "\n" . $text, 0, true);
        // Laravel >=5.3
        $this->appendToEndOfFile(base_path() . '/routes/web.php', "\n" . $text, 0, true);

        if ($message) {
            $this->commandObject->info('Adding Route: ' . $text);
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
          'formrequest', app_path() . '/Http/Requests/' . $modelName . 'FormRequest.php', $this->templateFolder
        );

        $this->commandObject->info('FormRequest Name: ' . $modelName . 'FormRequest');
    }

    /**
     * Generate the controller class file.
     */
    protected function generateControllerClassFile()
    {
        $this->createFiles(
          'controller', app_path() . '/Http/Controllers/' . $this->controllerName . 'Controller.php',
          $this->templateFolder
        );
    }

    protected function generateApiControllerClassFile()
    {
        $this->createFiles(
          'api_controller', app_path() . '/Http/Controllers/Api/' . $this->apiControllerName . 'Controller.php',
          $this->templateFolder
        );

    }


    /**
     * Generate the view create.blade.php file.
     */
    protected function generateCreateViewFile()
    {
//        $info = $this->templateEngine->render('view.create.twig', $this->options);
//        file_put_contents(base_path() . '/resources/views/' . $this->viewFolderName . '/create.blade.php', $info);

        $this->createFiles(
          'view.create', base_path() . '/resources/views/' . $this->viewFolderName . '/create.blade.php',
          $this->templateFolder
        );
    }

    /**
     * Generate the view show.blade.php file.
     */
    protected function generateShowViewFile()
    {
//        $info = $this->templateEngine->render('view.show.twig', $this->options);
//        file_put_contents(base_path() . '/resources/views/' . $this->viewFolderName . '/show.blade.php', $info);
        $this->createFiles(
          'view.show', base_path() . '/resources/views/' . $this->viewFolderName . '/show.blade.php',
          $this->templateFolder
        );
    }

    /**
     * Generate the view edit.blade.php file.
     */
    protected function generateEditViewFile()
    {
//        $info = $this->templateEngine->render('view.edit.twig', $this->options);
//        file_put_contents(base_path() . '/resources/views/' . $this->viewFolderName . '/edit.blade.php', $info);
        $this->createFiles(
          'view.edit', base_path() . '/resources/views/' . $this->viewFolderName . '/edit.blade.php',
          $this->templateFolder
        );
    }

    protected function generateFormViewFile()
    {
//        $info = $this->templateEngine->render('view._form.twig', $this->options);
//        file_put_contents(base_path() . '/resources/views/' . $this->viewFolderName . '/_form.blade.php', $info);


        $this->createFiles(
          'view._form', base_path() . '/resources/views/' . $this->viewFolderName . '/_form.blade.php',
          $this->templateFolder
        );
    }

    /**
     * Generates the view index.blade.php file.
     */
    protected function generateIndexViewFile()
    {
//        $info = $this->templateEngine->render('view.index.twig', $this->options);
//        file_put_contents(base_path() . '/resources/views/' . $this->viewFolderName . '/index.blade.php', $info);


        $this->createFiles(
          'view.index', base_path() . '/resources/views/' . $this->viewFolderName . '/index.blade.php',
          $this->templateFolder
        );
    }

    /**
     * Generate the file according the template name and the path.
     *
     * @param $templateName
     * @param $completeFilename
     * @param $templateFolder
     */
    protected function createFiles($templateName, $completeFilename, $templateFolder)
    {

        $this->fileCreator->setTemplateNameAttribute($templateName);
        $this->fileCreator->setPathAttribute($completeFilename);
        $this->fileCreator->Generate($templateName);
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
        $cols = $this->getColumnNames($tablename);

        $ret = [];

        foreach ($cols as $c) {
            $field = isset($c->Field) ? $c->Field : $c->field;
            $type = isset($c->Type) ? $c->Type : $c->type;
            $cadd = [];
            $cadd['name'] = $field;

            $related = $this->getRelatedObjDataFK($tablename, $cadd['name']);
            $relatedTableName = '';

            if ($related) {
                $relatedTableName = $this->getRelatedTableName($related);
            }


            if ($cadd['name'] == $relatedTableName) {
                $cadd['type'] = 'related';
                $cadd['relatedName'] = $related->REFERENCED_TABLE_NAME;
                $cadd['display'] = ucwords($related->REFERENCED_TABLE_NAME);
                // Get Second field from the related table
                $relCols = $this->getColumnNames($related->REFERENCED_TABLE_NAME);

                $cadd['displayMethodName'] = str_singular(strtolower($related->REFERENCED_TABLE_NAME));
                $cadd['displayRelatedField'] = $relCols[1]->Field;

            } else {
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
// This seems to be MySQL specific
        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
          ->select('*')
          ->where('CONSTRAINT_SCHEMA', $dbname)
          ->where('TABLE_NAME', $tablename)
          ->where('CONSTRAINT_NAME', $tablename . '_' . $fkName . '_foreign')
          ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
          ->first(); //Changed the get to a first in order to avoid problems down the line
        return $result;
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
        if ($obj) {
            $patterns = ['/' . $obj->TABLE_NAME . '_/', '/_foreign/'];
            $replacements = ['', ''];
            $fkTableName = preg_replace($patterns, $replacements, $obj->CONSTRAINT_NAME);

            return $fkTableName;
        }
        return null;
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
        if (str_contains($dbtype, 'varchar')) {
            return 'text';
        }
        if (str_contains($dbtype, 'int') || str_contains($dbtype, 'float')) {
            return 'number';
        }

        if (str_contains($dbtype, 'date') || str_contains($dbtype, 'timestamp')) {
            return 'date';
        }

        if (str_contains($dbtype, 'text')) {
            return 'textarea';
        }
        $this->commandObject->info('FieldType ' . $dbtype);
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
        if (empty($table_name)) {
            $table_name = strtolower(str_plural($modelname));
        }
        $columns = $this->getColumns($prefix . $table_name);
        Artisan::call('make:model', ['name' => $modelname]); // Executes default make model


        $this->appendUseDb(app_path() . '/' . $modelname . '.php');
        $this->commandObject->info('Custom table name: ' . $prefix . $table_name);
        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
          "    protected \$table = '" . $table_name . "';\n\n}", 2, true);

        $cc = collect($columns);

        $this->addFillable($modelname, $cc);

        $this->addAppends($modelname, $table_name);
        //TODO the comparison states that if there is no updated_at OR no created_at it should not generate with timestamps  would be better if there is no updated_at AND no created_at then do not genereate with timestamps
        if (!$cc->contains('name', 'updated_at') && !$cc->contains('name', 'created_at')) {
            $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php', "\n    public \$timestamps = false;\n\n}",
              2, true);
        }

        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php', "", 2, true);

        $dataRelated = $this->getRelatedObjData($table_name);

        if (!empty($dataRelated)) {

            foreach ($dataRelated as $obj) {

                if ($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $this->appendBelongTo($modelname, $obj->REFERENCED_TABLE_NAME);
                }
            }
        }

        $dataReferenced = $this->getReferencedObjData($table_name);

        if (!empty($dataReferenced)) {

            foreach ($dataReferenced as $obj) {

                if ($table_name == $obj->REFERENCED_TABLE_NAME) {
                    $this->appendHasMany($modelname, $obj->TABLE_NAME);
                }
            }
        }

        $this->appendAttributes($modelname, $table_name, $cc, $prefix);

        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php', "\n}", 0, false);

        $this->commandObject->info('Model created, columns: ' . json_encode($columns));

        return $columns;
    }

    /**
     * Add attributes to the model.
     *
     * @param $modelname
     * @param $table_name
     * @param $columName
     * @param $prefix
     */
    protected function appendAttributes($modelname, $table_name, $columName, $prefix)
    {
        $dataRelated = $this->getRelatedObjData($table_name);
        //$this->commandObject->info('Model Name '.$modelname );
        //$columName = $columName[1]['name'];

        if (!empty($dataRelated)) {

            foreach ($dataRelated as $obj) {

                if ($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $referencedClassName = ucwords($obj->REFERENCED_TABLE_NAME);

                    $referencedTable = collect($this->getColumns($prefix . $obj->REFERENCED_TABLE_NAME));

                    $columName = $referencedTable[1]['name'];
                    $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
                      "\n    public function get" . "$referencedClassName" . "ListAttribute()\n" .
                      "    {\n" .
                      "        \$data = " . str_singular($referencedClassName) . "::all()->pluck('$columName' , 'id');\n\n" .
                      "        return \$data;\n" .
                      "    }\n   /* Do not forget to add:\n     Use App\\" . str_singular($referencedClassName) . ";\n   above */",
                      0, true);
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

        if (!empty($dataRelated)) {

            foreach ($dataRelated as $obj) {

                if ($table_name == $obj->TABLE_NAME && !empty($obj->REFERENCED_TABLE_NAME)) {
                    $appends .= "                            '" . $obj->REFERENCED_TABLE_NAME . 'List' . "',\n";
                }
            }

            $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
              "    protected \$appends = [$appends                         ];\n\n}", 2, true);
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

        foreach ($fields as $field) {

            if ('id' == $field['name']) {
                continue;
            } elseif ('created_at' == $field['name']) {
                continue;
            } elseif ('updated_at' == $field['name']) {
                continue;
            } elseif ('deleted_at' == $field['name']) {
                continue;
            } else {
                $fillables .= "                            '" . $field['name'] . "',\n";
            }
        }

        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
          "    protected \$fillable = [$fillables                          ];\n\n}", 2, true);
    }

    /**
     * Add belongTo to the model.
     *
     * @param $modelname
     * @param $referencedName
     */
    protected function appendBelongTo($modelname, $referencedName)
    {
        $referencedClassName = str_singular(ucwords($referencedName));
        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
          "\n    public function " . str_singular($referencedName) . "()\n" .
          "    {\n" .
          "        return \$this->belongsTo('App\\$referencedClassName');\n" .
          "    }\n",
          0, true);
    }

    /**
     * Add hasMany relationship to the model.
     *
     * @param $modelname
     * @param $relatedName
     */
    protected function appendHasMany($modelname, $relatedName)
    {
        $relatedClassName = str_singular(ucwords($relatedName));
        $this->appendToEndOfFile(app_path() . '/' . $modelname . '.php',
          "\n    public function $relatedName()\n    {\n        return \$this->hasMany('App\\$relatedClassName');\n    }\n",
          0, true);
    }

    /**
     * Delete existing files (Controller, index, create, edit, show, _form).
     *
     * @param $name
     */
    protected function deletePreviousFiles($name)
    {
        $todelete = [
          app_path() . '/Http/Controllers/' . ucfirst($name) . 'Controller.php',
          app_path() . '/Http/Controllers/Api/' . ucfirst($name) . 'Controller.php',
          base_path() . '/resources/views/' . str_plural($name) . '/index.blade.php',
          base_path() . '/resources/views/' . str_plural($name) . '/create.blade.php',
          base_path() . '/resources/views/' . str_plural($name) . '/edit.blade.php',
          base_path() . '/resources/views/' . str_plural($name) . '/show.blade.php',
          base_path() . '/resources/views/' . str_plural($name) . '/_form.blade.php',
        ];

        $todelete[] = app_path() . '/' . ucfirst(str_singular($name)) . '.php';

        foreach ($todelete as $path) {

            if (file_exists($path)) {
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

        if (!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content) - $remove_last_chars) . $text;
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
        $this->commandObject = $output;
    }

    /**
     * Get output.
     *
     * @return null
     */
    public function getOutputAttribute()
    {
        return $this->commandObject;
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
    public function setLayoutAttribute($layout)
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
        $this->commandObject->info('');
        $this->commandObject->info('****************************************************');
        $this->commandObject->info(' The files for ' . $modelname);
        $this->commandObject->info(' were succesfully generated.');
        $this->commandObject->info('');
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function convertToColumns($columns)
    {
        $modColumns = [];
        foreach ($columns as $i) {
            $d = [];

            if (is_array($i)) {

                foreach ($i as $key => $value) {
                    $d['column.' . $key] = $value;
                }
            } else {
                $d['column'] = $i;
            }
            array_push($modColumns, $d);

        }
        return $modColumns;
    }

    /**
     * @param $tablename
     * @return mixed
     */
    protected function getColumnNames($tablename)
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
        return $cols;
    }
}
