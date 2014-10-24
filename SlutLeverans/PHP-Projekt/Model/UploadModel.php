<?php
		require_once('./Controller/LoginController.php');
		
		class UploadModel{
		private $myConnection;
		
		public function __construct()
		{
			$this->myConnection = new mysqli("127.0.0.1", "root", "", "projektphp");
		}
		
		/* Kontrollerar den uppladdade filens format och validerar om det är ett godkänt
		sådant. Existerar inte filformatet med arrayen "$Allowed så kastas ett undantag */
		public function isTheFileValid($file, $name)
		{
			if(isset($_POST['submit']))
			{
				$allowed =  array("GIF","PNG","JPG","MP4", "mp4", "WMA", "wma", "MP3", "mp3", "txt", "TXT");
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
	
			$result = mysqli_query($this->myConnection, $sqlCommand);		
			$row_count = mysqli_num_rows($result);
			
			if($row_count > 0)
			{
				return true;
			}
			return false;
		}
		
		/*Uppladdningsgfunktion av filer till datorbasen och FTP Servern */
		public function uploadFile($file, $name, $category, $chmodValue)
		{
		if(isset($_POST['submit']))
			{
			$name = $_FILES['file']['name'];
			$temp = $_FILES['file']['tmp_name'];
			$remote_file = '/jh222vp.com/public_html/uploaded/'.$name;
			$fileName = pathinfo($name, PATHINFO_BASENAME);
			
			$ftp_server = "ftp.citynetwork.se";
			$ftp_username = "132212-master";
			$ftp_password = "Bioshock1";
			
			$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
			$login = ftp_login($ftp_conn, $ftp_username, $ftp_password);

			if(empty($temp))
			{
				return "fileWasNotUploaded";
			}
			ftp_pasv($ftp_conn, true);
			if(ftp_put($ftp_conn, $remote_file, $temp, FTP_BINARY))
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
			
			$url = "http://jh222vp.com/uploaded/".$name;
			$sqlCommand = "INSERT INTO files VALUE ('', '$fileName', '$url', '$category')";
			$result = mysqli_query($this->myConnection, $sqlCommand);
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
		
		$query = mysqli_query($this->myConnection, $sqlCommandd);
		
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
					$ny[$i++] = "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?video_id=$id'><img src='./images/play_video.png' height='32' width='32'></a><a href='$url'> $name</a>";
					$i++;
				}
				else if($category == "audio")
				{
					$ny[$i++] = "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?audio_id=$id'><img src='./images/play_audio.png' height='32' width='32'></a><a href='$url'> $name</a>";
					$i++;
				}
				else if($category == "image")
				{
					$ny[$i++] = "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?image_id=$id'><img src='./images/view_image.png' height='32' width='32'></a><a href='$url'> $name</a>";
					$i++;
				}
				else if($category == "other")
				{
					$ny[$i++] = "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?image_id=$id'><a href='$url'> $name</a>";
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
			$ily = mysqli_query($this->myConnection, $nameQuery);
		
			$row = mysqli_fetch_assoc($ily);
			$name = $row['name'];
		
			$query = mysqli_query($this->myConnection, $sqlCommandd);
			
			$file = '/jh222vp.com/public_html/uploaded/'.$name;
			$ftp_server = "ftp.citynetwork.se";
			$ftp_username = "132212-master";
			$ftp_password = "Bioshock1";

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
			$sqlCommand = "SELECT * FROM files WHERE name LIKE '$searchString%'";
			$result = mysqli_query($this->myConnection, $sqlCommand);		
			$row_count = mysqli_num_rows($result);
			$ily = mysqli_query($this->myConnection, $sqlCommand);
			$row = mysqli_fetch_assoc($ily);
			$nameOfFile = $row['name'];
			$id = $row['ID'];
			$name = $row['category'];
			$url = $row['url'];


			
			if($row_count > 0)
			{
				switch($name)
				{
					case "video": {return "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?video_id=$id'><img src='./images/play_video.png' height='32' width='32'></a><a href='$url'> $nameOfFile</a>"; break;}
					case "audio": {return "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?audio_id=$id'><img src='./images/play_audio.png' height='32' width='32'></a><a href='$url'> $nameOfFile</a>"; break;}
					case "image": {return "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a><a href='?image_id=$id'><img src='./images/view_image.png' height='32' width='32'></a><a href='$url'> $nameOfFile</a>"; break;}
				}
			}
			else
			{
				return "NoSearchFileFound";
			}
		}
		
		/*Uppspelning av video*/
		public function PlayVideoFile($id)
		{
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'video'";
			$sendCommand = mysqli_query($this->myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			
						
			return " <video autoplay width='320' height='240' controls>
			<source src='$urlToFile' type='video/mp4'>
			<source src='$urlToFile' type='video/ogg'>
			Your browser does not support the video tag.
			</video> ";
		}
		
		/*Uppspelning av ljud*/
		public function PlayAudioFile($id)
		{
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'audio'";
			$sendCommand = mysqli_query($this->myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
				
			return "<audio controls>
			<source src='$urlToFile' type='audio/mp3'>
			<source src='$urlToFile' type='audio/ogg'>
			<source src='$urlToFile' type='audio/mpeg'>
			Your browser does not support the audio element.
			</audio>";
		}
		
		/*Uppspelning av bild*/
		public function ShowImageFile($id)
		{
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'image'";
			$sendCommand = mysqli_query($this->myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			return "<img src='$urlToFile' width='500' height='500'>";
		}
		
		/*Sparar undan önskad anteckning*/
		public function SaveInputNote($saveNote)
		{
			$query = "UPDATE notes SET notes = '$saveNote' WHERE ID = 1";
			$sendCommand = mysqli_query($this->myConnection, $query);
			return "NoteIsSaved";
		}
		
		/*Läser ur datorbasen vad som finns tidigare sparad som anteckning*/
		public function ReadFromNote()
		{
			$query = "SELECT * FROM notes WHERE ID='1'";
			$sendCommand = mysqli_query($this->myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand); 
			$note = $row['notes'];
			return $note;
		}		
	}
?>