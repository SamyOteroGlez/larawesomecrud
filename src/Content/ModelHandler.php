<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Artisan;

/**
 * Description of ModelHandler
 *
 * @author tesa
 */
class ModelHandler
{
    public $name, $tableName;
    
    public function __construct($name = null, $tableName = null)
    {
        $this->name = $name;
        $this->tableName = $tableName;
    }
    
    public function newInstance($name = null, $tableName = null)
    {
        return new ModelHandler($name, $tableName);
    }
    
    /**
     * Create a new empty model.
     * 
     * @return $this
     */
    public function createModel()
    {
        Artisan::call('make:model', ['name' => $this->name]);
        
        return $this;
    }

    protected function createModel1($modelname, $prefix, $table_name)
    {
        $table_name = strtolower($modelname);
        $columns = $this->getColumns($prefix.$table_name);
        
        Artisan::call('make:model', ['name' => $modelname]);

        //change to config file path
        $this->appendUseDb(app_path().'/'.$modelname.'.php');        
        $this->output->info('Custom table name: '.$prefix.$table_name);
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$table = '".$table_name."';\n\n}", 2, true);
        
        $cc = collect($columns);

        $this->addFillable($modelname, $cc);

        $this->addAppends($modelname, $table_name);

        //change to config file path
        if(!$cc->contains('name', 'updated_at') || !$cc->contains('name', 'created_at')) { 
            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n    public \$timestamps = false;\n\n}", 2, true);
        }

        //change to config file path
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

        //change to config file path
        $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "\n}", 0, false);

        $this->output->info('Model created, columns: '.json_encode($columns));

        return $columns;
    }
}
