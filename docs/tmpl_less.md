# Styling the templates, CSS and LESS

As stated [in the template file tree docs](tmpl_files), you can use plain
CSS to style your templates, but mind that BraDyCMS has a built-in support for the LESS
language, which is an extension of the CSS syntax, that compiles into CSS.

All the [LESS official docs](http://lesscss.org/) state:
    Less is a CSS pre-processor, meaning that it extends the CSS language, adding features that allow variables, mixins, functions and many other techniques that allow you to make CSS that is more maintainable, themable and extendable.

You don't have to process the LESS files. If you follow the BraDyCMS conventions
for [LESS file names](tmpl_files) compiling the LESS file into CSS is
as simple as clicking a button. You can edit and compile LESS files directly in the
[built-in editor](tmpl_editor).

Please refer [the official LESS doc](http://lesscss.org/features/) for a complete
panorama over the LESS features including:
- Variables
- Mixins
- Nested rules
- Media queries bubbling and nested media queries
- Operation
- Functions
- Namespaces and accessors
- Scope
- Comments
- Importing
- etc...

---

## Built-in Twitter Bootstrap support

If you love [Twitter Bootstrap HTML, CSS, JS framework](http://getbootstrap.com/)
you will be happy to learn that BraDyCMS integrates the whole framework in the core.
You can pick and load all the components of the Twitter Bootstrap and just start and use
them.

If you have never heard about this framework, well it's time to do it, because,
quoting the [official web page](http://getbootstrap.com/):
> Bootstrap is the most popular HTML, CSS, and JS framework for developing responsive, mobile first projects on the web.

Visit the official page on [http://getbootstrap.com](http://getbootstrap.com),
star it on [Github](https://github.com/twbs/bootstrap/) and start using and experimenting.

---

#### Import components from already available less files
You can load the several Twitter Bootstrap LESS components directly in the
`styles.less` file using the `import` statement, or using a separate file as index for
the Bootstrap components, e.g.:

file `styles.less`:
    // define the path to the Twitter Bootstrap LESS directory
    // This directory is included by default in all BraDyCMS installations
    @tb-path: "../../../less/bootstrap/"

    // import bootstrap.less, an index file for all Bootstrap components
    @import "bootstrap.less";

    // start using Boostrap components and mixins
    div.slide{
      img{
        .img-responsive();
      }
      .caption{
        .text-center();
      }
    }
    ... etc...

---

#### Customize Bootstrap variables and defaults
You can easily customize the default Twitter Bootstrap [variables and defaults](http://getbootstrap.com/customize/)
by simply overwriting them:

    @tb-path: "../../../less/bootstrap/"

    @import "bootstrap.less";

    @body-bg: "#ebebeb";
    @text-color: rgb(200, 200, 200);

    div.wrapper{
      background: @body-bg;
    }

    ... etc...

---

#### The bootstrap.less index file
The file `bootstrap.less` is the same as the default `bootstrap.less` file included
in the default distribution of the Bootstrap framework
([Github example](https://github.com/twbs/bootstrap/blob/master/less/bootstrap.less)),
except for the fact that you have to put the correct path before any component. A complete example
should look like this:

file `styles.less`:
    // Core variables and mixins
    @import "@{tb-path}variables.less";
    @import "@{tb-path}mixins.less";

    // Reset and dependencies
    @import "@{tb-path}normalize.less";
    @import "@{tb-path}print.less";
    @import "@{tb-path}glyphicons.less";

    // Core CSS
    @import "@{tb-path}scaffolding.less";
    @import "@{tb-path}type.less";
    @import "@{tb-path}code.less";
    @import "@{tb-path}grid.less";
    @import "@{tb-path}tables.less";
    @import "@{tb-path}forms.less";
    @import "@{tb-path}buttons.less";

    // Components
    @import "@{tb-path}component-animations.less";
    @import "@{tb-path}dropdowns.less";
    @import "@{tb-path}button-groups.less";
    @import "@{tb-path}input-groups.less";
    @import "@{tb-path}navs.less";
    @import "@{tb-path}navbar.less";
    @import "@{tb-path}breadcrumbs.less";
    @import "@{tb-path}pagination.less";
    @import "@{tb-path}pager.less";
    @import "@{tb-path}labels.less";
    @import "@{tb-path}badges.less";
    @import "@{tb-path}jumbotron.less";
    @import "@{tb-path}thumbnails.less";
    @import "@{tb-path}alerts.less";
    @import "@{tb-path}progress-bars.less";
    @import "@{tb-path}media.less";
    @import "@{tb-path}list-group.less";
    @import "@{tb-path}panels.less";
    @import "@{tb-path}responsive-embed.less";
    @import "@{tb-path}wells.less";
    @import "@{tb-path}close.less";

    // Components w/ JavaScript
    @import "@{tb-path}modals.less";
    @import "@{tb-path}tooltip.less";
    @import "@{tb-path}popovers.less";
    @import "@{tb-path}carousel.less";

    // Utility classes
    @import "@{tb-path}utilities.less";
    @import "@{tb-path}responsive-utilities.less";

This example will load all Twitter Bootstrap components in the compiled
`styles.css` file. If some of the components are not needed you can comment them out
in the `bootstrap.less` file and they will not be loaded, e.g.:
    ...
    // Components w/ JavaScript
    // @import "@{tb-path}modals.less";
    // @import "@{tb-path}tooltip.less";
    // @import "@{tb-path}popovers.less";
    // @import "@{tb-path}carousel.less";
    ...
