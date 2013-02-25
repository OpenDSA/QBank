<html>

<head>
<title>Saved the question - QBank</title>  
  
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
Your question has been saved sucessfully!!!
</h2>
<?

/*This file saves an existing question that is edited by users into an existing intermediate format file and also calls a method to convert the intermdiate format to Khan Academy format*/


$q_type = $_POST['q_type'];

if( $q_type == "par")
{
$fileName = $_POST['file_name'];
/*Intermediate file location*/
$myFile = './Intermediate_files/Parameter/'.$fileName.'.txt';

$stringData = "Inroduction text => ";
$stringData .= $_POST['intro_text'];

$stringData .= "$$\nQuestion text => ";
$stringData .= $_POST['q_text'];

$check_option = $_POST['range1_min'];
if(isset($check_option)){
	$option = "range";
}else{
	$option = "list";
}

if ($option){
	$stringData .= "$$\nParameer type => ";
	$stringData .= $option;
}

if($option == "range")
{
$i = 1;
$var_name = "range";
$var_name .= $i;
$var_name .= "_name";
$var1 = $_POST[$var_name];

/*Populating the intermediate file below*/
while(isset($var1))
{
	$stringData .= "$$\nVariable name => ";
	$stringData .= $var1;
	$stringData .= "$$\nVariable min value => ";
	$val_name = "range";
	$val_name .= $i;
	$val_name .= "_min";
	$val1 = $_POST[$val_name];
	$stringData .= $val1;
	
	$stringData .= "$$\nVariable max value => ";
	$val_name = "range";
	$val_name .= $i;
	$val_name .= "_max";
	$val1 = $_POST[$val_name];
	$stringData .= $val1;
	
	$i++;
	
	$var_name = "range";
	$var_name .= $i;
	$var_name .= "_name";
	$var1 = $_POST[$var_name];

}
	$ans_name = $_POST['range_ans'];
	$stringData .= "$$\nRange answer string => ";
	$stringData .= $ans_name;
	

}
else if($option =="list") 
{

$varsize = array();

$j = 1;
$var_name = "list";
$var_name .= $j;
$var_name .= "_name";
$var1 = $_POST[$var_name];

$values_array = array();
$num_rows = 1;

while(isset($var1))
{
	$stringData .= "$$\nVariable name => ";
	$stringData .= $var1;
	$stringData .= "$$\nVariable values => ";
	$val_name = "list";
	$val_name .= $j;
	$val_name .= "_val";
	$val1 = $_POST[$val_name];
	$stringData .= $val1;
	
	$listarray = explode(",", $val1);
	
	$colarray= array($j-1 => sizeof($listarray));
	$varsize = array_merge($varsize, $colarray);
	$values_array[$j-1]=$listarray;
	$num_rows = $num_rows*sizeof($listarray);
	
	$j++;
	
	$var_name = "list";
	$var_name .= $j;
	$var_name .= "_name";
	$var1 = $_POST[$var_name];

}

	$list_var_num = $j;

	$num_rows = $num_rows/sizeof($listarray);
	$A = array();
	for($i1=0; $i1<$num_rows; $i1++){
			$A[$i1] = array();
	}
	$combination = array();
	$comb = array();
	for($i1=0; $i1< $list_var_num-1; $i1++){
			$combination[$i1]=0;
			$comb[$i1]=0;
	}
	$combination[$list_var_num-2] = 1;
	$comb[0]=1;
	for($i1=$list_var_num-3; $i1>=0 ; $i1--){
		$combination[$i1]=sizeof($values_array[$i1+1])*$combination[$i1+1];
	}		
	for($i1=1; $i1<$list_var_num-1; $i1++){
		$comb[$i1] = $comb[$i1-1]*sizeof($values_array[$i1-1]);
	}
	for($i1=1; $i1<$list_var_num; $i1++){
			for($l=1; $l<=$comb[$i1-1] ; $l++){
				for($j1=1; $j1<=sizeof($values_array[$i1-1]) ;$j1++){
					for($k1=1; $k1<=$combination[$i1-1]; $k1++){
						$temp3 = ($l-1)*sizeof($values_array[$i1-1])*$combination[$i1-1];
						$temp = ($j1-1)*$combination[$i1-1];
						$temp2 = $temp3+$temp+($k1-1);
						$A[$temp2][$i1-1] = $values_array[$i1-1][$j1-1];
					}
				}
			}
	}

/*Code for createing equation that computes index of answer in the answer array*/

	$eq = "x".sizeof($varsize);
	$i = sizeof($varsize)-1;
	$coef1 = 1;
	while($i>0){
		
		$coef1 = $coef1*$varsize[$i];
		
		$eq = $coef1."x".$i.'+'.$eq;
		$i--;	

	}
	
	$stringData .= "$$\n";
	$stringData .= "eq = ".$eq;
		

	$m = 0;
	$var_nam = "ans";
	$var_nam .= $m;
	$var1 = $_POST[$var_nam];
	$tempvar = "";
	if(isset($var1)){
	
		$tempvar .= "[";
		$tempvar .= $var1;
		$m++;
		$var_nam = "ans";
		$var_nam .= $m;
		$var1 = $_POST[$var_nam];
	
	}
	while(isset($var1))
	{
		$tempvar .= ",";
		$tempvar .= $var1;
		$m++;
		$var_nam = "ans";
		$var_nam .= $m;
		$var1 = $_POST[$var_nam];

	}

	$tempvar .= "]";
	$stringData .= "$$\nList answer array => ";
	$stringData .= $tempvar;
	
	$ans_tab = "";
	for($i=0; $i<sizeof($A); $i++){
			$ans_tab .= $A[$i][0];
			for($j=1; $j<sizeof($A[$i]); $j++){
				$ans_tab .= "##".$A[$i][$j];
			}
			if($i<(sizeof($A)-1)){
				$ans_tab .= "&&";
			}
		}
	
	$stringData .= "$$\nList answer table => ";
	$stringData .= $ans_tab;
	$stringData .= "\n";
	

}


file_put_contents($myFile, $stringData);

convertToKA();


}
else if($q_type== "grp"){

$fileName = $_POST['file_name'];


/*Intermediate file location*/
$myFile = './Intermediate_files/Group/'.$fileName.'.txt';


$stringData = "Number of questions => ";
$stringData .= $_POST['q_num'];
$stringData .= "$$\n";

$i=0;
$count =(int)$_POST['q_num'];

$var = $_POST[q_text.$i];


while($i < $count){
$stringData .= "Question text => ";
$stringData .= $_POST[q_text.$i];

$stringData .= "$$\nSolution => ";
$stringData .= $_POST[s_text.$i];

$stringData .= "$$\nChoice 1 => ";
$stringData .= $_POST[c1_text.$i];
$stringData .= "$$\nChoice 2 => ";
$stringData .= $_POST[c2_text.$i];
$stringData .= "$$\nChoice 3 => ";
$stringData .= $_POST[c3_text.$i];
$stringData .= "$$\nChoice 4 => ";
$stringData .= $_POST[c4_text.$i];
$stringData .= "$$\nChoice 5 => ";
$stringData .= $_POST[c5_text.$i];

$stringData .= "$$\nHint 1 => ";
$stringData .= $_POST[h1_text.$i];
$stringData .= "$$\nHint 2 => ";
$stringData .= $_POST[h2_text.$i];
$stringData .= "$$\nHint 3 => ";
$stringData .= $_POST[h3_text.$i];
$stringData .= "$$\nHint 4 => ";
$stringData .= $_POST[h4_text.$i];
$stringData .= "$$\n";

$i++;


}


/*Following code assigns read, write and execute permission to the newly created intermediate file so that it can be edited later when the question is edited on the front-end*/
file_put_contents($myFile, $stringData);

convertToKA1();
}
else
{
$fileName = $_POST['file_name'];


/*Intermediate file location*/
$myFile = './Intermediate_files/Simple/'.$fileName.'.txt';
$fh = fopen($myFile, 'w');


$stringData = "Question text => ";
$stringData .= $_POST[q_text];

$stringData .= "$$\nSolution => ";
$stringData .= $_POST[s_text];

$stringData .= "$$\nChoice 1 => ";
$stringData .= $_POST[c1_text];
$stringData .= "$$\nChoice 2 => ";
$stringData .= $_POST[c2_text];
$stringData .= "$$\nChoice 3 => ";
$stringData .= $_POST[c3_text];
$stringData .= "$$\nChoice 4 => ";
$stringData .= $_POST[c4_text];
$stringData .= "$$\nChoice 5 => ";
$stringData .= $_POST[c5_text];

$stringData .= "$$\nHint 1 => ";
$stringData .= $_POST[h1_text];
$stringData .= "$$\nHint 2 => ";
$stringData .= $_POST[h2_text];
$stringData .= "$$\nHint 3 => ";
$stringData .= $_POST[h3_text];
$stringData .= "$$\nHint 4 => ";
$stringData .= $_POST[h4_text];
$stringData .= "$$\n";




/*Following code assigns read, write and execute permission to the newly created intermediate file so that it can be edited later when the question is edited on the front-end*/
file_put_contents($myFile, $stringData);
}




