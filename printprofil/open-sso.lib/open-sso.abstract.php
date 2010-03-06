<?php
/**
 * Core Absctract for OpenSSO PHP library.
 *
 * This class implements the API and the core functions.
 *
 * @category   OpenSSO_PHP
 * @package    OpenSSO_PHP
 * @link       http://kir-dev.sch.bme.hu/opensso-phplib/
 * @author     Pásztor Gergő <pairghu@gmail.com> from KirDev <kir-dev@sch.bme.hu>
 * @copyright  Copyright (c) 2009, KirDev
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL
 * @filesource
 *
 * @version 1.5.1
 */

require_once('open-sso.interface.php');

abstract class openSSO_Abstract implements openSSO_API
{

	/**
	 * Abstract functions
	 */

	/**
	 * Get data from the session.
	 *
	 * @param (string) $name Name of the data.
	 * @return (multi) Value of the data.
	 */
	abstract protected function getSessionData($name);
	
	/**
	 * Set data to the session.
	 * Set the '$value' data to the session with '$name' name.
	 * If this name exists overwrite it.
	 * If this name don't exists create it.
	 *
	 * @param (string) $name Name of the data.
	 * @param (multi) $value Value of the data.
	 * @return void
	 */
	abstract protected function setSessionData($name, $value);
	
	/**
	 * Create user.
	 * Create user with data from the '$data' param (user's SSO data).
	 * Insert to DB or whatever you store the users.
	 * Syntax: The data name that you setted in config file ('user' array) with 'sso_' prefix.
	 *	        Plus 'user_name', 'user_virid', 'user_groups' variables, and these with 'sso_' prefix.
	 *
	 * @param (array) $data Single array of the user's SSO data. Keys represents the data name.
	 * @return void
	 */
	abstract protected function insertUser($data);
	
	/**
	 * Get the user's data.
	 * Select user where the user name is equal with the '$userName' param.
	 * Read from DB or whatever you store the users.
	 * Return with all of the data that you store to this user (SSO and none SSO data).
	 * You must return the data names that the program add when the data stored (insert, update)!
	 *
	 * @param (string) $userName The unique user name.
	 * @return (array) Single array of the user's data (SSO and none SSO). Keys represents the data name.
	 */
	abstract protected function selectUser($userName);
	
	/**
	 * Update user's data.
	 * Update all (SSO and none SSO) user's data where the user name is equal with the '$userName' param.
	 * Update in DB or whatever you store the users.
	 * '$data' syntax: The data name that you setted in config file with 'sso_' prefix.
	 *                 Plus 'user_name', 'user_virid', 'user_groups' variables, and these with 'sso_' prefix.
	 *                 Plus your other data that added with 'setUserData();' function.
	 *
	 * @param (string) $userName The unique user name.
	 * @param (array) $data Single array of the user's data (SSO and none SSO). Keys represents the data name.
	 * @return void
	 */
	abstract protected function updateUser($userName, $data);

	/**
	 * End of Abstract functions
	 */


	/**
	 * Hooks
	 */
	
	/**
	 * Hook: Login
	 * Call this function after the lib checked the required user data, and created the session.
	 * (Call this function after the login method.)	 
	 *
	 * @param void
	 * @return void
	 */
	protected function hookLogin(){}
	
	/**
	 * Hook: Logout
	 * Call this function after the lib "destroy" the session.
	 * (Call this function after the logout method.)	 
	 *
	 * @param void
	 * @return void
	 */
	protected function hookLogout(){}

	/**
	 * Hook: Is Error?
	 * Call this function to determine that error is occured or not.
	 *
	 * @param void
	 * @return (bool) 'TRUE' if error is occured and 'FALSE' otherwise.
	 */
	protected function hookIsError(){}

	/**
	 * Hook: Error
	 * Call this function when the IDP or the SP is down.
	 *
	 * @param void
	 * @return void
	 */
	protected function hookError(){}
	
	/**
	 * End of Hooks
	 */
	
	
	
	/**
	 * Class data
	 */
	
	// Data from IDP
	private $userName;
	private $userGroups;
	private $userVirid;
	private $ssoUser = array();
	
	// Data from database
	private $user = array();
	
	// Other data
	private $conf = array();
	private $idpCookie;
	
