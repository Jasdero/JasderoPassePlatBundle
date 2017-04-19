Passe-Plat Bundle
=================

The Passe-Plat Bundle is an order management system for Symfony 3 based on status oriented management 
rules and coupled with Google Drive (optional).

Features :
  - creation and edition of statuses
  - real-time status changes update your orders
  - automatic import and creation of orders from/to Google Drive
  - self-creating and organizing orders on Google Drive


  
Status oriented management
--------------------------

Principle
^^^^^^^^^
The Passe-Plat bundle is built on the principle that an order status (i.e. on hold, ready etc...)
depends on the statuses of the products it's made of. The product with the most important status will define its order status.
Since it can be used in many ways, orders are considers as "containers" and products as its "content".

How-to
^^^^^^

All you have to do is to create some statuses and order them on the statuses main page. There you have 
a table which rows you can sort to establish your own ruling hierarchy.

Google Drive (optional)
-----------------------
Principle
^^^^^^^^^
Google Drive sheets are used to create containers and as a way to keep track of it.

How-to
^^^^^^
Create containers directly on the drive or directly from your platform. There is a button to scan new containers
from the drive in the bundle. On content update, both local and drive containers are updated. However when you modify the hierarchy of your statuses,
the tasks are separated since google requests may take a long time. A page is dedicated to this action.

Requirements
------------
 - `Jquery`_
 - `Table Sorter plugin`_
 - `Row Sorter plugin`_
 - The templates were built using `Materialize`_

Don't forget to put the scripts in your ``base.html.twig`` as it is extended by the bundle and make to have a ``{% block body %}`` inside.

Example :
 .. code-block:: html

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script
                src="http://code.jquery.com/jquery-3.1.1.min.js"
                integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
        <script src="{{ asset('js/RowSorter.js') }}"></script>
        <script src="{{ asset('js/jquery.tablesorter.min.js') }}"></script>


Configuration
-------------
Bundle
^^^^^^

Step 1 : download the bundle
""""""""""""""""""""""""""""

Begin by adding the following line to ``composer.json`` :

 .. code-block:: json

            "minimum-stability" : "dev",


This is required to allow the latest version of FOSUserBundle.

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

 .. code-block:: terminal

    $ composer require jasdero/passe-plat-bundle

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Or directly from GitHub : `Source code`_

Step 2 : enable the bundle
""""""""""""""""""""""""""

Then, enable the bundle by adding it to the list of registered bundles
in the ``app/AppKernel.php`` file of your project:

.. code-block:: php

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


Step 3 : configure the bundle
"""""""""""""""""""""""""""""

Open the ``config.yml`` file of your project and put the following lines with your values corresponding to the folders on the drive
(see next section):

.. code-block:: yml

        # app/config/config.yml

        jasdero_passe_plat:
            activation: true  # mandatory, determines if you want to use Google Drive (other option is 'false')

            # necessary if you set activation to true

            folders :
                to_scan: yourValue  # where new orders will be put
                new_orders: yourValue  # transition folder for new orders
                errors: yourValue  # where invalid orders will be redirected
            drive_folder_as_status:
                root_folder: yourValue  # base folder from where you want to work on your Drive
            credentials:
                path_to_refresh_token : "%path_to_refresh_token%"
                auth_config : "%auth_config%"

        # the following lines determine what name you want to give to your container and content

        twig:
            globals:
                container: yourValue # i.e. Order
                content: yourValue # i.e. Products


Update your ``parameters.yml`` if you activated Drive :

.. code-block:: yml

        # app/config/parameters.yml
            # other parameters

            path_to_refresh_token: yourPath # i.e. myProject/vendor/refreshToken.json
            auth_config: yourPath # i.e. myProject/vendor/clientSecret.json

For security purposes, it is strongly advised that your ``path_to_refresh_token`` and ``auth_config`` parameters point to a
non-shared location (in your ``Vendor`` folder for example).

Since the bundle uses `FOSUserBundle`_
you also need to configure your app accordingly.
Please note that this bundle provides a User table if you don't want/need to create a custom one . To extend it, just put the following line while
configuring FOSUser.



.. code-block:: yml

        fos_user:

            user_class: Jasdero\PassePlatBundle\Entity\User #this is the passe-plat basic user class

You also need to activate the `Knp Paginator Bundle`_


Step 4 : importing routes
"""""""""""""""""""""""""

