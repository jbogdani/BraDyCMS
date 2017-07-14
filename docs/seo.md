# Easy to use SEO customization

BraDyCMS provides an easy way to improve SEO and provide custom meta tags for
articles, article list or any custom URL.

There are several ways to define a set of `title`, `description` and `keywords`
for each article but a fully customizable way for lists of articles or custom
URL was still lacking until version 3.14.28.

## Article meta tags
Article's meta tags (`<title>`, `<description>`, `<keywords>`) can be set in 3
different ways:

- using `Title`, `Keywords` and `Summary` fields in main article edit form.
- if you a differentiation is needed between the article's title (or
description) and it's title (or description) meta tag two custom fields should
be added to the `article` table of the main database schema
([how to customize the database schema](customfields.md)):
`customtitle` and `customdescription`. These values will be used by BraDyCMS
to display `title` and `description` meta tags, instead of `Title` and `Summary`
default fields.
- finally the SEO plugin can be used to customize the meta tags (see below).

## SEO plugin: article, tags and custom URL meta tags
The SEO plugin can be easily reach at
    Main menu > Plugins > SEO manager
or using [this direct link](#seo/all).

Custom `title`, `description` and `keywords` can be added for each URL, be it
an article, an article list or a special function. Using this plugin SEO data
will not be linked to a single content but to a URL.


### How to add a new SEO record
Adding a new SEO records for an URL is plain.
1. Click on `+ Add a new record` button
2. Fill the form:
  - Enter the relative url (eg: in `http://myhost.com/myarticle` the relative
  path will be `myarticle` or in `http://myhost.com/mytag.all` the relative path
  will be `mytag.all`)
  - The html tag title text
  - The html tag description text
  - The html tag keywords text

### Add translated versions of a SEO record
SEO plugin supports different locale versions of each URL, be it an article, a
list of articles (tag) or a special function.
If a site is configured to serve content in different languages
([See System language paragraph of main configuration docs](customfields.md))
these languages will be listed in the main [list of SEO records](#seo/all).

By clicking on the specific language button will show up the SEO for for that language.

<p class="text-danger"><strong>Please note</strong>: if a SEO record is deleted,
also <strong>all</strong> translated versions will be automatically deleted!</p>