	/**
	 * End of Class data
	 */
	
	
	/**
	 * PHP5 constructor.
	 */
	function __construct()
	{
		// Load config
		$this->_loadConfig();

		// Downtime check
		if($this->_isError()){
			$this->_logout();
			$this->hookError();
			return;
		}

		// Get HTTPS protocol
		if($this->conf['ssl'] AND $this->isLogin() === TRUE) $this->https();

		// Set the user's data
		$this->userName = $this->server($this->conf['userName']);
		if(is_array($this->conf['user']))
		{
			foreach($this->conf['user'] AS $key => $value)
			{
				if($this->server($value) !== FALSE) $this->ssoUser['sso_'.$key] = $this->server($value);
			}
		}

		// Clean and convert user's special data
		$this->userGroups = $this->_setGroups();
		$this->userVirid = $this->_setVirid();

		if($this->conf['shibSp'])
		{
			// If the user logged in, but the user name not setted OR the shib session is inactive: logout
			if($this->_isRealLogin() === FALSE AND $this->isLogin() === TRUE)
			{
				$this->setSessionData('sso_login_state', TRUE);
				$this->logOut();
			}

			// If the session's user name not equal with the server's user name: reset user's data
			if($this->getSessionData('sso_user') !== $this->userName AND $this->isLogin() === TRUE)
			{
				$this->_logout();
				$this->_login();
			}
		}
		else
		{
			// Set idpCookie
			$this->idpCookie = $this->cookie($this->conf['idpCookie']);

			// If the session's user name not equal with the server's user name: reset user's data
			if($this->getSessionData('sso_user') !== $this->userName AND $this->isLogin() === TRUE)
			{
				$this->_logout();
				$this->_login();
			}

			// If the user logged in, but logged out in idp: logout
			if(empty($this->idpCookie) AND $this->isLogin() === TRUE)
			{
				$this->setSessionData('sso_login_state', TRUE);
				$this->logOut();
			}

			// If the user logged in, but their data in the server not setted: redirect to trigger
			if($this->isLogin() === TRUE AND !empty($this->idpCookie) AND empty($this->userName))
				$this->_redirect($this->_path2url($this->conf['trigger']));

		}
	}
	
	
	/**
	 * PHP5 destructor
	 */
	function __destruct()
	{
		unset($this->userName);
	}
	
	
	/**
	 * Load config and set default values.
	 * Default values make more compatibility to older versions.
	 *
	 * @param void
	 * @return void
	 */
	private function _loadConfig()
	{
		// Load config
		if(!defined('SSOCONFIG'))
		{
			define('SSOCONFIG', 1);
			// Compatibility to ver. 1.0.0
			define('CONFIG', 1);
			require_once('config.php');
			$this->conf = $config;
		}

		// Convert version 1.0.0 config to 1.5.0 config - Compatibility
		$convertConfig = array(
			'user_name' => 'userName',
			'user_groups' => 'userGroups',
			'user_virid' => 'userVirid',
			'loginUrl' => 'idpLoginUrl',
			'logoutUrl' => 'idpLogoutUrl',
			'returnIdpParam' => 'idpUrlParam',
			'returnParam' => 'urlParam'
			);

		foreach($convertConfig AS $old => $new)
		{
			if(!isset($this->conf[$new]) AND isset($this->conf[$old])) $this->conf[$new] = $this->conf[$old];
		}
		
		// Compatibility - Default values
		$defaultConfig = array(
			'shibSp' => FALSE,
			'shibIdpLogout' => TRUE,
			'ssl' => TRUE,
			'userName' => 'REMOTE_USER',
			'userGroups' => 'HTTP_EDUPERSONENTITLEMENT',
			'userVirid' => 'HTTP_VIRID',
			'user' => '',
			'userRequire' => '',
			'idpCookie' => 'sunIdentityServerAuthNServer',
			'idpLoginUrl' => 'https://idp.sch.bme.hu/opensso/UI/Login',
			'idpLogoutUrl' => 'https://idp.sch.bme.hu/opensso/UI/Logout',
			'idpUrlParam' => 'goto',
			'spLoginUrl' => '/Shibboleth.sso/Login',
			'spLoginUrlParam' => 'target',
			'spLogoutUrl' => '/Shibboleth.sso/Logout',
			'spLogoutUrlParam' => 'return',
			'trigger' => '/trigger.php',
			'loginPage' => '',
			'urlParam' => '',
			'downtimeStart' => '',
			'downtimeEnd' => ''
		);

		foreach($defaultConfig AS $configName => $configValue)
		{
			if(!isset($this->conf[$configName])) $this->conf[$configName] = $configValue;
		}

		// Overwrite the SSL config when you use Shibboleth SP
		if($this->conf['shibSp']) $this->conf['ssl'] = TRUE;
		
	}
	

