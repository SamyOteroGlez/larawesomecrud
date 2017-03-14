<?php

namespace CrudGenerator\Console\Commands;

use DB;
use Artisan;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use CrudGenerator\CrudGeneratorService as Generator;

class CrudGeneratorCommand extends Command
{
    private $blacklist;
    private $modelname;
    private $prefix;
    private $custom_table_name;
    private $custom_controller;
    private $formrequest;
    private $dashboard;
    private $templateFolder;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:crud
        {model-name=all : Name of the model. In some cases you may enter a list (firstModel,secondModel,finalModel)}
        {--a|--all : Generate all the models}
        {--o|--only : Generate all the models in the list}
        {--b|--all-but : Generate all the models except for the ones in the list}
        {--r|--formrequest : Generates the form request}
        {--f|--force : Force to generate the CRUD}
        {--m|--dashboard-menu : Generates the links in the dashboard menu}
        {--t|--theme= : Use a specific base template to generate (materialize or bootstrap)}
        {--l|--master-layout= : Use a particular layout}
        {--c|--custom-controller= : Generate the views and the controller only}
        {--black-list : Show the ignored tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create fully functional CRUD code based on a mysql table instantly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->formrequest = false;
        $this->dashboard = false;

        /**
         * List of tables to ignore when generates the crud.
         */
        $this->blacklist = [
          'migrations',
          'password_resets',
        ];
        $this->templateFolder = 'materialize';
    }

    /**
     * @param null $objects
     * @return mixed|null
     */
    private function ArrayOfObjectsToArray($objects = null)
    {
        if ($objects == null) {
            return null;
        }
        return json_decode(json_encode($objects), true);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->showWelcomeMessage();

        $this->modelname = strtolower($this->argument('model-name'));
        $this->prefix = \Config::get('database.connections.mysql.prefix');
        $this->custom_controller = $this->option('custom-controller');


//        $this->info('Options: '. implode("; ",$this->options()));
//        $this->info('Arguments: '. implode("; ",$this->arguments()));

        $this->info('');

        if ('black-list' == $this->option('black-list')) {
            $this->commandBlackList();
            return false;
        }

        if ('dashboard-menu' == $this->option('dashboard-menu')) {
            $this->dashboard = true;
        }
//Check if customer template folder exists if not check if there is a custom theme to be used
        if (is_dir(base_path() . '/resources/templates/')) {
            $this->templateFolder = base_path() . '/resources/templates/';
        } else {
            $this->templateFolder = realpath(__DIR__ . '/../../Templates/materialize/');

            if ($this->option('theme')) {
                if (is_dir(realpath(__DIR__ . '/../../Templates/' . $this->option('theme')))) {
                    $this->templateFolder = realpath(__DIR__ . '/../../Templates/' . $this->option('theme'));
                }
            }
        }
        $this->Info('Template Folder:' . $this->templateFolder);

        $pretables = $this->ArrayOfObjectsToArray(DB::select("show tables"));

        $tables = [];
        $createThese = [];

        foreach ($pretables as $p) {
            list($key) = array_keys($p);
            //This makes sure not to include the pivot tables
            if (!strpos($p[$key], '_')) {
                $tables[] = $p[$key];
            }
        }

        if ('all' == $this->option('all') || 'all' == $this->modelname) {
            $createThese = $this->commandAll($tables);
        } elseif ('all-but' == $this->option('all-but')) {
            $createThese = $this->commandAllBut($tables);
        } elseif ('only' == $this->option('only')) {
            $createThese = $this->commandOnly($tables);
        } else {
            $modelName = $this->commandModelName();
            $createThese = [$modelName];
        }

        foreach ($createThese as $createThis) {

            $modelName = ucfirst($createThis['modelname']);
            $controllerName = ucfirst(strtolower($this->custom_controller)) ?: str_plural($modelName);
            $this->info('ControllerName: ' . $controllerName);
            if ('formrequest' == $this->option('formrequest')) {
                $this->formrequest = $modelName . 'FormRequest';
            }


            $generator = new Generator(
              $this,
              Container::getInstance()->getNamespace(),
              $modelName,
              $createThis['tablename'],
              $this->formrequest,
              $this->prefix,
              $this->option('force'),
              $this->option('master-layout'),
              $controllerName,
              $controllerName, // Api Controller This allows the use of a different name
              null,
              null,
              $this->dashboard,
              $this->templateFolder
            );

            $generator->Generate();
        }

        $this->showByeByeMessage();
        return;
    }

    /**
     * Command --black-list.
     * Show the black list.
     */
    private function commandBlackList()
    {
        $this->comment('These tables are excluded: ');
        $this->table([], array($this->blacklist));
    }

    /**
     * Command --all.
     * Set the list of models to generate. Return an array with the table names to generate.
     *
     * @param $tables
     *
     * @return array
     */
    private function commandAll($tables)
    {
        $this->info("Generate all models");
        $this->info("List of tables: " . implode($tables, ","));
        $tocreate = $this->generateModelList($tables);
        $this->resetValuesToNull();

        return $tocreate;
    }

