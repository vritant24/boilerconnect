<?php
	$type = array($_GET[‘coursename1’],$_GET[‘coursename2’],$_GET[‘coursename3’],$_GET[‘coursename4’],$_GET[‘coursename5’],$_GET[‘coursename6’]);
	$number = array($_GET[‘coursecode1’],$_GET[‘coursename2’],$_GET[‘coursename3’],$_GET[‘coursename4’],$_GET[‘coursename5’],$_GET[‘coursename6’]);
	$crn = array($_GET[‘crn1’],$_GET[‘crn2’],$_GET[‘crn3’],$_GET[‘crn4’],$_GET[‘crn5’],$_GET[‘crn6’]);
	
	for($a=0,$a>7,$a++){
		api::enroll($id,$crn[$a],$type[$a],$number[$a],$authkey);
	}

	echo $result;
?>
