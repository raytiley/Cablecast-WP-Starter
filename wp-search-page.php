<?php 
//Configure Script
$searchPageID = 38;
$showDetailsURL = "/?page_id=15&";  // Must end with a '?' or '&'
$server = "http://frontdoor.ctn5.org/"; //include trailing backslash
$channelID = 1; //Cablecast Channel ID
//End Configure

//SOAP Client Setup
$server= $server."/CablecastWS/CablecastWS.asmx?WSDL";
$client = new SoapClient($server); 
//End SOAP Client Setup

echo "<form>\n";
echo "<input type=\"hidden\" name=\"page_id\" value=\"".$searchPageID."\" />";
echo "<table>\n";
echo "<tr><th>Title Contains</th><td><input type=\"text\" name=\"search_title\" value=\"".$_GET["search_title"]."\"/></td></tr>\n";
echo "<tr><td colspan=2><input type=\"submit\" value=\"Search\" />";
echo "</table>\n";
echo "</form>";

if($_GET["search_title"])
{
  //Do Search on Search terms
  $result = $client->SimpleShowSearch(array("ChannelID" => $channelID, "searchString" => $_GET["search_title"]));
  
  echo "<h2>Results</h2>";
  //Build Results Table
  $results_count = count($result->SimpleShowSearchResult->SiteSearchResult->Shows->ShowInfo);
  if($results_count > 0)
  {
    echo "<table>";
    foreach($result->SimpleShowSearchResult->SiteSearchResult->Shows->ShowInfo as $show)
    {
      echo "<tr><th colspan=2>".$show->Title."</th></tr>";
      echo "<tr><td>";
      echo "<div style=\"margin-left:25px\">";
      echo $show->Comments."<br/>";
      echo "<a href=\"".$showDetailsURL."ShowID=".$show->ShowID."\">View Detials</a>";
      echo "</div>";
      echo "</td></tr>";
    }
    echo "</table>";
  }
  else
  {
    echo "Your Search yeilded no results.  Please try again.";
  }
  
}
?>