# BraDyCMS

### A highly customizable, easy to setup & use php (>=5.3) Content Managing System, created for designers

## Features:

* Five seconds auto install
* Multi-user administrator backend
* Multi-language administrator backend
* System translation integrated tool
* Search engines fiendly URLs
* Fully responsive backend, for optimal usage in mobile devices (smartphones and tablets)
* Organize content in articles & sections
* Create custom menus with submenus
* Organize menus with drag&drop
* Mobile theme support
* Multilanguage content
* Customizable layout, both Desktop and Mobile
* Easy to build image slideshows, galleries and videos
* Lots of prebuild custom tags for easy integration of web services
* Easy integration social network
* Fully customizable OAI-PMH interface for metadata exposing
* User plugin system for easy expansion
* Multiple database support: MySQL, SQLite (default), PostgreSQL, CUBRID, etc.
* **Rich built in documentation**

# System requirements

* PHP >= 5.3
* PHP PDO
* PHP pdo_sqlite (for default SQLite database)
* Apache mod_rewrite enabled
* Imagemagick or Imagick for image manipulation (optional)


# Installation guide
A five-step guide to install and get running BraDyCMS

1. [Fork](https://github.com/jbogdani/BraDyCMS/) or [Download](https://github.com/jbogdani/BraDyCMS/archive/master.zip) from Github
2. Unzip everything in a web accesible directory (eg. `/var/www/bduscms` accessible at `http://localhost/bduscms`)
3. (Eventually edit the second line of `.htaccess` file to match your installation path, if not installing in root directory, eg: `RewriteBase /bduscms`)
4. Point your browser to the web path (eg. `http://localhost/bduscms`) and follow the instructions:

> 4.1 Enter a valid email address and  password  
> 4.2 Click on "Create my site"  
> 4.3 Read all the instructions  and  "Go to login page"   
> 4.4 Login
5. Update your site's information and metadata, add some articles, write a template, [report all issues](https://github.com/jbogdani/BraDyCMS/issues) you might have

**That's all! Enjoy!**

## Websites already using BraDyCMS

### Version 3.x
* [belightproject.com](http://belightproject.com)
* [bradypus.net](http://bradypus.net)
* [http://cliffyoungltd.com](http://cliffyoungltd.com)
* [costruzioniartigiane.it](http://costruzioniartigiane.it)
* [e-review.it](http://e-review.it)
* [fabriziolapalombara.com](http://fabriziolapalombara.com)
* [iraq.routes-assn.org](http://iraq.routes-assn.org)
* [medantico.org](http://medantico.org/)
* [pastandpresent.al](http://pastandpresent.al)
* [santamariainportuno.it](http://www.santamariainportuno.it)
* [tozziindustries.com] (http://tozziindustries.com)


### Comming soon (Version 3.x)
* [artemusa.it]
* ghazni-project
* Teatro Masini

## Tecnical features

* Works with php >= 5.3, best with 5.4
* [RedBeanPHP](http://www.redbeanphp.com/) is used as main ORM, both for frontend and backend interfaces
* [TWIG](http://twig.sensiolabs.org/) is used as template engine, both for frontend and backend templates
* [Twitter Bootstrap](http://getbootstrap.com/) framework is used for backend controle panel
* [Composer](http://getcomposer.org/) is used as dependency manager for external libraries
* [Swift](http://swiftmailer.org) is used as for system emails
