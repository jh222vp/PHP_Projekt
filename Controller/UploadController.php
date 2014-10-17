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
		//$message = $this->upload->ReadFromNote();
		//$this->loggedInView->TextAreaMessage($message);
		
		
		//Försök göra en switch eller något här
		if($this->loggedInView->showAllFiles())
		{
			$this->upload->listUploadedFiles(1);
		}
		if($this->loggedInView->showAudioFiles())
		{
			$this->upload->listUploadedFiles(2);
		}
		if($this->loggedInView->showVideoFiles())
		{
			$this->upload->listUploadedFiles(3);
		}
		if($this->loggedInView->showImageFiles())
		{
			$this->upload->listUploadedFiles(4);
		}
		if($this->loggedInView->showOtherFiles())
		{
			$this->upload->listUploadedFiles(5);
		}
		
		if($this->loggedInView->ClickedDeleteFile())
		{
			$id = $this->loggedInView->deleteFile();
			$this->upload->DeleteFileFromDatabase($id);
			
			$message = $this->loggedInView->fileWasRemoved();
			$this->loggedInView->displayMessage($message);
		}
		
		if($this->loggedInView->ClickedSearch())
		{
			$searchString = $this->loggedInView->SearchFile();
			$this->upload->SearchForFile($searchString);
		}
		
		if($this->loggedInView->ClickedSaveNote())
		{
			$saveNote = $this->loggedInView->SaveNote();
			$this->upload->SaveInputNote($saveNote);
		}
		
		if($this->loggedInView->uploadFile());
		{
			$selected_radio = $this->loggedInView->RadioButtonValue();
			$chmodValue = $this->loggedInView->chModButtonValue();
			$name = $this->loggedInView->getFileName();
			$file = $this->loggedInView->getFileTemp();
			if($this->upload->isTheFileValid($file, $name))
			{
				$this->upload->uploadFile($file, $name, $selected_radio,$chmodValue);
			}
		}
		if($id = $this->loggedInView->videoPlayback())
		{
			$this->upload->PlayVideoFile($id);
		}
		if($id = $this->loggedInView->audioPlayback())
		{
			$this->upload->PlayAudioFile($id);
		}
		if($id = $this->loggedInView->imagePlayback())
		{
			$this->upload->ShowImageFile($id);
		}
	}
}