	/**
	 * Is Error?
	 *
	 * @param void
	 * @return (bool) 'TRUE' if error is occured and 'FALSE' otherwise.
	 */
	private function _isError()
	{
		if(!empty($this->conf['downtimeStart']))
		{
			$start = strtotime($this->conf['downtimeStart']);
			if($start === FALSE OR $start === -1) $start = 0;
		} else $start = 0;

		if(!empty($this->conf['downtimeEnd']))
		{
			$end = strtotime($this->conf['downtimeEnd']);
			if($end === FALSE OR $end === -1) $end = 0;
		} else $end = 0;
		
		$downtime = FALSE;
		if(!$start AND !$end) $downtime = FALSE;
		elseif(!$end) if($start <= time()) $downtime = TRUE;
		elseif(!$start) if($end >= time()) $downtime = TRUE;
		elseif($start AND $end) if($start <= now() AND $end >= now()) $downtime = TRUE;

		$hook = $this->hookIsError();
		if(isset($hook) AND is_bool($hook)) $downtime = $hook;
		
		return $downtime;
	}
	
	
	/**
	 * Get the element of the '$_SERVER' array.
	 *
	 * @param (string) $element Element's name. For example: 'REMOTE_USER' -> $_SERVER['REMOTE_USER']
	 * @return (bool) FALSE if the element isn't setted, and the value of the element if is setted.
	 */
	private function server($element)
	{
		if(@isset($_SERVER[$element])) return $_SERVER[$element];
		else return FALSE;
	}
	
