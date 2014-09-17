<?php

session_start(); 

$email = $_REQUEST['email'];

//check for email in signup db
$con2 = mysql_connect("localhost","root","bitnami");
if(!$con2){die('Could not connect: ' . mysql_error($con2));}
$dblink2 = mysql_select_db("master");

$emailquery = "SELECT `id`,`time`,`user_status` from user_signup WHERE `email`= '".$email."'  ";
$resultemail = mysql_query($emailquery);

if(mysql_num_rows($resultemail)>0){ 

	while($data = mysql_fetch_assoc($resultemail)){
		
		if($data['user_status'] == 1){
			
			//echo "Guide with this email id is already activated";
			$response = array('status'=>101); 
			$callback = $_GET["callback"];
			echo $callback . "(" . json_encode($response) . ")";
			exit;

		}else{	
		
			if($data['user_status'] == 0){
				
				$ctime = time();
				$calculateday = $ctime - $data['time'];
				if($calculateday <= 86400 ){ 
				
					//echo "This Email is registered in last 24 hours.";
					$response = array('status'=>102); 
					$callback = $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				
			}else{
				
				updateProcess($data['id']);
			}
		}
		}
	}
}else{
	//echo "Thanks";
		$response = array('status'=>100); 
		$callback = $_GET["callback"];
		echo $callback . "(" . json_encode($response) . ")";
		exit;
}

mysql_close($con2);

function updateProcess($did){

		$activation = md5(uniqid(rand(), true));
		$ip = $_SERVER['SERVER_ADDR'];
		$time = time();
		
		$query_insert_user = "UPDATE `user_signup` SET `ip` = '$ip',`email` = '".$_REQUEST['email']."',`time` = '$time' WHERE `id` = $did ";
		
		$result_insert_user = mysql_query($query_insert_user);
		
		if($result_insert_user){
		
			$response=array('status'=>103);
			$callback = $_GET["callback"];
			echo $callback . "(" . json_encode($response) . ")";
			exit;
		}

	}				
?> 
