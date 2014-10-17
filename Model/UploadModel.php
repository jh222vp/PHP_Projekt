<?php
		
		class UploadModel{
		
		public function isTheFileValid($file, $name)
		{
			if(isset($_POST['submit']))
			{
			$allowed =  array("GIF","PNG","JPG","MP4", "mp4", "WMA", "wma", "MP3", "mp3", "txt", "TXT");
			if(isset($_FILES['file']['name']))
			{
				$name = $_FILES['file']['name'];
				$temp = $_FILES['file']['tmp_name'];
			}
			
			$ext = pathinfo($name, PATHINFO_EXTENSION);
			
				if(!in_array($ext,$allowed))
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		
		
		public function uploadFile($file, $name, $category, $chmodValue)
		{
		if(isset($_POST['submit']))
			{
			$name = $_FILES['file']['name'];
			$temp = $_FILES['file']['tmp_name'];
			$remote_file = '/jh222vp.com/public_html/uploaded/'.$name;
			$fileName = pathinfo($name, PATHINFO_FILENAME);
			$target_path = "C:\Program Files (x86)\EasyPHP-DevServer-14.1VC11\data\localweb\uploaded\.$name";
			
			$ftp_server = "server";
			$ftp_username = "username";
			$ftp_password = "password";
			
			
			$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
			$login = ftp_login($ftp_conn, $ftp_username, $ftp_password);

			ftp_pasv($ftp_conn, true);
			//ftp_chdir($ftp_conn, '/Folder/');
			if(ftp_put($ftp_conn, $remote_file, $temp, FTP_BINARY))
			{
				// Try to set read and write for owner and read for everybody else
				if (ftp_chmod($ftp_conn, $chmodValue, $remote_file) !== false)
				  {
					echo "Successfully chmoded $temp to $chmodValue.";
				  }
				else
				  {
				  echo "chmod failed.";
				  }
				}
			else
			{
				echo "gick inte att ladda upp filen";
			}
			

			// close connection
			ftp_close($ftp_conn);
			
			
			

			
			
			$url = "http://127.0.0.1/uploaded/.$name";
			
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$sqlCommand = "INSERT INTO files VALUE ('', '$fileName', '$url', '$category')";

			//Sparar undan resultatet i variabler
			$result = mysqli_query($myConnection, $sqlCommand);
			}
		}
			
		public function listUploadedFiles($choice)
		{
		$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
		
		switch($choice)
		{
			case 1: {$sqlCommandd = "SELECT * FROM files";break;}
			case 2: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'audio'";break;}
			case 3: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'video'";break;}
			case 4: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'image'";break;}
			case 5: {$sqlCommandd = "SELECT * FROM `files` WHERE `category` = 'other'";break;}
		}
		
		$query = mysqli_query($myConnection, $sqlCommandd);
		
			while($row = mysqli_fetch_assoc($query))
			{
				$url = $row['url'];
				$name = $row['name'];
				$id = $row['ID'];
				$category = $row['category'];
				
				if($category == "video" )
				{
					echo "<a href='?video_id=$id'><img src='./images/play_video.png' height='32' width='32'></a>";
				}
				else if($category == "audio")
				{
					echo "<a href='?audio_id=$id'><img src='./images/play_audio.png' height='32' width='32'></a>";
				}
				else if($category == "image")
				{
					echo "<a href='?image_id=$id'><img src='./images/view_image.png' height='32' width='32'></a>";
				}
				
				echo "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a>";
				echo "<a href='$url'> $name</a>";
			}
		}
		
		public function DeleteFileFromDatabase($id)
		{
		$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
		$sqlCommandd = "DELETE FROM files WHERE ID = $id";
		
		$nameQuery = "SELECT name FROM `files` WHERE ID = $id";
		$ily = mysqli_query($myConnection, $nameQuery);
		
			$row = mysqli_fetch_assoc($ily);
			
			$name = $row['name'];
		
		//Tar bort sökvägen i datorbasen
		$query = mysqli_query($myConnection, $sqlCommandd);
		//Tar bort filen från mappen

		unlink("C:\Program Files (x86)\EasyPHP-DevServer-14.1VC11\data\localweb\uploaded\.$name");
		}
		
		public function SearchForFile($searchString)
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektphp");
			$sqlCommand = "SELECT * FROM files WHERE name='$searchString'";
			//Sparar undan resultatet i variabler
			$result = mysqli_query($myConnection, $sqlCommand);		
			$row_count = mysqli_num_rows($result);
			
			
			
			$ily = mysqli_query($myConnection, $sqlCommand);
			$row = mysqli_fetch_assoc($ily);
			$id = $row['ID'];
			$name = $row['category'];
			$url = $row['url'];
			
			
			if($row_count > 0)
			{
			
				switch($name)
				{
					case "video": {echo "<a href='?video_id=$id'><img src='./images/play_video.png' height='32' width='32'></a>"; break;}
					case "audio": {echo "<a href='?audio_id=$id'><img src='./images/play_audio.png' height='32' width='32'></a>"; break;}
					case "image": {echo "<a href='?image_id=$id'><img src='./images/view_image.png' height='32' width='32'></a>"; break;}
				}
				echo "<a href='?Delete_id=$id'><img src='./images/delete.png' height='32' width='32'></a>";
				echo "<a href='$url'> $searchString</a>";
			}
			else
			{
				echo "ingen fil funnen";
			}
		}
		
		public function PlayVideoFile($id)
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'video'";
			$sendCommand = mysqli_query($myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
						
			echo " <video autoplay width='320' height='240' controls>
			<source src='$urlToFile' type='video/mp4'>
			<source src='$urlToFile' type='video/ogg'>
			Your browser does not support the video tag.
			</video> ";
		}
		
		public function PlayAudioFile($id)
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'audio'";
			$sendCommand = mysqli_query($myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
				
			echo "<audio controls>
			<source src='$urlToFile' type='audio/mp3'>
			<source src='$urlToFile' type='audio/ogg'>
			<source src='$urlToFile' type='audio/mpeg'>
			Your browser does not support the audio element.
			</audio>";
		}
		
		public function ShowImageFile($id)
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$query = "SELECT * FROM files WHERE ID = $id AND category = 'image'";
			$sendCommand = mysqli_query($myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand);
			$urlToFile = $row['url'];
			echo "<img src='$urlToFile' width='500' height='500'>";
		}
		
		public function SaveInputNote($saveNote)
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$query = "UPDATE notes SET notes = '$saveNote' WHERE ID = 1";
			$sendCommand = mysqli_query($myConnection, $query);				
		}
		
		public function ReadFromNote()
		{
			$myConnection = new mysqli("127.0.0.1", "root", "", "projektPHP");
			$query = "SELECT * FROM notes WHERE ID='1'";
			$sendCommand = mysqli_query($myConnection, $query);
			$row = mysqli_fetch_assoc($sendCommand); 
			$note = $row['notes'];
			return $note;
		}
		
		//Vid eventuellt behov av automatisk uppdatering/refresh av sidan.
		public function RefreshPage()
		{	
			$page = $_SERVER['PHP_SELF'];
			$sec = "0";
			header("Refresh: $sec; url=$page");
		}
	}
?>