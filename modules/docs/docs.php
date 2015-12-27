<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 21, 2012
 */
 
class docs_ctrl extends Controller
{
  public function read()
  {
    $file = $this->get['param'][0];
    
    if (file_exists('docs/' . $file . '.md'))
    {
      echo Parsedown::instance()->text(file_get_contents('docs/' . $file . '.md'));
      
      echo '<hr>'
        . '<p class="text-muted"><big><i class="icon big ion-edit"></i></big> Enhance this documentation file: '
        . '<a href="https://github.com/jbogdani/BraDyCMS/edit/dev/docs/' . $file . '.md" target="_blank">edit this page on Github (you must sign in to make or propose changes)</a>'
        . ' or <a href="https://github.com/jbogdani/BraDyCMS/raw/dev/docs/' . $file . '.md" target="_blank">download the raw file</a>, edit it and send it by email to '
        . '<a href="mailto:developer@bradypus.net">developer@bradypus.net</a>'
        . "<script>$('.active a').each(function(i, el){ if ($(el).attr('href').indexOf('http:') > -1 ){ $(el).attr('target', '_blank'); } });</script>" 
      ;
    }
    else if (file_exists(MOD_DIR . 'docs/tmpl/' . $file . '.twig'))
    {
      $this->render('docs', $file, array(
        'art_arr'=>$art_array,
        'docs' => ($file === 'template' ? $this->stuctured_docs() : ''),
        'ct' => ($file === 'customtags' ? $this->structuredCustomTags() : '')
        
      ));
    }
  }
  
  
  public function structuredCustomTags()
  {
   
    
    $ct[] = array(
      'tag' => 'download',
      'description' => 'Displays table with file download node data and links '
        . 'or array with files download data',
      'content' => 'string, required. Name of download node',
      'params' => array(
        'class' => 'string, optional. One or more (space separated) CSS class to apply to main ul tag',
        'getObject' => 'boolean, optional, default false. If true only array of data will be returned, '
          . 'otherwize, default option, full html will be returned'
        ),
      'min_example' => '[[download]]node_name[[/download]]',
      'max_example' => '[[download class="table table-striped"]]node_name[[/download]]'
    );
    
    
    $ct[] = array(
      'tag' => 'cl_gallery',
      'description' => 'Displays imageless galleries, for conditional image loading. ' .
        'This custom tags should be used together with the provided jquery.cl_gallery.js javascript plugin',
      'content' => 'string, required. Name of gallery',
      'params' => array(
        'class' => 'string, optional. One or more (space separated) CSS class to apply to main ul tag'
        ),
      'min_example' => '[[cl_gallery]]gallery_name[[/cl_gallery]]',
      'max_example' => '[[cl_gallery class="horizontal small"]]gallery_name[[/cl_gallery]]'
    );
    
    $ct[] = array(
      'tag' => 'gallery',
      'description' => 'Displays an existing image gallery',
      'content' => 'string, required. Name of gallery',
      'params' => array(
        'class' => 'string, optional. One or more (space separated) CSS class to apply to main ul tag'
        ),
      'min_example' => '[[gallery]]gallery_name[[/gallery]]',
      'max_example' => '[[gallery class="horizontal small"]]gallery_name[[/gallery]]'
    );
    
    $ct[] = array(
      'tag' => 'twitter',
      'description' => 'Adds the necessary HTML and javascript code to show ' . 
        'a Twitter widget',
      'content' => 'string, optional. The text that will be shown if something goes ' .
        'wrong and Twitter widget fails to be initialized.',
      'params' => array(
        'twitter_username' => 'string, required. Twitter username',
        'id' => 'string, required. The ) The widget\'s id, provided by Twitter'
        ),
      'min_example' => '[[twitter user="twitter_username" id="twitter_widget_id"]]Some intro text[[/twitter]]'
    );
    
    $ct[] = array(
      'tag' => 'youtube',
      'description' => 'Adds necessary HTML code to embed a Youtube video',
      'content' => 'string, required. Youtube video\'s id',
      'params' => array(
        'width' => 'int, optional. Video\'s width in pixels',
        'height' => "int, optional. Video's height in pixels",
        'ratio' => "string, optional. Can be '16by9' or '4by3'. No other values are allowed. If defined the Twitter Bootstrap responsive layout will be used (http://getbootstrap.com/components/#responsive-embed) and width and height values will be ignored. Twitter Bootstrap >= 3.2 is required"
        ),
      'min_example' => '[[youtube]]youtube_id[[/youtube]]',
      'max_example' => '[[youtube width="560" height="315"]]you_tube_id[[/youtube]]'
    );
    
   $ct[] = array(
      'tag' => 'vimeo',
      'description' => 'Adds necessary HTML code to embed a Vimeo video',
      'content' => 'string, required. Vimeo video\'s id',
      'params' => array(
        'width' => 'int, optional. Video\'s width in pixels',
        'height' => "int, optional. Video's height in pixels"
        ),
      'min_example' => '[[vimeo]]vimeo_id[[/vimeo]]',
      'max_example' => '[[vimeo width="560" height="315"]]vimeo_id[[/vimeo]]'
    );
    
   
    
   $ct[] = array(
      'tag' => 'fb_comments',
      'description' => 'Adds the necessary HTML and javascript code to display ' . 
        'Facebook\'s comments widget. For a list of all the available options '.
        'check this link: https://developers.facebook.com/docs/reference/plugins/comments',
      'content' => '-',
      'params' => array(
        'width' => 'int, optional, default: 470. Widget\'s width, in pixels.',
        'num_posts' => 'int, required, default: 10. Number of posts to display',
        'colorscheme' => 'string, optional, default: light, available values: light, dark.' .
          'The color scheme to use'
        ),
      'min_example' => '[[fb_comments]][[/fb_comments]]',
      'max_example' => '[[fb_comments width="470" posts="10" colorscheme="dark"]][[/fb_comments]]'
    );
    
   $ct[] = array(
      'tag' => 'fb_follow',
      'description' => 'Adds the necessary HTML and javascript code to display ' . 
        'Facebook\'s follow widget. For a list of all the available options '.
        'check this link: https://developers.facebook.com/docs/reference/plugins/follow',
      'content' => 'string, required. URL of the Facebook page to follow',
      'min_example' => '[[fb_follow]]http://facebook.com/rest_of_the_url_to_follow[[/fb_follow]]'
    );
    
   $ct[] = array(
      'tag' => 'fb_like_box',
      'description' => 'Adds the necessary HTML and javascript code to display ' . 
        'Facebook\'s Like-box widget. For a list of all the available options '.
        'check this link: https://developers.facebook.com/docs/reference/plugins/like-box',
      'content' => '-',
      'min_example' => '[[fb_like_box]][[/fb_like_box]]'
    );
    
   $ct[] = array(
      'tag' => 'fb_like',
      'description' => 'Adds the necessary HTML and javascript code to display ' . 
        'Facebook\'s Like widget. For a list of all the available options '.
        'check this link: https://developers.facebook.com/docs/reference/plugins/like',
      'content' => '-',
      'min_example' => '[[fb_like]][[/fb_like]]'
    );
    
   $ct[] = array(
      'tag' => 'fb_send',
      'description' => 'Adds the necessary HTML and javascript code to display ' . 
        'Facebook\'s Send widget. For a list of all the available options '.
        'check this link: https://developers.facebook.com/docs/reference/plugins/send',
      'content' => '-',
      'min_example' => '[[fb_send]][[/fb_send]]'
    );
    
   $ct[] = array(
      'tag' => 'addThis',
      'description' => 'Adds the necessary HTML and javascript code to display ' .
        'an AddThis share widget',
      'content' => 'string, optional. HTML or text to show in your page. If not provided the default AddThis image will be shown',
      'params' => array(
        'only_js' => 'boolean, optional, default false (not present). ' .
          'If true no HTML will be shown from AddThis, only javascript will be included'),
      'min_example' => '[[addThis]][[/addThis]]',
      'max_example' => '[[addThis only_js="true"]]Share +[[/addThis]]'
    );
    
   $ct[] = array(
      'tag' => 'skype',
      'description' => 'Adds the necessary HTML anf javascript code to display '.
        'a Skype call, chat call & chat widget',
      'content' => 'string, required. Skype username',
      'params' => array(
        'type' => 'string, optional, default: call, available values: call, chat, dropdown.' . 
          'Widget type: call, chat or both (dropdown)',
        'imageSize' => 'int, optinal, default: 32, available values: 10, 12, 14, 16, 24, 32.' .
          'The size, in pixels, of the image to show.'
        ),
      'min_example' => '[[skype]]skype_username[[/skype]]',
      'max_example' => '[[skype type="call" imageSize="32"]]skype_username[[/skype]]'
    );
    
   $ct[] = array(
      'tag' => 'soundcloud',
      'description' => 'Adds the necessary HTML anf javascript code to display '.
        'a SoundCloud player',
      'content' => 'string, required. Track URL',
      'params' => array(
        'width' => 'int, optional, default: 100%. The widget\'s width in pixels',
        'height' => 'int, optional, default: 100%. The widget\'s height in pixels',
        'color' => 'string, optinal, default: #ff6600". The widget\'s color.',
        'autoplay' => 'boolean, optional, default: false. ' .
          'If true the track will start playing after page is loaded',
        'artwork' => 'boolean, optional, default: false. ' .
          'If true the track\'s artwork will be displayed'
        ),
      'min_example' => '[[soundcloud]]track_url[[/soundcloud]]',
      'max_example' => '[[soundcloud width="100%" height="166" color="#ff6600" autoplay="true" artwork="true"]]track_url[[/soundcloud]]'
    );
    
   $ct[] = array(
      'tag' => 'prezi',
      'description' => 'Adds the necessary HTML anf javascript code to display '.
        'a Prezi presentation',
      'content' => 'string, required. Prezi presentation\'s ID',
      'params' => array(
        'bgcolor' => 'string, optional, default: ffffff. Background color.',
        'lock_to_path' => 'boolean, optional, default: 0. If 1 the user will be locked to the given path',
        'autoplay' => 'boolean, optional, default: 0. ' .
          'If 1 the presentation will start after page is loaded',
        'autohide_ctrls' => 'boolean, optinal, default:0.' .
          'If 1 the conftols will be hidden automatically',
        'width' => 'int, optional, deafult: 500. Widget\'s width in pixels',
        'height' => 'int, optional, deafult: 550. Widget\'s height in pixels',
        ),
      'min_example' => '[[prezi]]prezi_id[[/prezi]]',
      'max_example' => '[[prezi width="500" height="550" color="#ff6600" autoplay="true" artwork="true"]]prezi_id[[/prezi]]'
    );
   
   $ct[] = array(
      'tag' => 'fig',
      'description' => 'Adds complete HTML code to display figures and captions easily. This custom tag, differntly from the [[figure]] custom tag permits the use of rich html text in captions',
      'content' => 'string, optional. The image\'s caption',
      'params' => array(
        'path' => 'string, required. Images\'s path, without the "/sites/default/images/" part.',
        'width' => 'int, optional. Image\'s max-width. If absent the file width will be used',
        'align' => 'string, optional, available values: left, right, center. ' .
          'Widget\'s alignment in the page',
        'fancybox' => 'boolean, optional, default false. If present the system will try to create a valid html fancybox popup, using thumbnail, if present',
        'href' => 'string, optional. If present the image will hold a link to this URL',
        'href_class' => 'string, optional. If present the imag\'s link will have this CSS class',
        'gal' => 'string, optional. If present the string will be used to collect images in the same page in galleries'
        ),
      'min_example' => '[[fig path="image_url"]][[/fig]]',
      'max_example' => '[[fig path="image_url" width="300px" align="left" href="http://bradypus.net" href_class="bordered" gal="first_gallery"]]image_caption[[/figure]]'
    );
   
   $ct[] = array(
      'tag' => 'figure',
      'description' => 'Adds complete HTML code to display figures and captions easily',
      'content' => 'string, required. Images\'u path, without the "/sites/default/images/" part.',
      'params' => array(
        'width' => 'int, optional. Image\'s max-width. If absent the file width will be used',
        'caption' => 'string, optional. The image\'s caption',
        'align' => 'string, optional, available values: left, right, center. ' .
          'Widget\'s alignment in the page',
        'fancybox' => 'boolean, optional, default false. If present the system will try to create a valid html fancybox popup, using thumbnail, if present',
        'href' => 'string, optional. If present the image will hold a link to this URL',
        'href_class' => 'string, optional. If present the imag\'s link will have this CSS class',
        'gal' => 'string, optional. If present the string will be used to collect images in the same page in galleries'
        ),
      'min_example' => '[[figure]]image_url[[/figure]]',
      'max_example' => '[[figure width="300px" caption="image_caption" align="left" href="http://bradypus.net" href_class="bordered" gal="first_gallery"]]image_url[[/figure]]'
    );
    
   $ct[] = array(
      'tag' => 'disqus',
      'description' => 'Adds the necessary HTML and javascript code to display ' .
        'Disqus comments widget.',
      'content' => 'string, required. Site\'s shortname in disqus',
      'min_example' => '[[disqus]]disqus_website_id[[/disqus]]'
    );
    
   $ct[] = array(
      'tag' => 'flash',
      'description' => 'Adds HTML code to include a FLW file',
      'content' => 'string, required. Path to flw file',
      'params' => array(
        'width' => 'int, optional. Object\'s width',
        'height' => 'int, optional. Object\'s height',
        'other' => 'Any other parameter can be added.'
        ),
      'min_example' => '[[flash]]./sites/default/images/flash/test.flw[[/flash]]',
      'max_example' => '[[flash width="400" height="300" autoplay="true"]]./sites/default/images/flash/test.flw[[/flash]]'
    );
    
   $ct[] = array(
      'tag' => 'userform',
      'description' => 'Shows a user form in article content',
      'content' => 'string, required. User form name (uique ID)',
      'params' => array(
        'inline' => 'boolean, default: false. If true the form-inline CSS class ' .
          'will be added to the form element, and all inputs will be paginated in double column'
        ),
      'min_example' => '[[userform]]contact[[/userform]]',
      'max_example' => '[[userform inline="true"]]contact[[/userform]]'
    );
   
   $ct[] = array(
      'tag' => 'link',
      'description' => 'Adds an internal link to an article',
      'content' => 'string, required. Text of the link content that will be visible to users',
      'params' => array(
        'art' => 'string, required. Textid of the destination article. To link to home page use the string "home"',
        'title' => 'string, optional. Link\'s title attribute',
        'rel' => 'string, optional. Link\'s rel attribute',
        'class' => 'string, optional. Link\'s class attribute',
        'id' =>  'string, optional. link\'s id attribute'
        ),
      'min_example' => '[[link art="contacts"]]Contact us[[/link]]',
      'max_example' => '[[link art="contacts" title="Contact us" rel="help" class="primary-link" id="contact"]]Contact us[[/link]]'
    );
   
   $ct[] = array(
      'tag' => 'map',
      'description' => 'Shows a user map in article content',
      'content' => 'string, required. User formap name (uique ID)',
      'params' => array(
        'width' => 'string, default: 100%. Width of the map container',
        'height' => 'string, default: 400%. height of the map container'
        ),
      'min_example' => '[[map]]where_we_are[[/map]]',
      'max_example' => '[[map width="300px" height="200px"]]where_we_are[[/map]]'
    );
    
    return $ct;
    /*
    $ct[] = array(
      'tag' => '',
      'description' => '',
      'content' => '',
      'params' => array('' => ''),
      'min_example' => '',
      'max_example' => ''
    );
     */
  }
  
}
