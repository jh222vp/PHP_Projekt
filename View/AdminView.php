<?php 

	require_once('.\Model\UploadModel.php');
	
	class AdminView{
	
	private $file;
	private $name;
	
	private $message;
	private $uploaded;
	
		
	//Vad som ska visas när man är inloggad.
	public function AdminView()
	{
		$ret = "<div id='container'>
        <div id='sidebar'>
            <ul id='navigation'>
                <li><a href='?user_settings'>User Settings</a></li>
                <li><a href='/identities'>Identities</a></li>
                <li><a href='/services'>Services</a></li>
                <li><a href='/shop'>Shop</a></li>
                <li><a href='/contact'>Contact</a></li>
            </ul>
        </div>
        <div id='main'>
            <center><h1>Du är inloggad som administratör<center></h1>
        </div>
    </div>
		<a href='?logOut'>Logga ut</a>";
		
		return $ret;
	}
}
