# Customtags
BraDyCMS has a built-in support for a number of **customtags**, i.e., a fast way
of adding dynamic content, snippets, external addons, etc., in template files or
inside article's content.

All custom tags have a name, may have content and may none, one or more have attributes.
All custom tags **should be enclosed in double square brackets** and **should always be closed**.
This way you should always have an opening tag, e.g.: `[[mycustomtag]]` and a closing
tag, e.g.: `[[/mycustomtag]]`. In between you may have some content.

You can also have none, one or more parameters. Parameters should be inserted in the opening
tag, e.g.: [[mycustomtag param1="param1 value" param2="param2 value" etc...]]

For example the following examples are all valid:
- `[[mycustomtag]][[/mycustomtag]]`: simplest form, no content, no parameters.
- `[[mycustomtag]]some content[[/mycustomtag]]`: some content is provided
- `[[mycustomtag]]some <strong>content</strong>[[/mycustomtag]]`: content can be
rich html text, but tag **must** contain only plain text!
- `[[mycustomtag param1="param 1 value"]]some content[[/mycustomtag]]`: some content
and one paramter, named `param1` and having value `param 1 value` are provided
- `[[mycustomtag param1="param 1 value" param2="param 2 value"]]some content[[/mycustomtag]]`:
some content and two parameters are provided
- `[[mycustomtag param1="param 1 value" param2="param 2 value"]][[/mycustomtag]]`:
two parameters and no content is provided


