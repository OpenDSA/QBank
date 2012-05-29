

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
   
   <form accept-charset="UTF-8" action="Qdel.php" autocomplete="off" class="question_edit" id="question_edit" method="post"><div style="margin:0;padding:0;display:inline"><input name="utf8" type="hidden" value="&#x2713;" /> 


      
<input name="utf8" type="hidden" value="&#x2713;" /> 

   
 <input id="question_type" name="question[type]" type="hidden" value="SimpleQuestion" />



 <h2>You are viewing a simple question
 </h2>


 <?
	$qName = $_POST['QFileName'];
	$qName = trim($qName);
	
	  echo "<input type= \"hidden\" name='file_name'  value=$qName \>";
	
	$myFile = '/home/algoviz-beta/QBank/QBank/OpenDSA/Intermediate_files/'.$qName.'.txt';
	if(!file_exists($myFile)) {
		die("Please go back and enter a valid question file name");
	}
	else {
	
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
 echo "<table class=\"target question_form\" width=\"100%\" border =\"1\" height = \"400px\">
	 <tr id=\"setup_row\" >
       <td valign=\"top\"><i>Save Question As (FileName):  </i></td>
       <td>
          <div class=\"field\">
             
                $qName
				
     </div>
       </td>
       
    </tr>";
	
}	
?>	
 
    <tr id="setup_row" >
       <td valign="top"><i>Intro text:</i></td>
       <td>
          <div class="field">
             
                <? echo $int_text	?>			
     </div>
       </td>
          </tr>
          
    <tr>
       <td valign="top"><i>Question text:</i></td>
       <td>
          <div class="field">
             <?echo $qt_text;?>
          </div>
       </td>
          </tr>
	
	<tr>
	<td valign="top"><i>Parameters:</i></td>
	<td>	
	
	
	<?
	//Shows textboxes for range name, min an max values on screen along with the existing values.
	if($var_type == "range"){
		$range_var_num = 1;
		echo " <table class=\"range_var\" border=\"2\" width=\"100%\"> ";
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
			<td valign=\"top\"> $range_name[$i]</td>
			<td valign=\"top\"> $range_min[$i]</td>
			<td valign=\"top\">$range_max[$i]</td>
			</tr>";
			$range_var_num++;
		}
		echo "</table>";	
		
	//Shows textboxes for list name and values on screen along with the existing values.
	}elseif($var_type == "list"){
		$list_var_num = 1;
		echo " <table class=\"list_var\" border=\"2\" width=\"100%\"> ";
		for($i=0; $i<sizeof($list_name); $i++){
			$var_name = "list".$list_var_num."_name";
			$var_val = "list".$list_var_num."_val";
			echo "<tr>
			<td valign=\"top\">Variable name</td>
			<td valign=\"top\">Values separated by commas</td>
			</tr>
		
			<tr>
			<td valign=\"top\"> $list_name[$i]</td>
			<td valign=\"top\"> $list_val[$i] </td>
			</tr>";
			$list_var_num++;
		}
		echo "</table>";

	}
	?>
	</td>
	</tr>
	
	
	<tr>
	<?
	
	//Generates a textbox for range answer function along with its existing values.
	if($var_type == "range"){
		echo "<td valign=\"top\"><i>Answer equation</i></td>
		<td valign=\"top\"> $range_ans</td>";
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
				echo "<table class=\"ans_table\" border=\"2\" width=\"100%\">";
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
				echo "<td> $ans_tab_col[$j]</td>";
			}
			$ans_name = "ans".$i;
			echo "<td> $ans_rows[$i]</td>";
			echo "</tr>";
			if($i == ((sizeof($ans_tab_rows))-1)){
				echo "</table>";
			}
		}
		echo "</td>";
	}
	?>
	</tr>
	
     </table>

 <p>   
 
<h2> Do you want to delete this question: 

<input class="ui-state-default ui-corner-all submitButton" data-disable-with="Saving..." id="save_button" name="commit" type="submit" value="Delete"/>
</h2>




    <div id="footer" style="float:center">
     	  <a href="index.html">Features</a> | 
         <a href="index.html">About QBank</a> | 
         <a href="index.html">Contact Us</a>
      </div>


  </form>

