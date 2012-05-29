<?
$fileName = $_POST['file_name'];
$myFile = '/home/nidhip/data/'.$fileName.'.txt';
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
	

$KAFile = '/home/nidhip/KA/'.$fileName.'KA.txt';
$fh = fopen($KAFile, 'w');
$stringData = "<!DOCTYPE html> ";
$stringData.= "<html data-require=\"math\">";
$stringData.= "<head>";
$stringData.= "<title>";
$stringData.= 	$fileName;
$stringData.= "</title>";
$stringData.= "<script src=\"../khan-exercise.js\"></script>"
$stringData.= "</head><body><div class=\"exercise\"> ";
$stringData.=  "<div class=\"vars\"> ";
if($var_type == "range"){
	$stringData.=$range_name;
}
elseif($var_type == "list"){
	$stringData.=$list_name;
}
$stringData.=  "</div> ";
$stringData.= "<div class=\"problems\"> <div id=\"problem-type-or-description\">";
$stringData.= "<p class=\"problem\">";
$stringData.= $int_text;
$stringData.= "</p>";
$stringData.= "</div> </div> </div> </body></html>";
$stringData.= "End of html file";

fwrite($fh, $stringData);
fclose($fh);
?>