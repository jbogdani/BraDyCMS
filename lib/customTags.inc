<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Apr 7, 2013
 */
 
class customTags
{
	public static function cl_gallery($data)
	{
		return self::format_gallery($data, true);
	}
	
	public static function gallery($data)
	{
		return self::format_gallery($data);
	}
	
	private static function format_gallery($data, $responsive = false)
	{
		$gal = $data['content'];
		$rel = $data['rel'];
		$class = $data['class'];
		$lang = $data['lang'];
	
		if (!file_exists(GALLERY_DIR . $gal  . '/data.json'))
		{
			return tr::sget('gallery_does_not_exist', $gal);
		}
	
		$captions = @json_decode(file_get_contents(GALLERY_DIR . $gal  . '/data' . ($lang ? '_' . $lang : '') . '.json'), 1);
	
		if (!is_array($captions))
		{
			return tr::sget('gallery_not_well_formatted', $gal);
		}
		
		
		krsort($captions);
		
		$html = '<div class="gallery-container">';
		
		if ($responsive)
		{
			/**
			 * <ul data-rel="sth" class="a b c cl_gallery">
			 * 	<li data-img="img_src" data-thumb="thumb_img_src">caption text</li>
			 * 	...
			 * </ul>
			 */
			$html .= '<ul class="cl_gallery ' . $gal . ($class ? ' ' . $class : '') . '" ' . ( $rel ? 'data-rel="' . $rel . '"' : '') . '>';
			
			foreach ($captions as $img => $dida)
			{
				$img = str_replace('__x__', '.', $img);
			
				$html .= '<li data-id="' . $img . '" data-img="' . GALLERY_DIR .  $gal . '/' . $img . '" data-thumb="' . GALLERY_DIR . $gal . '/thumbs/' . $img . '">' .
							$dida . 
						'</li>';
			}
			
			$html .='</ul>';
		}
		else
		{
			$html .= '<ul class="gallery ' . $gal . ($class ? ' ' . $class : '') . '">';
			
			foreach ($captions as $img => $dida)
			{
				$img = str_replace('__x__', '.', $img);
			
				$html .= '<li data-id="' . $img . '">' .
						'<a href="' . GALLERY_DIR .  $gal . '/' . $img . '" class="fancybox" title="' . str_replace('"', '\"', $dida). '" ' . ($rel ? ' rel="' . $gal . '" ' : '') . '>' .
						'<img src="' . GALLERY_DIR . $gal . '/thumbs/' . $img . '" alt="' . str_replace('"', '\"', $dida). '" />' .
						'</a>' .
						'<div class="caption">' . $dida . '</div>' .
						'</li>';
			}
			
			$html .='</ul>';
		}
		
		$html .= '</div>';
		return $html;
	}
	
	public static function youTube($data)
	{
		$code = $data['content'];
		$width = $data['width'] ? $data['width'] : 560;
		$height = $data['height'] ? $data['height'] : 315;
		
		return '<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $code . '?rel=0" frameborder="0" allowfullscreen></iframe>';
	}
	
