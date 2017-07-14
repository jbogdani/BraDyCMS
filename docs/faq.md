# FAQ

---


#### How to add a new article?

    Main menu > Articles > Add new Article

Fill the form and save it. Please note that article's `Text ID` is required. No spaces ore special carachtares are allowed. This string will be the URL of the article.

---
		
#### How to edit existing article?

    Main menu > Articles > Show all articles

Find in the list the article you want to edit. Also the search filter can be used. Click on article's title or on edit button to open the edif foem.

--- 

#### How to create a new menu?

    Main menu > Menu > Add new menu item

Fill the field "Menu" with the name of the new menu.

---

#### What information are needed to create a new menu item?

To add a new menu item the following information is necessary:
* Text: is the text that users will see</li>
* Link: the link the menu item will point to. For external links also the `http://` part is needed (eg.: `http://bradypus.net`). For internal links the following syntaxt should be used:
  * `article_textid` for links to articles
  * `tag1.all` for links to lists of articles tagged by a single tag
  * `tag1-tag2-tag3.all` for links to lists of articles tagged by all provided tags (tag1, tag2, tag3, etc)
  * `tag1~tag2~tag3.all` for links to lists of articles tagged by any of the provided tags (tag1, tag2, tag3, etc)
  * `./` to link to the home page
  * `#` do nothing link
Links to articles, tags lists and more can also be entered by using the widget on the left. Clicking on one article or tags, etc, the input will be filled automatically.
* Target: where the link will be opened:
  * if not set all links will be opened in the same window or tab
  * `_blank` will open the link in a new window or tab
* Title: an explicative text that the user will see on hovering the menu text with the cursor
* Menu: menu name. If choosen from the available list, the menu item will be added to that menu. If a new string is entered a new menu is created.
  
  `Hold on`: no white spaces, upper case or special characters ar allowed!
  
*  Sub menu of: if an existing menu is select a list of the available menu items (siblings) will be shown. If one of them is selected this menu item will be nested to the selected item. `Attention:` Please see the "How to manage menus?" section for easily nesting menu items
* Sorting: Only integers are allowed in this field. Use this field to control the order of appearance of the menu items.

  `Hold on`: please see the "How to manage menus?" section for easily sorting menu items


---

#### How to manage menus?

    Main menu > Menu > Show all menus

If menus are available will be listed here. Fot each available menu will be provided a list containing all items. Clicking on an item will open it in a new tab for editing (for detail see `What information are needed to create a new menu item?`).

Menu items can be ordered bu dragging & dropping them. In the same way these items can be nested into one another. Settings are automatically saved.

---

#### How to insert menu divider item

To add menu divider just add a new menu item having as **Text** and as **Link** just a dot (.). This will output `<li class="divider"></li>`

---

#### How to upload files?

    Main menu > Media > Media

Click on "Upload a file" button, or drag&drop files from your folders to the "Upload a file" button. Multiple files can be loaded simultanously. 

---

#### Can I organize my files in folders?

Yes. In the media folder you can create all the directories you need to beter organize your files. Creating a folder is easy as typing its name:

    Main menu > Media > Media

Simply click on one folder to enter in it and use the navigation bar to go back up to the root folder.

---

#### Can I navigate my files and folders?

    Main menu > Media > Media

In the navigation bar enter in the input the name of the directory you want to create and click "Create & Go!". The new folder will be created and you will be durected there, ready to upload files.

---

#### Can I delete or rename my files?

    Main menu > Media > Media
Clicking on the file thumbnail a popup will appear (click againg on file's thumbnail to cose the popup). In the popup you can fine the file URL to use in your articles, an input where you can enter the new name, to rename the file.

`Attention:` make sure to use the same file extension), and the open / download and delete buttons.

`Tip:` in the new name input you can also use relative paths to move your file in other folder!

---

#### How can I delete a a folder I don't need anymore?

You can not delete folders that contains files. Thus, to delete a folder you must delete or move (see: `Can I delete or rename my files?`) all files inside. Once the folder is empty a button will appear and you can delete it.

** How can I translate an article? **

    Main menu > Articles > Translations


