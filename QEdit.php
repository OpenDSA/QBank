<?

?>

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
   
   
      
<form accept-charset="UTF-8" action="formsave2.php" autocomplete="off" class="question_edit" id="question_edit" method="post"><div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="&#x2713;" /> 
    
 <input id="question_type" name="question[type]" type="hidden" value="SimpleQuestion" />



 <?
	$qName = $_POST['QFileName'];
	$qName = trim($qName);

	$type = $_POST['radio1'];

if($type == 'par'){

	echo "<input id=\"q_type\" name=\"file_name\" type=\"hidden\" value=".$qName." />";	
	
echo "<input id=\"q_type\" name=\"q_type\" type=\"hidden\" value=\"par\" />";	
	$myFile = './Intermediate_files/Parameter/'.$qName.'.txt';
	if(!file_exists($myFile)) {
echo "<h2>";
		die("Please go back and enter a valid question file name!!!!");
echo "</h2>";
	}
	else {
	 echo "<h2>You are editing a question</h2>";

	$fh = fopen($myFile, 'r');
	
	$theData = "";
	//Reads the intermediate file and stores question text, introduction, variable names and values, answer etc in variables that could be later on displayed on edit screen. 
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
	if(strcmp($tent[0], "Inroduction text")==0){
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
	
	//Stores range variabel names and min, max values into range_name, range_min, an range_max arrays and answer function into range_ans variable.
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
		//Stores list type variable names and values into arrays list_name and list_val.
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
		//Stores list answer values into a variable list_ans
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		if(strcmp ($tent[0], "List answer array")==0){
			$list_ans = $tent[1];
			$ans_size = strlen($list_ans);
			$ans_size = $ans_size -2;
			$list_ans = substr($list_ans,1,$ans_size);
		}
		$file_line++;
		//Stores list answer table into a variable list_ans_tab.
		$tent = explode(" => ",$contents[$file_line]);
		$tent[0]=trim($tent[0]);
		if(strcmp ($tent[0], "List answer table")==0){
			$list_ans_tab = $tent[1];
		}
	
	}
	
	

 
 //Displays question filename on screen.
 echo "<table class=\"target question_form\" width=\"100%\" border=\"1\" height=\"400px\">
	 <tr id=\"setup_row\" >
       <td valign=\"top\"><i>Save Question As (FileName): </i></td>
       <td>
          <div class=\"field\">
             
                <input type= \"text\" name='file_name'  maxlength=\"10\" value=$qName>
				
     </div>
       </td>
          </tr>";
	
}	
	
 
   echo" <tr id=\"setup_row\" >
       <td valign=\"top\"><i>Intro text:</i></td>
       <td>
          <div class=\"field\">
             
                <textarea class=\"mark_it_up\" cols=\"80\" id=\"question_question_setup_attributes_content\" name=\"intro_text\" rows=\"4\"> $int_text</textarea>
				
     </div>
       </td>
         </tr>";
        
    echo"<tr>
       <td valign=\"top\"><i>Question text:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" id=\"question_content\" name=\"q_text\" rows=\"4\">$qt_text</textarea>
          </div>
       </td>
         </tr>";
	
	echo "<tr>
	<td valign=\"top\"><i>Parameters:</i></td>
	<td>";	
	
	//Shows textboxes for range name, min an max values on screen along with the existing values.
	if($var_type == "range"){
		$range_var_num = 1;
		echo " <table class=\"range_var\" border=\"2\"> ";
		for($i=0; $i<sizeof($range_name); $i++){
			$var_name = "range".$range_var_num."_name";
			$var_min = "range".$range_var_num."_min";
			$var_max = "range".$range_var_num."_max";
			echo "<tr>
			<td valign=\"top\">Variable name</td>
			<td valign=\"top\">Min Value</td>
			<td valign=\"top\">Max Value</td>
			</tr>
		
			<tr>
			<td valign=\"top\"> <input type=\"text\" value=\"$range_name[$i]\" name=$var_name></td>
			<td valign=\"top\"> <input type=\"text\" value=\"$range_min[$i]\" name=$var_min> </td>
			<td valign=\"top\"><input type=\"text\" value=\"$range_max[$i]\" name=$var_max></td>
			</tr>";
			$range_var_num++;
		}
		echo "</table>";	
		
	//Shows textboxes for list name and values on screen along with the existing values.
	}elseif($var_type == "list"){
		$list_var_num = 1;
		echo " <table class=\"list_var\" border=\"2\"> ";
		for($i=0; $i<sizeof($list_name); $i++){
			$var_name = "list".$list_var_num."_name";
			$var_val = "list".$list_var_num."_val";
			echo "<tr>
			<td valign=\"top\">Variable name</td>
			<td valign=\"top\">Values separated by commas</td>
			</tr>
		
			<tr>
			<td valign=\"top\"> <input type=\"text\" value=$list_name[$i] name=$var_name></td>
			<td valign=\"top\"> <input type=\"text\" value=\"".$list_val[$i]."\" name=$var_val> </td>
			</tr>";
			$list_var_num++;
		}
		echo "</table>";

	}
	echo"</td>
	</tr>";
	
	
	echo"<tr>";
	
	//Generates a textbox for range answer function along with its existing values.
	if($var_type == "range"){
		echo "<td valign=\"top\"><i>Answer equation</i></td>
		<td valign=\"top\"> <input type=\"text\" value=\"$range_ans\" name='range_ans'></td>";
	//Generates a table for list answers along with its existing values. in corresponding textboxes.
	}elseif($var_type == "list"){
		echo "<td valign=\"top\"><i>Answer</i></td>";
		echo "<td>";
		$ans_tab_rows = array();
		$ans_rows = array();
		$list_ans_tab = trim($list_ans_tab);
		$ans_tab_rows = explode("&&",$list_ans_tab);	
		$list_ans = trim($list_ans);
		$ans_rows = explode(",",$list_ans);		
		for($i=0; $i<sizeof($ans_tab_rows); $i++){
			if($i == 0){
				echo "<table class=\"ans_table\" border=\"2\">";
				echo "<tr>Please provide parameter values in order.</tr>";
				echo "<tr>";
				for($k=0; $k<sizeof($list_name); $k++){
					echo "<td valign=\"top\">$list_name[$k]</td>";
				}
				echo "<td valign=\"top\">Answer</td>";
				echo "</tr>";
			}
			echo "<tr>";
			$ans_tab_col = array();
			$ans_tab_rows[$i] = trim($ans_tab_rows[$i]);
			$ans_tab_col = explode("##",$ans_tab_rows[$i]);
			for($j=0; $j<sizeof($ans_tab_col); $j++){
				echo "<td> <input type=\"text\" value=\"".$ans_tab_col[$j]."\"></td>";
			}
			$ans_name = "ans".$i;
			echo "<td> <input type=\"text\" value=\"".$ans_rows[$i]."\" name=$ans_name></td>";
			echo "</tr>";
			if($i == ((sizeof($ans_tab_rows))-1)){
				echo "</table>";
			}
		}
		echo "</td>";
	}
	
	echo"</tr>";
	
    echo" </table>";
}
else if ($type == 'grp'){
	echo "<input id=\"q_type\" name=\"q_type\" type=\"hidden\" value=\"grp\" />";	

	echo "<input id=\"q_type\" name=\"file_name\" type=\"hidden\" value=".$qName." />"; 	
	$myFile = './Intermediate_files/Group/'.$qName.'.txt';
	if(!file_exists($myFile)) {
	echo "<h2>";
		die("Please go back and enter a valid question file name!!!!");
	echo "</h2>";
	}
	else {
	 echo "<h2>You are editing a question</h2>";

	$fh = fopen($myFile, 'r');
	
	$theData = "";
	//Reads the intermediate file and stores question text, introduction, variable names and values, answer etc in variables that could be later on displayed on edit screen. 
	while(($line=fgets($fh))!==false){
		$theData.=$line;
	}
	fclose($fh);
	
	$contents = array();
	$contents = explode("$$",$theData);
	
	$q_text =array();
	$tent = array();
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


	$tent = explode(" => ",$contents[0]);
	$q_num = $tent[1];
	$i=0;
	$j=1;

	while($i < $q_num){
	$tent = explode(" => ",$contents[$j]);
	$j++;
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
}
	
	

 //Displays question filename on screen.
 echo "<table class=\"target question_form\" width=\"100%\" border=\"1\" height=\"400px\">
	 <tr id=\"setup_row\" >
       <td valign=\"top\"><i>Number of questions: </i></td>
       <td>
          <div class=\"field\">
             
                <input type= \"text\" name='q_num'  maxlength=\"10\" value=$q_num>
				
     </div>
       </td>
          </tr>";
	
$k=0;
$num=0;
while($k < $q_num)
{

 echo"<tr>
       <td valign=\"top\">Question ";
	echo $k+1;
	echo"</td>
      </tr>";



    echo"<tr>
       <td valign=\"top\"><i>Question text:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" id=\"question_content\" name=\"".q_text.$k."\" rows=\"4\">$q_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Solution text:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".s_text.$k."\" rows=\"4\">$s_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 1:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".c1_text.$k."\" rows=\"4\">$c1_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 2:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"".c2_text.$k."\" rows=\"4\">$c2_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 3:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".c3_text.$k."\" rows=\"4\">$c3_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 4:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"".c4_text.$k."\" rows=\"4\">$c4_text[$k]</textarea>
          </div>
       </td>
         </tr>";

    
    echo"<tr>
       <td valign=\"top\"><i>Choice 5:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".c5_text.$k."\" rows=\"4\">$c5_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 1:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".h1_text.$k."\" rows=\"4\">$h1_text[$k]</textarea>
          </div>
       </td>
         </tr>";

    
    echo"<tr>
       <td valign=\"top\"><i>Hint 2:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"".h2_text.$k."\" rows=\"4\">$h2_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 3:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"".h3_text.$k."\" rows=\"4\">$h3_text[$k]</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 4:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"".h4_text.$k."\" rows=\"4\">$h4_text[$k]</textarea>
          </div>
       </td>
         </tr>";    
		
    
$k++;
}
echo" </table>";
}
else
{
	echo "<input id=\"q_type\" name=\"q_type\" type=\"hidden\" value=\"simple\" />";	
	echo "<input id=\"q_type\" name=\"file_name\" type=\"hidden\" value=".$qName." />";	
	$myFile = './Intermediate_files/Simple/'.$qName.'.txt';
	if(!file_exists($myFile)) {
	echo "<h2>";
		die("Please go back and enter a valid question file name!!!!");
	echo "</h2>";
	}
	else {
	 echo "<h2>You are editing a question</h2>";

	$fh = fopen($myFile, 'r');
	
	$theData = "";
	//Reads the intermediate file and stores question text, introduction, variable names and values, answer etc in variables that could be later on displayed on edit screen. 
	while(($line=fgets($fh))!==false){
		$theData.=$line;
	}
	fclose($fh);
	
	$contents = array();
	$contents = explode("$$",$theData);


	
	$j=0;
	$tent = explode(" => ",$contents[$j]);
	$j++;
	$q_text = $tent[1];
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
	
		
}
	
	

 //Displays question filename on screen.
 echo "<table class=\"target question_form\" width=\"100%\" border=\"1\" height=\"400px\">";
	 
 echo"<tr>
       <td valign=\"top\">Question: ";
	
	echo"</td>
      </tr>";

    echo"<tr>
       <td valign=\"top\"><i>Question text:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" id=\"question_content\" name=\"q_text\" rows=\"4\">$q_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Solution text:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"s_text\" rows=\"4\">$s_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 1:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"c1_text\" rows=\"4\">$c1_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 2:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"c2_text\" rows=\"4\">$c2_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 3:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"c3_text\" rows=\"4\">$c3_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Choice 4:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"c4_text\" rows=\"4\">$c4_text</textarea>
          </div>
       </td>
         </tr>";

    
    echo"<tr>
       <td valign=\"top\"><i>Choice 5:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"c5_text\" rows=\"4\">$c5_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 1:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"h1_text\" rows=\"4\">$h1_text</textarea>
          </div>
       </td>
         </tr>";

    
    echo"<tr>
       <td valign=\"top\"><i>Hint 2:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"h2_text\" rows=\"4\">$h2_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 3:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\"  name=\"h3_text\" rows=\"4\">$h3_text</textarea>
          </div>
       </td>
         </tr>";
    
    echo"<tr>
       <td valign=\"top\"><i>Hint 4:</i></td>
       <td>
          <div class=\"field\">
             <textarea class=\"mark_it_up\" cols=\"80\" name=\"h4_text\" rows=\"4\">$h4_text</textarea>
          </div>
       </td>
         </tr>";    

echo" </table>";

}
 ?>
<p>
 
  <span id="newText">
  
  <div class="actions">
    <center>
      
      &nbsp;&nbsp;&nbsp;&nbsp;

	  
      <input class="ui-state-default ui-corner-all submitButton" data-disable-with="Saving..." id="save_button" name="commit" type="submit" value="Save"/>

      &nbsp;&nbsp;&nbsp;&nbsp;
	  
       <input  id="back_button" name="back" type="button" value="Back" onclick="javascript:history.back(1)"/>

      
    </center>
  </div>

  </span>
 <p></p>

    <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>

  
</form>