Open your ``app/config/routing.yml`` and copy the following lines :

.. code-block:: yml

        passe-plat-bundle:
            resource: "@JasderoPassePlatBundle/Controller"
            type:     annotation

Please note that all routes are under the ``/admin`` prefix so you will need the according rights to access it.

Step 5 : generate the tables
""""""""""""""""""""""""""""

Generate the tables for the bundle :

.. code-block:: terminal

        $ php bin/console doctrine:schema:update --force


Step 6 : Installing assets
""""""""""""""""""""""""""

To install assets, type the following command :

.. code-block:: console

        $ php bin/console assets:install


Then activate it in your base layout :

.. code-block:: html

        <link rel="stylesheet" href="{{ asset('bundles/jasderopasseplat/css/admin.css') }}">
        <script src="{{ asset('bundles/jasderopasseplat/js/main.js') }}"></script>


If you don't need Google Drive, then you're ready to start. Go to the last section.

Google Drive
^^^^^^^^^^^^
`Reference`_

Step 1 : Google configuration
"""""""""""""""""""""""""""""

Create a Google Account if you don't have one yet.
Then you `activate the Drive API`_  for your application.
After that you need to `create credentials`_
and configure the redirect URI. By defaults it is the "/checked" and "/admin/drive/index" routes in the bundle (for example during dev it is "http://localhost:8000/app_dev.php/admin/drive/index"
 AND "http://localhost:8000/app_dev.php/checked").
Once you have downloaded your credentials, put it in the path you declared as ``auth_config``.

Step 2 : Create the base folders
""""""""""""""""""""""""""""""""

Go to your Google Drive and create the root folder for your application.
Inside create 3 more folders : one that will be scanned by your app (the ``folder_to_scan``), another one for the newly registered
containers (``new_orders_folder``) and lastly one for invalid containers (``errors_folder``).
Just be sure that you enter the same values as in your ``config.yml``.

Step 3 : Order format
"""""""""""""""""""""

On the first row as column titles : user | products and eventually comments.
On following rows : the user mail | catalog ID and eventually comments.
The user needs to be registered in your platform so that the order is valid.
You MUST keep this format and titles for the scan to work.


Example :

+-----------------+----------+-----------+
| user            | products | comments  |
+=================+==========+===========+
| gmail@gmail.com | 1        | something |
+-----------------+----------+-----------+
|                 | 2        |           |
+-----------------+----------+-----------+
|                 | 4        |           +
+-----------------+----------+-----------+


Recommendations
"""""""""""""""
Be aware that if you change anything in your Google Drive Api configuration you MUST download credentials again as those will be
different.


Using the bundle
----------------

Settings : this is where you start
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

All these features are under the "Settings" menu.

Start with statuses : click on "Settings" and "Statuses". Click on the cross to create your first status ! Fill in the blanks and add your own management rules. When you have some, go back
to the statuses index and order them from the most important to the least. Don't forget to valid.

Secondly :  categories. It is optional, but you should create some categories so that your catalog entries will be organized.
To do so, follow the same procedure under the "Categories" entry of the "Settings" menu.

Then, you can then add some VAT rates if you need. These are optional but should be useful for business needs.

Finally, go to the "Catalog" entry. You can now create your entries with all necessary data.

Operational
^^^^^^^^^^^

When everything is done, you are ready to create your first containers, with contents provided from the catalog, locally or on the Drive, or both.

You will notice that you can add comments on containers and content.


.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`Reference`: https://developers.google.com/api-client-library/php/auth/web-app
.. _`activate the Drive API`: https://console.developers.google.com/apis/library
.. _`create credentials`: https://console.developers.google.com/projectselector/apis/credentials
.. _`Jquery`: http://code.jquery.com/
.. _`Table Sorter plugin`: http://tablesorter.com/docs/#Download
.. _`Row Sorter plugin`: http://www.jqueryscript.net/table/jQuery-Plugin-For-Drag-n-Drop-Sortable-Table-RowSorter-js.html
.. _`Materialize`: http://materializecss.com/getting-started.html
.. _`FOSUserBundle`: https://symfony.com/doc/master/bundles/FOSUserBundle/index.html
.. _`Source code` : https://github.com/Jasdero/JasderoPassePlatBundle
.. _`Knp Paginator Bundle` : https://github.com/KnpLabs/KnpPaginatorBundle
