<?php
//--------------------- PrayTime Class -----------------------

class PrayTime
{

var $date;
var $timezone;
var $longitude;
var $latitude;

  
    //----------------------- Constructors -------------------------

    function getSunriseSunset(){


    	
    	//http://api.usno.navy.mil/rstt/oneday?date=06/22/2016&coords=35.2270869,-80.84312669999997&tz=-4
    	$service_url = $service_url = 'http://api.usno.navy.mil/rstt/oneday?date='.$this->date.'&coords='.$this->latitude.','.$this->longitude.'&tz='.$this->timezone;
       
    	$curl = curl_init($service_url);
    	
    	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($curl, CURLOPT_POST, true);
    	//curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    	$curl_response = curl_exec($curl);
        if ($curl_response === false) {
  		
  		$message['error'] = 'Curl error: ' . curl_error($curl);
 		//$message['params']=$params;
  		$result = array('STATUS' => 'FAILED', 'DATA' => $message);
  	} else {
  		$result = json_decode($curl_response, true);
  	}
         
    	curl_close($curl);
	

    	
    	//$result = 'http://api.sunrise-sunset.org/json?lat='.$this->latitude.'&lng='.$this->longitude.'&date='.$date;
    	return $result;
    	
    	
    }
    function resultArray(){
    	 $sr = $this->convertSunriseSunsetInGMT();
    	
    	  
    	$data = [
    	    'date'    => date("d-m-Y"),
    	    'geoLong' => $this->longitude,
    	    'geolang' => $this->latitude,
    	    'timezone'=> $this->timezone,
    	    'sihori'  => $this->calculateSihoriTime(),
    	    'fajr'    => $this->calculateFajrTime(),
    	    'sunrise' => date('H:i a',strtotime($sr['sunrise'])),
    	    'zawaal'  => $this->calculateZawaalTime(),
    	    'zuhr'    => $this->calculateZuhrTime(),
    	    'asr'     => $this->calculateAsrTime(),
    	    'maghrib'  => $this->calculateMaghribTime(),
'dayGhari' => $this->toCalculateGhari()['dayghari'],
'nightGhari' => $this->toCalculateGhari()['nightghari']
    	];
    	return json_encode($data);
    }
    
    function convertSunriseSunsetInGMT(){
    	$result = $this->getSunriseSunset();
    	$sunrise = date("H:i:s", strtotime("-1 minute", strtotime($result['sundata'][1]['time'])));
    	//$sunrise  = date("H:i:s",strtotime($result['sundata'][1]['time']));
    	$sunset   = date("H:i:s",strtotime($result['sundata'][3]['time']));
    
    	$data = ['sunrise'=>$sunrise,'sunset'=>$sunset];
    	
    	return $data;
    	
    }
    
    function calculateZawaalTime(){
    	$daylight = $this->toCalculateDaylight();
    	$sunrise = $this->convertSunriseSunsetInGMT();
    	$sec = ((($daylight->h)*3600) + (($daylight->i)*60) + ($daylight->i))/2;
    	$sum = strtotime("+".round($sec)." second", strtotime($sunrise['sunrise']));// strtotime($sec/2)/60 + $sunrise['sunrise'];
    	
    	$dn    = date('h:i a', $sum);
  
        return $dn;
    	
    //	$ztime  = $this
    }
    
    function calculateZuhrTime(){
    	$gharitime = $this->toCalculateGhari();
    	$timecal = $this->calculateZawaalTime();
    	$sec =  $gharitime['dayghari']*2;
    	$sum = strtotime("+".round($sec)." second", strtotime($timecal));
    	//$ztime = (($gharitime['dayghari']*2)/60)/60 + $timecal;
    	$dn    = date('h:i a', $sum);
    	return  $dn;   
    	
        
    }
    
    function calculateAsrTime(){
    	$gharitime = $this->toCalculateGhari();
    	$timecal = $this->calculateZawaalTime();
    	$sec =  $gharitime['dayghari']*4;
    	$sum = strtotime("+".round($sec)." second", strtotime($timecal));
    	//$ztime = (($gharitime['dayghari']*2)/60)/60 + $timecal;
    	$dn    = date('h:i a', $sum);
    	return  $dn;   
    }
    
    function calculateMaghribTime(){
    	$timecal = $this->convertSunriseSunsetInGMT();
    	$ztime =   date('h:i a',strtotime($timecal['sunset']));
    	
    	return  $ztime;
    }
    
    function calculateSihoriTime(){
    	$timecal = $this->convertSunriseSunsetInGMT();
    	
    	$sec = (1*3600) + (15*60);
    	$sum = strtotime("-".$sec." second", strtotime($timecal['sunrise']));
    	$dn    = date('h:i a', $sum);
    	
    	 
    	return  $dn;
    }
    
    function calculateFajrTime(){
    	$timecal = $this->convertSunriseSunsetInGMT();
    	$gharitime = $this->toCalculateGhari();
    	
    	$sec = $gharitime['nightghari'];
    	$sum = strtotime("-".round($sec)." second", strtotime($timecal['sunrise']));
    	
    	$dn    = date('h:i a', $sum);
    	
    	return  $dn;
    }
    
    function toCalculateGhari(){
    	$daylight = $this->toCalculateDaylight();
    	$sec =    (($daylight->h)*3600) + (($daylight->i)*60) + ($daylight->i);
    	$day_ghari = ($sec/12);
    	$night_ghari = 120*60 - $day_ghari -1;
    	
    	$ghari = [
    	    'dayghari' => round($day_ghari),
    	    'nightghari' => round($night_ghari)
    	];
    	
    	return $ghari;
    }
    
    function toCalculateDaylight(){
    	$timecal = $this->convertSunriseSunsetInGMT();
    	
    	$datetime1 = new DateTime($timecal['sunrise']);
    	$datetime2 = new DateTime($timecal['sunset']);
    	$interval = $datetime1->diff($datetime2);
    	
    	
    	return $interval;
    }
    
    /*function PrayTime($latitude,$longitude,$timezone,$date)
    {

        return $this->getSunriseSunset($latitude,$longitude,$timezone,$date);
       
    }*/

    function __construct($latitude=0,$longitude=0,$timezone=0,$date='2016-01-01')
    {
$this->date = $date;
$this->longitude = $longitude;
$this->latitude = $latitude;
$this->timezone = $timezone;
        
    }



    //-------------------- Interface Functions --------------------



}

//---------------------- prayTime Object -----------------------

//$prayTime = new PrayTime();

?>