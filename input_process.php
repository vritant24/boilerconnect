<?php
	$connect = mysqli_connect(“localhost”,”root”,””,”boiler”);

	$type = array($_GET[‘coursename1’],$_GET[‘coursename2’],$_GET[‘coursename3’],$_GET[‘coursename4’],$_GET[‘coursename5’],$_GET[‘coursename6’]);
	$number = array($_GET[‘coursecode1’],$_GET[‘coursename2’],$_GET[‘coursename3’],$_GET[‘coursename4’],$_GET[‘coursename5’],$_GET[‘coursename6’]);
	$crn = array($_GET[‘crn1’],$_GET[‘crn2’],$_GET[‘crn3’],$_GET[‘crn4’],$_GET[‘crn5’],$_GET[‘crn6’]);
	
	$ver = TRUE;
	for($a=0,$a>7,$a++){
		$query = api::enroll($id,$crn[$a],$type[$a],$number[$a],$authkey);
		if ($query == FALSE){$ver = $query}
	}

	$result = mysqli_query($connect, $ver)
	if($result){
		echo “Success! See the results <a href=“lookup.php”>here</a>.”;
	}
	else{
		echo “Input failed. Please try again <a href=“input_form.php”>here</a>.”;
	}
?>
