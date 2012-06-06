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
  <script>urlBaseOverride = \"../ODSAkhan-exercises/\";</script>
   <script src=\"../ODSAkhan-exercises/khan-exercise.js\"></script>
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
$stringData.= "<var>\"";
$stringData.= $s_text[$k];
$stringData.= "\"</var></div>\n";
$stringData.= "<ul class =\"choices\" data-show=\"4\">\n";
$stringData.= "<li><var>\"";
$stringData.= $c1_text[$k];
$stringData.= "\"</var></li>\n";
$stringData.="<li><var>\"";
$stringData.=$c2_text[$k];
$stringData.="\"</var></li>\n";
$stringData.="<li><var>\"";
$stringData.=$c3_text[$k];
$stringData.="\"</var></li>\n";
$stringData.="<li><var>\"";
$stringData.=$c4_text[$k];
$stringData.="\"</var></li>\n";
$stringData.="<li><var>\"";
$stringData.=$c5_text[$k];
$stringData.="\"</var></li>\n";
$stringData.="</ul>";

$stringData.= "<div class =\"hints\">\n";
$stringData.= "<p>\"";
$stringData.=$h1_text[$k];
$stringData.="\"</p>\n";
$stringData.="<p>\"";
$stringData.=$h2_text[$k];
$stringData.="\"</p>\n";
$stringData.="<p>\"";
$stringData.=$h3_text[$k];
$stringData.="\"</p>\n";
$stringData.="<p>\"";
$stringData.=$h4_text[$k];
$stringData.="\"</p>\n";

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