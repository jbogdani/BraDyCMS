<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Apr 3, 2013
 */
 
class log_ctrl extends Controller
{
	
	public function out()
	{
		$users_log = './sites/default/users.log';
		
		error_log('user:' . $_SESSION['user_confirmed'] . ' logged OUT on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . "\n", 3, $users_log);
		
		$_SESSION['user_confirmed'] = false;
		$_SESSION['user_admin'] = false;
		
		utils::emptyTmp();
		
		echo '<script>window.location = "./admin"</script>';
	}
	
	public function encodePwd($password = false, $echo = false)
	{
		$password = $password ? $password : $this->request['password'];
		
		$echo = $echo ? $echo : $this->request['echo'];
		
		$password = sha1($password);
		
		if($echo)
		{
			echo $password;
		}
		else
		{
			return $password;
		}
	}
	
	public function in()
	{
		$username = $this->post['username'];
		
		$password = $this->post['password'];
		
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
		
		foreach ($cfg_users as $user)
		{
			if($username == $user['name'] && $this->encodePwd($password) == $user['pwd'])
			{
				$_SESSION['user_confirmed'] = $username;
				if ($user['admin'] == 'admin')
				{
					$_SESSION['user_admin'] = true;
				}
				
				//$json = json_decode(file_get_contents("http://api.easyjquery.com/ips/?ip=" . $_SERVER['REMOTE_ADDR'] . "&full=true"));
					
				error_log('user:' . $username . ' logged IN on ' . date('r') . ' using IP :' . $_SERVER['REMOTE_ADDR'] . (is_object($json) ? ' from ' .$json->countryName . ', ' . $json->cityName : '') . "\n", 3, $users_log);
				
				echo json_encode(array('status' => 'success'));
				return;
			}
			else
			{
				continue;
			}
		}
		
		echo json_encode(array('status' => 'error', 'text' => tr::get('access_denied')));
	}
}