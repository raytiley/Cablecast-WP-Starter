<?php
//Configure Script
$server = "http://frontdoor.ctn5.org/"; //include trailing backslash
$channelID = 1; //Cablecast Channel ID
$displayDays = 7;  //Number of Days to Display
$showDetailsURL = "/?page_id=15&";  // Must end with a '?' or '&'
date_default_timezone_set('America/New_York');
//End Configure

//SOAP Client Setup
$server= $server."/CablecastWS/CablecastWS.asmx?WSDL";
$client = new SoapClient($server); 
//End SOAP Client Setup

//Create search dates
$dateString = date("Y-m-d")."T00:00:00";  
$endDate = date("Y-m-d", strtotime($dateString) + ($displayDays * 24 * 60 * 60))."T12:00:00";

//Search the schedule 
$result = $client->GetScheduleInformation(array(
  'ChannelID' => $channelID,
  'FromDate' => $dateString,
  'ToDate' => $endDate,
  'restrictToShowID' => 0));

//Loop through runs creating a table
$startDay = "";
echo "<table>\n<tr><th>Time</th><th>Program Title</th></tr>\n";
foreach($result->GetScheduleInformationResult->ScheduleInfo as $run)
{
  $day = date("Y-m-d", strtotime($run->StartTime));
  if($day != $startDay)
  {
    echo "<tr><th colspan=\"2\">".date("l F jS, Y", strtotime($day))."</th></tr>\n";
    $startDay = $day;
  }
  echo "<tr><td><NOBR>".date("g:i a", strtotime($run->StartTime))."</NOBR></td><td><a href=\"".$showDetailsURL."ShowID=".$run->ShowID."\">".$run->ShowTitle."</a></td></tr>\n";
  $count++;
}
echo "</table>\n";


?>