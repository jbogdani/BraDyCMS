# Site configuration

The main settings of your BraDyCMS site can be set and changed using the
[configuration editor module](#cfg/edit). Some of the options can be edited by
all users, other require administrator rights to be accessed.

Before making any change in the main configuration module be sure to read and fully
understand these documentation.

**A misconfiguration of the site can prevent it from
working at all.**

---

## List of the available settings

### Site metadata
A collection of the default information to use for the website. It is strongly
recommended to carefully compile this section to
obtain a SEO friendly website.

#### Site name
Site's short name. It is not used by default in site's metadata
#### Web site mission
Site's mission. It is not used by default in site's metadata
#### Default title for site pages
This will be the default html `title` tag
for pages that are missing an article title or any other type of customized title.

If you re interested in manually setting the title of each page, check the
**Special custom fields** section of these documentation.
#### Site description
This will be the default html `description` tag
for pages that are missing an article summary or any other type of customized description.

If you re interested in manually setting the description of each page, check the
**Special custom fields** section of these documentation.
#### Keywords
These comma separated keywords will be the default html `keywords` tag
for pages that are missing an article specific set of keywords.
#### System language id
Two digits code of main language of the website, eg. `en`, `fr`, `it`, etc.
#### System language name
The name of the main language of the site. Please enter only lower case digits.

---

### Server settings
Server settings should be set once and then never change, unless the hosting plan changes.
#### Default robots meta tag
This is the global default value for [robots meta tag](https://support.google.com/webmasters/answer/79812).
The default value is `index, follow`.

If you re interested in manually setting the description of each page, check the
**Special custom fields** section of these documentation.

#### Site timezone
Please enter a valid timezone for the web site. A full list of valid timezones
can be found at [http://php.net/manual/en/timezones.php](http://php.net/manual/en/timezones.php)
#### Site path relative to the main domain
If your site is not located in the root directory of the domain enter here the
relative path to the installation folder, preceded by single slash (`/`),
eg. `/new_site`.

    This will change the RewriteBase directive of the main .htaccess

#### Enable www third level
By default BraDyCMS will redirect all third level domains (www.\*) to the main
level domain, removing `www` from url.
If this option is enabled `www` can be used in the domain name.

    If this setting is enabled the fowwoling .htaccess rows will be commented out:
    RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
    RewriteRule ^(.\*)$ http://%1/$1 [R=301,L]

#### Additional .htaccess directives
Here can be entered some additional, customized,  .htaccess directives that will
be automatically appended at the end of the .htaccess file after each system update.
Different directives should be placed in separated lines, just as you would do
in the .htaccess file.

---

### Database settings
#### Database connection string
Database full DNS connection string (eg.: `mysql:host=localhost;dbname=bduscms`).
Leave blank if you are using the default SQLITE database.
#### Database user
Database username. Leave blank if you are using the default SQLITE database.
#### Database password
Database password. Leave blank if you are using the default SQLITE database.

---

### Development settings
#### Debug mode
If you turn off debug mode remember to clear the application cache (see below).
#### Update channel
For a stable use please use the MASTER branch. Use development branch on your own risk.
#### CDN usage
If turned on CDN (where available) will be used to load third parties libraries (eg. jQuery, Twitter Bootstrap, etc).

---

### Image settings
#### Article's Images dimensions
You can simply associate an image to each article, this is the article image,
and various versions of it, in different sizes, automatically created and ready to
use in your templates. Check [General concepts](general) to learn more
about article images.

You can set here the different sizes that BraDyCMS will use to automatically create
your additional images.
Please enter here a valid format expressed in pixels in `width x height` format,
eg. `800x400`).
You can have many dimension steps. Just save the form to have a new blank field added.

#### Pagination
BraDyCMS has a built-in support for paginating long lists of articles (articles
of the same section or long search results). Pagination can really speed up your site.
You can set here the default number of articles to show in each page.
Leave blank to disable pagination.

    You need to add `html.pagination` or `html.getPagination` to your template files to get full advantage of this feature.
Read the [template docs](tmpl_html) to learn more about their usage and syntax.

---

### Google Analytics
#### Google Analytics ID
Your Google Analytics CODE ID
#### Limit Google Analytics to domain
If you enter here a domain (yourdomain.ext) the Google analytics code will be
shown only if domain matches. This is very useful if you have a localhost
copy of your website and you do not want to update your analytics
 from localhost domain.

---

### System languages
You can here enable the support for translating and making available your site
in different languages (besides the system language, see above).
Use the fields above to add new languages to your site.
You can have many languages. Just save the form to have a new blank row added.

    You need to add `html.langMenu` or `html.getLanguages` to your template files to show and format the language menu.
Read the [template docs](tmpl_html) to learn more about their usage and syntax.

#### Language ID
Two-digits code of the language (eg: `fr`)
#### Language name
Lower case name of the language (eg.: `français`)
#### Is published?
If `1` the language will be available in the main language menu, if blank you
will be able to translate articles in the admin backend, but the language will
not be available to your end users.

---

### Custom fields
Use the fields above to add new custom fields to your articles. For each field please please provide a lower-case name, using no spaces, dashes, underscores or other special characters; a field type, a label, and eventually a default value. If you chose "select" as field type you should enter a comma separated list of values as default fields to use as select options.

Please remember that you should update the database manually to include the new fields.
Special fields The field name `customtitle` is a special field. If provided it's value will be used to throughout the website for the HTML `title` metatag output.

You can add many custom fields. Just save the form to have a new blank row added.
- **Field name** this is the database name of the field. No spaces, dashes, underscores or other special characters must be used
- **Field type** One of: input, longtext or select
- **Field label** this is the label that will be showed to admin users in the article edit form
- **Default field values** if set this will be the pre-filled value of the field. In case of field type select, please enter here e comma separated list of options for the select drop down menu
- **Translatable** wether this field should show or not in the article translate form
- **Rich text** if true (1) the field on tha article edit form will be set to use a wysiwyg editor

> ### Special custom fields
> customtitle
> customdescription
> robots

---
### System paths
#### convert
If you are planing to use [php Imagick extension](http://php.net/manual/en/book.imagick.php) for image manipulation enter here the string `imagick`.
If you plan to use [Imagemagick binary](http://www.imagemagick.org/script/index.php) please enter here the absolute path to the Imagemagick convert command (usually `/usr/bin/convert` or `/usr/local/bin/convert` on most \*nix systems).
This setting is fundamental for the image manipulating system to work. Settings depend on your hosting plans.

---

### Users
Be sure to use the "encrypt password tool" before submitting a new user. Only system admins can grant admin privileges to other users.
You can add many users. Just save the form to have a new blank row added.
#### Email address
#### Encrypted password
#### Is admin
