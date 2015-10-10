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


## Tecnical features

* Works with php >= 5.3, best with >= 5.4

### External libraries

#### Javascript/CSS

* [jQuery v. 1.11.2 & v. 2.1.3](http://jquery.com/)
* [Twitter Bootstrap v.3.3.5](http://getbootstrap.com/)
* [Datepicker for Bootstrap](http://www.eyecon.ro/bootstrap-datepicker/)
* [DataTables v. 1.9.4](https://datatables.net/)
* [FineUploader 3.8.2](http://fineuploader.com/)
* [Fancybox v. 2.1.4](http://fancyapps.com/fancybox/)
* [Nestable](https://github.com/dbushell/Nestable)
* [Pnotify v. 2.0.1](https://github.com/sciactive/pnotify)
* [google-code-prettify](https://code.google.com/p/google-code-prettify/)
* [select2 v. 3.4.8](http://ivaynberg.github.io/select2/)
* [tinyMCE v. 4.2](http://www.tinymce.com/)

#### PHP

* [RedBeanPHP v. 4.3.0](http://www.redbeanphp.com/)
* [TWIG v. 1.15.1](http://twig.sensiolabs.org/)
* [FeedWriter](https://github.com/mibe/FeedWriter)
* [Mobile_Detect v. 2.8.0](https://github.com/serbanghita/Mobile-Detect)
* [Lessphp v. 1.7.0.2](https://github.com/oyejorge/less.php)
* [oaiprovider-php](https://github.com/jbogdani/oaiprovider-php)
* [OOCurl v. 0.3.0](https://github.com/jbogdani/oocurl)
