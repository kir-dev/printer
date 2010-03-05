<?php
/**
 * API interface for OpenSSO PHP library.
 * 
 * This is an API so we try to not modificate this across the versions. 
 * You can use these functions in your application if you include or require the 'open-sso.class.php' file! 
 *  
 * @category   OpenSSO_PHP
 * @package    OpenSSO_PHP 
 * @link       http://kir-dev.sch.bme.hu/opensso-phplib/
 * @author     Pásztor Gergő <pairghu@gmail.com> from KirDev <kir-dev@sch.bme.hu>
 * @copyright  Copyright (c) 2009, KirDev
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL  
 * @filesource
 *
 * @version 1.0.0 
 */

interface openSSO_API
{
 
	/**
	 * Get HTTP connection, not HTTPS.
	 * If the user request this 'page' with SSL (HTTPS) this function redirect to the non secured version of the 'page'
	 * (HTTP). For example: https://example.org/dir/file.php?var=1 redirect to http://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return void
	 */
	public function http();
 
	/**
	 * Get HTTPS connection, not HTTP.
	 * If the user request this 'page' without SSL (HTTP) this function redirect to the secured version of the 'page'
	 * (HTTPS). For example: http://example.org/dir/file.php?var=1 redirect to https://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return void
	 */
	public function https();
 
	/**
	 * Is secured, HTTPS connection?.
	 * If the user request this 'page' without SSL (HTTP) this function redirect to the secured version of the 'page'
	 * (HTTPS). For example: http://example.org/dir/file.php?var=1 redirect to https://example.org/dir/file.php?var=1
	 *
	 * @param void
	 * @return (bool) TRUE if is a HTTPS connection and FALSE is a HTTP connection.
	 */
	public function isHttps();
 
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
	public function trigger();
 
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
	public function logIn();

	/**
	 * Logout the SSO user.
	 * Use these function in your logout page.
	 *
	 * @param void
	 * @return void
	 */
	public function logOut();
 
	/**
	 * Is the user logged in?
	 *
	 * @param void
	 * @return (bool) TRUE if the user logged in and FALSE if not.
	 */
	public function isLogin();

	/**
	 * Get the user's data.
	 * Logged in usage only.
	 *
	 * @param (string) $dataName The name of the user's data. Use 'sso_' prefix to access the user's SSO data.
	 * @return (array|bool|multi) The value of the user's data. If '$dataName' is empty return a single array with all of
	 * 		the data. If '$dataName' is invalid return (bool)FALSE. Use 'sso_' prefix to access the user's SSO data.
	 */
	public function getUserData($dataName = '');
 
	/**
	 * Set the user's data.
	 * Store user's data with 'updateUser();' function. Only accept none SSO data!
	 * Logged in usage only.
	 *
	 * @param (string) $data Single array of the user's data. Keys represents the data name.
	 * @return void
	 */
	public function setUserData($data);
 
}

?>