/*Function that reads an intermediate file for a question and converts it into a Khan Academy compliant format*/

function convertToKA() {
$fileName = $_POST['file_name'];

/*Intermediate file to be converted to KA format is specified below*/
$myFile = './Intermediate_files/Parameter/'.$fileName.'.txt';

$fh = fopen($myFile, 'r');



	$theData = "";
	
	while(($line=fgets($fh))!==false){
		$theData.=$line;
	}
	fclose($fh);
	
	$contents = array();
	$contents = explode("$$",$theData);
	$int_text ="";
	$qt_text ="";
	$tent = array();
	$tent = explode(" => ",$contents[0]);
	if(strcmp($tent[0], "Inroducion text")==0){
	 $int_text = $tent[1];
	}
	$tent = explode(" => ",$contents[1]);
	$qt_text = $tent[1];
	
	$tent = explode(" => ",$contents[2]);
	$var_type = $tent[1];
	
	$var_num=0;
	$file_line = 3;
	
	$range_name = array();
	$range_min = array();
	$range_max = array();
	$range_ans = "";
	
	$list_name = array();
	$list_val = array();
	$list_ans = "";
	$list_ans_tab = "";
	$eq = "";
/*Code to handle mapping of "range of values" of variables and the answer string to Khan Academy format*/
	if($var_type == "range"){
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		$file_line++;
		while(strcmp ($tent[0], "Variable name")==0){
			
			$range_name[$var_num] = $tent[1];
			$tent = explode(" => ",$contents[$file_line]);
			$file_line++;
			$range_min[$var_num] = $tent[1];
			$tent = explode(" => ",$contents[$file_line]);
			$file_line++;
			$range_max[$var_num] = $tent[1];
			$var_num++;
			$tent = explode(" => ",$contents[$file_line]);
			$tent[0]=trim($tent[0]);
			$file_line++;
		}
		$tent[0]=trim($tent[0]);
		if(strcmp ($tent[0], "Range answer string")==0){
			$range_ans = $tent[1];
		}
/* Code to handle mapping of "valid list of variable values" to Khan Academy format. */
	}elseif($var_type == "list"){
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		$file_line++;

		while(strcmp ($tent[0], "Variable name")==0){
			
			$list_name[$var_num] = $tent[1];
			$tent = explode(" => ",$contents[$file_line]);
			$file_line++;
			$list_val[$var_num] = $tent[1];
			$var_num++;
			$tent = explode(" => ",$contents[$file_line]);
			$tent[0]=trim($tent[0]);
			$file_line++;
		}
		$eq = explode("=", $tent[0]);
		$ind = $eq[1];
		
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		if(strcmp ($tent[0], "List answer array")==0){
			$list_ans = $tent[1];
			$ans_size = strlen($list_ans);
			$ans_size = $ans_size -2;
			$list_ans = substr($list_ans,1,$ans_size);
		}
		$file_line++;
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		if(strcmp ($tent[0], "List answer table")==0){
			$list_ans_tab = $tent[1];
		}
	
	}

/*Following code creates a new file in the Khan Academy format and writes the parameter values and answer function computed above into the file. The code creates an HTML file using PHP*/	

$KAFile = './Exercises/'.$fileName.'KA.html';
$fh = fopen($KAFile, 'w');
$stringData = "<!DOCTYPE html>\n";
$stringData.= "<html data-require=\"math\">\n";
$stringData.= "<head>\n";
$stringData.= "<title>\n";
$stringData.= $fileName;
$stringData.= "\n</title>\n";
$stringData.= "<script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\">
</script><script src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js\"></script>
   <script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>
   <script src=\"../../ODSAkhan-exercises/khan-exercise.js\"></script>
\n";
$stringData.= "</head><body><div class=\"exercise\">\n";
$stringData.=  "<div class=\"vars\">\n";
$list_new_ans = array();
$list_ans_ar = array();
if($var_type == "range"){
	for($i=0; $i<sizeof($range_name); $i++){
		$stringData.= "<var id=\"";
		$stringData.=$range_name[$i];
		$stringData.= "\">randRange(";
		$stringData.=$range_min[$i];
		$stringData.= ",";
		$stringData.=$range_max[$i];
		$stringData.= ")</var>";
	}
}
elseif($var_type == "list"){
	$num_ans=0;
	$list_ans = str_replace("[","",$list_ans);
	$list_ans = str_replace("]","",$list_ans);
	$list_ans_ar = explode(",",$list_ans);
	for($i=0; $i<sizeof($list_ans_ar); $i++){
		$flag=0;
		for($j=0; $j<$i; $j++){
			if($list_ans_ar[$i]==$list_ans_ar[$j]){
				$flag=1;
			}
		}
		if($flag==0){
			$list_new_ans[$num_ans]=$list_ans_ar[$i];
			$num_ans++;
		}
	}
	for($i=0; $i<sizeof($list_new_ans); $i++){
		
		$index = "A".($i+1);
		$stringData.= "<var id=\"";
		$stringData.=$index;
		$stringData.="\">\"".$list_new_ans[$i];
		$stringData.= "\"</var>";		
	}
	for($i=0; $i<sizeof($list_name); $i++){
		
		$index = "x".($i+1);
		$stringData.= "<var id=\"";
		$stringData.=$list_name[$i];
		$stringData.="\">[";
		$temp = array();
		$temp = explode(",",$list_val[$i]);
		$stringData.="\"".$temp[0]."\"";
		for($j=1; $j<sizeof($temp); $j++){
			$stringData.=",\"".$temp[$j]."\"";
		}
		$stringData.="]";
		$stringData.= "</var>";
		$stringData.= "<var id=\"";
		$stringData.=$index;
		$stringData.= "\">randRange(0,";
		$valArr = array();
		$valArr = explode(",", $list_val[$i]);
		$stringData.= sizeof($valArr)-1;
		$stringData.= ")</var>";
		
	}
	
		$stringData.= "<var id=\"";
		
		$stringData.= "INDEX";
		$stringData.="\">".$ind;
		$stringData.= "</var>";
		
		$stringData.= "<var id=\"";
		$stringData.= "ANSWER";
		$stringData.="\">[";
		$stringData.="\"".$list_ans_ar[0]."\"";
		for($i=1; $i<sizeof($list_ans_ar); $i++){
				$stringData.=",\"".$list_ans_ar[$i]."\"";
		}
		$stringData.= "]</var>";
		
}
$stringData.= "\n</div>\n";
$stringData.= "<div class=\"problems\"> <div id=\"problem-type-or-description\">\n";
$stringData.= "<p class=\"problem\">\n";
$stringData.= $int_text;


$stringData.= "<p class=\"question\">\n";
$temp= " ";
$temp2= " ";
$new_qtxt = $qt_text;
if($var_type == "list"){
for($i=0; $i<sizeof($list_name); $i++) {
		
	$temp = "<var>";	
	$temp.= $list_name[$i]."[x".($i+1)."]";
	$temp.= "</var>";
	
	$temp2 = "@".$list_name[$i];
	
	$new_qtxt = str_replace($temp2,$temp,$new_qtxt);
	
	
	}
	
}
elseif($var_type == "range"){
for($i=0; $i<sizeof($range_name); $i++) {
	
	
	$temp = "<var>";	
	$temp.= $range_name[$i];
	$temp.= "</var>";
	
	$temp2 = "@".$range_name[$i];
	$new_qtxt = str_replace($temp2, $temp, $new_qtxt);

	}
}
$stringData.= $new_qtxt;
$stringData.= "</p>\n";

if($var_type == "list"){
$stringData.= "<div class=\"solution\">";
$stringData.= "<var>ANSWER[INDEX]</var> </div>";
$stringData.= "<ul class=\"choices\" data-category=\"true\">";
for($i=0;$i<sizeof($list_new_ans);$i++){
	$stringData.= " <li><var>";
	$stringData.= A.($i+1);
	$stringData.= "</var> </li>";
}
$stringData.= "</ul>";
}

elseif($var_type == "range"){
$stringData.= "<div class=\"solution\">";
$stringData.= "<var>";
$stringData.= $range_ans;
$stringData.= "</var> </div>";
}

$stringData.= "</div></div>";
$stringData.= "</div> </body></html>\n";

file_put_contents($myFile, $stringData);
}


