<?php
/**
 * Config file for OpenSSO PHP library.
 *
 * Set the Service Provider software in the 'shibSp' config!
 * Read the 'Usage' information, that contains the Service Provider softwares that uses the config.
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

// Exit before anything else
if(!defined('SSOCONFIG')) die('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Shibboleth Service Provider
|--------------------------------------------------------------------------
|
| You use Shibboleth or OpenSSO service provider software?
| If you use Shibboleth set it to 'TRUE'.
| Type: bool
| Default: FALSE
| Usage: OpenSSO, Shibboleth
|
*/
$config['shibSp'] = TRUE;

/*
|--------------------------------------------------------------------------
| Logout the user in Identity Provider
|--------------------------------------------------------------------------
|
| If you use Shibboleth Service Provider and set this config to 'TRUE' the
| application logout the user in the Identity Provider.
| WARNING: The other Shibboleth applications can't logout automatically!
| Type: bool
| Default: TRUE
| Usage: Shibboleth
|
*/
$config['shibIdpLogout'] = FALSE;

/*
|--------------------------------------------------------------------------
| Useing SSL (HTTPS)
|--------------------------------------------------------------------------
|
| Useing SSL after the user logged in?
| If you use Shibboleth the program overwrite this config with 'TRUE' value.
| Type: bool
| Default: TRUE
| Usage: OpenSSO
|
*/
$config['ssl'] = TRUE;

/*
|--------------------------------------------------------------------------
| User's name from Identity Provider
|--------------------------------------------------------------------------
|
| The elements of the _SERVER variable, that cointains the user's name.
| Type: string
| Default: REMOTE_USER
| Usage: OpenSSO, Shibboleth
|
*/
$config['userName'] = 'REMOTE_USER';

/*
|--------------------------------------------------------------------------
| User's groups from Identity Provider
|--------------------------------------------------------------------------
|
| The elements of the _SERVER variable, that cointains the user's groups.
| Type: string
| Default: HTTP_EDUPERSONENTITLEMENT
| Usage: OpenSSO, Shibboleth
|
*/
$config['userGroups'] = 'HTTP_EDUPERSONENTITLEMENT';

/*
|--------------------------------------------------------------------------
| User's VIRID from Identity Provider
|--------------------------------------------------------------------------
|
| The elements of the _SERVER variable, that cointains the user's VIRID.
| Type: string
| Default: HTTP_VIRID
| Usage: OpenSSO, Shibboleth
|
*/
$config['userVirid'] = 'HTTP_VIRID';

/*
|--------------------------------------------------------------------------
| User data from Identity Provider
|--------------------------------------------------------------------------
|
| The elements of the _SERVER variable, that contains the user's data.
| We recommend these:
| - 'email': 'HTTP_EMAIL'
| - 'firstname': 'HTTP_FIRSTNAME'
| - 'lastname': 'HTTP_LASTNAME'
| - 'nickname': 'HTTP_NICKNAME'
| - 'common_name': 'HTTP_COMMON_NAME'
| Type: array
| Syntax: array( '<data name>' => '<_SERVER's element name>', ... );
| Default: -
| Usage: OpenSSO, Shibboleth
|
*/
$config['user']['email'] = 'HTTP_EMAIL';
$config['user']['firstname'] = 'HTTP_FIRSTNAME';
$config['user']['lastname'] = 'HTTP_LASTNAME';
$config['user']['nickname'] = 'HTTP_NICKNAME';
$config['user']['common_name'] = 'HTTP_COMMON_NAME';

/*
|--------------------------------------------------------------------------
| Required user data from Identity Provider
|--------------------------------------------------------------------------
|
| The elements of the _SERVER variable, that contains the user's data and
| require to the application to create new user. If one of these elements is
| not exists the user can't login to the application.
| Use the data names that you add in the 'user' config (User data from
| Identity Provider).
| User name (from 'userName' config) is require the program. Do not set here.
| Type: array
| Syntax: array( '<data name>', ... );
| Default: -
| Usage: OpenSSO, Shibboleth
|
*/
$config['userRequire'] = array('email');

/*
|--------------------------------------------------------------------------
| Identity Provider's cookie
|--------------------------------------------------------------------------
|
| The name of the Identity Provider's cookie, that setted when a user logged in.
| Type: string
| Default: sunIdentityServerAuthNServer
| Usage: OpenSSO
|
*/
$config['idpCookie'] = 'sunIdentityServerAuthNServer';

