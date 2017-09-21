# CPCA Web Application

### Server Set Up

The PHP server should be run from the `public/` directory. This is where the `.htaccess` file and the `index.php` file are stored.
When using an Apache server, this would be the `DocumentRoot`.

**Location of the code**

The code is normally placed in the `htdocs/` folder of the Apache server instance.

*Note: It really doesn't matter the location of the code, as long as the `DocumentRoot` is pointing to the code location.*

**Update the DocumentRoot**

To change the `DocumentRoot` to use the `public/` folder, locate the `httpd.conf` file, which is usually located in
the `conf/` folder within the Apache server. Change the commented lines below to represent the path to the code's `/public`
folder.

```apacheconfig
# httpd.conf
 
...
 
DocumentRoot "path/to/base/directory/public"            # change this
<Directory "path/to/base/directory/public">             # change this
    ...
</Directory>
 
 
...
 
 
<VirtualHost 127.0.0.1>
    DocumentRoot "path/to/base/directory/public"        # change this
    ServerName 127.0.0.1
    <Directory "path/to/base/directory/public">         # change this
        Options FollowSymLinks Indexes ExecCGI
        AllowOverride All
        Order deny,allow
        Allow from 127.0.0.1
        Deny from all
        Require all granted
     </Directory>
</VirtualHost>
```


*Note: the `<VirtualHost>` is not always present in all Apache httpd configurations*

### Project Structure

**Directories**
* `core/` - Contains key components of the PHP web app
* `models/` - Any sort of model that is reused
* `public/` - Where the application is run from
* `shared/` - Files that are shared between views
* `views/` - Location of all the application's pages

**Files**
* `config.php` - Global configuration variable initialization
    * the `define('BASEURL', '');` should be changed to whatever the server sees as the base URL

* `bootstrap.php` - Run when the app first loads
* `routes.php` - A collection of the app's routes

### Creating Pages

**HTML/PHP Page Content**

Each page should follow the format below and placed in the `view/` directory.

```php
<?php include('header.php'); ?>

    <!-- Put Page Content Here -->

<?php include('footer.php'); ?>
```

If you want to make a page one that requires a user to log in, put `authorizedPage();` above the header include. Ex:

```php
<?php 
authorizedPage();
include('header.php'); 
?>

...

```

**Routes**

Each page has a specific route assigned to it. This is located in the `routes.php` file. 
To add a new route add a line to the file like the following:

```php
$router->add('/new-page', 'new-page.php', 'New Page Title');
```