# The template file tree

### Twig template files

Everything that concerns your web site is located in the `sites/default/` folder.


Here you will find all the template files, identified by the `.twig` extension.
These are basically [Twig](tmpl_twig) template files. You need at least one 
template file named **index.twig** to make the template system work normally, but
it is common to separate the different templates or templates parts in separate files to
enhance readability, using the Twig [`include` statement](http://twig.sensiolabs.org/doc/tags/include.html).

---

### Stylesheets

Stylesheet files, typically CSS and [LESS](tmpl_less) files are located
in the `sites/default/css/` folder. These files can be named in any way, 
but, in order to have full advantage of the LESS processing engine integrated in 
the BraDyCMS [built-in editor](tmpl_editor), a **strict rule** must be followed:
the LESS file **should** be named `styles.less` and this will be automatically compiled
and minified in the file `styles.css`.

Many other css and less files can be added, but only the styles.less file will be compiled. Anyway, if other css and less files 
are imported using the [`@import` directive](http://lesscss.org/features/#import-directives-feature)
inside the style.less file, these will be imported and automatically processed.

---

### Layout related images

There is not a standard place where all the image files related to the site layout (such as logos, background and other assets) are stocked. Anyway, to take full advantage of the BraDypUs image manager, these files should be placed somewhere inside the 
`sites/default/images/` folder. Typically, these images are located inside the `sites/default/images/css/` folder, to mantain them separate from the content-related images.

---

### Javascript

Javascript files are located in the `sites/default/js/` folder. These files can be named in any way, but, in order to have full advantage of the BraDyCMS [built-in editor](tmpl_editor), the main file **must be named** `frontend.js`.

Typically, there is no need to include here the main javascript libraries, such as jQuery, Twitter Bootstrap or Fancybox. These libraries are already present in the `js` folder of the site root. Anyway, a number of custom versions or other libraries can be placed in this folder.

---

### Typical file and folder tree

    + css/
      - bootstrap.less
      - styles.css
      - styles.less
    + images/
      + css/
      ...
    - index.twig
    + js/
      - frontend.js
