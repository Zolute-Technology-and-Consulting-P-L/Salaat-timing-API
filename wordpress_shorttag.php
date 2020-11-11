<?php

define( 'CUSTOM_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );
require_once CUSTOM_THEME_DIR . 'PrayTime.class.php';

function zolute_salaat_time_shorttag($att){
	$lat = $att['lat'];
	$long = $att['long'];
	$tz = $att['tz'];
	$prayTime = new PrayTime($lat,$long,$tz,date('Y-m-d'));
    $result = json_decode($prayTime->resultArray()); 
	echo "<table>
<tbody>
<tr>
<td>Event</td>
<td>Time</td>
</tr>
<tr>
<td>Date</td>
<td>$result->date</td>
</tr>
<tr>
<td>Sihori</td>
<td>$result->sihori</td>
</tr>
<tr>
<td>Fajar</td>
<td>$result->fajr</td>
</tr>
<tr>
<td>Sunrise</td>
<td>$result->sunrise</td>
</tr>
<tr>
<td>Zawaal</td>
<td>$result->zawaal</td>
</tr>
<tr>
<td>Zuhr</td>
<td>$result->zuhr</td>
</tr>
<tr>
<td>Asr</td>
<td>$result->asr</td>
</tr>
<tr>
<td>Maghrib</td>
<td>$result->maghrib</td>
</tr>
</tbody>
</table>";
	
}
add_shortcode('zolute_salaat_time', 'zolute_salaat_time_shorttag'); 