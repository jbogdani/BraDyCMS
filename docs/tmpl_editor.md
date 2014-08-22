# The built-in template editor

You don't need any external software or application to create beautiful templates
and stylesheets for BraDyCMS. You don't need FTP accounts or FTP clients to download
and/or upload your files. All you need to create and edit templates is already included
in the BraDyCMS core, this is the **Template manager** module.

You can access this module from the `Main menu > Site configuration > Template manager`
or following this link [#template/dashboard](#template/dashboard).

Using this module you can:
- Edit template (*.twig) files
- Edit CSS files
- Edit LESS files
- Edit javascript files
- Customize the Welcome page
- Compile LESS files
- Empty the application cache
- Create new files
- Delete existing files

## Common troubleshooting

---

### Edit template files
Template files have `.twig` extension and are written in the [Twig language](#docs/read/tmpl_twig).
You can have all the template files you need, but at least, the `index.twig` file
should exist for the application to work correctly.

All available template files will be listed in the left side of the page. To 
edit them just click on the button and their content will be shown in the right
part of the page. You can edit and then click on the `Save` button on the upper right
part of the file content. A success message should appear to notify that all edits
were correctly saved.

In order to render these edits available to all user you probably should empty the
application cache, after saving your edits. Use the `Empty cache` button on the 
left to empty the cache.

If you don't know what the application cache is and how it works please read the
paragraph about the **caching system** in the [section of the docs dedicated to the Twig syntax](#docs/read/tmpl_twig)

---

### Edit CSS files
You probably do not want to directly edit the CSS files, and will prefer using 
the LESS syntax, described in details in the [section of the docs dedicated to the CSS, LESS and Bootstrap](#docs/read/tmpl_less).

Anyway you can still edit the CSS files available in the `site/default/css` directory.
These CSS files will be listed in the left side of the page. To 
edit them just click on the button and their content will be shown in the right
part of the page. You can edit and then click on the `Save` button on the upper right
part of the file content. A success message should appear to notify that all edits
were correctly saved.

You don't have to compile anything to make all changes automatically available 
after each save action.

---

### Edit LESS files
If you don't know what LESS is and how Twitter Bootstrap framework can be integrated
in your web site, probably you should read the [dedicated section of the docs](#docs/read/tmpl_less).

These LESS files available in the `site/default/css` directory of our site 
will be listed in the left side of the page. To  edit them just click on the 
button and their content will be shown in the right part of the page. 
You can edit and then click on the `Save` button on the upper right
part of the file content. A success message should appear to notify that all edits
were correctly saved.

Mind that you should compile LESS file to CSS in order to see your changes applied
in the live web site. To do this, after all edit have been saved, click on the 
`Compile styles.less` button on the left side of the screen. This will compile 
LESS syntax to CSS, will minify the script for best performance and will save 
CSS content in the `styles.css` file.

---

### Edit javascript files
The javascript files available in the `site/default/js` directory of your web site
will be listed in the left side of the page. To  edit them just click on the 
button and their content will be shown in the right part of the page. 
You can edit and then click on the `Save` button on the upper right
part of the file content. A success message should appear to notify that all edits
were correctly saved.

You don't have to compile anything to make all changes automatically available 
after each save action.

---

### Customize the Welcome page
Clicking on th `welcome.md` button in the left side of the page will show the 
content of the customized welcome page in the right part. You can edit and then 
click on the `Save` button on the upper right part of the file content. 
A success message should appear to notify that all edits were correctly saved.

---

### Compile LESS files
After ypu have applied edit to any of the LESS files you should compile 
the `styles.less` file to CSS in order to see your changes applied
in the live web site. To do this, after all edit have been saved, click on the 
`Compile styles.less` button on the left side of the screen. This will compile 
LESS syntax to CSS, will minify the script for best performance and will save 
CSS content in the `styles.css` file.

Compiling `styles.less` all less files imported with the `@import` directive in the
`styles.less` files will also be compiled.


---

### Empty the application cache
If you don't know what the application cache is and how it works please read the
paragraph about the **caching system** in the [section of the docs dedicated to the Twig syntax](#docs/read/tmpl_twig).

In order to render all edits you have applied to the twig files available to all 
users you should empty the application cache. Use the `Empty cache` button on the 
left to empty the cache.

---

### Create new files
It is possible to create new template, stylesheets and javascript files using a very
simple user interface directly in the Template manager module.

Just click on `Create new file` button in the left side of the screen. Then you 
will be prompted for a filename and a file type. You should choose a non existing filename
in order to successfully create the file.

Please do not use spaces, special characters or diacritics in the filename. Remember:
keep it simple, stupid (KISS!).

After the file is created you will find it listed in the left side of the page.

> **Note**: you will not be prompted to enter the directory where the file will be located.
This will be handled automatically by the system using the [file tree conventions](#docs/read/tmpl_files).

---

### Delete existing files
It is possible to deleted template, stylesheets and javascript files using a very
simple user interface directly in the Template manager module.

Just click on `delete file` button in the left side of the screen. Then you 
can select the file to delete from a drop-down menu.

Select the file and type `DELETE` in the text input on the right to confirm your action.
You should use uppercase letters. The delete button will appear and you will be 
able to permanently delete the file.


> **Note**: the delete action can not be undone! Once deleted a file CAN NOT be 
restored back!
