<?php 

	require_once('.\Model\UploadModel.php');
	require_once('.\Controller\LoginController.php');
	
	class LoggedInView{
	
	private $file;
	private $name;
	private $message;
	private $storedNote;
	private $uploaded;
	
	public function __construct()
	{
		$this->uploaded = new UploadModel();
	}
	
	//Lyssnar efter om användaren loggar ut..
	public function userLogOut(){
	if(isset($_GET['logOut'])){
		return true;
			}else{return false;}
	}
	
	public function uploadFile()
	{
		if(isset($_GET['submit']))
		{
			return true;
		}
	}
	
	public function RadioButtonValue()
	{
		if(isset($_POST['media']))
		{
		$radioButton = $_POST['media'];
		return $radioButton;
		}
	}
	
	public function chModButtonValue()
	{
		if(isset($_POST['media']))
		{
		$chmodButton = $_POST['chmod'];
		return $chmodButton;
		}
	}
	
	public function collectFileNames()
	{
		if(isset($_FILES['file']['name']))
		{
		$this->name = $_FILES['file']['name'];
		$this->temp = $_FILES['file']['tmp_name'];
		}
	}
	
	public function getFileName()
	{
		return $this->name;
	}
	
	public function getFileTemp()
	{
		return $this->file;
	}
	
	public function ClickedDeleteFile()
	{
		if(isset($_GET['Delete_id']))
		{
			return true;
		}
	}
	
	public function ClickedSearch()
	{

		if(isset($_GET['search']))
		{
			return true;
		}
	}
	
	public function SearchFile()
	{
		if(isset($_GET['search']))
		{
			$searchString = $_POST['term'];
			return $searchString;
		}
	}
	
	public function ClickedSaveNote()
	{

		if(isset($_GET['saveNote']))
		{
			return true;
		}
	}
	
	public function SaveNote()
	{
		if(isset($_GET['saveNote']))
		{
			if(isset($_POST['formPostDescription']))
			{
				$saveNote = $_POST['formPostDescription'];
				return $saveNote;
			}
		}
	}
	
	public function showAllFiles()
	{
		if(isset($_GET['display_all_files']))
		{
			return true;
		}
	}
	
	public function showAudioFiles()
	{
		if(isset($_GET['display_audio_files']))
		{
			return true;
		}
	}
	
	public function showVideoFiles()
	{
		if(isset($_GET['display_video_files']))
		{
			return true;
		}
	}
	
	public function showImageFiles()
	{
		if(isset($_GET['display_image_files']))
		{
			return true;
		}
	}
	
	public function showOtherFiles()
	{
		if(isset($_GET['display_other_files']))
		{
			return true;
		}
	}
	
	public function userSettings()
	{
		if(isset($_GET['user_settings']))
		{
			return true;
		}
	}
	
	public function DeleteFile()
	{
		$id = $_GET['Delete_id'];
		return $id;
	}
	
	public function videoPlayback()
	{
		if(isset($_GET['video_id']))
		{
			$videoID = $_GET['video_id'];
			return $videoID;
		}
	}
	
	public function audioPlayback()
	{
		if(isset($_GET['audio_id']))
		{
			$audioID = $_GET['audio_id'];
			return $audioID;
		}
	}
	
	public function imagePlayback()
	{
		if(isset($_GET['image_id']))
		{
			$imageID = $_GET['image_id'];
			return $imageID;
		}
	}
	
	//Visar meddelande
	
	public function displayAllFilesMessage($message)
	{
		$this->message = implode($message);
	}
	
	public function displayMessage($message)
	{
		$this->message = $message;
	}
	
	public function TextAreaMessage($message)
	{
		$this->storedNote = $message;
	}
	
	//Visar utloggningsmeddelande
	public function logOutMessage()
	{
		return $this->message = "Du är nu utloggad";
	}
	
	public function NoteHasBeenSaved()
	{
		return $this->message = "Du sparade undan ett stycke text";
	}
	
	public function fileIsUploaded()
	{
		return $this->message = "Du har laddat upp en fil";
	}
	
	public function fileIsNotUploaded()
	{
		return $this->message = "Filen är i fel format, försök med en godkänd filtyp";
	}
	
	public function fileIsNotDeleted()
	{
		return $this->message = "Gick inte att ta bort filen, rättigheter..?";
	}
	
	public function chmodFailed()
	{
		return $this->message = "Lyckades inte sätta de önskade rättigheterna på filen..";
	}
	
	public function NoSearchFileFound()
	{
		return $this->message = "Ingen fil hittades vid sökning";
	}
	
	public function fileWasRemoved()
	{
		return $this->message = "Du tog bort en fil!";
	}
	
	public function fileIsNotInValidFormat()
	{
		return $this->message = "Filen är inte i ett godkänt format!";
	}
	public function fileNameAlreadyExist()
	{
		return $this->message = "Filnamnet finns redan i datorbasen, byt namn på filen!";
	}
	
	
	//Vad som ska visas när man är inloggad.
	public function LoggedInView()
	{
		$ret = "<div id='container'>
		
		<div id='main'>
		<center></center>
        </div>
        <div id='sidebar'>
		<center>
            <ul id='navigation'>
                <li><a href='?display_all_files'>Visa alla filer</a></li>
                <li><a href='?display_audio_files'>Visa ljudfiler</a></li>
                <li><a href='?display_video_files'>Visa alla videofiler</a></li>
                <li><a href='?display_image_files'>Visa alla bilder</a></li>
                <li><a href='?display_other_files'>Visa övriga filer</a></li>
            </ul>
		</center>
        </div>
		
		<center>
		<div id='upload'>
		
		<form action='?submit' method='POST' enctype='multipart/form-data'>
		<p>Kategori!</P>
		<input type='radio' name='media' value='audio'>Ljud
		<input type='radio' name='media' value='video'>Video
		<input type='radio' name='media' value='image'>Bild
		<input type='radio' name='media' value='other' checked>Övrig
		<br>
		<p>Rättigheter!</P>
		<input type='radio' name='chmod' value='0678'>678
		<input type='radio' name='chmod' value='0676'>777
		<input type='radio' name='chmod' value='0766' checked>766
		<br>

		
		Browse file to upload <input type='file' name='file' id='file'>
		<input type='submit' name='submit' id='submit' value='Upload'>
		</form>
		
		<form action='?search' method='POST'>  
		Search: <input type='text' name='term' />  
		<input type='submit' value='Search!' />  
		</form>

		<form action='?saveNote' method='POST'>
		<textarea rows='6' cols='50' name='formPostDescription'>
$this->storedNote
		</textarea>
		<input type='submit'>
		</form>	
		
		</div>
		</center>
		<center>
		<div id='uploadedFiles'>
		<p>$this->message</p>
		</div>
		</center>
		<a href='?logOut'>Logga ut</a>";
		
		return $ret;
	}
}
