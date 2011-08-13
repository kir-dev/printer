<?php

//dummy sso class
class openSSO {

	private $loggedIn = true;
	private $userData = array(
			'user_name' => 'jozsi',
			'sso_email' => 'jozsika@mail.us',
			'sso_nickname' => 'józsika'
			);

	public function trigger() {
	
		// Redirect
		header('Location: /');
		exit(0);
	}
	
	public function isLogin() {
		return $this->loggedIn;
	}
	
	public function logIn() {
		$this->trigger();
	}
	
	public function logOut() {
		return 0;
	}
	
	public function getUserData($param) {
		return $this->userData[$param];
	}
}

?>