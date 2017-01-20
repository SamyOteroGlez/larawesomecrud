# larawesomecrud

CRUD generator for Laravel. Generates models, controller and views based on the database tables. This project is a fork of kEpEx/laravel-crud-generator from Alfredo Aguirre (alfrednx@gmail.com).


###Installing

	to do


Add to config/app.php the following line to the 'providers' array:

    CrudGenerator\CrudGeneratorServiceProvider::class,


###Usage

Use the desired model name as the input 


Generate CRUD for one table

	php artisan make:crud table_1

Generate CRUD for all database

	php artisan make:crud all
	php artisan make:crud --all
	php artisan make:crud -a
	php artisan make:crud
	
Generate CRUD for a list of tables

	php artisan make:crud table_1,table_2,table_3 --only
	php artisan make:crud table_1,table_2,table_3 -o

Generate CRUD for all except for the lables in a given list

	php artisan make:crud table_1,table_2,table_3 --all-but
	php artisan make:crud table_1,table_2,table_3 -b

Generate CRUD for all database with a custom layout

	php artisan make:crud all --master-layout=layouts.master 

Use singular names

	php artisan make:crud table_1 --singular
	php artisan make:crud table_1 -s

For more options 

	php artisan help make:crud

###Custom Templates

You can customize your own templates

Run this command:

    php artisan vendor:publish

and you will have now in resources/templates/ the files you need to modify

If you want to go back to the default, just delete them.

