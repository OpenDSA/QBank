<?php

	$qName = $_POST['QFileName'];
	$qName = trim($qName);
	
	$myFile1 = '/./QBank/Exercises/'.$qName.'.html';
	$myFile = '/QBank/Exercises/'.$qName.'.html';


	if(!file_exists($myFile1)) {

echo "<html>";
echo "<head>";


echo "<title>Question Find And Edit - QBank</title>";
  
echo "<link href=\"new.css\" rel=\"stylesheet\" type=\"text/css\" />";

echo "<table border =\"1\" width=\"100%\">";
echo "<tr>";
  echo "<td border=\"1\" align=\"center\" width=\"10%\"><a href=\"index.html\"><img src=\"QBank.png\" href = /></a></td>
  <td align=\"left\" width=\"90%\"><h1>QBank - Question banking made easy with parameterization. </h1></td>
</tr>
</table>  ";


echo "<h2>Please go back and enter a valid question file name!!!!";

		
echo "</h2>";
 echo "<div id=\"footer\" style=\"float:center\">
     	  <a href=\"index.html\">Features</a> | 
         <a href=\"index.html\">About QBank</a> | 
         <a href=\"index.html\">Contact Us</a>";
    echo "</div>";
echo "</head>";

echo "</html>";

	}
	else {
		header("Location: $myFile");
	}
 
?>