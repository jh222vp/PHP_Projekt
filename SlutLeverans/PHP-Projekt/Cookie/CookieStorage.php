<?php

class CookieStorage{

	//Privata variabler
	private $cookieName = "CookieStorage";
	private $cookieExpireTime;
	private $message;
	
	
	public function cookieSave($string){
		$this->cookieExpireTime = time()+250;
		setcookie($this->cookieName, $string, $this->cookieExpireTime);
		return $this->cookieExpireTime;
		}
				
		public function load(){
			if(isset($_COOKIE[$this->cookieName])){
				return true;
			}
			else{return false;}
		}
		
		public function delete(){
		setcookie($this->cookieName, "", time() - 1);
		}
		
		public function doesCookieAlreadyExist(){
			if(isset($_COOKIE[$this->cookieName])){
				return $_COOKIE[$this->cookieName];
			}
		}
}