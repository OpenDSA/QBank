<?php
$myFile = "/home/algoviz-beta/QBank/QBank/NewFile2.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = $_POST['question_text'];
fwrite($fh, $stringData);
fclose($fh);
?>


<?php
	$a="hello";

?>


<html>

Finally my php code is working!!!!!!!!!!


<?php
$myFile = "/home/algoviz-beta/QBank/QBank/abc.txt";
$fh = fopen($myFile, 'w');
$stringData = "HEy";
fwrite($fh, $stringData);
fclose($fh);

?>

</html>