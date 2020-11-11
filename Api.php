<?php
header('Content-type: application/json');
	// Prayer Times Calculator, Sample Usage
	// By: Hamid Zarrabi-Zadeh
	// Inputs : $method, $year, $latitude, $longitude, $timeZone
include_once('PrayTime.class.php');


?>

<?php
  ini_set('display_errors',1);
    
    if(isset($_GET)){
         if(!empty($_GET['let'])){
               $latitude  = $_GET['let'];
             }
          else{
              $result['let'] = "22.719569";
            }
         if(!empty($_GET['lng'])){
               $longitude  = $_GET['lng'];
             }
          else{
              $result['lng'] = "75.857726";
            }
          if(!empty($_GET['tz'])){
               $timezone  = $_GET['tz'];
             }
          else{
              $result['tz'] = "5.5";
            }	
		
		$date = isset($_GET['date']) ? $_GET['date'] : date("m/d/Y");
      }
    if(empty($result))
    {
         $prayTime = new PrayTime($latitude,$longitude,$timezone,$date);
         $rsult = $prayTime->resultArray(); 
         echo $rsult;
    }
    else{
          echo json_encode($result,true);
      }
	

	
?>