function convertToKA1() {
$fileName = $_POST['file_name'];
/*Intermediate file to be converted to KA format is specified below*/
$myFile = './Intermediate_files/Group/'.$fileName.'.txt';
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

$KAFile = './Exercises/'.$fileName.'KA.html';

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
 <script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>
   <script src=\"../../ODSAkhan-exercises/khan-exercise.js\"></script>
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

file_put_contents($myFile, $stringData);
}


function convertToKA2() {
$fileName = $_POST['file_name'];
/*Intermediate file to be converted to KA format is specified below*/
$myFile = './Intermediate_files/Simple/'.$fileName.'.txt';
$fh = fopen($myFile, 'r');

	$theData = "";
	
	while(($line=fgets($fh))!==false){
		$theData.=$line;
	}
	fclose($fh);
	
	$contents = array();
	$contents = explode("$$",$theData);
	
	$tent = array();
	
$j=0;


$tent = explode(" => ",$contents[$j]);
$q_text= $tent[1];
$j++;
$tent = explode(" => ",$contents[$j++]);
$s_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c1_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c2_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c3_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c4_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$c5_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h1_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h2_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h3_text = $tent[1];

$tent = explode(" => ",$contents[$j++]);
$h4_text = $tent[1];




/*Following code creates a new file in the Khan Academy format and writes the parameter values and answer function computed above into the file. The code creates an HTML file using PHP*/	

$KAFile = './Exercises/'.$fileName.'KA.html';

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
 <script>urlBaseOverride = \"../../ODSAkhan-exercises/\";</script>
   <script src=\"../../ODSAkhan-exercises/khan-exercise.js\"></script>
<script src=\"http://algoviz.org/OpenDSA/JSAV/build/JSAV-min.js\"></script>    
<link rel=\"stylesheet\" href=\"http://algoviz.org/OpenDSA/JSAV/css/JSAV.css\" type=\"text/css\" />
\n";
$stringData.= "</head><body><div class=\"exercise\">\n";
$stringData.=  "<div class=\"vars\">\n";
$stringData.=  "</div>\n";
$stringData.= "<div class=\"problems\"> \n";

$stringData.= "<div id=\"problem-type-or-description\">\n";
$stringData.= "<p class=\"question\">\n";
$stringData.= $q_text;
$stringData.= "\n</p>\n";
$stringData.= "<div class=\"solution\">\n";

$pos = strpos($s_text,'<code>');

if($pos === false) {
 $stringData.= "<var>\"";
$stringData.= $s_text;
$stringData.= "\"</var></div>\n";
}
else {
 $stringData.= "<var>";
$stringData.= $s_text;
$stringData.= "</var></div>\n";
}

$show =2;
$val5= $c5_text;
$val4= $c4_text;
$val3= $c3_text;
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
$pos1 = strpos($c1_text,'<code>');

if($pos1 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c1_text;
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c1_text;
$stringData.= "</var></li>\n";

}


$pos2 = strpos($c2_text,'<code>');

if($pos2 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c2_text;
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c2_text;
$stringData.= "</var></li>\n";

}

if ($val3 != ""){

$pos3 = strpos($c3_text,'<code>');

if($pos3 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c3_text;
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c3_text;
$stringData.= "</var></li>\n";

}

}

if ($val4 != ""){
$pos4 = strpos($c4_text,'<code>');

if($pos4 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c4_text;
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c4_text;
$stringData.= "</var></li>\n";

}
}

if ($val5 != ""){
$pos5 = strpos($c5_text,'<code>');

if($pos5 === false) {
$stringData.= "<li><var>\"";
$stringData.= $c5_text;
$stringData.= "\"</var></li>\n";

}
else {
$stringData.= "<li><var>";
$stringData.= $c5_text;
$stringData.= "</var></li>\n";

}
}
$stringData.="</ul>";


$stringData.= "<div class =\"hints\">\n";
$v1= $h1_text;
if ($v1 != ""){
$stringData.= "<p>\"";
$stringData.=$h1_text;
$stringData.="\"</p>\n";
}
$v2= $h2_text;
if ($v2 != ""){
$stringData.="<p>\"";
$stringData.=$h2_text;
$stringData.="\"</p>\n";
}
$v3= $h3_text;
if ($v3 != ""){
$stringData.="<p>\"";
$stringData.=$h3_text;
$stringData.="\"</p>\n";
}
$v4= $h4_text;
if ($v4 != ""){
$stringData.="<p>\"";
$stringData.=$h4_text;
$stringData.="\"</p>\n";
}


$stringData.= "</div></div>";


$stringData.= "</div></div> </body></html>\n";

file_put_contents($myFile, $stringData);
}

?>

</html>