	public static function vimeo($data)
	{
		$code = $data['content'];
		$width = $data['width'] ? $data['width'] : 560;
		$height = $data['height'] ? $data['height'] : 315;
		
		return '<iframe src="http://player.vimeo.com/video/' . $code . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	
	
	public static function twitter($data)
	{
		$user = $data['user'];
		$widget_id = $data['id'];
		$content = $data['content'] ? $data['content'] : 'Tweets by @' . $data['user'];
		 
		return '<a class="twitter-timeline"  href="https://twitter.com/' . $user . '"  data-widget-id="' . $widget_id . '">' . $content. '</a>' .
			'<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
	}
	
	
	private static function fb_sdk()
	{
		return '<div id="fb-root"></div> ' .
				'<script>(function(d, s, id) {' .
					' var js, fjs = d.getElementsByTagName(s)[0];' .
					' if (d.getElementById(id)) return;' .
					' js = d.createElement(s); js.id = id;' .
					' js.src = "//connect.facebook.net/it_IT/all.js#xfbml=1";' .
					' fjs.parentNode.insertBefore(js, fjs);' .
					' }(document, \'script\', \'facebook-jssdk\'));</script>';
	}
	
	
	public static function fb_comments($data)
	{
		$width = $data['width'] ? $data['width'] : 470;
		$num_posts = $data['num_posts'] ? $data['num_posts'] : 10;
		$dark = $data['dark'];
		
		return self::fb_sdk() . '<div class="fb-comments" data-href="http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" data-width="' . $width . '" data-num-posts="' . $num_posts . '" data-colorscheme="' . ($data['colorscheme'] == 'darl' ? 'dark' : 'light') . '"></div>';
	}
	
	
	public static function fb_follow($data)
	{
		$width = $data['width'] ? $data['width'] : 470;
		
		return self::fb_sdk() . '<div class="fb-follow" ' .
					' data-href="' . $data['content'] . '" ' .
					' data-show-faces="' . ($data['show-faces'] ? 'true': 'false' ). '" ' .
					' data-width="' . $width . '"' .
					($data['layout'] ? ' data-layout="' . $data['layout'] . '" ' : '') .
					($data['colorscheme'] ? ' data-colorscheme="' . $data['colorscheme'] . '" ' : '') .
					($data['font'] ? ' data-colorscheme="' . $data['font'] . '" ' : '') .
					' ></div>';
	}
	
	public static function fb_like_box($data)
	{
		$width = $data['width'] ? $data['width'] : 292;
	
		return self::fb_sdk() . '<div class="fb-like-box" ' .
				' data-href="http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" ' .
				' data-show-faces="' . ($data['show-faces'] ? 'true': 'false' ). '" ' .
				' data-width="' . $width . '"' .
				($data['height'] ? ' data-height="' . $data['height'] . '" ' : '') .
				($data['show_faces'] ? ' data-show_faces="' . $data['show_faces'] . '" ' : '') .
				($data['colorscheme'] ? ' data-colorscheme="' . $data['colorscheme'] . '" ' : '') .
				($data['stream'] ? ' data-stream="' . $data['stream'] . '" ' : '') .
				($data['header'] ? ' data-header="' . $data['header'] . '" ' : '') .
				($data['border_color'] ? ' data-border_color="' . $data['border_color'] . '" ' : '') .
				($data['force_wall'] ? ' data-force_wall="' . $data['force_wall'] . '" ' : '') .
				' ></div>';
	}
	
	public static function fb_like($data)
	{
		$width = $data['width'] ? $data['width'] : 450;
	
		return self::fb_sdk() . '<div class="fb-like" ' .
				' data-href="http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" ' .
				($data['layout'] ? ' data-layout="' . $data['layout'] . '" ' : '') .
				' data-show-faces="' . ($data['show-faces'] ? 'true': 'false' ). '" ' .
				' data-width="' . $width . '"' .
				
				($data['action'] ? ' data-action="' . $data['action'] . '" ' : '') .
				($data['font'] ? ' data-font="' . $data['font'] . '" ' : '') .
				($data['colorscheme'] ? ' data-colorscheme="' . $data['colorscheme'] . '" ' : '') .
				($data['ref'] ? ' data-ref="' . $data['ref'] . '" ' : '') .
				' ></div>';
	}
	
	public static function fb_send($data)
	{
		return self::fb_sdk() . '<div class="fb-like" ' .
				' data-href="http://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . '" ' .
				($data['font'] ? ' data-font="' . $data['font'] . '" ' : '') .
				($data['colorscheme'] ? ' data-colorscheme="' . $data['colorscheme'] . '" ' : '') .
				($data['ref'] ? ' data-ref="' . $data['ref'] . '" ' : '') .
				' ></div>';
	}
	
	/**
	 * 
	 * @param array $data
	 * 					content: html to show. Default: addthis small image
	 * @return string
	 */
	public static function addThis($data)
	{
		return '<!-- AddThis Button BEGIN -->' .
				'<a class="addthis_button" href="http://www.addthis.com/bookmark.php?v=300&amp;pubid=ra-4d8b051f4951c18f">' .
					( $data['content'] ? $data['content'] : '<img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="Bookmark and Share" style="border:0"/>') .
				'</a>' .
				'<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4d8b051f4951c18f"></script>' .
			'<!-- AddThis Button END -->';
	}
	
	/**
	 * 
	 * @param array $data
	 * 					content*: skype username
	 * 					type: call, chat, dropdown. Default: call
	 * 					imageSize: 10, 12, 14, 16, 24, 32. Default: 32
	 * @return string
	 */
	public static function skype($data)
	{
		$uid = uniqid('skype');
		
		return '<script type="text/javascript" src="http://cdn.dev.skype.com/uri/skype-uri.js"></script>' . 
			'<div id="' . $uid . '">' .
				'<script type="text/javascript">' .
					'Skype.ui({' .
						'"name": "' . ( $data['type'] ? $data['type'] : 'call' ) . '",' .
						'"element": "' . $uid . '",' .
						'"participants": ["' . $data['content'] . '"],' .
						'"imageSize": ' . ( $data['imageSize'] ? $data['imageSize'] : '32' ) . '' .
					'});' . 
				'</script>' .
			'</div>';
	}
	
	/**
	 * 
	 * @param array $data
	 * 		content*: file url
	 * 		width: widget's width, default 100%
	 * 		height: widget's height, default 166
	 * 		color: widget's color, default #ff6600
	 * 		autoplay: if true autoplay will be enabled, default false
	 * 		artwork: if true artwork will be shown, default false
	 * @return string
	 */
	public static function soundcloud($data)
	{
		return '<iframe ' .
			' width="' . ($data['width'] ? $data['width'] : '100%') . '" ' .
			' height="' . ($data['height'] ? $data['height'] : '166') . '" scrolling="no" ' .
			' frameborder="no" ' .
			' src="https://w.soundcloud.com/player/?' .
				'url=' . $data['content'] . '&amp;' .
				'color=' . ( $data['color'] ? $data['color'] : '#ff6600') . '&amp;' .
				'auto_play=' . ( ($data['autoplay'] && $data['autoplay'] != 'false' ) ?  'true' : 'false') . '&amp;' .
				'show_artwork=' . ( ($data['artwork'] && $data['artwork'] != 'false' ) ?  'true' : 'false') . '">' .
		'</iframe>';
	}
	
	/**
	 * 
	 * @param array $data
	 * 		content*: image file relative url
	 * 		width:	main div (container) width
	 * 		caption: image caption
	 * 		align: image alignent (left|right)
	 * 		href: href of image link
	 * 		href_class: css class of href link
	 * 
	 * @return string
	 */
	public static function figure($data)
	{
		$alt = $data['caption'] ? $data['caption'] : $data['content'];
		
		if ($data['width'])
		{
			$width = $data['width'];
		}
		else
		{
			$file_dim = getimagesize('./sites/default/images/' . $data['content']);
			$width = $file_dim[0];
		}
		
		if ($data['align'])
		{
			$figure_width = $width;
		}
		else if ($data['width'])
		{
			$figure_width = $data['width'];
		}
		
		$alt = str_replace('"', '', strip_tags($alt));
		
		return '<div class="figure' . ($data['align'] ? ' ' . $data['align'] : '') . '" ' . ($figure_width ? ' style="width: ' . $figure_width . '"' : '') . '>' .
			'<div class="image">' .
				( $data['href'] ? '<a href="' . $data['href'] . '"' . ( $data['href_class'] ? ' class="' . $data['href_class'] . '" ' : '') . '>' : '') .
					'<img src="./sites/default/images/' . $data['content'] . '" alt="' . $alt . '" />' .
					( $data['href'] ? '</a>' : '') .
			'</div>' . 
			($data['caption'] ?
					'<div class="caption">' .
						$data['caption'] .
					'</div>' 
					: 
					'') .
		'</div>';
	}
}