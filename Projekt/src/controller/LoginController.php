<?php
	
	require_once("./src/model/LoginModel.php");
	require_once("./src/view/LoginView.php");
	require_once("./src/model/DBDetails.php");
	
	class LoginController
	{
		private $view;
		private $model;
		private $db;
		
		public function __construct()
		{
						
			// Skapar nya instanser av modell- & vy-klassen och l�gger dessa i privata variabler.
			$this->model = new LoginModel();
			$this->view = new LoginView($this->model);
			$this->db = new DBDetails();
			
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

				if($this->view->didUserPressRegister() && !$this->view->didUserPressLogin() && !$this->model->checkLoginStatus())
				{
					$this->doRegister();
				}
			}
			if($this->model->checkLoginStatus() && $this->view->searchForCookies())
			{
				$this->view->showLoginPage();
			}
			if($this->model->checkLoginStatus() && !$this->view->searchForCookies())
			{
				
				$this->view->showLoginPage();
			}
			
		}
		
		// H�mtar sidans inneh�ll.
		public function doHTMLBody()
		{
			if(!$this->view->didUserPressRegister() && !$this->view->didUserPressLogin() && !$this->model->checkLoginStatus())
			{
				
				$this->view->showLoginPage();
			}

			
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
					$inputUsername = $this->view->getInputUsername();
					$inputPassword = $this->view->getInputPassword();

					$this->model->verifyUserInput($inputUsername, $this->model->cryptPassword($inputPassword));
					
					// Kontrollerar om "H�ll mig inloggad"-rutan �r ikryssad.
					if($checkboxStatus === true)
					{
						// Skapa cookies.
						$this->view->createCookies($inputUsername, $this->model->cryptPassword($inputPassword));
						
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
			
				
			
			if(!$this->view->didUserPressLogout() && !$this->model->checkLoginStatus())
			{
					
				$this->view->showLoginPage();
			}




				
			
			
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

		public function doRegister(){

			$registerUsername = $this->view->getRegisterUsername();
			$registerPassword = $this->view->getRegisterPassword();
			$registerRepeatPassword = $this->view->getRepeatRegisterPassword();

			if($this->view->didUserPressRegister() && !$this->view->didUserPressLogin() && !$this->model->checkLoginStatus())
			{
				try{

						
					if($this->view->didUserPressCreateUser())
					{
						
						
						
						if($this->model->CheckBothRegInput($registerUsername,$registerPassword))
						{
							if($this->model->CheckRegUsernameLength($registerUsername) && $this->model->CheckReqPasswordLength($registerPassword))
							{
								if($this->model->ComparePasswordRepPassword($registerPassword,$registerRepeatPassword))
								{

									if($this->db->ReadSpecifik($registerUsername))
									{
										if($this->model->ValidateInput($registerUsername))
										{
											
											$this->model->addUsersetSuccess($registerUsername,$this->model->cryptPassword($registerPassword));
											
											if($this->model->UserRegistered())
											{
												$this->view->successfulRegistration();
												$this->view->showLoginPageWithRegname();
												
											}

											
										}
									}
								}
								
							}
						}
					}
					
				}
				catch(Exception $e)
				{
					$this->view->showMessage($e->getMessage());
				}
				
			}
			if($this->model->UserRegistered() == false)
			{

			
			
				return $this->view->showRegisterPage();
			}
			
		


		}
	}
	
?>