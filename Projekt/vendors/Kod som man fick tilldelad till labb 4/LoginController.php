<?php
	
	require_once("src/LoginModel.php");
	require_once("src/LoginView.php");
	
	class LoginController
	{
		private $view;
		private $model;
		
		public function __construct()
		{
			// Sparar ner anv�ndarens anv�ndaragent och ip. Anv�nds vid verifiering av anv�ndaren.
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
						
			// Skapar nya instanser av modell- & vy-klassen.
			$this->model = new LoginModel($userAgent);
			$this->view = new LoginView($this->model);
			
			// Kontrollerar ifall det finns kakor och ifall anv�ndaren inte �r inloggad.
			if($this->view->searchForCookies() && !$this->model->checkLoginStatus())
			{
				try
				{
					// Logga in med kakor.
					$this->view->loginWithCookies();
				}
				catch(Exception $e)
				{
					// Visar eventuella felmeddelanden.
					$this->view->showMessage($e->getMessage());
					
					// Tar bort de felaktiga kakorna.
					$this->view->removeCookies();
				}
			}
			else // Annars, visa standardsidan p� normalt vis.
			{
				// Ifall anv�ndaren tryckt p� "Logga in" och inte redan �r inloggad...
				if($this->view->didUserPressLogin() && !$this->model->checkLoginStatus())
				{
					// ...s� loggas anv�ndaren in.
					$this->doLogin();
				}
			
				// Ifall anv�ndaren tryckt p� "Logga ut" och �r inloggad...
				if($this->view->didUserPressLogout() && $this->model->checkLoginStatus())
				{
					// ...s� loggas anv�ndaren ut.
					$this->doLogout();
				}
			}
		}
		
		// H�mtar sidans inneh�ll.
		public function doHTMLBody()
		{
			return $this->view->showLoginPage();
		}
		
		// F�rs�ker verifiera och logga in anv�ndaren.
		public function doLogin()
		{
			// Kontrollerar ifall anv�ndaren tryckt p� "Logga in" och inte redan �r inloggad.
			if($this->view->didUserPressLogin() && !$this->model->checkLoginStatus())
			{
				// Kontrollerar indata
				$checkboxStatus = false;
				
				// Kontrollera ifall "H�ll mig inloggad"-rutan �r ikryssad.
				if(isset($_POST['checkbox']))
				{
					$checkboxStatus = true;
				}
				
				try
				{
					// Verifiera data i f�lten.
					$this->model->verifyUserInput($_POST['username'], md5($_POST['password']));
					
					// Kontrollerar om "H�ll mig inloggad"-rutan �r ikryssad.
					if($checkboxStatus === true)
					{
						// Skapa cookies.
						$this->view->createCookies($_POST['username'], md5($_POST['password']));
						
						// Visar cookielogin-meddelande.
						$this->view->successfulLoginAndCookieCreation();
					}
					else
					{
						// Visar login-meddelande.
						$this->view->successfulLogin();
					}
				}
				catch(Exception $e)
				{
					// Visar eventuella felmeddelanden.
					$this->view->showMessage($e->getMessage());
				}
			}
			
			//Generera utdata
			return $this->view->showLoginPage();
		}
		
		// Loggar ut anv�ndaren.
		public function doLogout()
		{
			// Kontrollera indata, tryckte anv�ndaren p� Logga ut?
			if($this->view->didUserPressLogout() && $this->model->checkLoginStatus())
			{
				// Logga ut.
				$this->model->logOut();
				
				// Ifall det finns cookies...
				if($this->view->searchForCookies())
				{
					// ...ta bort dem.
					$this->view->removeCookies();
				}
				
				//Generera utdata, till�t anv�ndaren att logga in igen.
				$this->doLogin();
				$this->view->successfulLogout();
			}
		}
	}
	
?>