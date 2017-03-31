<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use DB;

/**
 * Description of DatabaseHandler
 *
 * @author tesa
 */
class DatabaseHandler
{
    protected $driver, $dbname, $tablename, $columns;
    
    /**
     * Constructor.
     * 
     * @param type $dbname
     * @param type $tablename
     */
    public function __construct($tablename)
    {
        $this->driver = DB::getDriverName();
        $this->dbname = DB::connection()->getDatabaseName();
        $this->tablename = $tablename;
    }

    /**
     * Get new instance of DatabaseHandler class.
     * 
     * @param type $dbname
     * @param type $tablename
     * @return \DatabaseHandler
     */
    public function newInstance($dbname, $tablename)
    {
        return new DatabaseHandler($dbname, $tablename);
    }
    
    /**
     * Get the column name of the table to generate the model. Get the column type to generate the form fields.
     *
     * @return array
     */
    public function getColumns()
    {
        $columns = $this->getTableColumnsBasedOnTheDriver()
            ->setColumTypesForGeneration();

        return $columns;
    }
    
    /**
     * Set the columns types to be used later during the generation process. Based on each column a field type 
     * can be generated in the forms.
     * 
     * @return type
     */
    protected function setColumTypesForGeneration()
    {
        $ret = [];

        foreach ($this->columns as $colum) {
            $field = isset($colum->Field) ? $colum->Field : $colum->field;
            $type = isset($colum->Type) ? $colum->Type : $colum->type;
            
            $cadd = [];
            $cadd['name'] = $field;

            $related = $this->getRelatedObjDataFK($cadd['name']);
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
     * Set the driver been use in the application.
     * 
     * @return $this
     */
    protected function setUsedDriver()
    {
        $this->driver = DB::getDriverName();
        
        return $this;
    }
    
    /**
     * Get the driver been use in the application.
     * 
     * @return type
     */
    public function getUsedDriver()
    {
        return $this->driver;
    }
    
    /**
     * Get the database name.
     * 
     * @return type
     */
    public function getDatabaseName()
    {
        return $this->dbname;
    }
    
    /**
     * Get the table name.
     * 
     * @return type
     */
    public function getTablenaName()
    {
        return $this->tablename;
    }

    /**
     * Get the columns.
     * 
     * @return type
     */
    public function getTableColumns()
    {
        return $this->columns;
    }
    
    /**
     * Get the table columns based on the used driver.
     * 
     * @return $this
     */
    protected function getTableColumnsBasedOnTheDriver()
    {
        switch ($this->driver) {
            
            case "pgsql":
                $this->columns = $this->queryForPgsqlDriver();
                break;

            default:
                $this->columns = $this->queryForSqlDriver();
                break;
        }
        
        return $this;
    }

    /**
     * Execute the query for Sql driver.
     * 
     * @param type $tablename
     * @return type
     */
    protected function queryForSqlDriver()
    {
        $query = DB::select("show columns from " . $this->tablename);
        
        return $query;
    }
    
    /**
     * Execute the query for Pgsql driver.
     * 
     * @return type
     */
    protected function queryForPgsqlDriver() 
    {
        $query = DB::select("select column_name as Field, "
                                . "data_type as Type, "
                                . "is_nullable as Null "
                                . "from INFORMATION_SCHEMA.COLUMNS "
                                . "where table_name = '" . $this->tablename . "'");
        
        return $query;
    }
    
    /**
     * Get the related table data, the foreingkey table data.
     *
     * @param $fkName
     * @return mixed
     */
    protected function getRelatedObjDataFK($fkName)
    {
        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $this->dbname)
            ->where('TABLE_NAME', $this->tablename)
            ->where('CONSTRAINT_NAME', $this->tablename . '_' . $fkName . '_foreign')
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return reset($result);
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
     * Get the related table name and data.
     *
     * @return mixed
     */
    protected function getRelatedObjData()
    {
        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $this->dbname)
            ->where('TABLE_NAME', $this->tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }
    
    /**
     * Get the referenced table name and data.
     *
     * @return mixed
     */
    protected function getReferencedObjData()
    {
        $result = DB::table('INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS')
            ->select('*')
            ->where('CONSTRAINT_SCHEMA', $this->dbname)
            ->where('REFERENCED_TABLE_NAME', $this->tablename)
            ->where('UNIQUE_CONSTRAINT_NAME', 'PRIMARY')
            ->get();

        return $result;
    }
}
