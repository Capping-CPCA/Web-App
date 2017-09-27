# CPCA Web Application

### Project Structure

**Directories**
* `core/` - Contains key components of the PHP web app
* `models/` - Any sort of model that is reused
* `public/` - Where the application is run from
* `views/` - Location of all the application's pages

**Files**
* `config.php` - Global configuration variable initialization
    * the `define('BASEURL', '');` should be changed to whatever the server sees as the base URL

* `bootstrap.php` - Run when the app first loads
* `routes.php` - A collection of the app's routes

### Development Help

*See the [Web-App wiki](https://github.com/Capping-CPCA/Web-App/wiki)* 