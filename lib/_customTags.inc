<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Feb 22, 2013
 * 
 * $str = 'Lorem ipsum [link file="5"]capitolo5[/link] Lorem ipsum [fig name="0101.jpg" class="ciao"]didascalia[/fig] '
 * 			. 'Lorem ipsum[note]Note text[/note] '
 * 			. 'Lorem ipsum[note]Note text 2[/note]';
 * 
 * $twig = new Twig_Environment(new Twig_Loader_String());
 * $twig->addFilter(new Twig_SimpleFilter('parse', array('customTags', 'parseHtml')));
 * 
 * echo $twig->render('<strong>Original:</strong><pre>{{ string }}</pre>'
 * 		. '<strong>HTML output:</strong><pre>{{ string|parse|raw }}</pre>',
 * 		array('string' => $str));
 * 
 */
 
class customTags
{
	private static $counter = 0;
	
	private static $notes = array();
	
	public static function parseHtml($html)
	{
		preg_match_all('/\[(\w+)\s*([^\]]*)\](.*?)\[\/\1\]/s', $html, $customTags, PREG_SET_ORDER);
		
		foreach ($customTags as $customTag)
		{
			$originalTag = $customTag[0];
	
			$tag = $customTag[1];
	
			$rawAttributes = $customTag[2];
	
			$content = $customTag[3];
				
			preg_match_all('/([^=\s]+)="([^"]+)"/', $rawAttributes, $attributes, PREG_SET_ORDER);
				
			$formatedAttributes['content'] = $content;
				
			foreach ($attributes as $attribute)
			{
				$formatedAttributes[$attribute[1]] = $attribute[2];
			}
			
			if (method_exists(get_class(), $tag))
			{
				$replace = call_user_func(array(get_class(), $tag), $formatedAttributes);
				
				$html = str_replace($originalTag, $replace, $html);
			}
		}
			
		return $html . '<hr />' . self::renderFootNotes();
	}
	
	
	private static function link($attr)
	{
		return '<a href="' . $attr['file'] . '">' . $attr['content'] . '</a>';
	}
	
	private static function fig($attr)
	{
		error_log('called: ' . microtime(1));
		return '<figure' . ($attr['class'] ? ' class="' . $attr['class'] . '" ' : '') . '>'
				. '<div>'
					. '<img src="' . $attr['name'] . '" alt="' . ($attr['content'] ?  $attr['content'] : $attr['name']) . '" />'
					. ($attr['content'] ? '<figcaption>' . $attr['content'] . '</figcaption>' : '')
				. '</div>'
			.'</figure>';
	}
	
	private static function note($attr)
	{
		self::$notes[] = $attr['content'];
		
		return '<a href="#ft-note-' . count(self::$notes) . '" id="bd-note-' . count(self::$notes) . '" title="' . $attr['content'] . '">[' . count(self::$notes) . ']</a>';
	}
	
	private static function renderFootNotes()
	{
		if (count(self::$notes) == 0)
		{
			return;
		}
		
		$html = '<div class="footNotes">';
		foreach(self::$notes as $k => $note)
		{
			$html .= '<p><a href="#bd-note=' . ($k + 1) . '">' . ($k + 1) . '</a>. ' . $note . '</p>';
		}
		$html .= '</div>';
		
		return  $html;
	}
}