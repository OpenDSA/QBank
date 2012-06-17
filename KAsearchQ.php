<html>
<head>
    <title>Question Find And Edit - QBank</title>  
  
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
List of existing converted Khan Academy question files:
</h2>
<?php


//Displays list of existing questions on screen.
$pathQ = './Exercises';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".html", $output);
echo "<table border=1 align=center CELLSPACING=1  CELLPADDING=2 width=20%>";
for($i=0; $i<sizeof($filearray); $i++){
	
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo "<a href=\"/QBank/Exercises/$filearray[$i].html\";>";
	echo $filearray[$i]; 
	echo "</a>";
	echo "<p>";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
?>

<form accept-charset="UTF-8" action="KAView.php" autocomplete="off" class="question_edit" id="question_edit" method="post"><div style="margin:0;padding:0;display:inline">
<input name="utf8" type="hidden" value="&#x2713;" /> 

<!-- <h2> Enter filename to view: 
<input type="text" name="QFileName" maxlength="20"/> 

<input class="ui-state-default ui-corner-all submitButton" data-disable-with="Saving..." id="save_button" name="commit" type="submit" value="View"/> -->
</h2>
</form>
 <p></p>

    <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>


</html>