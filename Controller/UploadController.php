<?php

require_once('.\View\LoginView.php');
require_once('.\Model\LoginModel.php');
require_once('.\View\LoggedInView.php');
require_once('.\ServiceHelper\ServiceHelper.php');
require_once('.\Cookie\CookieStorage.php');
require_once('.\Model\UploadModel.php');

class UploadController{

	//Privata strängar
	private $view;
	private $model;
	private $loggedInView;
	private $CookieStorage;
	private $serviceHelper;
	private $con;
	private $upload;

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
	
	public function doController()
	{	
		$message = $this->upload->ReadFromNote();
		$this->loggedInView->TextAreaMessage($message);
		
		
		//Försök göra en switch eller något här
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

		if($this->loggedInView->uploadFile());
		{
			$selected_radio = $this->loggedInView->RadioButtonValue();
			$chmodValue = $this->loggedInView->chModButtonValue();
			$this->loggedInView->collectFileNames();
			$name = $this->loggedInView->getFileName();
			$file = $this->loggedInView->getFileTemp();
					$m = $this->upload->sameNameOnFile($name);
					if($this->upload->sameNameOnFile($name))
					{
						return "FileNameAlreadyExist";
					}
					if($this->upload->isTheFileValid($file, $name))
					{
						return "fileIsNotValidFormat";
					}	
					$message = $this->upload->uploadFile($file, $name, $selected_radio, $chmodValue);
					return $message;					

		}

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