<?php

namespace CrudGenerator;


use Illuminate\Console\Command;
use DB;
use Artisan;

class CrudGeneratorService 
{
    public $modelName = '';
    public $tableName = '';
    public $prefix = '';
    public $force = false;
    public $layout = '';
    public $existingModel = '';
    public $controllerName = '';
    public $viewFolderName = '';
    public $output = null;
    public $appNamespace = 'App';
 
    public function __construct()
    {

    }
  
    public function Generate() 
    {
        $modelname = ucfirst(str_singular($this->modelName));
        $this->viewFolderName = strtolower($this->controllerName);

        $this->output->info('');
        $this->output->info('Creating catalogue for table: '.($this->tableName ?: strtolower(str_plural($this->modelName))));
        $this->output->info('Model Name: '.$modelname);

        $options = [
            'model_uc' => $modelname,
            'model_uc_plural' => str_plural($modelname),
            'model_singular' => strtolower($modelname),
            'model_plural' => strtolower(str_plural($modelname)),
            'tablename' => $this->tableName ?: strtolower(str_plural($this->modelName)),
            'prefix' => $this->prefix,
            'custom_master' => $this->layout ?: 'crudgenerator::layouts.master',
            'controller_name' => $this->controllerName,
            'view_folder' => $this->viewFolderName,
            'route_path' => $this->viewFolderName,
            'appns' => $this->appNamespace,
        ];

        if(!$this->force) { 
            if(file_exists(app_path().'/'.$modelname.'.php')) { $this->output->info('Model already exists, use --force to overwrite'); return; }
            if(file_exists(app_path().'/Http/Controllers/'.$this->controllerName.'Controller.php')) { $this->output->info('Controller already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/add.blade.php')) { $this->output->info('Add view already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/show.blade.php')) { $this->output->info('Show view already exists, use --force to overwrite'); return; }
            if(file_exists(base_path().'/resources/views/'.$this->viewFolderName.'/index.blade.php')) { $this->output->info('Index view already exists, use --force to overwrite');  return; }
        }

        $this->deletePreviousFiles($options['tablename']);

        $columns = $this->createModel($modelname, $this->prefix, $this->tableName);
        
        $options['columns'] = $columns;
        $options['first_column_nonid'] = count($columns) > 1 ? $columns[1]['name'] : '';
        $options['num_columns'] = count($columns);
        
        //###############################################################################
        if(!is_dir(base_path().'/resources/views/'.$this->viewFolderName)) { 
            $this->output->info('Creating directory: '.base_path().'/resources/views/'.$this->viewFolderName);
            mkdir( base_path().'/resources/views/'.$this->viewFolderName); 
        }

        $filegenerator = new \CrudGenerator\CrudGeneratorFileCreator();
        $filegenerator->options = $options;
        $filegenerator->output = $this->output;

        $filegenerator->templateName = 'controller';
        $filegenerator->path = app_path().'/Http/Controllers/'.$this->controllerName.'Controller.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.create';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/create.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.edit';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/edit.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view._form';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/_form.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.show';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/show.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.index';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/index.blade.php';
        $filegenerator->Generate();
        //###############################################################################

        $addroute = '//Start routes for ' . $this->viewFolderName;
        $this->appendToEndOfFile(base_path().'/app/Http/routes.php', "\n".$addroute, 0, true);

        $addroute = 'Route::get(\'/'.$this->viewFolderName.'/grid\', \''.$this->controllerName.'Controller@grid\');';
        $this->appendToEndOfFile(base_path().'/app/Http/routes.php', "\n".$addroute, 0, true);
        $this->output->info('Adding Route: '.$addroute );

        $addroute = 'Route::resource(\'/'.$this->viewFolderName.'\', \''.$this->controllerName.'Controller\');';
        $this->appendToEndOfFile(base_path().'/app/Http/routes.php', "\n".$addroute, 0, true);
        $this->output->info('Adding Route: '.$addroute );

        $addroute = '//End routes for ' . $this->viewFolderName;
        $this->appendToEndOfFile(base_path().'/app/Http/routes.php', "\n".$addroute."\n", 0, true);
    }

    protected function getColumns($tablename) {
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

    protected function getRelatedObjDataFK($tablename, $fkName) {
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

    protected function getRelatedObjData($tablename) {
        $dbname = DB::connection()->getDatabaseName();

        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $dbname)
            ->where('TABLE_NAME', $tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }

    protected function getReferencedObjData($tablename) {
        $dbname = DB::connection()->getDatabaseName();

        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $dbname)
            ->where('REFERENCED_TABLE_NAME', $tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }

    protected function getRelatedTableName($obj) {
        $patterns = ['/' . $obj->TABLE_NAME . '_/', '/_foreign/'];
        $replacements = ['', ''];
        $fkTableName = preg_replace($patterns, $replacements, $obj->CONSTRAINT_NAME);

        return $fkTableName;
    }

    protected function getTypeFromDBType($dbtype) {
        if(str_contains($dbtype, 'varchar')) { return 'text'; }
        if(str_contains($dbtype, 'int') || str_contains($dbtype, 'float')) { return 'number'; }
        if(str_contains($dbtype, 'date')) { return 'date'; }
        if(str_contains($dbtype, 'text')) { return 'textarea'; }

        return 'unknown';
    }

    protected function createModel($modelname, $prefix, $table_name) {

        Artisan::call('make:model', ['name' => $modelname]);

        $this->appendUseDb(app_path().'/'.$modelname.'.php');

        if($table_name) {
            $this->output->info('Custom table name: '.$prefix.$table_name);
            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$table = '".$table_name."';\n\n}", 2, true);
        }

        $columns = $this->getColumns($prefix.($table_name ?: strtolower(str_plural($modelname))));

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

    protected function appendAttributes($modelname, $table_name, $columName) {
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

    protected function addAppends($modelname, $table_name) {
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

    protected function addFillable($modelname, $fields) {
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

    protected function appendBelongTo($modelname, $referencedName) {
        $referencedClassName = ucwords($referencedName);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public function $referencedName()\n    {\n        return \$this->belongsTo('App\\$referencedClassName');\n    }\n", 0, true);
    }

    protected function appendHasMany($modelname, $relatedName) {
        $relatedClassName = ucwords($relatedName);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public function $relatedName()\n    {\n        return \$this->hasMany('App\\$relatedClassName');\n    }\n", 0, true);
    }

    protected function deletePreviousFiles($tablename) {
        $todelete = [
                app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php',
                base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/show.blade.php',
            ];

        $todelete[] = app_path().'/'.ucfirst(str_singular($tablename)).'.php';

        foreach($todelete as $path) {

            if(file_exists($path)) { 
                unlink($path);    
                $this->output->info('Deleted: '.$path);
            }   
        }
    }

    protected function appendToEndOfFile($path, $text, $remove_last_chars = 0, $dont_add_if_exist = false) {
        $content = file_get_contents($path);

        if(!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content)-$remove_last_chars).$text;
            file_put_contents($path, $newcontent);    
        }
    }

    protected function appendUseDb($path) {
        $lines = file($path);
        $lines[2] .= "\n";
        $lines[3] = "use DB;\n";

        file_put_contents($path, implode('', $lines));
    }
}
