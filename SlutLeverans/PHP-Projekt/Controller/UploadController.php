<?php

require_once('./View/LoginView.php');
require_once('./Model/LoginModel.php');
require_once('./View/LoggedInView.php');
require_once('./ServiceHelper/ServiceHelper.php');
require_once('./Cookie/CookieStorage.php');
require_once('./Model/UploadModel.php');

class UploadController{

	//Privata strängar
	private $view;
	private $model;
	private $loggedInView;
	private $CookieStorage;
	private $serviceHelper;
	private $con;
	private $upload;
	
	//Konstruktor
	public function __construct()
	{
	$this->upload = new UploadModel();
	$this->view = new LoginView();
	$this->model = new LoginModel();
	$this->loggedInView = new LoggedInView();
	$this->serviceHelper = new serviceHelper();
	$this->cookieStorage = new CookieStorage();
	$this->con = new mysqli("127.0.0.1", "root", "", "Login");
	}
	
	/*Kontroller som lyssnar efter all funktionalitet som kan exikveras på hemsidan.
	Så som borttagning av fil, uppladdning av fil, uppspelningar av filer, sparande av text
	samt filtreringar och sökningar av olika filer */
	
	public function doController()
	{	
		$message = $this->upload->ReadFromNote();
		$this->loggedInView->TextAreaMessage($message);
		
		if($this->loggedInView->showAllFiles())
		{
			$message = $this->upload->listUploadedFiles(1);
			return $message;
		}
		if($this->loggedInView->showAudioFiles())
		{
			$message = $this->upload->listUploadedFiles(2);
			return $message;
		}
		if($this->loggedInView->showVideoFiles())
		{
			$message = $this->upload->listUploadedFiles(3);
			return $message;
		}
		if($this->loggedInView->showImageFiles())
		{
			$message = $this->upload->listUploadedFiles(4);
			return $message;
		}
		if($this->loggedInView->showOtherFiles())
		{
			$message = $this->upload->listUploadedFiles(5);
			return $message;
		}
		
		if($this->loggedInView->ClickedDeleteFile())
		{
			$id = $this->loggedInView->deleteFile();
			$message = $this->upload->DeleteFileFromDatabase($id);
			
			return $message;
		}
		
		if($this->loggedInView->ClickedSearch())
		{
			$searchString = $this->loggedInView->SearchFile();
			$message = $this->upload->SearchForFile($searchString);

			return $message;
		}
		
		if($this->loggedInView->ClickedSaveNote())
		{
			$saveNote = $this->loggedInView->SaveNote();
			$message = $this->upload->SaveInputNote($saveNote);
			$note = $this->upload->ReadFromNote();
			return $message;
		}

		/* Uppladdning av filer går genom här samt validerar om filnamnet redan existerar i datorbasen
		eller inte, kontrollerar också så att filformatet är i ett godkänt format*/
		if($this->loggedInView->uploadFile());
		{
			$clickedSubmit = true;
			$selected_radio = $this->loggedInView->RadioButtonValue();
			$chmodValue = $this->loggedInView->chModButtonValue();
			$this->loggedInView->collectFileNames();
			$name = $this->loggedInView->getFileName();
			$file = $this->loggedInView->getFileTemp();

					if($this->upload->isTheFileValid($file, $name, $clickedSubmit))
					{
						return "fileIsNotValidFormat";
					}	
					if($message = $this->upload->uploadFile($file, $name, $selected_radio, $chmodValue, $clickedSubmit))
					{
						return $message;
					}									
		}
		$clickedSubmit = false;

		/* Kontroll om video, ljud eller en bild ska visas */
		if($id = $this->loggedInView->videoPlayback())
		{
			
			$message = $this->upload->PlayVideoFile($id);
			return $message;
		}

		if($id = $this->loggedInView->audioPlayback())
		{
			$message = $this->upload->PlayAudioFile($id);
			return $message;
		}
		if($id = $this->loggedInView->imagePlayback())
		{
			$message = $this->upload->ShowImageFile($id);
			return $message;
		}
	}
}