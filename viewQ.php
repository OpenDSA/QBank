<html>
<head>
    <title>View a question - QBank</title>  
  
<meta name="Description" content="A bank of community-written homework and test questions where authors get credit and access is completely free.">
<link href="new.css" rel="stylesheet" type="text/css" />

<table border ="1" width="100%">
<tr>
  <td border="1" align="center" width="10%"><a href="index.php"><img src="QBank.png" href = /></a></td>
  <td align="left width="90%"><h1>QBank - Question banking made easy with parameterization. </h1></td>
</tr>
</table>  

</head>
<h2>
List of existing question files:
</h2>


<?php

//Displays list of existing questions on screen.
$pathQ = './Intermediate_files/Parameter';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".txt", $output);
echo "<table border=1 align=center CELLSPACING=1  CELLPADDING=2 width=20%>";
echo "<h3>";
echo "Parameterised Q";
echo "</h3>";



for($i=0; $i<sizeof($filearray)-1; $i++){
	echo "<html>";
	echo "<form method=\"post\">";
	echo "<tr>";
	echo "<td width=\"50%\">";
	echo "<p>";
	echo $filearray[$i]; 
       echo "<input type=\"hidden\" name=\"radio1\" value=\"par\" />";
	echo "<input type=\"hidden\" name=\"QFileName\" value=\"$filearray[$i]\"/> ";
	echo "<p>";
	echo "</td>";
	echo "<td width=\"50%\">";
	echo "<p>";
	echo "<button type=\"submit\" formaction=\"Qview.php\">View/Delete</button>";
	echo "<button type=\"submit\" formaction=\"QEdit.php\">Edit</button>";
	echo "<p>";
	echo "</td>";
	echo "</tr>";
	echo "</form>";
	echo "</html>";
}
echo "</table>";

$pathQ = './Intermediate_files/Group';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".txt", $output);
echo "<table border=1 align=center CELLSPACING=1  CELLPADDING=2 width=20%>";

echo "<h3>";
echo "Group Q";
echo "</h3>";

for($i=0; $i<sizeof($filearray)-1; $i++){
	echo "<html>";
	echo "<form method=\"post\">";
	echo "<tr>";
	echo "<td width=\"50%\">";
	echo "<p>";
	echo $filearray[$i]; 
 	echo "<input type=\"hidden\" name=\"radio1\" value=\"grp\" />";
	echo "<input type=\"hidden\" name=\"QFileName\" value=\"$filearray[$i]\"/> ";
	echo "<p>";
	echo "</td>";
	echo "<td width=\"50%\">";
	echo "<p>";
	echo "<button type=\"submit\" formaction=\"Qview.php\">View/Delete</button>";
	echo "<button type=\"submit\" formaction=\"QEdit.php\">Edit</button>";
	echo "<p>";
	echo "</td>";
	echo "</tr>";
	echo "</form>";
	echo "</html>";

}
echo "</table>";

$pathQ = './Intermediate_files/Simple';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".txt", $output);
echo "<table border=1 align=center CELLSPACING=1  CELLPADDING=2 width=20%>";

echo "<h3>";
echo "Simple Q";
echo "</h3>";
for($i=0; $i<sizeof($filearray)-1; $i++){
	echo "<html>";
	echo "<form method=\"post\">";
	echo "<tr>";
	echo "<td width=\"50%\">";
	echo "<input type=\"hidden\" name=\"radio1\" value=\"simple\" />";
	echo "<input type=\"hidden\" name=\"QFileName\" value=\"$filearray[$i]\"/> ";
	echo "<p>";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "<td width=\"50%\">";
	echo "<p>";
	echo "<button type=\"submit\" formaction=\"Qview.php\">View/Delete</button>";
	echo "<button type=\"submit\" formaction=\"QEdit.php\">Edit</button>";

	echo "<p>";
	echo "</td>";
	echo "</tr>";
	echo "</form>";
	echo "</html>";

}
echo "</table>";
?>

 <p></p>

    <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>

</html>