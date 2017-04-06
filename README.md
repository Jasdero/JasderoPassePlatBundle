Passe-Plat Bundle
=============

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5c2963ad-8776-4d88-9efd-1b94026e7f10/mini.png)](https://insight.sensiolabs.com/projects/5c2963ad-8776-4d88-9efd-1b94026e7f10)

The Passe-Plat Bundle is an order management system for Symfony 3 based on status oriented management 
rules and coupled with Google Drive.

Features :
  * creation and edition of statuses
  * real-time status changes update your orders
  * automatic import and creation of orders from/to Google Drive
  * self-creating and organizing orders on Google Drive 
  
  
  
### Status oriented management

#### Principle
The Passe-Plat bundle is built on the principle that an order status (i.e. on hold, ready etc...)
depends on the statuses of the products it's made of. The product with the most important status will define its order status.
#### Use
All you have to do is to create some statuses and order them on the statuses main page. There you have 
a table which rows you can drag'n'drop in the order you want.


### Google Drive 
#### Principle
Google Drive sheets are used to create orders and as a way to keep track of it.

#### Use
Create orders directly on the drive or directly from your platform. There is a button to scan new orders 
from the drive in the bundle. On product update, both local and drive orders are updated. However when you modify the hierarchy of your statuses,
the tasks are separated since google requests may take a long time. A page is dedicated to this action.
### Requirements
* [Jquery](http://code.jquery.com/)
* [Table Sorter plugin](http://tablesorter.com/docs/#Download)
* [Row Sorter plugin](http://www.jqueryscript.net/table/jQuery-Plugin-For-Drag-n-Drop-Sortable-Table-RowSorter-js.html)
* The templates were built using [Materialize](http://materializecss.com/getting-started.html)

Don't forget to put the scripts in your `base.html.twig` as it is extended by the bundle and make sure to have a `{% block body %}` inside.


### Configuration
#### Bundle

##### Step 1 : download the bundle

Begin by adding the following line to ``composer.json`` : 
```json
            "minimum-stability" : "dev",
```

This is required to allow the latest version of FOSUserBundle.


Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:
```console
$ composer require jasdero/passe-plat-bundle
```
This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Or directly from GitHub : [Source code](https://github.com/Jasdero/JasderoPassePlatBundle)
##### Step 2 : enable the bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Jasdero\PassePlatBundle\JasderoPassePlatBundle(),
        );
        // ...
    }
    // ...
}
```

##### Step 3 : configure the bundle

Open the `config.yml` file of your project and put the following lines with your values corresponding to the folders on the drive
(see next section):
```yml
# app/config/config.yml

parameters:
    # other parameters
    jasdero_passe_plat.folder_to_scan: yourValue # i.e. RepoFolder
    jasdero_passe_plat.new_orders_folder: yourValue # i.e. NewOrders
    jasdero_passe_plat.errors_folder: yourValue # i.e. Errors
    

jasdero_passe_plat:
    drive_connection:
        path_to_refresh_token: "%path_to_refresh_token%"
        auth_config: "%auth_config%"
    drive_folder_as_status:
        root_folder: "%root_folder%"
        
    # the following lines determine what name you want to give to your container and content
twig:
    globals:
        container: yourValue # i.e. Order
        content: yourValue # i.e. Products
```

Update your `parameters.yml` accordingly :
```yml
# app/config/parameters.yml
    # other parameters
    
    path_to_refresh_token: yourPath # i.e. myProject/vendor/refreshToken.json
    auth_config: yourPath # i.e. myProject/vendor/clientSecret.json
    root_folder: yourValue # i.e. MyApp
```
For security purposes, it is strongly advised that your `path_to_refresh_token` and `auth_config` parameters point to a non-shared location
 (in your `Vendor` folder for example).
 
 Since the bundle uses [FOSUserBundle](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html)
 you also need to configure your app accordingly.
 Please note that this bundle provides a User table if you don't want/need to create a custom one . To extend it, just put the following line while
 configuring FOSUser.
 
 
```yml
fos_user:
        user_class: Jasdero\PassePlatBundle\Entity\User #this is the passe-plat basic user class
```

 You also need to activate the [Knp Paginator Bundle](https://github.com/KnpLabs/KnpPaginatorBundle)


##### Step 4 : importing routes

Open your `app/config/routing.yml` and copy the following lines :

```yml
passe-plat-bundle:
    resource: "@JasderoPassePlatBundle/Controller"
    type:     annotation
```
Please note that all routes are under the ``/admin`` prefix so you will need the according rights to access it.


##### Step 5 : Generate the tables

Generate the tables for the bundle :

 ```console
 $ php bin/console doctrine:schema:update --force
 ```
 
##### Step 6 : Installing assets

to install assets, type the following command :
 ```console
 $ php bin/console assets:install
 ```
 
Then activate it in your base layout :
```html
    <link rel="stylesheet" href="{{ asset('bundles/jasderopasseplat/css/admin.css') }}">
    <script src="{{ asset('bundles/jasderopasseplat/js/main.js') }}"></script>

```

#### Google Drive
[Reference](https://developers.google.com/api-client-library/php/auth/web-app)

##### Step 1 : Google configuration

Create a Google Account if you don't have one yet.
Then you [activate the Drive API](https://console.developers.google.com/apis/library) for your application.
After that you need to [create credentials](https://console.developers.google.com/projectselector/apis/credentials)
and configure the redirect URI. By defaults it is the "/checked" and "/admin/checking" routes in the bundle (for example during dev it is "http://localhost:8000/app_dev.php/admin/checking" 
AND "http://localhost:8000/app_dev.php/checked").
Once you have downloaded your credentials, put it in the path you declared as `auth_config`.

##### Step 2 : Create the base folders

Go to your Google Drive and create the root folder for your application.
Inside create 3 more folders : one that will be scanned by your app (the `folder_to_scan`), another one for the newly registered
orders (`new_orders_folder`) and lastly one for invalid orders (`errors_folder`).
Just be sure that you enter the same values as in your `config.yml`.

##### Step 3 : Order format

On the first row as column titles : user | products and eventually comments.
On following rows : the user mail | catalog ID and eventually comments.
The user needs to be registered in your platform so that the order is valid.

Example :

user | products | comments
--- | --- | ---
mail@mail.com | 1 | something
 <empty> | 2 | <empty>
 <empty> | 4 | <empty>

##### Recommendations
Be aware that if you change anything in your Google Drive Api configuration you MUST download credentials again as those will be
different.