> You can build your own custom tags by [building a new plugin](#docs/read/customplugin)

---

## Docs

---

#### addThis('only_js')
Adds the necessary HTML and javascript code to display an AddThis share widget
- **content**, string, optional. HTML or text to show in your page. If not provided the default AddThis image will be shown
- **only_js**, boolean, optional, default false (not present). If true no HTML will be shown from AddThis, only javascript will be included

Minimal example:
    [[addThis]][[/addThis]]

Complete example:
    [[addThis only_js="true"]]Share +[[/addThis]]

---

#### cl_gallery('class')
Displays imageless galleries, for conditional image loading. This custom tags should be used together with the provided jquery.cl_gallery.js javascript plugin
- **content**, string, required. Name of gallery
- **class**, string, optional. One or more (space separated) CSS class to apply to main ul tag
= **rel**, string, optional, default: gallery name (content). rel attribute to use to cluster
images in slideshows. If not provided the gallery name will be used

Minimal example:
    [[cl_gallery]]gallery_name[[/cl_gallery]]

Complete example:
    [[cl_gallery class="horizontal small" rel="gallery-one"]]gallery_name[[/cl_gallery]]

---

#### disqus('class')
Adds the necessary HTML and javascript code to display Disqus comments widget.
- **content**, string, required. Site's shortname in disqus

Minimal example:
    [[disqus]]disqus_website_id[[/disqus]]

---

#### download('class','limit','getObject','getList')
Displays table with file download node data and links or array with files download data
- **content**, string, required. Name of download node
- **class**, string, optional. One or more (space separated) CSS class to apply to main ul tag
- **limit**, int, optional. Number of elements to return
- **getObject**, boolean, optional, default false. If true only array of data will be returned, otherwise, default option, full html will be returned
- **getList**, boolean, optional, default false. If true elements will be output as unordered list

Minimal example:
    [[download]]node_name[[/download]]

Complete example:
    [[download class="table table-striped"]]node_name[[/download]]

---

#### dwnl('file','title', 'rel', 'class')
Returns valid html with link to download file, if file is available, with file download count
- **content**, string, optional. Link's text. If not available the file parameter will be used. The variable {tot}, if used, will be replaced with the total number of downloads for the file
- **file**, string, optional. Absolute or relative path (some default system paths will be tested) to file to be downloaded.
- Any other element will be added as attribute to the main link element (<a>)

Minimal example:
    [[dwnl file="pdf_file.pdf"]]Download now PDF version (total downloads: {tot})[[/dwnl]]

Complete example:
    [[dwnl file="pdf_file.pdf" title="Download PDF version" rel="PDF" class="download_link"]]Download now PDF version (total downloads: {tot})[[/dwnl]]

---

#### fb_comments(content)
Adds the necessary HTML and javascript code to display Facebook's comments widget. For a list of all the available options check this link: https://developers.facebook.com/docs/reference/plugins/comments
- **content**, - URL of the page to comment. If not provided the current page will be commented
- **lang**, - 5-digits language definition of Fabebook SDK (eg: en_US or it_IT, etc)

Minimal example:
    [[fb_comments]][[/fb_comments]]

---

#### fb_follow(content)
Adds the necessary HTML and javascript code to display Facebook's follow widget. For a list of all the available options check this link: https://developers.facebook.com/docs/reference/plugins/follow
- **content**, string, required. URL of the Facebook page to follow
- **lang**, - 5-digits language definition of Fabebook SDK (eg: en_US or it_IT, etc)

Minimal example:
    [[fb_follow]]http://facebook.com/rest_of_the_url_to_follow[[/fb_follow]]

---

#### fb_like(content)
Adds the necessary HTML and javascript code to display Facebook's Like widget. For a list of all the available options check this link: https://developers.facebook.com/docs/reference/plugins/like
- **content**, - URL to like
- **lang**, - 5-digits language definition of Fabebook SDK (eg: en_US or it_IT, etc)

Minimal example:
    [[fb_like]][[/fb_like]]

---

#### fb_like_box(data)
Adds the necessary HTML and javascript code to display Facebook's Like-box widget.
For a list of all the available options check this link: https://developers.facebook.com/docs/reference/plugins/like-box
- **content**, -
- **lang**, - 5-digits language definition of Fabebook SDK (eg: en_US or it_IT, etc)

Minimal example:
    [[fb_like_box]]https://www.facebook.com/BraDypUS.net[[/fb_like_box]]

---

#### fb_send('width','num_posts','colorscheme')
Adds the necessary HTML and javascript code to display Facebook's Send widget. For a list of all the available options check this link: https://developers.facebook.com/docs/reference/plugins/send
- **content**, -
- **lang**, - 5-digits language definition of Fabebook SDK (eg: en_US or it_IT, etc)

Minimal example:
    [[fb_send]][[/fb_send]]

---

#### fig('path','width','align','fancybox','href','href_class','gal')
Adds complete HTML code to display figures and captions easily. This custom tag, differntly from the [[figure]] custom tag permits the use of rich html text in captions
- **content**, string, optional. The image's caption
- **path**, string, required. Images's path, without the "/sites/default/images/" part.
- **width**, int, optional. Image's max-width. If absent the file width will be used
- **align**, string, optional, available values: left, right, center. Widget's alignment in the page
- **fancybox**, boolean, optional, default false. If present the system will try to create a valid html fancybox popup, using thumbnail, if present
- **href**, string, optional. If present the image will hold a link to this URL
- **href_class**, string, optional. If present the imag's link will have this CSS class
- **gal**, string, optional. If present the string will be used to collect images in the same page in galleries

Minimal example:
    [[fig path="image_url"]][[/fig]]

Complete example:
    [[fig path="image_url" width="300px" align="left" href="http://bradypus.net" href_class="bordered" gal="first_gallery"]]image_caption[[/figure]]

---

#### figure('width','caption','align','fancybox','href','href_class','gal')
Adds complete HTML code to display figures and captions easily
- **content**, string, required. Images'u path, without the "/sites/default/images/" part.
- **width**, int, optional. Image's max-width. If absent the file width will be used
- **caption**, string, optional. The image's caption
- **align**, string, optional, available values: left, right, center. Widget's alignment in the page
- **fancybox**, boolean, optional, default false. If present the system will try to create a valid html fancybox popup, using thumbnail, if present
- **href**, string, optional. If present the image will hold a link to this URL
- **href_class**, string, optional. If present the imag's link will have this CSS class
- **gal**, string, optional. If present the string will be used to collect images in the same page in galleries

Minimal example:
    [[figure]]image_url[[/figure]]

Complete example:
    [[figure width="300px" caption="image_caption" align="left" href="http://bradypus.net" href_class="bordered" gal="first_gallery"]]image_url[[/figure]]

---

#### flash('width','height','other')
Adds HTML code to include a FLW file
- **content**, string, required. Path to flw file
- **width**, int, optional. Object's width
- **height**, int, optional. Object's height
- **other**, Any other parameter can be added.

Minimal example:
    [[flash]]./sites/default/images/flash/test.flw[[/flash]]

Complete example:
    [[flash width="400" height="300" autoplay="true"]]./sites/default/images/flash/test.flw[[/flash]]

---

#### gallery('class')
Displays an existing image gallery
- **content**, string, required. Name of gallery
- **class**, string, optional. One or more (space separated) CSS class to apply to main ul tag
- **rel**, string, optional, default: gallery name (content). rel attribute to use to cluster
images in slideshows. If not provided the gallery name will be used

Minimal example:
    [[gallery]]gallery_name[[/gallery]]

Complete example:
    [[gallery class="horizontal small" rel="gallery_one"]]gallery_name[[/gallery]]

---

#### gplus_plusone('href','href','annotation', 'width', 'align', 'expandTo', 'callback', 'onstartinteraction', 'onendinteraction', 'recommendations', 'count')
Adds the necessary HTML and javascript code to display GooglePlus' +1 widget. For a list of all the available options check this link: https://developers.google.com/+/web/+1button/

Minimal example:
    [[gplus_plusone]][[/gplus_plusone]]

---

#### gcalendar('class')
Outputs well formatted html string that embeds a google calendar
- **content**, string, required. Google calendar id eg: somecalendar@group.calendar.google.com
- **showPrint**, boolean, optional, efault false. If true the print option will be visible
- **showTabs**, boolean, optional, default false. If true the tabs will be visible
- **showCalendars**, boolean, optional, default false. If true the list of calendars be visible
- **height**, int|false, optional. Default 600. The height in pixels of the calendar
- **width**, int|false, optional, default 800. The width in pixels of the calendar
- **wkst**, int|false, optional, default 2. Start day of the week 1: sunday, 2 monday, etc..
- **bgcolor**, string|false, optional, default FFFFFF. Background color code
- **color**, string|false, optional, default 8C500B. Text color
- **ctz**, string|false, optional, default Europe/Rome. Time zone
- **mode**, string|false, optional, default false. Calndar mode, one of: WEEK or AGENDA
- **hl**, string|false, optional, default system language. Language of the calendar


Minimal example:
    [[gcalendar]]somecalendar@group.calendar.google.com[[/gcalendar]]

---

#### gplus_plusone('href','href','annotation', 'width', 'align', 'expandTo', 'callback', 'onstartinteraction', 'onendinteraction', 'recommendations', 'count')
Adds the necessary HTML and javascript code to display GooglePlus' +1 widget. For a list of all the available options check this link: https://developers.google.com/+/web/+1button/

Minimal example:
    [[gplus_plusone]][[/gplus_plusone]]

---

#### link('art','title','rel','class','id')
Adds an internal link to an article
- **content**, string, required. Text of the link content that will be visible to users
- **art**, string, required. Textid of the destination article. To link to home page use the string "home"
- **title**, string, optional. Link's title attribute
- **rel**, string, optional. Link's rel attribute
- **class**, string, optional. Link's class attribute
- **id**, string, optional. link's id attribute

Minimal example:
    [[link art="contacts"]]Contact us[[/link]]

Complete example:
    [[link art="contacts" title="Contact us" rel="help" class="primary-link" id="contact"]]Contact us[[/link]]

---

#### map('width','height')
Shows a user map in article content
- **content**, string, required. User formap name (uique ID)
- **width**, string, default: 100%. Width of the map container
- **height**, string, default: 400%. height of the map container

Minimal example:
    [[map]]where_we_are[[/map]]

Complete example:
    [[map width="300px" height="200px"]]where_we_are[[/map]]

---

#### prezi('bgcolor','lock_to_path','autoplay','autohide_ctrls','width','height')
Adds the necessary HTML anf javascript code to display a Prezi presentation
- **content**, string, required. Prezi presentation's ID
- **bgcolor**, string, optional, default: ffffff. Background color.
- **lock_to_path**, boolean, optional, default: 0. If 1 the user will be locked to the given path
- **autoplay**, boolean, optional, default: 0. If 1 the presentation will start after page is loaded
- **autohide_ctrls**, boolean, optinal, default:0.If 1 the conftols will be hidden automatically
- **width**, int, optional, deafult: 500. Widget's width in pixels
- **height**, int, optional, deafult: 550. Widget's height in pixels

Minimal example:
    [[prezi]]prezi_id[[/prezi]]

Complete example:
    [[prezi width="500" height="550" color="#ff6600" autoplay="true" artwork="true"]]prezi_id[[/prezi]]

---

#### skype('type','imageSize')
Adds the necessary HTML anf javascript code to display a Skype call, chat call & chat widget
- **content**, string, required. Skype username
- **type**, string, optional, default: call, available values: call, chat, dropdown.Widget type: call, chat or both (dropdown)
- **imageSize**, int, optinal, default: 32, available values: 10, 12, 14, 16, 24, 32.The size, in pixels, of the image to show.

Minimal example:
    [[skype]]skype_username[[/skype]]

Complete example:
    [[skype type="call" imageSize="32"]]skype_username[[/skype]]

---

#### soundcloud('width','height','color','autoplay','artwork')
Adds the necessary HTML anf javascript code to display a SoundCloud player
- **content**, string, required. Track URL
- **width**, int, optional, default: 100%. The widget's width in pixels
- **height**, int, optional, default: 100%. The widget's height in pixels
- **color**, string, optinal, default: #ff6600". The widget's color.
- **autoplay**, boolean, optional, default: false. If true the track will start playing after page is loaded
- **artwork**, boolean, optional, default: false. If true the track's artwork will be displayed

Minimal example:
    [[soundcloud]]track_url[[/soundcloud]]

Complete example:
    [[soundcloud width="100%" height="166" color="#ff6600" autoplay="true" artwork="true"]]track_url[[/soundcloud]]

---

#### twitt_share('data')
Adds the necessary HTML and javascript code to show a Twitter share button
- **content**, string, optional, default: current page. URL to share
- **data.via**, string, optional, default: false. A Twitter username
- **data.text**, string, optional, default: false. The Tweet's default text
- **data.hashtags**, string, optional, default: false. The Tweet's default hashtags
- **data.count**, string, optional, default: false. If 'none' no count will be shown
- **data.lang**, string, optional, default: false. Two-digits language code

Minimal example:
    [[twitt_share]][[/twitt_share]]

Complete Example
    [[twitt_share via="TheBraDypUS" text="BraDypUS rocks!" hashtags="bradycms" lang="en"]]http://bradypus.net[[/twitt_share]]

---

#### twitter('twitter_username','id')
Adds the necessary HTML and javascript code to show a Twitter widget
- **content**, string, optional. The text that will be shown if something goes wrong and Twitter widget fails to be initialized.
- **twitter_username**, string, required. Twitter username
- **id**, string, required. The ) The widget's id, provided by Twitter

