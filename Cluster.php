<!DOCTYPE html>

<html>

<head>
<title>Saved the question - QBank</title>  
  
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
Your question has been grouped sucessfully!!!
</h2>

<?

/*This file saves a new question into an intermediate format and also calls a method to convert intermdiate format to Khan Academy format*/

$fileName = $_POST['f_name'];
$n = count($fileName);
$filesize = $n;


$stringData ="";
$grpName = $_POST['FileName'];

if(empty($fileName))
{
echo("Please select a question to be grouped");
}
else
{
$n = count($fileName);

for ($i=0; $i <$n; $i++)
{
$myFile1 = './Intermediate_files/Simple/'.$fileName[$i].'.txt';
	if(!file_exists($myFile1)) {
	echo "<h2>";
		die("Please go back and enter a valid question file name!!!!");
	echo "</h2>";
	}
	else {
	$fh = fopen($myFile1, 'r');
	
	$theData = "";
	//Reads the intermediate file and stores question text, introduction, variable names and values, answer etc in variables that could be later on displayed on edit screen. 
		while(($line=fgets($fh))!==false){
		$theData.=$line;
		}
	fclose($fh);
	
	$stringData .=$theData;

	$myFile1 = './Intermediate_files/'.$grpName.'Temp.txt';
	$fh = fopen($myFile1, 'a');
	fwrite($fh, $theData);
	fclose($fh);
	$pathQ = './Intermediate_files/*.*';
	$execCmd = 'chmod 777 '.$pathQ;
	$output = shell_exec($execCmd);
	}
}



$myFile = './Intermediate_files/Group/'.$grpName.'.txt';
if(!file_exists($myFile)) {

	$stringData .="";

	}
	else {
	$fh = fopen($myFile, 'r');
	
	$theDataOld = "";
	//Reads the intermediate file and stores question text, introduction, variable names and values, answer etc in variables that could be later on displayed on edit screen. 
		while(($line=fgets($fh))!==false){
		$theDataOld.=$line;
		}
	fclose($fh);
	
	
	$ontents = array();
	$contents = explode("$$",$theDataOld);
	$i=1;


while($i < sizeof($contents)-1)
{
	
$stringData .= $contents[$i];
$stringData .= "$$";

$i++;
}

}

$num = substr_count($stringData, 'Question');





/*Intermediate file location*/
$myFile3 = './Intermediate_files/Group/'.$grpName.'.txt';
$fh = fopen($myFile3, 'w');

$stringData1 = "Number of questions => ";
$stringData1 .= $num;
$stringData1 .= "$$\n";


fwrite($fh, $stringData1);
fclose($fh);
$pathQ = './Intermediate_files/*.*';
$execCmd = 'chmod 777 '.$pathQ;
$output = shell_exec($execCmd);



$myFile3 = './Intermediate_files/Group/'.$grpName.'.txt';
$fh = fopen($myFile3, 'a');
fwrite($fh, $stringData);
fclose($fh);
$pathQ = './Intermediate_files/*.*';
$execCmd = 'chmod 777 '.$pathQ;
$output = shell_exec($execCmd);




convertToKA1();




}




/*Following code assigns read, write and execute permission to the newly created intermediate file so that it can be edited later when the question is edited on the front-end*/








/*Function that reads an intermediate file for a question and converts it into a Khan Academy compliant format*/

