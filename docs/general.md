# Welcome to BraDyCMS

This document sums up some basic concept about how BraDyCMS works, about how you can organize articles, menus and how to format your URLs in order to get a beautiful and functional web site.

More detailed documentation, on specific issues, tutorials and other help can be found in the `Documentation` item of the main menu.

---

### KISS principle
[Keep it simple, stupid](http://en.wikipedia.org/wiki/KISS_principle) is the basic principle that moved us towards the development of this CMS.

We wanted to create a simple to use, yet versatile CMS, that we could use in various our projects, and than we shared our work on [Github](https://github.com/jbogdani/BraDyCMS/), making BraDyCMS an open source project.

Our principal aim was to get have a powerful tool for content managing with a easy way to organize the content and to display (template system) to public. That's why we say that **BraDypCMS is a designer-oriented CMS**.

The creation of a simple web site or of a big web portal passes through 3 steps:

1. Add and organize some content
2. Add some menus
3. Design a template to present your content to other people


> That's all BradyCMS does!

---

## Adding and managing content

All content is organized in `articles`. Articles are organized using tags. No sections, no categories, just tags! Tags a far more elastic way to keep your content organized.

It's up to you to design the structure of your data, and you can build liquid or static categories and sections just using tags. Which category belongs to which section? Nested categories? No need for this, tags can make a single article can belong to many categories and/or section.

Article `apple` can be tagged with tags `fruit`, `food`, `edible`, `tasty`; it means it belongs to all this categories and you don't need sub-categories. If you need to display all articles of sub-category `edible` of category `fruit` just search for all articles having both tags `fruit` and `edible`!

Each article has the following **default** fields:

- `Title`: this will also be used for page HTML title tag
- `Text ID`: this is the UNIQUE name of your article. Keep this simple and meaningful: it will be also the URL of the article
- `Sorting`: custom sorting for list views
- `Keywords`: this is mainly used for page HTML keywords tag
- `Author`: The author of the article
- `Is published`: you can save drafts and publish in a second moment using this flag
- `Creation date`: Article creation date stamp
- `Publishing date`: You can use this field if you want to schedule the publishing of the article
- `Expiration date`: You can use this field if you want to schedule the expiration date of your content
- `Summary`: This will be used also for the page HTML description tag. Can be useful also in list view, as it can be used as a preview of article contents
- `Text`: Finally, the article main content

### Custom fields and custom database schema

If you need more fields for your articles, you can add them by your own. There is a simple and safe procedure adapt the database schema to match your needs.

For a detailed guide check the [How to customize database schema](customfields) guide.

---

## Article images

Very often we need to have a specific image linked to an article. And we want to use it in different sizes, for different purposes (slideshows, thumbnails, big images, etc). We know that scaling and adjusting manually all images, uploading and organizing in folders, establishing different filenames and filenames patterns and  can be very time consuming.

BraDyCMS simplifies all this. Uploading, resizing in different sizes, organizing in folders is as easy as dragging an image from your computer to the article's edit form. Add all the image sizes you want to have for each article in the [site configuration module](#cfg/edit). Drag and drop images in the right part of the article edit form! Images will be named with the article `ID` (not text id) and will be converted to have the `jpg` extension. **BradyCMS** will create the folder system for you.

For example, if you have defined three sizes for your article images, 600x300, 450x225 and 200x100, and you upload an image for the article with ID 5, your images will be available at the following paths:
- ./sites/default/images/articles/600x300/5.jpg
- ./sites/default/images/articles/450x225/5.jpg
- ./sites/default/images/articles/200x100/5.jpg
- ./sites/default/images/articles/orig/5.jpg    

---
    
## Media > images

BraDyCMS has a built in system for loading, organizing and editing images. You can load images and files, create folders, delete, and edit them using a GUI interface.

You can copy, move, convert in multiple formats, resize and crop images easily and safely.
    
--- 
## Media > galleries

Photo galleries are awesome! That's why we have built an easy to use tool to upload files with a simple drag&drop, create thumbnails with a click, add and translate caption, ecc. You can then easily embed your galleries in articles body by using a simple fast tag `[[gallery]]here_galley_name[[/gallery]]` ([click to learn mode](faq)).
    
---

## Menu

You can have all the menus you need, and translate them in several languages. You can also nest menu items in multiple levels. And you do not need difficult code to do this: just drag & drop menu items to reorder or nest them. You can find more on how to create menu in the [FAQ section](faq) of the documentation.

---

## URL and linking system

Retriving articles from the database to use in the templates is simple! BraDyCMS has two methods that you can use to build meaningful (and search engines friendly) URLs. Let's suppose http://mysite.ext it the URL of your installation:

- to get a single article just add append the article's text id to your URL:
 - `http://mysite.ext/myfirstarticle`
- to get a list of articles use tags; one or more tags can be used simultaneously in the URL and, don't forget, use the `.all` extention to tell the CMS you are using tags:
 - `http://mysite.ext/tag1.all`: will retrieve all articles tagged with tag **tag1**
 - `http://mysite.ext/tag1-tag2-tag3.all`: will retrieve all articles tagged with **all** listed (hyphen separated) tags **tag1**, **tag3**, **tag3**
 - `http://mysite.ext/tag1~tag2~tag3.all`: will retrieve all articles tagged with **any** of the listed (tilde separated) tags **tag1**, **tag3**, **tag3**
    
This way, if you want to show all web related news of year 2013, just tag the articles with all these tags (news, 2013, web) and use this URL: `http://mysite.ext/news-2013-web.all`

Learn more on menu and URL in the [FAQ section](faq) of the documentation.
    
- - -

## Almost finished...

Please read all docs to learn how to translate articles and menu items, translate BraDyCMS admin control panel in your language, Create and use beautiful user forms, author plugins and extend functionality and report all problems you may encounter on [Github](https://github.com/jbogdani/BraDyCMS/issues).

