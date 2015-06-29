<?php 
	require_once(dirname(__FILE__)."/config.php");
	class api{

		/*
					Private Functions 
		*/

		private static function authorize($authkey){
			$res=true;
			if(!$res){
				$arr["status"]=401;
				$arr["description"]="Authorization Failed";
				die(json_encode($arr));
			}
		}

		private static function db(){

			$link = new mysqli(host, dbuser, dbpassword, dbname) ;
			if($link->connect_errno){
				$arr["status"]=500;
				$arr["description"]="Database Connection Failed";
				die(json_encode($arr));
			}
			return $link;
		}

		private static function checkMail($email,$pdmail=null){

			$link=api::db();
			$query="SELECT COUNT(id) FROM `user` WHERE email=? OR pdmail=?";
			$param=$link->prepare($query) or die("fooled");
			$param->bind_param("ss",$email,$pdmail) or die("failed");
			$param->execute() or die("fucked");
			$result=$param->get_result();
			$result=$result->fetch_assoc();
			$link->close();
			if($result['COUNT(id)']>0)return false;
			else return true;

		}

		private static function checkId($id){
			$link=api::db();
			$query="SELECT COUNT(id) FROM `user` WHERE id=? LIMIT 1";
			$param=$link->prepare($query) or die("fooled");
			$param->bind_param("i",$id) or die("failed");
			$param->execute() or die("fucked");
			$result=$param->get_result();
			$result=$result->fetch_assoc();
			$link->close();
			if($result['COUNT(id)']>0)return true;
			else {
				$res['status']=409;
				$res['description']="id doesnt exist";
				die(json_encode($res));
			}		

		}

		/*
					Public Functions
		*/

		public static function add($fname=null,$lname=null,$email=null,$pdmail=null,$image=null,$authkey=null){

			api::authorize($authkey);
			$link=api::db();
			if(!api::checkMail($email,$pdmail)){
				$arr["status"]=404;
				$arr["description"]="Email or Purdue Email already exists";
				die(json_encode($arr));
			}
			$query="INSERT INTO `user` (fname,lname,email,pdmail,image) VALUES (?,?,?,?,?)";
			$param=$link->prepare($query) or die("fucked 1");
			$param->bind_param("sssss",$fname,$lname,$email,$pdmail,$image)or die("fucked 2");
			$param->execute() or die($param->error);
			$link->close();
			$result['status']=201;
			$result['description']="created";
			echo json_encode($result);
		}

		public static function details($id,$authkey){
			api::authorize($authkey);
			$link=api::db();
			api::checkId($id);
			$query="SELECT * FROM `user` WHERE id=?";
			$param=$link->prepare($query) or die("fucked");
			$param->bind_param("i",$id) or die("fucked 2");
			$param->execute() or die("fucked 3");
			$result=$param->get_result();
			$result=$result->fetch_assoc();
			$link->close();
			$result['status']=200;
			$result['description']="OK";
			echo json_encode($result);
		}

		public static function pub($id,$authkey) {
			api::authorize($authkey);
			api::checkId($id);
			$link=api::db();
			$query="SELECT fname,lname,image from `user` WHERE id=? LIMIT 1";
			$param=$link->prepare($query) or die("fucked");
			$param->bind_param("i",$id) or die("fucked 2");
			$param->execute() or die("fucked 3");
			$result=$param->get_result();
			$result=$result->fetch_assoc();
			$link->close();
			$result['status']=200;
			$result['description']="OK";
			echo json_encode($result);

		}
	public  static function login($email,$authkey){
		api::authorize($authkey);
		$link=api::db();
		if(api::checkMail($email)){
			$res['status']=403;
			$res['description']="Email not found";
			die(json_encode($res));
		}
		$query="SELECT id FROM `user` WHERE email=? LIMIT 1";
		$param=$link->prepare($query) or die("l1");
		$param->bind_param("s",$email);
		$param->execute() or die("l2");
		$result=$param->get_result();
		$result=$result->fetch_assoc();
		$link->close();
		$result['status']=200;
		$result['description']="Success";
		echo json_encode($result);
	}
	public static function enroll($id,$crn,$type,$number,$authkey){
		api::authorize($authkey);
		api::checkId($id);
		$link=api::db();
		$query="SELECT count(id) from `courses`  WHERE crn=? AND userid=?";
		$param=$link->prepare($query) or  die("1");
		$param->bind_param("ii",$crn,$id) or die("2");
		$param->execute() or die("3");
		$result=$param->get_result();
		$result=$result->fetch_assoc();
		echo $result["count(id)"];
		if($result["count(id)"]>0){
			$res['status']=409;
			$res['description']="already exists";
			die(json_encode($res));
		}
		$result=NULL;
		$query="INSERT INTO `courses` (userid,crn,type,number) VALUES(?,?,?,?)";
		$param=$link->prepare($query) or die("11");
		$param->bind_param("iisi",$id,$crn,$type,$number);
		$param->execute() or die("12");
		$link->close();
		$result['status']=201;
		$result['description']="Added";
		echo json_encode($result);
	}
	public static function drop($id,$userid,$crn,$authkey){
		api::authorize($authkey);
		api::checkId($userid);	
		$link=api::db();
		$query="SELECT `id` FROM `courses` WHERE crn=? AND userid=? LIMIT 1";
		$param=$link->prepare($query);
		$param->bind_param("ii",$crn,$userid);
		$param->execute() or die("f3");
		$result=$param->get_result();
		if(!$result->num_rows>0){
			$res['status']=404;
			$res['description']="course Not Found";
			die(json_encode($res));
		}
		$result=$result->fetch_assoc();
		if(!($result['id']==$id)){
			$res['status']=401;
			$res['description']="Unauthorized";
			die(json_encode($res));
		}
		$query="DELETE FROM `courses` WHERE id=?";
		$param=$link->prepare($query);
		$param->bind_param("i",$id);
		$param->execute() or die("f3");
		$res['status']=200;
		$res['description']="Success";
		echo json_encode($res);
		$link->close();
	}
	public static function searchCrn($crn,$authkey){
		api::authorize($authkey);
		$link=api::db();
		$query="SELECT `userid` FROM `courses` WHERE crn=?";
		$param=$link->prepare($query);
		$param->bind_param("i",$crn);
		$param->execute() or die("f3");
		$result=$param->get_result();
		$res['status']=200;
		$res['description']="Success";
		while($row=$result->fetch_array(MYSQLI_BOTH)){
			$resd[]=$row['userid'];
		}
		$res['id']=$resd;
		$result->free();
		$link->close();
		echo json_encode($res);
	}
	public static function searchCourse($type,$number,$authkey){
		api::authorize($authkey);
		$link=api::db();
		$query="SELECT `userid` FROM `courses` WHERE type=? AND number=?";
		$param=$link->prepare($query);
		$param->bind_param("si",$type,$number);
		$param->execute() or die("f3");
		$result=$param->get_result();
		$res['status']=200;
		$res['description']="Success";
		while($row=$result->fetch_array(MYSQLI_BOTH)){
			$resd[]=$row['userid'];
		}
		$res['id']=$resd;
		$result->free();
		$link->close();
		echo json_encode($res);
	}
}
	
	?>