function convertToKA1() {


$grpName = $_POST['FileName'];

/*Intermediate file to be converted to KA format is specified below*/
$myFile = './Intermediate_files/Group/'.$grpName.'.txt';
$fh = fopen($myFile, 'r');

	$theData = "";
	
	while(($line=fgets($fh))!==false){
		$theData.=$line;
	}
	fclose($fh);
	
	$contents = array();
	$contents = explode("$$",$theData);
	
	$tent = array();
	$tent = explode(" => ",$contents[0]);
	$q_no = $tent[1]; 
	$i=0;
	$j=1;
	$q_num= (int)$q_no;
	
$q_text = array();
$s_text = array();
$c1_text = array();
$c2_text = array();
$c3_text = array();
$c4_text = array();
$c5_text = array();

$h1_text = array();
$h2_text = array();
$h3_text = array();
$h4_text = array();



while($i < $q_num)
{

$tent = explode(" => ",$contents[$j++]);
$q_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$s_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c1_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c2_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c3_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c4_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c5_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h1_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h2_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h3_text[$i] = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h4_text[$i] = $tent[1];



$i++;
}


/*Following code creates a new file in the Khan Academy format and writes the parameter values and answer function computed above into the file. The code creates an HTML file using PHP*/	

$KAFile = './Exercises/'.$grpName.'KA.html';
$fh = fopen($KAFile, 'w');
$stringData = "<!DOCTYPE html>\n";
$stringData.= "<html data-require=\"math\">\n";
$stringData.= "<head>\n";
$stringData.= "<title>\n";
$stringData.= $fileName;
$stringData.= "\n</title>\n";
$stringData.= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\">
</script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script>
   <script type=\"text/javascript\"
   src=\"http://cdn.mathjax.org/mathjax/1.1-latest/MathJax.js?config=http://algoviz.org/OpenDSA/dev/OpenDSA/ODSAkhan-exercises/KAthJax-77111459c7d82564a705f9c5480e2c88.js\">
</script>
 <script>urlBaseOverride = \"../../../OpenDSA/OpenDSA/ODSAkhan-exercises/\";</script>
   <script src=\"../../../OpenDSA/OpenDSA/ODSAkhan-exercises/khan-exercise.js\"></script>
<script src=\"http://algoviz.org/OpenDSA/JSAV/build/JSAV-min.js\"></script>    
<link rel=\"stylesheet\" href=\"http://algoviz.org/OpenDSA/JSAV/css/JSAV.css\" type=\"text/css\" />
\n";
$stringData.= "</head><body><div class=\"exercise\">\n";
$stringData.=  "<div class=\"vars\">\n";
$stringData.=  "</div>\n";
$stringData.= "<div class=\"problems\"> \n";
$k = 0;
while($k < $q_num){
$stringData.= "<div id=\"problem-type-or-description\">\n";
$stringData.= "<p class=\"question\">\n";
$stringData.= $q_text[$k];
$stringData.= "\n</p>\n";
$stringData.= "<div class=\"solution\">\n";


$pos = strpos($s_text[$k],'<code>');

if($pos === false) {
$stringData.= "<var>\"";
$stringData.= $s_text[$k];
$stringData.= "\"</var></div>\n";

}
else {
$stringData.= "<var>";
$stringData.= $s_text[$k];
$stringData.= "</var></div>\n";

}

$show =2;
$val5= $c5_text[$k];
$val4= $c4_text[$k];
$val3= $c3_text[$k];
if( $val5 == "")
{
$show =4;
}
if($val4 == "")
{
$show =3;
}
if($val3 == "")
{
$show =2;
}

$stringData.= "<ul class =\"choices\" data-show=\"";
$stringData.= $show;
$stringData.="\">\n";


$pos1 = strpos($c1_text[$k],'<code>');

if($pos1 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c1_text[$k];
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c1_text[$k];
$stringData.= "</var></li>\n";

}



$pos2 = strpos($c2_text[$k],'<code>');

if($pos2 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c2_text[$k];
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c2_text[$k];
$stringData.= "</var></li>\n";

}


if ($val3 != ""){
$pos3 = strpos($c3_text[$k],'<code>');

if($pos3 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c3_text[$k];
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c3_text[$k];
$stringData.= "</var></li>\n";

}

}

if ($val4 != ""){

$pos4 = strpos($c4_text[$k],'<code>');

if($pos4 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c4_text[$k];
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c4_text[$k];
$stringData.= "</var></li>\n";

}
}

if ($val5 != ""){

$pos5 = strpos($c5_text[$k],'<code>');

if($pos5 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c5_text[$k];
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c5_text[$k];
$stringData.= "</var></li>\n";

}
}
$stringData.="</ul>";

$stringData.= "<div class =\"hints\">\n";
$v1= $h1_text[$k];
if ($v1 != ""){
$stringData.= "<p>\"";
$stringData.=$h1_text[$k];
$stringData.="\"</p>\n";
}
$v2= $h2_text[$k];
if ($v2 != ""){
$stringData.="<p>\"";
$stringData.=$h2_text[$k];
$stringData.="\"</p>\n";
}
$v3= $h3_text[$k];
if ($v3 != ""){
$stringData.="<p>\"";
$stringData.=$h3_text[$k];
$stringData.="\"</p>\n";
}
$v4= $h4_text[$k];
if ($v4 != ""){
$stringData.="<p>\"";
$stringData.=$h4_text[$k];
$stringData.="\"</p>\n";
}

$stringData.= "</div></div>";

$k++;


}



$stringData.= "</div></div> </body></html>\n";

fwrite($fh, $stringData);
fclose($fh);
$pathQ = './Exercises/*.*';
$execCmd = 'chmod 777 '.$pathQ;
$output = shell_exec($execCmd);
}


?>

</html>