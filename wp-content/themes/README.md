# Unity WP Website Starter Template

## Local Dev Environment

Create new project in Local by Flywheel:
* Set site name, local dev URL, and project directory
* Choose custom environment:
  * PHP Version >= 7.0.0
  * Web server = Apache
  * MySQL >= 5.5

## Git Repo

Clone the Starter Template repo without big commit history

````shell
# @ app/public/
$ git clone --depth 1 https://github.com/unitymakesus/starter-template.git
````

Remove .git files

````shell
# @ app/public/
$ rm -rf starter-template/.git
````


Move Starter Template files into project directory

````shell
# @ app/public/
$

## Install Sage

Make sure all dependencies have been installed:

* [PHP](http://php.net/manual/en/install.php) >= 5.6.4
* [Composer](https://getcomposer.org/download/)
* [Node.js](http://nodejs.org/) >= 6.9.x
* [Yarn](https://yarnpkg.com/en/docs/install)

Install Sage using Composer from your WordPress themes directory (replace `your-theme-name` below with the name of your theme):

```shell
# @ wp-content/themes/
$ composer create-project roots/sage your-theme-name dev-master
```

During theme installation you will have the options to:

* Do you want to remove the existing VCS (.git, .svn..) history? **Y**
* Theme Name: **Name Theme**
* Theme URI: **Github repo URL**
* Theme Description: **Custom WordPress theme for [Client]**
* Theme Version: **Sage version**
* Theme Author: **Unity**
* Theme URI: **https://www.unitymakes.us/**
* Local development URL of WP site: **Dev URL set when creating site in Local**
* Path to theme directory: **/wp-content/themes/[your-theme-name]**
* Which framework would you like to load? **[0] None**
* Do you want to install Font Awesome? **no**
* Are you sure you want to overwrite the following files? (this just removes the framework-related SCSS files) **yes**
* Update theme headers (theme name, description, author, etc.)
* Select a CSS framework (Bootstrap, Foundation, Tachyons, none)
* Add Font Awesome
* Configure Browsersync (path to theme, local development URL)

From the command line on your host machine (not on your Vagrant box), navigate to the theme directory then run `yarn`:

```shell
# @ themes/your-theme-name/
$ yarn
```
