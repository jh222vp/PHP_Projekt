<?php
		require_once('./Controller/LoginController.php');
		require_once('./Settings/Settings.php');
		
		class UploadModel{
		private $myConnection;
		private $settings;
		private $connect;
		
		public function __construct()
		{
			//$this->myConnection = new mysqli("mysql14.citynetwork.se", "132212-vz49232", "JagheterJonas1", "132212-projekt");
			$this->settings = new Settings();
			$this->connect = $this->settings->databaseSettings();
		}
		
		/* Kontrollerar den uppladdade filens format och validerar om det är ett godkänt
		sådant. Existerar inte filformatet med arrayen "$Allowed så kastas ett undantag */
		public function isTheFileValid($file, $name, $clickedSubmit)
		{
			$t = $this->settings->databaseSettings();
			if(isset($clickedSubmit))
			{
				$allowed =  array("GIF", "gif", "PNG", "png", "JPG", "jpg", "JPEG", "jpeg", "MP4", "mp4", "WMA", "wma", "MP3", "mp3", "txt", "TXT");
				$ext = pathinfo($name, PATHINFO_EXTENSION);
			
				if(!in_array($ext,$allowed))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		/* Funktion som validerar om det redan existerar en fil med samma format och filnamn
		i datorbasen, om true så kastas ett undantag och felmeddelande */
		public function sameNameOnFile($name)
		{
			$sqlCommand = "SELECT * FROM files WHERE name='$name'";
	
			$result = mysqli_query($this->connect, $sqlCommand);		
			$row_count = mysqli_num_rows($result);
			
			if($row_count > 0)
			{
				return true;
			}
			return false;
		}
		
		/*Uppladdningsgfunktion av filer till datorbasen och FTP Servern */
		public function uploadFile($file, $name, $category, $chmodValue, $clickedSubmit)
		{
		if($clickedSubmit)
			{
			$fileName = pathinfo($name, PATHINFO_FILENAME);
			$file_exstension = pathinfo($name, PATHINFO_EXTENSION);
			
			$date = date('Y-m-d H:i:s');

			$remote_file = '/jh222vp.com/public_html/uploaded/'.$fileName."-".$date.".".$file_exstension;
			$newFileName = $fileName."-".$date.".".$file_exstension;
			
			$ftp_server = $this->settings->FTP_Server();
			$ftp_username = $this->settings->FTP_User();
			$ftp_password = $this->settings->FTP_Password();
			
			$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
			$login = ftp_login($ftp_conn, $ftp_username, $ftp_password);

			if(empty($file))
			{
				return "fileWasNotUploaded";
			}
			ftp_pasv($ftp_conn, true);
			if(ftp_put($ftp_conn, $remote_file, $file, FTP_BINARY))
			{
				if (ftp_chmod($ftp_conn, $chmodValue, $remote_file) !== false)
				  {
				  }
				else
				  {
				  return "chmodFailed";
				  }
			}
			else
			{
				return "fileWasNotUploaded";
			}
			
			ftp_close($ftp_conn);			
			
		
			$url = "http://jh222vp.com/uploaded/".$newFileName;
			$sqlCommand = "INSERT INTO files VALUE ('', '$newFileName', '$url', '$category')";
			$result = mysqli_query($this->connect, $sqlCommand);
			return "fileWasUploaded";
			}
		}
		
		/*Här listar vi de existerande filerna som finns i datorbasen och på FTP Servern.
		användaren får välja hur det ska filtreras. Existerar inte filer av önskad kategorin
		eller om fallet är att inga filer finns så genereras ett felmeddelande*/
		public function listUploadedFiles($choice)
		{
		
		switch($choice)
		{
			case 1: {$sqlCommandd = "SELECT * FROM files";break;}
			case 2: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'audio'";break;}
			case 3: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'video'";break;}
			case 4: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'image'";break;}
			case 5: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'other'";break;}
		}
		
		$query = mysqli_query($this->connect, $sqlCommandd);
		
			$i=0;
			while($row = mysqli_fetch_assoc($query))
			{
				$contentOfUploadedFiles = array();
				$url = $row['url'];
				$name = $row['name'];
				$id = $row['ID'];
				$category = $row['category'];
				
				if($category == "video" )
				{
					$ny[$i++] = array($id, $url, $name, "video");
					$i++;
				}
				else if($category == "audio")
				{
					$ny[$i++] = array($id, $url, $name, "audio");
					$i++;
				}
				else if($category == "image")
				{
					$ny[$i++] = array($id, $url, $name, "image");
					$i++;
				}
				else if($category == "other")
				{
					$ny[$i++] = array($id, $url, $name, "other");//"<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?image_id=$id'><a href='$url'> $name</a>";
					$i++;
				}
			}
			if(isset($ny))
			{
				$contentOfUploadedFiles = $ny;
				return $contentOfUploadedFiles;
			}
			else
			{
				return "Inga filer finns i denna kategorin";
			}
		}
		
		/*Funktion som tar bort filer ur datorbasen samt från FTP Servern*/
		public function DeleteFileFromDatabase($id)
		{
			$sqlCommandd = "DELETE FROM files WHERE ID = $id";
			
			$nameQuery = "SELECT name FROM `files` WHERE ID = $id";
			$ily = mysqli_query($this->connect, $nameQuery);
		
			$row = mysqli_fetch_assoc($ily);
			$name = $row['name'];
		
			$query = mysqli_query($this->connect, $sqlCommandd);
			
			$file = '/jh222vp.com/public_html/uploaded/'.$name;
			
			$ftp_server = $this->settings->FTP_Server();
			$ftp_username = $this->settings->FTP_User();
			$ftp_password = $this->settings->FTP_Password();

			//Skapar anslutning
			$conn_id = ftp_connect($ftp_server);

			// Inloggningsuppgifter
			$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

			// try to delete $file
			if (ftp_delete($conn_id, $file))
			{
			} 
			else 
			{
			 return "NotDelete";
			}
		
			ftp_close($conn_id);
			return "delete";
			}
		
		/*Sökfunktion som matchar vad användaren skriver in mot datorbasen */
		public function SearchForFile($searchString)
		{
			$sqlCommand = "SELECT * FROM files WHERE name LIKE '%$searchString%'";
			$result = mysqli_query($this->connect, $sqlCommand);		
			$row_count = mysqli_num_rows($result);
			$ily = mysqli_query($this->connect, $sqlCommand);
			$row = mysqli_fetch_assoc($ily);
			$nameOfFile = $row['name'];
			$id = $row['ID'];
			$name = $row['category'];
			$url = $row['url'];
			$i = 0;

			if($row_count > 0)
			{
				switch($name)
				{
					case "video": {$searchedFile[$i++] = array($id,$url,$nameOfFile, "video", "search");}
					case "audio": {$searchedFile[$i++] = array($id,$url,$nameOfFile, "audio", "search");}
					case "image": {$searchedFile[$i++] = array($id,$url,$nameOfFile, "image", "search");}
					case "other": {$searchedFile[$i++] = array($id,$url,$nameOfFile, "other", "search");}
				}
				return $searchedFile;
			}
			else
			{
				return "NoSearchFileFound";
			}
		}
		
		/*Uppspelning av video*/
		public function PlayVideoFile($id)
		{
			$i = 0;
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'video'";
			$sendCommand = mysqli_query($this->connect, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			
						
			return $searchedFile[$i++] = array($urlToFile,"video");

		}
		
		/*Uppspelning av ljud*/
		public function PlayAudioFile($id)
		{
			$i = 0;
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'audio'";
			$sendCommand = mysqli_query($this->connect, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			
			return $searchedFile[$i++] = array($urlToFile,"audio");
		}
		
		/*Uppspelning av bild*/
		public function ShowImageFile($id)
		{
			$i = 0;
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'image'";
			$sendCommand = mysqli_query($this->connect, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			return $searchedFile[$i++] = array($urlToFile,"image");
			
		}
		
		/*Sparar undan önskad anteckning*/
		public function SaveInputNote($saveNote)
		{
			$query = "UPDATE notes SET notes = '$saveNote' WHERE ID = 1";
			$sendCommand = mysqli_query($this->connect, $query);
			return "NoteIsSaved";
		}
		
		/*Läser ur datorbasen vad som finns tidigare sparad som anteckning*/
		public function ReadFromNote()
		{
			$query = "SELECT * FROM notes WHERE ID='1'";
			$sendCommand = mysqli_query($this->connect, $query);
			$row = mysqli_fetch_assoc($sendCommand); 
			$note = $row['notes'];
			return $note;
		}		
	}
?>