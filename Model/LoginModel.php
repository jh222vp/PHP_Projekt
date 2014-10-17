<?php

class LoginModel{
	
	//Privata variabler..
	private $isTheUserAuthenticated = false;
	private $randomString = "ffsdsfdfsdffsd";
	private $storedUsername;
	
	
	//Funktion som tar emot filtrerade användarnamn och lösenord.
	public function loginValidation($username, $password)
	{	
		//Specificerar uppgifter för anslutning mot önskad datorbas samt SQL-Query
		$myConnection = new mysqli("127.0.0.1", "root", "", "projektphp");
		$sqlCommand = "SELECT * FROM users WHERE username='$username' AND password='$password'";
	
		//Sparar undan resultatet i variabler
		$result = mysqli_query($myConnection, $sqlCommand);		
		$row_count = mysqli_num_rows($result);
		
		//Kontroll för om lösenord och användarnamn var korrekt
		if($row_count > 0){
		
			$_SESSION["ValidLogin"] = $username;
			$username = $this->storedUsername;
			
			return true;
		}else{return false;}
	}
	
	public function isTheUserAdmin($username, $password)
	{
		//Specificerar uppgifter för anslutning mot önskad datorbas samt SQL-Query
		$myConnection1 = new mysqli("127.0.0.1", "root", "", "projektphp");
		$sqlCommand1 = "SELECT * FROM users WHERE username='$username' AND password='$password'";
	
		//Sparar undan resultatet i variabler
		$result1 = mysqli_query($myConnection1, $sqlCommand1);
		$run1 = mysqli_fetch_array($result1);
		$type1 = $run1['type'];
		$userlevel1 = $run1['user_level'];

		
		$row_count1 = mysqli_num_rows($result1);
		die();
		
		//Om det var korrekt så kontrollerar vi om användaren är aktiv och vilken typ dvs Admin eller vanlig

			if($type1 == "deactive")
			{
				echo "Ditt konto är inaktiverat av admin";
				return false;
			}

			if($userlevel1 > 0)
			{
				echo "Du har ett adminkonto";
				return true;
			}else{return false;}
	}
	
	//Kontroll för om användaren är inloggad sedan tidigare med sessions eller ej..
	public function getUserIsAuthenticated($userAgent){
		if(isset($_SESSION["ValidLogin"])){
			if(isset($_SESSION["ValidLogin"])){
				$this->isTheUserAuthenticated = true;
				}
				else{return false;}
			}
			return $this->isTheUserAuthenticated;
	}
	
	//Hämtar randomstring variablen som är privat ovan
	public function getRandomString(){
		return $this->randomString;
	}
	
	//Sparar ned tid i exs.txt dokumentet
	public function saveTheCookieTime($time){
		file_put_contents("exs.txt", $time);
	}
	
	public function checkTheCookieValue($cookieValue, $userAgent){
	
		$time = file_get_contents("exs.txt");
		if($time < time()){
			return $this->isTheUserAuthenticated = false;
		}
		else{
			if($this->randomString === $cookieValue)
			{
				$_SESSION["ValidLogin"] = $this->storedUsername;
				$_SESSION["UserAgent"] = $userAgent;
				return $this->isTheUserAuthenticated = true;
			}
		}
	}
	
	//Tar bort en existerande session när användaren loggar ut
	public function destroySession(){
		if(isset($_SESSION["ValidLogin"])){
		unset($_SESSION["ValidLogin"]);
		}
		return $this->isTheUserAuthenticated = false;
	}	
}