    /**
     * Command --all-but.
     * Set the list of models to generate. Return an array with the table names to generate.
     *
     * @param $tables
     * @return array
     */
    private function commandAllBut($tables)
    {
        $allBut = explode(",", $this->modelname);
        $this->info("Generate all models but this: " . implode($allBut, ","));
        $this->info("List of tables: " . implode($tables, ","));
        $tocreate = $this->generateModelList($tables, $allBut);
        $this->resetValuesToNull();

        return $tocreate;
    }

    /**
     * Command --only.
     * Set the list of models to generate. Return an array with the model names.
     *
     * @param $tables
     * @return array
     */
    private function commandOnly($tables)
    {
        //
        //TODO if more than one model change text to these models
        //if ($this->modelname)
        //$this->info("Count ".);
        $this->info("Generate this model: " . $this->modelname . " only");
        $this->info("List of tables: " . implode($tables, ","));

        $only = explode(",", $this->modelname);
        $tocreate = $this->generateModelList($only);
        $this->resetValuesToNull();

        return $tocreate;
    }

    /**
     * Command model name.
     * Set the model name to generate. Return an array with the model name and the table name.
     *
     * @return array
     */
    private function commandModelName()
    {
        $this->info("----------------------------------------------------");
        $this->info("- Generate the model: " . $this->modelname);
        $tocreate = [
          'modelname' => $this->modelname,
          'tablename' => '',
        ];

        return $tocreate;
    }

    /**
     * Generate the model list. Return an array with the list of models to generate.
     *
     * @param $tables
     * @param null $blacklist
     *
     * @return array
     */
    private function generateModelList($tables, $blacklist = null)
    {
        $tocreate = [];

        foreach ($tables as $t) {

            if (!$this->excludeToGenerate($t, $blacklist)) {

                if ($this->prefix == '' || str_contains($t, $this->prefix)) {
                    $t = strtolower(substr($t, strlen($this->prefix)));
                    $toadd = ['modelname' => str_singular($t), 'tablename' => ''];

                    if (str_plural($toadd['modelname']) != $t) {
                        $toadd['tablename'] = $t;
                    }
                    $tocreate[] = $toadd;
                }
            }
        }

        return $tocreate;
    }

    /**
     * Remove from the list of tables to generate the tables in the black list. Return true if a table name is in the
     * blacklist, false if don't so it can be generated.
     *
     * @param $name
     * @param null $blacklist
     *
     * @return bool
     */
    private function excludeToGenerate($name, $blacklist = null)
    {
        $ignore = $this->blacklist;

        if (!is_null($blacklist)) {
            $ignore = array_merge($ignore, $blacklist);
        }

        foreach ($ignore as $element) {

            if ($name == $element) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove options not applicable for multiple tables.
     */
    private function resetValuesToNull()
    {
        $this->custom_table_name = null;
        $this->custom_controller = null;
        $this->singular = null;
    }

    /**
     * Set blacklist.
     *
     * @param $blacklist
     */
    public function setBlackListAttribute($blacklist)
    {
        $this->blacklist = $blacklist;
    }

    /**
     * Get blackilist.
     *
     * @return array
     */
    public function getBlackListAttribute()
    {
        return $this->blacklist;
    }

    /**
     * Set modelname.
     *
     * @param $modelname
     */
    public function setModelNameAttribute($modelname)
    {
        $this->modelname = $modelname;
    }

    /**
     * Get modelname.
     *
     * @return mixed
     */
    public function getModelNameAttribute()
    {
        return $this->modelname;
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
     * @return mixed
     */
    public function getPrefixAttribute()
    {
        return $this->prefix;
    }

    /**
     * Set custom_table_name.
     *
     * @param $custom_table_name
     */
    public function setCustomTableNameAttribute($custom_table_name)
    {
        $this->custom_table_name = $custom_table_name;
    }

    /**
     * Get custom_table_name.
     *
     * @return mixed
     */
    public function getCustomTableNameAttribute()
    {
        return $this->custom_table_name;
    }

    /**
     * Set custom_controller.
     *
     * @param $custom_controller
     */
    public function setCustomControllerAttribute($custom_controller)
    {
        $this->custom_controller = $custom_controller;
    }

    /**
     * Get custom_controller.
     *
     * @return mixed
     */
    public function getCustomControllerAttribute()
    {
        return $this->custom_controller;
    }

    /**
     * Set singular.
     *
     * @param $singular
     */
    public function setSingularAttribute($singular)
    {
        $this->singular = $singular;
    }

    /**
     * Get singular.
     *
     * @return mixed
     */
    public function getSingularAttribute()
    {
        return $this->singular;
    }

    /**
     * Show welcome message
     */
    protected function showWelcomeMessage()
    {
        $this->info('');
        $this->info('* Lets create some files!                            *');
        $this->info('****************************************************');
        $this->info('');
    }

    /**
     * Show bye message.
     */
    protected function showByeByeMessage()
    {
        $this->info('');
        $this->info('* Done creating files                              *');

        $this->info('****************************************************');
        $this->info('* Thanks for using laracreatecrud.                 *');
        $this->info('');
    }
}




















