# larawesomecrud



###A CRUD generator for Laravel. Generates models, controller and views based on the database tables.
![laravel](https://cloud.githubusercontent.com/assets/8644532/22171498/74ab9b60-df5d-11e6-8e20-4617a38a8fec.png)

There are two frameworks supported at this point. These are bootstrap
Although this is usable code as it produces the models,controllers and views as promised its far from being done. Not all Materialize views are complete.

#Requirements

      Laravel 5.3 or higher
      Laravel collective
      Twig
        
      Bootstrap or
      MaterializeCSS

#Install

###Step 1

        composer require rascoop/laracreatecrud


###Step 2

Add your new provider to the providers array of config/app.php:

        CrudGenerator\CrudGeneratorServiceProvider::class,

###Step 3

Enjoy it.


#Usage

Use a table name, a list of table names as the input or just generate all the database.

###CRUD for all database

	php artisan create:crud all
	php artisan create:crud --all
	php artisan create:crud -a
	php artisan create:crud

###CRUD for one table

	php artisan create:crud table_1
	
###CRUD for a list of tables

	php artisan create:crud table_1,table_2,table_3 --only
	php artisan create:crud table_1,table_2,table_3 -o

###CRUD for all except for the tables in a given list

	php artisan create:crud table_1,table_2,table_3 --all-but
	php artisan create:crud table_1,table_2,table_3 -b

###Generate Form Requests

	php artisan create:crud table_1 --formrequest
	php artisan create:crud table_1 -r

###Add links to the dashboard menu

	php artisan create:crud table_1 --dashboard-menu
	php artisan create:crud table_1 -m

###To check all the options 

	php artisan help create:crud

###Custom Templates

###Use a custom layout

	php artisan create:crud all --master-layout=layouts.master 

###Customize your own templates

    php artisan vendor:publish

#Contact

GitHub: [rascoop](http://github.com/rascoop)
Twitter: [@rascoop](https://www.twitter.com/rascoop)

###This project is based on [larawesomecrud](https://github.com/SamyOteroGlez/larawesomecrud)

#License
**[MIT License](./LICENSE)**

[Richard Scoop](http://github.com/rascoop)

[SamyOteroGlez](http://github.com/SamyOteroGlez) & contributors

[bootstrap](http//www.getbootstrap.com)

[materialize-css](http://www.materializecss.com)