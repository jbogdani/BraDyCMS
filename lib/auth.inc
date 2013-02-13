<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Dec 1, 2012
 */
 
class auth
{
	public static function check($username = false, $password = false)
	{
		if (!$username && !$password)
		{
			return false;
		}
		
		$users_log = './sites/default/users.log';
		
		// Create users log if it does not exist
		if (!file_exists($users_log))
		{
			$fh = @fopen($users_log, 'w');
			@fclose($fh);
		}
		
		$cfg_users = cfg::get('users');
		
		if(array_key_exists($username, $cfg_users) AND $cfg_users[$username] == sha1($password))
		{
			$_SESSION['user_confirmed'] = true;
		
			$json = json_decode(file_get_contents("http://api.easyjquery.com/ips/?ip=" . $_SERVER['REMOTE_ADDR'] . "&full=true"));
		
			error_log('user:' . $username . ' logged IN on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . (is_object($json) ? ' from ' .$json->countryName . ', ' . $json->cityName : '') . "\n", 3, $users_log);
			return false;
		}
		else
		{
			return tr::get('access_denied');
		}
		
	}
	
	public static function logout()
	{
		$_SESSION['user_confirmed'] = false;
		
		error_log('user:' . $_POST['username'] . ' logged OUT on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . "\n", 3, $users_log);
		
		return tr::get('logged_out');
	}
}