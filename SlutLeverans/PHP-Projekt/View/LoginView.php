<?php 

	class LoginView{
	
	private $username;
	private $password;
	private $message;
	
	public function ViewLogin(){
	$returnViewLogInHTML = "<div class='image'></div>
							<center>
							<p>$this->message</p>
							
							<div id='loginSquare'>
							<form method='post' action='?Login'>
							User: <input type='text' name='username'><br>
							Pass: <input type='password' name='password'><br>
							
							Stay logged in <input type='checkbox' name='check'>
							<input type='submit' value='Sign in' name='submit'>
							</form>
							</center>
							</div>";
							
	return $returnViewLogInHTML;
	}
	
	//Funktion som kontrollerar om användarnamn och lösenord är satt
	//samt sparar unden dessa..
	public function getInformationFromUser(){
	
		if(isset($_POST['username']))
		{
			$this->username = $_POST['username'];
		}
		
		if(isset($_POST['password']))
		{
			$this->password = $_POST['password'];
		}
	}
	
	//Funktion som lyssnar/hämtar knapptrycket "login"
	public function getSubmit(){
		if(isset($_POST['submit']))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	//Sätter username i den privata variabeln ovan
	public function getUsername(){
	return $this->username;
	}
	
	//Sätter password i den privata variabeln ovan
	public function getPassword(){
	return $this->password;
	}
	
	//Kontrollerar om något av fälten är tomma och sparar sedan undan felmeddelandet
	//i message variabeln som tillslut skrivs ut.
	public function logInFailed($username, $password){
	if($username === ""){
		$this->message = "Användarnamn saknas";}
	else if($password === "")
	{
		$this->message = "Lösenord saknas";}
	else
	{
		$this->message = "Felaktigt användarnamn och/eller lösenord";}
	}
	
	//Meddelande som talar om att inloggningen lyckades.
	public function LogInWasSuccessful(){
	return $this->message = "Inloggning lyckades";
	}
	
	//Sätter meddelandet
	public function displayMessage($message)
	{
		$this->message = $message;
	}
	
	public function stayLoggedIn(){
		if(isset($_POST['check'])){
			return true;
		}else{return false;}
	}	
}