Minimal example:
    [[twitter user="twitter_username" id="twitter_widget_id"]]Some intro text[[/twitter]]

---

#### userform('inline')
Shows a user form in article content
- **content**, string, required. User form name (uique ID)
- **inline**, boolean, default: false. If true the form-inline CSS class will be added to the form element, and all inputs will be paginated in double column

Minimal example:
    [[userform]]contact[[/userform]]

Complete example:
    [[userform inline="true"]]contact[[/userform]]

---

#### vimeo('width','height')
Adds necessary HTML code to embed a Vimeo video
- **content**, string, required. Vimeo video's id
- **width**, int, optional. Video's width in pixels
- **height**, int, optional. Video's height in pixels

Minimal example:
    [[vimeo]]vimeo_id[[/vimeo]]

Complete example:
    [[vimeo width="560" height="315"]]vimeo_id[[/vimeo]]

---

#### youtube('width','height','ratio', 'align', 'class')
Adds necessary HTML code to embed a Youtube video
- **content**, string, required. Youtube video's id
- **width**, int, optional. Video's width in pixels
- **height**, int, optional. Video's height in pixels
- **ratio**, string, optional. Can be '16by9' or '4by3'. No other values are allowed. If defined the Twitter Bootstrap responsive layout will be used (http://getbootstrap.com/components/#responsive-embed) and width and height values will be ignored. Twitter Bootstrap >= 3.2 is required
- **align**, string, optional, default false. Can be left, right or center. If the align value is defined the container div will
have the text-{align value} CSS class
- **class**, string, optional, default false. Custom CSS class.

Minimal example:
    [[youtube]]youtube_id[[/youtube]]

Complete example:
    [[youtube width="560" height="315"]]you_tube_id[[/youtube]]
or
    [[youtube ratio="4by3"]]you_tube_id[[/youtube]]
or
    [[youtube width="560" height="315" align="center" class="myTube"]]you_tube_id[[/youtube]]

---

<script>
$('.active h2').after($('<input>').attr('placeholder', 'Search custom tag').on('keyup', function(){
  var val = $(this).val().toLowerCase();

  $('.active h4').each(function(i, el){
    var txt = $(el).text().toLowerCase();
    if (txt.indexOf(val) < 0){
      $(el).hide().nextUntil('h4').hide();
    } else {
      $(el).show().nextUntil('h4').show();
    }
  });
}));
</script>
