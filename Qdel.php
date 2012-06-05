<html>
<head>
    <title>QBank: Question Bank generation made easy.</title>
  
  <meta name="Description" content="A Question generation tool to create parameterized questions.">
  <link href="new.css" rel="stylesheet" type="text/css" />
<table border ="1" width="100%">
<tr>
  <td border="1" align="center" width="10%"><a href="index.html"><img src="QBank.png" href = /></a></td>
  <td align="left width="90%"><h1>QBank - Question banking made easy with parameterization. </h1></td>
</tr>
</table>


</head>

<body>


<?

/*This file saves a new question into an intermediate format and also calls a method to convert intermdiate format to Khan Academy format*/

$fileName = $_POST['file_name'];
echo "<h2>";
echo "<i>";
echo $fileName;
echo "</i>";
echo "  has been deleted</h2>";

$type = $_POST['file_type'];

/*Intermediate file location*/
if($type == 'par')
{
$myFile = './Intermediate_files/Parameter/'.$fileName.'.txt';
unlink($myFile);

deletefromKA();

}

else if($type == 'grp')

{
$myFile = './Intermediate_files/Group/'.$fileName.'.txt';
unlink($myFile);

deletefromKA();

}

else
{

$myFile = './Intermediate_files/Simple/'.$fileName.'.txt';
unlink($myFile);

deletefromKA();

}



/*Function that deletes an intermediate file and KA file*/

function deletefromKA() {


$fileName = $_POST['file_name'];

$type = $_POST['file_type'];


if($type == 'par'){
$KAFile = './Exercises/'.$fileName.'KA.html';
unlink($KAFile);
}


else if($type == 'grp'){
$KAFile = './Exercises/'.$fileName.'KA.html';
unlink($KAFile);
}

else {
$KAFile = './Exercises/'.$fileName.'KA.html';
unlink($KAFile);
}

}

?>

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



for($i=0; $i<sizeof($filearray); $i++){
	
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "</tr>";
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

for($i=0; $i<sizeof($filearray); $i++){
	
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "</tr>";
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
for($i=0; $i<sizeof($filearray); $i++){
	
	echo "<tr>";
	echo "<td>";
	echo "<p>";
	echo $filearray[$i]; 
	echo "<p>";
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
?>
 <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>

</body>
</html>