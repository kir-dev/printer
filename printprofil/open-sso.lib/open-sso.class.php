<?php
/**
 * Session and User (database) handler for OpenSSO PHP library.
 * 
 * This class's functions connect your application with the OpenSSO PHP library.
 * Please read the inline documentation and write this class!
 * Before you test it please look after the config file: config.php!
 *
 * API Abstract version: 1.5.1
 *  
 * @category   OpenSSO_PHP
 * @package    OpenSSO_PHP
 * @link       http://kir-dev.sch.bme.hu/opensso-phplib/
 * @filesource
 */
 
require_once('open-sso.abstract.php');

final class openSSO extends openSSO_Abstract
{

    private $conn = null;

    /**
     * connect to db
     */
    function __construct() {

        parent::__construct();

        global $host, $user, $pass, $db;

        $dsn = 'mysql:dbname='.$db.';host=' . $host;

        try {
            $this->conn = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }

        //
    }

	/**
	 * Get data from the session
	 *
	 * @param (string) $name Name of the data.
	 * @return (multi) Value of the data.
	 */
	protected function getSessionData($name)
	{
            if(isset($_SESSION[$name])) return $_SESSION[$name];
                    else return null;
	}
 
 
 
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
	protected function setSessionData($name, $value)
	{
            $_SESSION[$name] = $value;
	}
 
 
 
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
	protected function insertUser($data)
	{
            $sql = "INSERT INTO users (uid, nick, email) VALUES(:uid, :nick, :email) " .
                   "ON DUPLICATE KEY UPDATE email=:email";

            try {
                $sth = $this->conn->prepare($sql);
                $sth->execute(array(':uid' => $data['user_name'], ':nick' => $data['sso_nickname'], ':email' => $data['sso_email']));
                $sth->fetchAll();
            } catch (PDOException $e) {
                die('unable to insert user in the db');
            }
	}
 
 
 
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
	protected function selectUser($userName)
	{
            $sql = "SELECT uid, nick, email FROM users WHERE uid = ? LIMIT 0,1";

            try {
                $sth = $this->conn->prepare($sql);
                $sth->execute(array($userName));
                $result = $sth->fetch();

                if ($sth->rowCount())
                    return array('user_name' => $result['uid'],
                                 'sso_email' => $result['email'],
                                 'sso_nickname' => $result['nick']);
                else
                    return array();

            } catch (PDOException $e) {
                die('unable to select user from the db');
            }
	}
 
 
 
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
	protected function updateUser($userName, $data)
	{
		//
                throw new Exception('not implemented');
	}
 
 
 
	/**
	 * Hooks (optional)
	 */	 
 
 	/**
	 * Hook: Login
	 * Call this function after the lib checked the required user data, and created the session.
	 * (Call this function after the login method.)	 
	 *
	 * @param void
	 * @return void
	 */
	protected function hookLogin()
	{
	}
	
	
	
	/**
	 * Hook: Logout
	 * Call this function after the lib "destroy" the session.
	 * (Call this function after the logout method.)	 
	 *
	 * @param void
	 * @return void
	 */
	protected function hookLogout()
	{
	}



	/**
	 * Hook: Is Error?
	 * Call this function to determine that error is occured or not.
	 *
	 * @param void
	 * @return (bool) 'TRUE' if error is occured and 'FALSE' otherwise.
	 */
	protected function hookIsError()
	{
	}


	
	/**
	 * Hook: Error
	 * Call this function when the IDP or the SP is down.
	 *
	 * @param void
	 * @return void
	 */
	protected function hookError()
	{
	}
 
	/**
	 * End of Hooks
	 */	 	
 
}

?>
