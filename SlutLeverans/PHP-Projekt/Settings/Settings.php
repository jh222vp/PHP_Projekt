<?php
class Settings{
	
	public function databaseSettings()
	{
		$myConnection = new mysqli("Server", "User", "password", "Database");
		return $myConnection;
	}
	
	public function FTP_Server()
	{
		return "ftp.citynetwork.se";
	}
	
	public function FTP_User()
	{
		return "User";
	}
	
	public function FTP_Password()
	{
		return "Password";
	}
	
}