Click in the language you want to translate in, a table showinf the translation status for each article will appear. To translate an article click on the edit button. It will open the article translation form for the article. The automatic copy & paste buttons (`>>`) can help in copying the original text in the translation box, in order to import images and other tags in the translation. 

---

#### How can I translate a menu item?

    Main menu > Menu > Translations
Click in the language you want to translate in, a table showinf the translation status for each menu item will appear. To translate a menu item click on the edit button. It will open the menu translation form for the menu item. The automatic copy & paste buttons (`>>`) can help in copying the original text in the translation box, in order to import images and other tags in the translation.

---

#### Can I have nested articles?

BraDypUS CMS supports something similar to "Nested articles". It's a feature that permits to use an article as an introduction text for a list of articles, usually articles of the same section. To achieve this just create a new article having as `text id` the same string as the section name you want to use this article as introduction for. This article will be used as intro text for the section blog list!

---

#### For DESIGNERS: How to create and edit a site template?

To edit the **HTML** of the main index file:

    Main menu > Other > Template manager > Edit HTML
or follow [this link](#template/index). Plaese check the [template documentation](intro).
				
To edit the **CSS**:

    Main menu > Other > Template manager > Edit CSS
or follow [this link](#template/css).

---

#### How can I create a new image gallery?

    Main menu > Media > Galleries
Will list all available image galleries. To add a new Gallery just click on the PLUS button, `Add new gallery`. You will be prompted for the gallery name. Be careful: no spaces, special characters of commas (,), dashes (-), apices (') or double apices (") are allowed. If entered these characters will be automatically replaced with underscores (_). Gallery names must be lower case.

---

#### How can I edit an existing image gallery?

    Main menu > Media > Galleries
Will list all available image galleries. Just click on the gallery you want to edit.

Images can be loaded by clicking, in the lower part of the page, the `Upload file` button. More images can be load simultaneously. An easier way to load more images is by simply dragging and dropping them from your computer to the button.

After the images are loaded you can enter the captions, by writing in the `Description` column. Clicking on save will save all captions.

An easy way to create image thumbnails is by using the `(Re) create thumbnail` button in the Thumbnail column. This will create an image thumbnail measuring 200x200px.

`Hold on!`: for the thumbnail creator to work is necessary to have a valid convert path in the system configuration!

---

#### How can I translate an existing image gallery?

    Main menu > Media > Galleries
Will list all available image galleries; click on the gallery you want to translate.

Click in the menu bar, under the page title, the language you want to add a translation for and write caption translations.

When you have finished click on any save button, to save the translations.

---

#### How can I create delete an image or a whole gallery?

    Main menu > Media > Galleries
Click on the gallery you want to edit / delete

Clicking the `Delete` button in the image column will delete the original image, its thumbnail and captio if exist. This action can not be undone.
To delete a whole gallery all images must be deleted first. When no images are present a button `Delete gallery` will be shown and the gallery can be deleted. This action can not be undone.

---

#### Can I rename an image gallery?

    Main menu > Media > Galleries
Click on the gallery you want to rename
Clicking the `Rename gallery` You will be prompted for the gallery name. Be careful: no spaces, special characters of commas (,), dashes (-), apices (') or double apices (") are allowed. If entered these characters will be automatically replaced with underscores (_). Gallery names must be lower case. Galley names should be unique.

---

#### What is a *conditional loading gallery* and how can I add it in an article's body?

BraDyCMS permits the creation of conditional loading galleries. Using these kind of galleries instead of the normal ones permits web designres to create faster websites for mobile templates

If you are building responsive layouts and do not want to show your image gallery in smaller devices, you surel do not want to load gallery image at all, in order to save bandwidth.

Conditional loading galleries will load images only if the `unordered list (ul)` element which holds the galley itself is visible. Images will be loaded using javascript. A jQuery plugin (`jquery.cl_gallery.js`) is provided.

To add a conditional loading gallery in the article body a special syntax is provided:

    [[cl_gallery]]gallery_name[[/cl_gallery]]
Conditional loading galleries uses the same options as galleries

`Hold on!` For conditional loading image galleries to work properly the javascript jquery plugin `jquery.cl_gallery.js` should be loaded and executed!
