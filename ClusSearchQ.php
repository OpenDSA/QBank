<!DOCTYPE html>

<html>
<head>
    <title>Question Find And Edit - QBank</title>  
  
<meta name="Description" content="A bank of community-written homework and test questions where authors get credit and access is completely free.">
<link href="new.css" rel="stylesheet" type="text/css" />

<table border ="1" width="100%">
<tr>
  <td border="1" align="center" width="10%"><a href="index.html"><img src="QBank.png" href = /></a></td>
  <td align="left width="90%"><h1>QBank - Question banking made easy with parameterization. </h1></td>
</tr>
</table>  

</head>
<h2>
You are creating a question from existing Simple questions
</h2>


<form accept-charset="UTF-8" action="ClusterV1.php" autocomplete="off" class="question_edit" id="question_edit" method="post"><div style="margin:0;padding:0;display:inline">
<input name="utf8" type="hidden" value="&#x2713;" /> 

<?php


$pathQ = './Intermediate_files/Group';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".txt", $output);
echo "<table border=1 align=center width=20%>";
	
	
	
for($i=0; $i<sizeof($filearray)-1; $i++){
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "</tr>";

}

echo "<tr>";
echo "<td>";
echo "<i> Enter a filename to preview:</i> ";
echo "<input type=\"text\" name=\"QFileName2\" maxlength=\"20\"/>";
echo "<input type=\"hidden\" name=\"radio2\" value=\"grp\" />";

echo "<button type=\"submit\" formaction=\"ClusterV2.php\">View</button>";

echo "<p>";
echo "</td>";
echo "</tr>";


echo "</table>";

$pathQ = './Intermediate_files/Simple';
$execCmd = 'ls -rt '.$pathQ.' | tr " " "\t"';
$output = shell_exec($execCmd);
$filearray = explode(".txt", $output);
echo "<table border=1 align=center width=20%>";
	
	
echo "<i>";
echo "Update/Create Group question as(FileName): ";
echo "</i>";
echo "<input type=\"text\" name=\"FileName\" maxlength=\"20\"/>"; 





echo "<input name=\"file_size\" type=\"hidden\" value=\"".sizeof($filearray)."\" />";	
for($i=0; $i<sizeof($filearray)-1; $i++){
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo "<input type=\"checkbox\" name=\"f_name[]\" value=".$filearray[$i]." />";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "</tr>";

}
echo "<tr>";
echo "<td>";
echo "<i> Enter a filename to preview:</i> ";
echo "<input type=\"text\" name=\"QFileName\" maxlength=\"20\"/>";
echo "<input type=\"hidden\" name=\"radio1\" value=\"simple\" />";

echo "<input class=\"ui-state-default ui-corner-all submitButton\" data-disable-with=\"Saving...\" id=\"save_button\" name=\"commit\" type=\"submit\" value=\"View\"/>";
echo "<p>";
echo "</td>";
echo "</tr>";


echo "</table>";
?>





<h2>
Select checkboxes to group questions:

<button type="submit" formaction="Cluster.php">Group</button></h2>
</form>
 <p></p>

    <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>


</html>