<?php 

	require_once("DBDetails.php");

	class AddRatingModel{

		private $db;

		public function __construct(){

			
			$this->db = new DBDetails();
		}


		public function getDropdownlistEvent(){

			var_dump($this->db->fetchAllEvents());

		}



	}