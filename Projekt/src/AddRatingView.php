<?php

	require_once 'common/HTMLView.php';
	
	class AddRatingView extends HTMLView {

		private $loginmodel;
		private $message = "";

		public function __construct(LoginModel $model){

				$this->loginmodel = $model;
		}

		public function didUserPressAddGradeButton(){

			if(isset($_POST['creategradebutton']))
			{
				return true;
			}
			return false;
		}

		public function pickedGradeDropdownValue(){

			if(isset($_POST['dropdownpickgrade']))
			{
				return $_POST['dropdownpickgrade'];
			}
			return false;

		}

		public function ShowAddRatingPage(EventList $eventlist, BandList $bandlist, GradeList $gradelist){

			

			// Variabler
			$weekDay = ucfirst(utf8_encode(strftime("%A"))); // Hittar veckodagen, tillåter Å,Ä,Ö och gör den första bokstaven stor.
			$month = ucfirst(strftime("%B")); // Hittar månaden och gör den första bokstaven stor.
			$year = strftime("%Y");
			$time = strftime("%H:%M:%S");
			$format = '%e'; // Fixar formatet så att datumet anpassas för olika platformar. Lösning hittade på http://php.net/manual/en/function.strftime.php
			
			


			// visa Lägga till event och band sidan.
				
					$contentString = 
					 "
					<form method=post >
						<fieldset>
							<legend>Lägga till nytt betyg till spelning med följande band</legend>
							$this->message
							Plats:
							 <select name='dropdownpickevent'>";
							 foreach($eventlist->toArray() as $event)
							 {
							 	$contentString.= "<option value='". $event->getID()."'>".$event->getName()."</option>";
							 }
							 
							 $contentString .= "</select>
							 <br>
							Band:
							<select name='dropdownpickband'>";
							 foreach($bandlist->toArray() as $band)
							 {
							 	$contentString.= "<option value='". $band->getID()."'>".$band->getName()."</option>";
							 }
							 
							 $contentString .= "</select><br>
							 Betyg:
							<select name='dropdownpickgrade'>";
							 foreach($gradelist->toArray() as $grade)
							 {
							 	$contentString.= "<option value='". $grade->getID()."'>".$grade->getGrade()."</option>";
							 }
							 
							 $contentString .= "</select><br>
							Skicka: <input type='submit' name='creategradebutton'  value='Lägg till Betyg'>
						</fieldset>
					</form>";

					if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
					{
    					$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
					}

					$HTMLbody = "
				<h1>Lägg till betyg till vald spelning med band</h1>
				<p><a href='?login'>Tillbaka</a></p>
				$contentString<br>
				" . strftime('' . $weekDay . ', den ' . $format . ' '. $month . ' år ' . $year . '. Klockan är [' . $time . ']') . ".";

				$this->echoHTML($HTMLbody);
			
			}

			public function showMessage($message)
			{
				$this->message = "<p>" . $message . "</p>";
			}

				// Visar Lägga till event-meddelande.
			public function successfulAddGradeToEventWithBand()
			{
				$this->showMessage("Betyget har lagts till event med band!");
			}





	}