	/**
	 * Get the element of the '$_COOKIE' array.
	 *
	 * @param (string) $element Element's name. For example: 'sessionid' -> $_COOKIE['sessionid']
	 * @return (bool) FALSE if the element isn't setted, and the value of the element if is setted.
	 */
	private function cookie($element)
	{
		if(@isset($_COOKIE[$element]) AND !empty($_COOKIE[$element])) return $_COOKIE[$element];
		else return FALSE;
	}
	
	
	/**
	 * Create URL from path.
	 *
	 * @param (string) $path The path of the page
	 * @return (string) The complete URL
	 */
	private function _path2url($path = '')
	{
		$path = urldecode($path);
		$http = substr($path, 0, 7);
		$https = substr($path, 0, 8);
		if($http == 'http://' OR $https == 'https://' ) return $path;
		if(!$this->conf['ssl'] AND !$this->isHttps()) $protocol = 'http://';
		else $protocol = 'https://';
			
		if($path[0] == '/') $slash = '';
		else $slash = '/';
		
		return $protocol.$this->server('HTTP_HOST').$slash.$path;
	}
	
	
	/**
	 * Redirect the browser.
	 * If you set the second parameter this function save from redirect looping. After the 5. redirect stop it.
	 * When you exit from the loop use this code:
	 * $this->setSessionData('sso_loop_<loop name>', 0);
	 *
	 * @param (string) $url Where to redirect.
	 * @param (string) $redirectLoop The unique name of the redirect loop. One name - one loop.
	 * @return void
	 */
	private function _redirect($url, $redirectLoop = '')
	{
		// Error
		if($this->_isError()) return;

		// Save from redirect loop
		if(!empty($redirectLoop))
		{
			$loop = $this->getSessionData('sso_loop_'.$redirectLoop);
			$loop = (int)$loop + 1;
			if($loop >= 5)
			{
				$this->setSessionData('sso_loop_'.$redirectLoop, 0);
				return;
			}
			else $this->setSessionData('sso_loop_'.$redirectLoop, $loop);
		}
		
		// Redirect
		header('Location: '.$url);
		exit(0);
	}
	
	
	/**
	 * Get HTTPS connection, not HTTP.
	 * If the user request this 'page' without SSL (HTTP) this function redirect to the secured version of the 'page'
	 * (HTTPS). For example: http://example.org/dir/file.php?var=1 redirect to https://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return void
	 */
	public function https()
	{
		if(!$this->isHttps())
		{
			header('Location: https://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI'));
			exit(0);
		}
	}
	
	
	/**
	 * Get HTTP connection, not HTTPS.
	 * If the user request this 'page' with SSL (HTTPS) this function redirect to the non secured version of the 'page'
	 * (HTTP). For example: https://example.org/dir/file.php?var=1 redirect to http://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return void
	 */
	public function http()
	{
		if($this->isHttps())
		{
			header('Location: http://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI'));
			exit(0);
		}
	}
	
	
	/**
	 * Is secured, HTTPS connection?.
	 * If the user request this 'page' without SSL (HTTP) this function redirect to the secured version of the 'page'
	 * (HTTPS). For example: http://example.org/dir/file.php?var=1 redirect to https://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return (bool) TRUE if is a HTTPS connection and FALSE is a HTTP connection.
	 */
	public function isHttps()
	{
		$lower = strtolower($this->server('HTTPS'));
		$https = $this->server('HTTPS');
		if(isset($https) AND $lower === 'on') return TRUE;
		else return FALSE;
	}
	
	
	/**
	 * Set groups
	 *
	 * @param void
	 * @return (array) Syntax:
	 *         array( '<group id>' => array( 'positions' => array('<position>', ...), 'name' => '<group name>' ) )
	 */
	private function _setGroups()
	{
		$g = $this->server($this->conf['userGroups']);
		$groups = array();
		if($g)
		{
			if($this->conf['shibSp']) $exploder = ';';
				else $exploder = '|';
			$g = explode($exploder, $g);
			for($i=0; $i<count($g); $i++)
			{
				$data = explode(':', $g[$i]);
				$name = $data[6];
				$id = $data[7];
				$position = $data[5];
				if(isset($groups[$id]) AND !empty($groups[$id]['positions'])) (array)$group['positions'] = $groups[$id]['positions'];
				(array)$group['positions'][] = $position;
				$group['name'] = $name;
				$groups[$id] = $group;
				unset($group);
			}
		}
		return $groups;
	}
	
	
	/**
	 * Set VirID
	 *
	 * @param void
	 * @return (string) VIRID
	 */
	private function _setVirid()
	{
		$virid = $this->server($this->conf['userVirid']);
		$place = strrpos($virid, ':');
		return substr($virid, $place+1);
	}
	
	
	
	/*
	 * Login - Logout
	*/	
	
	/**
	 * Login the user to the application.
	 * Check required user data, create session, update user's SSO data or create user.
	 * 
	 * @param void
	 * @return void
	 */
	private function _login()
	{
		// Check required user data. If one of these data is empty: return null.
		if(is_array($this->conf['userRequire']))
		{
			for($i=0; $i < count($this->conf['userRequire']); $i++)
			{
				$req = $this->conf['userRequire'][$i];
				if(isset($this->conf['user'][$req]))
				{
					$userData = $this->conf['user'][$req];
					if(isset($userData) AND !$this->server($userData)) return;
				}
			}
		}

		// If user not exists: create new user
		$user = $this->selectUser($this->userName);
		if(empty($user))
		{
			$insertData = $this->ssoUser;
			$insertData['user_name'] = $this->userName;
			$insertData['user_groups'] = $this->userGroups;
			$insertData['user_virid'] = $this->userVirid;
			$this->insertUser($insertData);
			$this->user = $this->selectUser($this->userName);
		}
		else
		{
			// Check for updated SSO data and update it
			$newData = $this->ssoUser;
			$newData['user_name'] = $this->userName;
			$newData['user_groups'] = $this->userGroups;
			$newData['user_virid'] = $this->userVirid;
			$updateData = array();
			foreach($newData AS $data => $value)
			{
				if(isset($user[$data]) AND $user[$data] != $value) $updateData[$data] = $value;
			}
			if(!empty($updateData)) $this->updateUser($this->userName, $updateData);
		}

		// Login
		$this->setSessionData('sso_login', TRUE);
		$this->setSessionData('sso_user', $this->userName);
		
		// Login Hook
		$this->hookLogin();
	}


	/**
	 * Logout the user from the application.
	 * "Destroy" session. Delete user name.
	 *
	 * @param void
	 * @return void
	 */
	private function _logout()
	{
		$this->setSessionData('sso_login', FALSE);
		$this->setSessionData('sso_user', FALSE);
		
		// Logout Hook
		$this->hookLogout();
	}

	
	/**
	 * Login the SSO user.
	 * Use these function in your login page.
	 *
	 * WARNING: This function is can't garantee that the user logged in!
	 * You can always use the 'isLogin();' function after this function to check that the user really logged in.
	 *
	 * @param void
	 * @return void
	 */
	public function logIn()
	{
		// Error
		if($this->_isError()) return;
		
		// switch HTTP/HTTPS protocol
		if($this->conf['ssl']) $this->https();

		// This variable set that the user is really login or not
		$login = TRUE;

		// Check login status and if not logged in: redirect
		if($this->_isRealLogin() === FALSE)
		{
			$this->setSessionData('sso_page', $this->_getReturnParam());
			$this->setSessionData('sso_login_state', TRUE);
			if($this->conf['shibSp'])
			{
				$loginUrl = $this->_path2url($this->conf['spLoginUrl']).'?'.$this->conf['spLoginUrlParam'].'=https://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI');
				$this->_redirect($loginUrl, 'login');
				// If redirect loop...
				$login = FALSE;
			}
			else
			{
				$loginUrl = $this->conf['idpLoginUrl'].'?'.$this->conf['idpUrlParam'].'='.$this->_path2url($this->conf['trigger']);
				$this->_redirect($loginUrl, 'login');
				// If redirect loop...
				$login = FALSE;
			}
		}

		// Set to zero the redirect loop counter
		$this->setSessionData('sso_loop_login', 0);
			
		// Login
		if($login) $this->_login();
		
		// Redirect
		$redirect = $this->getSessionData('sso_page');
		$this->setSessionData('sso_page', FALSE);
		if(!$this->getSessionData('sso_login_state')) $redirect = $this->_getReturnParam();
			else $this->setSessionData('sso_login_state', FALSE);
		if($redirect) $this->_redirect($redirect);
	}
	
	
	/**
	 * Logout the SSO user.
	 * Use these function in your logout page.
	 *
	 * @param void
	 * @return void
	 */
	public function logOut()
	{
		// Error
		if($this->_isError()) return;

		// HTTPS protocol
		if($this->conf['ssl']) $this->https();

		// This variable set that the user is really logout or not
		$logout = TRUE;

		// Check login status and if logged in: redirect
		$this->setSessionData('sso_page', $this->_getReturnParam());
		if($this->conf['shibSp'])
		{
			$spLogoutUrl = $this->conf['spLogoutUrl'].'?'.$this->conf['spLogoutUrlParam'].'=';
			$idpLogoutUrl = $this->conf['idpLogoutUrl'].'?'.$this->conf['idpUrlParam'].'=';
			if($this->conf['shibIdpLogout'])
			{
				$logoutUrl = $idpLogoutUrl.$this->_path2url($spLogoutUrl).'https://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI');
			}
			else
			{
				$logoutUrl = $this->_path2url($spLogoutUrl).'https://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI');
			}
			if($this->_isRealLogin() === TRUE)
			{
				$this->setSessionData('sso_page', $this->_getReturnParam());
				$this->setSessionData('sso_login_state', TRUE);
				$this->_redirect($logoutUrl, 'logout');
				// If redirect loop...
				$logout = FALSE;
			}
		}
		else
		{
			$logoutUrl = $this->conf['idpLogoutUrl'].'?'.$this->conf['idpUrlParam'].'=http://'.$this->server('HTTP_HOST').$this->server('REQUEST_URI');
			if(!empty($this->idpCookie))
			{
				$this->setSessionData('sso_page', $this->_getReturnParam());
				$this->setSessionData('sso_login_state', TRUE);
				$this->_redirect($logoutUrl, 'logout');
				// If redirect loop...
				$logout = FALSE;
			}
		}

		// Set to zero the redirect loop counter
		$this->setSessionData('sso_loop_logout', 0);
			
		// Logout
		if($logout) $this->_logout();
		// For safety...
		$this->userName = FALSE;
		
		// Redirect
		$redirect = $this->getSessionData('sso_page');
		$this->setSessionData('sso_page', FALSE);
		if(!$this->getSessionData('sso_login_state')) $redirect = $this->_getReturnParam();
			else $this->setSessionData('sso_login_state', FALSE);
		if($redirect) $this->_redirect($redirect);
	}
	
	
	/**
	 * Trigger.
	 * These function create a simple redirect to the login page.
	 * Use these function in your trigger page.
	 * In default you shouldn't use these function. But if you should put trigger to another page than the default
	 * ('trigger.php'), you can do this with this function.
	 *
	 * @example /trigger.php
	 * @param (string) $url The URL of the login page. If not setted redirect to the 'loginPage' config. If the this config
	 * 				also unsetted create a login action that equal with logIn(); function.
	 * @return void
	 */
	public function trigger($url = '')
	{
		if(!empty($url)) $this->_redirect($url);
		elseif(!empty($this->conf['loginPage'])) $this->_redirect($this->conf['loginPage']);
		else $this->logIn();
	}
	
	
	/**
	 * Is the user logged in?.
	 * Look after in session that the user is logged in or not.
	 *
	 * @param void
	 * @return (bool) TRUE if the user logged in and FALSE if not.
	 */
	public function isLogin()
	{
		if($this->getSessionData('sso_login') == TRUE) return TRUE;
		else return FALSE;
	}
	
	
	/**
	 * Check that the user is logged or not in IDP and in the application server.
	 *
	 * @param void
	 * @return (bool) TRUE if logged in and FALSE is not.
	 */
	private function _isRealLogin()
	{
		if($this->conf['shibSp'])
		{
			if($this->server('HTTP_SHIB_IDENTITY_PROVIDER') OR $this->server('Shib-Identity-Provider') OR $this->server('Shib_Identity_Provider')) $shibSession = TRUE;
			else $shibSession = FALSE;
			if(empty($this->userName) OR $shibSession === FALSE) return FALSE;
			else return TRUE;
		}
		else
		{
			if(empty($this->userName) OR empty($this->idpCookie)) return FALSE;
			else return TRUE;
		}
	}
	
	
	/**
	 * Get the last page URL.
	 * Get the URL from the URL's parameter, but if it's not exists get it from the 'HTTP_REFERER' server variable.
	 *
	 * @param void
	 * @return (string|bool) FALSE if the URL not setted, and the complete URL of the last page if setted.
	 */
	private function _getReturnParam()
	{
		if(@isset($_GET[$this->conf['urlParam']])) $return = $_GET[$this->conf['urlParam']];
		$referer = $this->server('HTTP_REFERER');
		if($referer == $this->_path2url($this->server('REQUEST_URI'))) $referer = FALSE;
		if(empty($return) AND !empty($referer)) $return = $referer;
		if(empty($return)) return FALSE;
		else return $this->_path2url($return);
	}
	
	/**
	 * End of Login - Logout
	 */
	
	
	
	/*
	 * User
	 */
	
	/**
	 * Get the user's data.
	 * Logged in usage only.
	 *
	 * @param (string) $dataName The name of the user's data. Use 'sso_' prefix to access the user's SSO data.
	 * @return (array|bool|multi) The value of the user's data. If '$dataName' is empty return a single array with all of
	 * 		the data. If '$dataName' is invalid return (bool)FALSE. Use 'sso_' prefix to access the user's SSO data.
	 */
	public function getUserData($dataName = '')
	{
		if($this->isLogin() !== TRUE) return FALSE;
			
		// Get user's sso data
		if(!empty($dataName) AND substr($dataName, 0, 4) == 'sso_')
		{
			if(isset($this->ssoUser[$dataName])) return $this->ssoUser[$dataName];
		}
		
		// Special data
		if($dataName == 'user_name' OR $dataName == 'sso_user_name') return $this->userName;
		if($dataName == 'user_groups' OR $dataName == 'sso_user_groups') return $this->userGroups;
		if($dataName == 'user_virid' OR $dataName == 'sso_user_virid') return $this->userVirid;
			
		// Get user's data from the database
		if(empty($this->user)) $this->user = $this->selectUser($this->userName);
		if(isset($this->user[$dataName])) return $this->user[$dataName];

		// Return with all user data
		if(empty($dataName))
		{
			(array)$user = array_merge((array)$this->user, (array)$this->ssoUser);
			$user['user_name'] = $user['sso_user_name'] = $this->userName;
			$user['user_groups'] = $user['sso_user_groups'] = $this->userGroups;
			$user['user_virid'] = $user['sso_user_virid'] = $this->userVirid;
			return $user;
		}
		
		return FALSE;
	}
	
	
	/**
	 * Set the user's data.
	 * Store user's data with 'updateUser();' function. Only accept none SSO data!
	 * Logged in usage only.
	 *
	 * @param (string) $data Single array of the user's data. Keys represents the data name.
	 * @return void
	 */
	public function setUserData($data)
	{
		if($this->isLogin() !== TRUE) return FALSE;
		if(!is_array($data)) return;
		if(isset($data['user_name'])) unset($data['user_name']);
		if(isset($data['user_groups'])) unset($data['user_groups']);
		if(isset($data['user_virid'])) unset($data['user_virid']);
		foreach($data AS $key => $value)
		{
			if(substr($key, 0, 4) == 'sso_') unset($data[$key]);
		}
		$this->updateUser($this->userName, $data);
		$this->user = array_merge($this->user, $data);
	}
	
	/*
	 * End of User
	 */


}

?>