<?php

require_once('.\View\LoginView.php');
require_once('.\Model\LoginModel.php');
require_once('.\View\LoggedInView.php');
require_once('.\ServiceHelper\ServiceHelper.php');
require_once('.\Cookie\CookieStorage.php');
require_once('.\Controller\UploadController.php');
require_once('.\View\AdminView.php');

class LoginController{

	//Privata strängar
	private $admin;
	private $view;
	private $model;
	private $loggedInView;
	private $CookieStorage;
	private $serviceHelper;
	private $con;
	private $uploadController;
	private $upload;

	public function __construct()
	{
	$this->upload = new UploadModel();
	$this->admin = new AdminView();
	$this->view = new LoginView();
	$this->uploadController = new UploadController();
	$this->model = new LoginModel();
	$this->loggedInView = new LoggedInView();
	
	$message = $this->upload->ReadFromNote();
	$this->loggedInView->TextAreaMessage($message);
	
	$this->serviceHelper = new serviceHelper();
	$this->cookieStorage = new CookieStorage();
	$this->con = new mysqli("127.0.0.1", "root", "", "Login");
	}
	
	
	
	public function doController(){
		$wBrowserAgent = $this->serviceHelper->userAgent();

		
		//Kontrollerar om användaren tryck på "submit" i LoginView-klassen..
		$didUserClickSubmit = $this->view->getSubmit();
		
		if($didUserClickSubmit)
		{
		//Om "True" studsar vi vidare in i functionen "collectAndVerifyInfoFromUser"
			$this->collectAndVerifyInfoFromUser();
		}
		
		//Ropar på funktionen isAutorised..
		$this->isAutorised();
		//Här visas antingen inloggningsvyn eller inloggadVyn
		if($this->model->getUserIsAuthenticated($wBrowserAgent))
		{
			$this->uploadController->doController();
			return $this->loggedInView->LoggedInView();
		}
		else
		{
			return $this->view->ViewLogin();
		}
	}

	
	public function secureInputData($collectedUser, $collectedPass)
	{
		//Tvättar strängarna från farligheter så som SQL-Injections..
		$safeCollectedUsername = mysqli_real_escape_string($this->con ,$collectedUser);
		$safeCollectedPassword = mysqli_real_escape_string($this->con ,$collectedPass);
		
		//Skickar in de säkra variablerna till datorbasen
		$userAndPass = $this->model->loginValidation($safeCollectedUsername, $safeCollectedPassword);
		
		//Returnerar false eller true..
		return $userAndPass;
		
	}
	
	
	public function collectAndVerifyInfoFromUser()
	{
		
		//Sparar undan värdet som är satt i inloggningsrutan.
		$this->view->getInformationFromUser();
	
		//Hämtar värdet som matades in i inloggningsrutan.
		$collectedUsername = $this->view->getUsername();
		$collectedPassword = $this->view->getPassword();
	
		//Skickar vår data till funktionen secureInputData..
		$safeUserAndPass = $this->secureInputData($collectedUsername, $collectedPassword);

	
		//Kontroll om användaren och lösenord var korrekt eller inte..

		if($safeUserAndPass === false){
			$this->view->logInFailed($collectedUsername, $collectedPassword);
		}else{		
				//Har användaren tryck på "kom ihåg mig.." så skapas en cookie för användaren..annars inte..
				$stayOnline = $this->view->stayLoggedIn();

					if($stayOnline === true)
					{
						$randomString = $this->model->getRandomString();
						$time = $this->cookieStorage->cookieSave($randomString);
						$this->model->saveTheCookieTime($time);
						$message = "Inloggning lyckades och du kommer att kommas ihåg nästa gång du surfar in här";
						$this->loggedInView->displayMessage($message);
					}else{$message = $this->view->LogInWasSuccessful();
						$this->loggedInView->displayMessage($message);
			}
		}
	}
	
	//Validerar om användaren är inloggad med sessioner - om false så kontrolleras om det finns en cookie
	// och om det är false också så visas ett felmeddelande. Finns däremot en cookie så matchas denna så det är
	//en korrekt kaka. Om det inte är korrekt så får man fel.
	public function isAutorised()
	{
		$wBrowserAgent = $this->serviceHelper->userAgent();
		$isUserAuthenticated = $this->model->getUserIsAuthenticated($wBrowserAgent);
			if($isUserAuthenticated === false){
				if($this->cookieStorage->load()){
					$cookie = $this->cookieStorage->doesCookieAlreadyExist();
				if($this->model->checkTheCookieValue($cookie, $wBrowserAgent)){
					$message = "Inloggning lyckades via cookies";
					$this->loggedInView->displayMessage($message);
				}	else{
				$this->cookieStorage->delete();
				$message = "Felaktig information i cookie";
				$this->loginView->displayMessage($message);
			}
		}
	}
	if($this->model->getUserIsAuthenticated($wBrowserAgent)){
		$userLogOut = $this->loggedInView->userLogOut();
		if($userLogOut === true){
			$this->cookieStorage->delete();
			$message = $this->loggedInView->logOutMessage();
			$this->view->displayMessage($message);
			$this->model->destroySession();
			}
		}
	}
}