/*
|--------------------------------------------------------------------------
| Identity Provider's Login URL
|--------------------------------------------------------------------------
|
| The Identity Provider's URL, that login the user.
| Type: string
| Default: https://idp.sch.bme.hu/opensso/UI/Login
| Usage: OpenSSO
|
*/
$config['idpLoginUrl'] = 'https://idp.sch.bme.hu/opensso/UI/Login';

/*
|--------------------------------------------------------------------------
| Identity Provider's Logout URL
|--------------------------------------------------------------------------
|
| The Identity Provider's URL, that logout the user.
| Type: string
| Default: https://idp.sch.bme.hu/opensso/UI/Logout
| Usage: OpenSSO, Shibboleth
|
*/
$config['idpLogoutUrl'] = 'https://idp.sch.bme.hu/opensso/UI/Logout';

/*
|--------------------------------------------------------------------------
| Identity Provider's URL parameter
|--------------------------------------------------------------------------
|
| Parameter of the URL that contains the URL where the Identity Provider
| redirect the user after the login or the logout action.
| Type: string
| Default: goto
| Usage: OpenSSO, Shibboleth
|
*/
$config['idpUrlParam'] = 'goto';

/*
|--------------------------------------------------------------------------
| Service Provider's Login URL
|--------------------------------------------------------------------------
|
| The Service Provider's URL, that login the user.
| Type: string
| Default: /Shibboleth.sso/Login
| Usage: Shibboleth
|
*/
$config['spLoginUrl'] = '/Shibboleth.sso/Login';

/*
|--------------------------------------------------------------------------
| Service Provider's Login URL parameter
|--------------------------------------------------------------------------
|
| Parameter of the URL that contains the URL where the Service Provider
| redirect the user after the login action.
| Type: string
| Default: target
| Usage: Shibboleth
|
*/
$config['spLoginUrlParam'] = 'target';

/*
|--------------------------------------------------------------------------
| Service Provider's Logout URL
|--------------------------------------------------------------------------
|
| The Service Provider's URL, that logout the user.
| Type: string
| Default: /Shibboleth.sso/Logout
| Usage: Shibboleth
|
*/
$config['spLogoutUrl'] = '/Shibboleth.sso/Logout';

/*
|--------------------------------------------------------------------------
| Service Provider's Logout URL parameter
|--------------------------------------------------------------------------
|
| Parameter of the URL that contains the URL where the Service Provider
| redirect the user after the logout action
| Type: string
| Default: return
| Usage: Shibboleth
|
*/
$config['spLogoutUrlParam'] = 'return';

/*
|--------------------------------------------------------------------------
| Trigger
|--------------------------------------------------------------------------
|
| Path to the trigger file or directory. (File or directory that the Service
| Provider save.)
| Type: string
| Default: /trigger.php
| Usage: OpenSSO
|
*/
$config['trigger'] = '../trigger.php';

/*
|--------------------------------------------------------------------------
| Application's Login page
|--------------------------------------------------------------------------
|
| URL of the Application's login page.
| Type: string
| Default: -
| Usage: OpenSSO
|
*/
$config['loginPage'] = 'index.php';

/*
|--------------------------------------------------------------------------
| Application's URL parameter
|--------------------------------------------------------------------------
|
| Parameter of the URL that contains the path where the application redirect
| the user after the login or the logout action. This path is point to the
| last page that the user visit before the login/logout action started.
| Type: string
| Default: -
| Usage: OpenSSO, Shibboleth
|
*/
$config['urlParam'] = 'return';

/*
|--------------------------------------------------------------------------
| Downtime start
|--------------------------------------------------------------------------
|
| The first time when the IDP or the SP is down.
| Type: string
| Default: -
| Usage: OpenSSO, Shibboleth
|
*/
$config['downtimeStart'] = '';

/*
|--------------------------------------------------------------------------
| Downtime end
|--------------------------------------------------------------------------
|
| The first time when the IDP or the SP is not down (up).
| Type: string
| Default: -
| Usage: OpenSSO, Shibboleth
|
*/
$config['downtimeEnd'] = '';

?>
