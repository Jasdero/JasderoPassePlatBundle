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
Know that through this actions all concerned orders will be updated on your platform as well as moved to the right
folders on Google Drive.

### Google Drive 
#### Principle
Google Drive sheets are used to create orders and as a way to keep track of it.

#### Use
Create orders directly on the drive or directly from your platform. There is a button to scan new orders 
from the drive in the bundle. Whenever you update your statuses or your products, corresponding sheets
are moved to the right folders (if the folder doesn't exist it is created).

### Requirements
* [Jquery](http://code.jquery.com/)
* [Table Sorter plugin](http://tablesorter.com/docs/#Download)
* [Row Sorter plugin](http://www.jqueryscript.net/table/jQuery-Plugin-For-Drag-n-Drop-Sortable-Table-RowSorter-js.html)
* The templates were built using [Materialize](http://materializecss.com/getting-started.html)

Don't forget to put the scripts in your `base.html.twig` as it is extended by the bundle.
Since the bundle uses [FOSUserBundle](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html) 
you also need to configure your app accordingly.

### Configuration
#### Bundle

##### Step 1 : download the bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:
```console
$ composer require jasdero/passe-plat-bundle
```
This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Or directly from GitHub : [Source code](https://github.com/Jasdero/JasderoPassePlatBundle/tree/master/PassePlatBundle)
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
 
##### Step 4 : Generate the tables

Generate the tables for the bundle :

 ```console
 $ php bin/console doctrine:schema:update --force
 ```

#### Google Drive
[Reference](https://developers.google.com/api-client-library/php/auth/web-app)

##### Step 1 : Google configuration

Create a Google Account if you don't have one yet.
Then you [activate the Drive API](https://console.developers.google.com/apis/library) for your application.
After that you need to [create credentials](https://console.developers.google.com/projectselector/apis/credentials)
and configure the redirect URI. By defaults it is the "/auth/checked" route in the bundle (don't forget 
to put your domain ).
Once you have downloaded your credentials, put it in the path you declared as `auth_config`.

##### Step 2 : Create the base folders

Go to your Google Drive and create the root folder for your application.
Inside create 3 more folders : one that will be scanned by your app (the `folder_to_scan`), another one for the newly registered
orders (`new_orders_folder`) and lastly one for invalid orders (`errors_folder`).
Just be sure that you enter the same values as in your `config.yml`.

##### Recommendations
Be aware that if you change anything in your Google Drive Api configuration you MUST download credentials again as those will be
different.
