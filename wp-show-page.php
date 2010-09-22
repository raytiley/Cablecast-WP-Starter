<?php
//Configure Script
$server = "http://frontdoor.ctn5.org/"; //include trailing backslash
$scheduleDateFormat = "l F jS @ g:i a";  //search for date() on php.net for info on how to format this string
$eventDateFormat = "F jS Y";

//Configure the channels below by replacing the "CTN.." with your channels name and the appropriate channelID
//You can add as many channels as you have.

$channels = array(
  array("name" => "CTN Channel 5", "id" => 1),
  array("name" => "PPAC Channel 2", "id" => 2),
  );
date_default_timezone_set('America/New_York');

//End Setup


function padWithZeros($s, $n) 
{
  return sprintf("%0" . $n . "d", $s);
}

$server= $server."/CablecastWS/CablecastWS.asmx?WSDL";
$client = new SoapClient($server);


if(!$_GET['ShowID'])
{ 
  echo "Error!  No Show ID supplied";
}

else
{
  $result = $client->GetShowInformation(array('ShowID' => $_GET['ShowID']));

  $searchLength = strtotime(date("Y-m-d")."T".date("H:i:s")) + (60*60*24*35);

  echo "<table>\n";
  
  //Arange Field order by copy and pasting.  Use Two slashes to comment out a field '//'
  
  //Show Title
  echo "<tr><th>Program Title</th><td>".$result->GetShowInformationResult->Title."</td></tr>\n";
  //Show Producer
  echo "<tr><th>Producer</th><td>".$result->GetShowInformationResult->Producer."</td></tr>\n";
  //Show Comments
  echo "<tr><th>Comments</th><td>".$result->GetShowInformationResult->Comments."</td></tr>\n";
  //Show EventDate
  echo "<tr><th>Event Date</th><td>".date($eventDateFormat,strtotime($result->GetShowInformationResult->EventDate))."</td></tr>\n";
  //StreamingFileURL
  echo "<tr><th>Watch Online</th><td>".($result->GetShowInformationResult->StreamingFileURL != "" ? "<a href='".$result->GetShowInformationResult->StreamingFileURL."'>Launch Video</a>" : "Not Available" )."</td></tr>\n";
  //Show Length
  echo "<tr><th>Program Length:</th><td>".floor($result->GetShowInformationResult->TotalSeconds / 3600).":".padWithZeros(floor(floor($result->GetShowInformationResult->TotalSeconds % 3600) / 60), 2).":".padWithZeros(($result->GetShowInformationResult->TotalSeconds % 60), 2)."</td></tr>\n";


  foreach($channels as $channel)
  {

    $schedule = $client->GetScheduleInformation(array(
      'ChannelID'     => $channel["id"],
      'FromDate'      => date("Y-m-d")."T00:00:00",
      'ToDate'        => date("Y-m-d", $searchLength)."T".date("H:i:s", $searchLength),
      'restrictToShowID' => $_GET['ShowID']));

    echo "<tr><th>Scheduled on ".$channel['name']."</th>\n";

    if (count($schedule->GetScheduleInformationResult->ScheduleInfo) == '0')
    {
      echo "<td>This Program is Not Currently Scheduled on ".$channel['name']."</td></tr>\n";

    }
    else
      {	echo "<td>\n";
    foreach($schedule->GetScheduleInformationResult->ScheduleInfo as $run)
    {
      echo date($scheduleDateFormat,strtotime($run->StartTime))."<br />\n";
    }
    echo "</td></tr>\n";
  }
}
echo "</table>\n";
}


?>