### larawesomecrud

![laravel](https://cloud.githubusercontent.com/assets/8644532/22171498/74ab9b60-df5d-11e6-8e20-4617a38a8fec.png)

###A CRUD generator for Laravel. Generates models, controller and views based on the database tables. 
This project is based on [kEpEx/laravel-crud-generator] (https://github.com/kEpEx/laravel-crud-generator) from
Alfredo Aguirre (alfrednx@gmail.com).

###Install

	composer require samyoteroglez/larawesomecrud


Next, add your new provider to the providers array of config/app.php:

    CrudGenerator\CrudGeneratorServiceProvider::class,


###Usage

Use a table name, a list of table names as the input or just generate all the database.

CRUD for all database

	php artisan make:crud all
	php artisan make:crud --all
	php artisan make:crud -a
	php artisan make:crud

CRUD for one table

	php artisan make:crud table_1
	
CRUD for a list of tables

	php artisan make:crud table_1,table_2,table_3 --only
	php artisan make:crud table_1,table_2,table_3 -o

CRUD for all except for the tables in a given list

	php artisan make:crud table_1,table_2,table_3 --all-but
	php artisan make:crud table_1,table_2,table_3 -b

Generate Form Requests

	php artisan make:crud table_1 --formrequest
	php artisan make:crud table_1 -r

Use singular names

	php artisan make:crud table_1 --singular
	php artisan make:crud table_1 -s

For more options 

	php artisan help make:crud

###Custom Templates

Use a custom layout

	php artisan make:crud all --master-layout=layouts.master 

Customize your own templates

    php artisan vendor:publish

###Contact
GitHub: [SamyOteroGlez](http://github.com/SamyOteroGlez)
Twitter: [@SamyOteroGlez](@SamyOteroGlez)

###License
**[MIT License](./LICENSE)**
[SamyOteroGlez](http://github.com/SamyOteroGlez) & contributors
