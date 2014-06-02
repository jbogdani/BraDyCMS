<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2012
 * @license			MIT, See LICENSE file
 * @since			Jan 10, 2012
 */
 
class Autoloader
{
	public static function start()
	{
		spl_autoload_register(array(self, 'loader'));
	}
	
	private static function loader($className)
	{
		if (class_exists($className))
		{
			return true;
		}
    
    // Manually installed external libraries
    switch($className)
    {
      case 'Less_Parser':
        require_once LIB_DIR . 'vendor/lessphp/Less.php';
        return true;
        break;
        
      case 'R':
        require_once LIB_DIR . 'vendor/redbean/rb.php';
        return true;
        break;
        
      case 'Curl':
        require_once LIB_DIR . 'vendor/oocurl/OOCurl.php';
        return true;
        break;
        
      case 'Mobile_Detect':
        require_once LIB_DIR . 'vendor/Mobile_Detect/Mobile_Detect.php';
        return true;
        break;
        
      case 'PHPMailer':
        require_once LIB_DIR . 'vendor/phpmailer/class.phpmailer.php';
        return true;
        break;
      
      case 'FeedWriter\Atom':
        require_once LIB_DIR . 'vendor/FeedWriter/Feed.php';
        require_once LIB_DIR . 'vendor/FeedWriter/Item.php';
        require_once LIB_DIR . 'vendor/FeedWriter/ATOM.php';
        return true;
        break;
      
      case 'FeedWriter\RSS2':
        require_once LIB_DIR . 'vendor/FeedWriter/Feed.php';
        require_once LIB_DIR . 'vendor/FeedWriter/Item.php';
        require_once LIB_DIR . 'vendor/FeedWriter/RSS2.php';
        return true;
        break;
    }
		
		if (preg_match('/_ctrl/', $className))
		{
			$mod = str_replace('_ctrl', null, $className);
			if(file_exists(MOD_DIR . $mod . '/' . $mod . '.php'))
			{
				require_once MOD_DIR . $mod . '/' . $mod . '.php';
			}
			else
			{
				return false;
			}
		}
		else
		{
			if (file_exists('lib/' . $className . '.inc'))
			{
				require_once 'lib/' . $className . '.inc';
			}
			else if (file_exists('./sites/default/modules/' . $className . '/' . $className . '.inc'))
			{
				require_once './sites/default/modules/' . $className . '/' . $className . '.inc';
			}
			else
			{
				return false;
			}
		}
		
	}
}