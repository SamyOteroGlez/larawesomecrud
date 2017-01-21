<?php

namespace CrudGenerator\Console\Commands;

use Illuminate\Container\Container;
use Illuminate\Console\Command;
use DB;
use Artisan;
use Psy\Exception\ErrorException;

class CrudGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud
        {model-name=all : Name of the model. In some cases you may enter a list (firstModel,secondModel,finalModel)}
        {--a|--all : Generate all the models}
        {--o|--only : Generate all the models in the list}
        {--b|--all-but : Generate all the models except for the ones in the list}
        {--r|--formrequest : Generates the form request}
        {--f|--force : Force to generate the CRUD}
        {--s|--singular : Use singular names}
        {--table-name= : Generate for a particular table name}
        {--master-layout= : Use a particular layout}
        {--custom-controller= : Generate the views and the controller only}
        {--black-list : Show the ignored tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create fully functional CRUD code based on a mysql table instantly';

    private $blacklist, $modelname, $prefix, $custom_table_name, $custom_controller, $singular;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->blacklist = [
            'migrations',
            'password_resets',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->modelname = strtolower($this->argument('model-name'));
        $this->prefix = \Config::get('database.connections.mysql.prefix');
        $this->custom_table_name = $this->option('table-name');
        $this->custom_controller = $this->option('custom-controller');
        $this->singular = $this->option('singular');
        $this->formrequest = false;
        $this->info('');

        if('black-list' == $this->option('black-list')) {
            $this->comment('This tables are excluded: ');
            $this->table([], array ($this->blacklist));

            return false;
        }

        $pretables = json_decode(json_encode(DB::select("show tables")), true);
        $tables = [];
        $tocreate = [];

        foreach($pretables as $p) {
            list($key) = array_keys($p);
            $tables[] = $p[$key];
        }

        if('all' == $this->option('all') || 'all' == $this->modelname) {
            $this->info("Generate all models");
            $this->info("List of tables: ".implode($tables, ","));

            $tocreate = $this->generateModelList($tables);

            // Remove options not applicable for multiples tables
            $this->resetValuesToNull();
        }
        elseif('all-but' == $this->option('all-but')) {
            $allBut = explode(",", $this->modelname);
            $this->info("Generate all models but this: ".implode($allBut, ","));
            $this->info("List of tables: ".implode($tables, ","));

            $tocreate = $this->generateModelList($tables, $allBut);

            // Remove options not applicable for multiples tables
            $this->resetValuesToNull();
        }
        elseif('only' == $this->option('only')) {
           $only = explode(",", $this->modelname);
           $this->info("Generate only this models: ".implode($only, ","));
           $this->info("List of tables: ".implode($tables, ","));

           $tocreate = $this->generateModelList($only);

            // Remove options not applicable for multiples tables
            $this->resetValuesToNull();
        }
        elseif('black-list' == $this->option('black-list')) {
            $this->comment('This tables are excluded: ');
            $this->table(['Table Name'], $this->blacklist);
        }
        else {
            $this->info("Generate the model: ".$this->modelname);
            $tocreate = [
                'modelname' => $this->modelname,
                'tablename' => '',
            ];

            if($this->singular) {
                $tocreate['tablename'] = strtolower($this->modelname);
            }
            else if($this->custom_table_name) {
                $tocreate['tablename'] = $this->custom_table_name;
            }
            $tocreate = [$tocreate];
        }

        if('formrequest' == $this->option('formrequest')) {
            $this->formrequest = true;
        }

        foreach ($tocreate as $c) {
            $generator = new \CrudGenerator\CrudGeneratorService();
            $generator->output = $this;

            $generator->appNamespace = Container::getInstance()->getNamespace();
            $generator->modelName = ucfirst($c['modelname']);
            $generator->tableName = $c['tablename'];
            $generator->formRequest = $this->formrequest;
            $generator->prefix = $this->prefix;
            $generator->force = $this->option('force');
            $generator->layout = $this->option('master-layout');
            $generator->controllerName = ucfirst(strtolower($this->custom_controller)) ?: str_plural($generator->modelName);

            $generator->Generate();
        }
    }

    private function generateModelList($tables, $blacklist = null) {
        $tocreate = [];

        foreach ($tables as $t) {

            if(!$this->excludeToGenerate($t, $blacklist)) {
                // Ignore tables with different prefix
                if($this->prefix == '' || str_contains($t, $this->prefix)) {
                    $t = strtolower(substr($t, strlen($this->prefix)));
                    $toadd = ['modelname'=> str_singular($t), 'tablename'=>''];

                    if(str_plural($toadd['modelname']) != $t) {
                        $toadd['tablename'] = $t;
                    }
                    $tocreate[] = $toadd;
                }
            }
        }

        return $tocreate;
    }

    private function excludeToGenerate($name, $blacklist = null) {

        $ignore = $this->blacklist;

        if(!is_null($blacklist)) {
            $ignore = array_merge($ignore, $blacklist);
        }

        foreach($ignore as $element) {

            if($name == $element) {
                return true;
            }
        }

        return false;
    }

    private function resetValuesToNull() {
        // Remove options not applicabe for multiples tables
        $this->custom_table_name = null;
        $this->custom_controller = null;
        $this->singular = null;
    }
}




















