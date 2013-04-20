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
	
		if (!file_exists(GALLERY_DIR . $gal  . '/data.json'))
		{
			return tr::sget('gallery_does_not_exist', $gal);
		}
	
		$data = @json_decode(file_get_contents(GALLERY_DIR . $gal  . '/data.json'), 1);
	
		if (!is_array($data))
		{
			return tr::sget('gallery_not_well_formatted', $gal);
		}
		
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
			
			foreach ($data as $img => $dida)
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
			
			foreach ($data as $img => $dida)
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
}