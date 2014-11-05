<?php
# Included file for Amazon Email feature
include "twmailer.php";

session_start(); 
include "emailtemplate.php";
if (!empty($_REQUEST['captcha'])) {
	
    if (empty($_SESSION['captcha']) || trim(strtolower($_REQUEST['captcha'])) != $_SESSION['captcha']) {
 
		//echo "Captcha wrong";
		$response = array('status'=>107); 
		$callback = $_GET["callback"];
		echo $callback . "(" . json_encode($response) . ")";
		exit;
		
    } else {

		$_REQUEST['gname'] = strtolower($_REQUEST['gname']);
		$gname = $_REQUEST['gname'];
                $email = $_REQUEST['email'];

		//Master db cheking
		$sitename = $gname.".townwizard.com";
		$con = mysql_connect("localhost","root","bitnami");
		if(!$con){die('Could not connect: ' . mysql_error($con));}
		$dblink = mysql_select_db("master");
		$sql1="SELECT * from `master` WHERE `site_url` LIKE '%".$sitename."'";
		$result1 = mysql_query($sql1);

		if(mysql_num_rows($result1)>0){
			//check for guide entry in master table
			//echo "<br/>Guide already activated";
			$response = array('status'=>101); 
			$callback = $_GET["callback"];
			echo $callback . "(" . json_encode($response) . ")";
			exit;
		}

		mysql_close($con); 

		//check for email in signup db
		$con2 = mysql_connect("localhost","root","bitnami");
		if(!$con2){die('Could not connect: ' . mysql_error($con2));}
		$dblink2 = mysql_select_db("master");

		$emailquery = "SELECT `id`,`guide_name`,`email`,`time`,`user_status` from user_signup WHERE `email`= '".$email."'  ";
		$resultemail = mysql_query($emailquery);

		if(mysql_num_rows($resultemail)>0){ 

			//echo "Found email";
			$ctime = time();
			while($data = mysql_fetch_assoc($resultemail)){
		//		$calculateday = $ctime - $data['time'];
		//		if($calculateday <= 86400 ){ 
					//echo "<br/>Email is registerd within 24 hour. So they can not submit new guide name.";
		//			$response = array('status'=>102); 
		//			$callback = $_GET["callback"];
		//			echo $callback . "(" . json_encode($response) . ")";
		//			exit;
		//		}else{
					//echo "<br/>Email is registerd more than 24 hour So, Updating guide entry.";
					updateProcess($data['id']);
				/*}*/
			}

		}else{	
			//echo "Email id is not found So,Cheking guide name";
			$ctime = time();
			$guidesql = "SELECT `id`,`guide_name`,`email`,`time`,`user_status` from user_signup WHERE `guide_name`= '".$gname."' AND user_status='0' "; 
			$resultguidename = mysql_query($guidesql) or die(mysql_error());
			$guidenamecounter = mysql_num_rows($resultguidename);

			if($guidenamecounter == 0){
				//echo "<br/>We dont find guidename so Insert Process initiated .";
				insertProcess($_REQUEST);
			}else{
				//echo "<br/>We dont find email ID but guidename is there so cheking time.";
				checkDataLoop($resultguidename);
			}
		} 																																										
	 }
  
	$request_captcha = htmlspecialchars($_REQUEST['captcha']);
	unset($_SESSION['captcha']);
}	 
$response = array('status'=>107); 
$callback = $_GET["callback"];
echo $callback . "(" . json_encode($response) . ")";
exit;

mysql_close($con2);
	
function insertProcess($data){

			//echo "<br/>Captcha insertProcess";
			$activation = md5(uniqid(rand(), true));
			$ip = $_SERVER['SERVER_ADDR'];
			$time = time();

			$query_insert_user = "INSERT INTO user_signup  (`ip`,`first_name`,`last_name`, `email`, `password`,`guide_name`,`time`, `activation`,`signup_type`, `signup_id` ,`user_status`, `zip`,`language`,`timezone`, `timeformat`,`dateformat`, `temperature` ,`distance`) VALUES ('$ip', '".$data['fname']."', '".$data['lname']."', '".$data['email']."', '".$data['pass']."', '".$data['gname']."', '$time', '$activation','0',NULL,'0', '".$data['zip']."', '".$data['language']."', '".$data['time_zone']."', '".$data['time_format']."', '".$data['date_format']."', '".$data['temperature']."', '".$data['distance']."')";
			$result_insert_user = mysql_query($query_insert_user);
			

	
			//Successfully inserted into database
			if($result_insert_user){
				
				$var1 = "TownWizard Signup Email Verification";
				$headercode = mailheader($var1);
				$footercode = mailfooter();
				$link = "http://".$_REQUEST['url']."/activate.php?key=" .$activation;

				$message .= $headercode; 
				$message .= '<tr><td>&nbsp;</td><td><p style="font:22px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:0px 0 0 0;padding:0;color:#000;">Congratulations</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:20px 0 5px 0;padding:0;">Thanks for signing up for your free local guide from TownWizard! Click the email verification link below to complete the guide setup process.</p></td><td>&nbsp;</td></tr>';
				$message .= '<tr><td>&nbsp;</td><td style="text-align: center"><a href='.$link.' target="_blank" style="text-decoration: none; background: #e5292f; padding: 10px; margin: 10px 0px; color: rgb(255, 255, 255); border-radius: 5px; text-transform: capitalize; font: 20px/55px Helvetica Neue,Helvetica,Arial,sans-serif; box-shadow: 0px 1px 2px 2px rgba(0, 0, 0, 0.25);">click here </a></td><td>&nbsp;</td></tr>';
				$message .= '<tr><td>&nbsp;</td><td><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Once You click on above link, Your new local guide will ready! Check out the site link and login information below.</p></td><td>&nbsp;</td></tr>';	
				$message .= '<tr><td height="100">&nbsp;</td><td> 
					<table cellspacing="5"><tbody><tr><td width="180" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Guide Name : </td><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">'.$data['gname'].'</td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;vertical-align: top;">Guide Administration URL : </td><td><a target="_blank" href="http://'.$data['gname'].'.townwizard.com/administrator" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">http://'.$data['gname'].'.townwizard.com/administrator</a></td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Username : </td><td><a href="mailto:'.$data['email'].'" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">'.$data['email'].'</a></td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Password : </td><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">(password that you specified)</td></tr></tbody></table>
				</td><td>&nbsp;</td></tr>';
				$message .= '<tr><td height="100">&nbsp;</td><td><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:7px 0;color:#000000;">We are here to help.</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">You are invited to access TownWizard\'s Customer Self Service Portal. You can access the support site at <a target="_blank" href="http://www.townwizard.org/" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">http://www.townwizard.org</a>.</p></td><td>&nbsp;</td></tr>';
				//$message .= '<tr><td>&nbsp;</td><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Username : <a href="mailto:'.$data['email'].'" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">'.$data['email'].'</a></td><td>&nbsp;</td></tr>';
				//$message .= '<tr><td>&nbsp;</td><td><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:7px 0;color:#000000;">Please <a target="_blank" href="#" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;color:#1a1a1a;text-decoration:none;">click here</a> to accept invitation and set your password.</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">if you are not intended recipient of this invite, please contact the TownWizard Administrator.</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">If you face any difficulty while activation, please do not hasitate to send an email at <a target="_blank" href="mailto:support@townwizard.com" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">Support@townwizard.com</a>.</p></td></tr>';
				$message .= '<tr><td>&nbsp;</td><td><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Thank you,</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">The TownWizard Team</p></td><td>&nbsp;</td></tr>';
				//$message .= '<tr><td>&nbsp;</td><td><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:7px 0;color:#000000;">Please use following browsers for account activation:</p><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;margin:7px 0;color:#000000;">Latest version of Google Chrome, Mozilla Firefox, Internet explorer 9,10</p></td><td>&nbsp;</td></tr>';
				//$message .= '<tr><td height="50">&nbsp;</td><td><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Please <a target="_blank" href="http://www.townwizard.org/" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">click here</a> to access help documentation regarding account activation</p></td><td>&nbsp;</td></tr>';
				$message .= $footercode;
				
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1\r\n";
				$headers .= "From:TownWizard<no-reply@townwizard.com>";
				
				# OLD EMAIL CODE
				/*$insertmail = mail($data['email'], 'TownWizard Signup Email Verification', $message, $headers);
				if($insertmail){
					//echo "<br/>Entry Inserted and mail sent.";
					$response=array('status'=>100);
					$callback = $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				}else{
					echo '<div class="errormsgbox">You could not be registered due to a system error. We apologize for any inconvenience.</div>';
				}*/
				
				# NEW EMAIL CODE
				$insertmail = sendTwMail($data['email'],'TownWizard Signup Email Verification',$message,'operations@townwizard.com');
				if($insertmail == 'SUCCEED'){
					//echo "<br/>Entry Inserted and mail sent.";
					$response	= array('status'=>100);
					$callback	= $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				}else{
					//echo "<br/>Entry Inserted and mail not sent.";
					$response	= array('status'=>200);
					$callback	= $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				}
			}else{
				echo '<div class="errormsgbox">You could not be registered due to a system error. We apologize for any inconvenience.</div>';
			}
		}		
	
function checkDataLoop($resultguidename){
		 //echo "<br/>Captcha checkDataLoop";
		while($data = mysql_fetch_array($resultguidename)){
			$calculateday = $ctime - $data['time'];
			//Checking entries less that 24 hours
			if($calculateday <= 86400){ 
				// check for guide entry withing
				//echo "<br/>This Guide name is already registered in last 24 hour.";
				$response = array('status'=>104); 
				$callback = $_GET["callback"];
				echo $callback . "(" . json_encode($response) . ")";
				exit;	
			}else{ 
				//echo "<br/>Guide already aaded but it is older than 24hours and not activated yet so updating new email id";
				updateProcess($data['id']);
				//check for guide entry in master table
				$response = array('status'=>105); 
				$callback = $_GET["callback"];
				echo $callback . "(" . json_encode($response) . ")";
				exit;
			}
		}
	}		

function updateProcess($did){

		$activation = md5(uniqid(rand(), true));
		$ip = $_SERVER['SERVER_ADDR'];
		$time = time();
		
		$query_insert_user = "UPDATE `user_signup` SET `ip` = '$ip',`first_name` = '".$_REQUEST['fname']."',`last_name` = '".$_REQUEST['lname']."',`email` = '".$_REQUEST['email']."',`password` = '".$_REQUEST['pass']."',`guide_name` = '".$_REQUEST['gname']."',`time` = '$time',`activation` = '$activation',`signup_type` = '0',`signup_id` = NULL,`user_status` = '0' WHERE `id` = $did ";
		
		$result_insert_user = mysql_query($query_insert_user);
			
		//Successfully inserted into database
		if($result_insert_user){
			
				$var1 = "EMAIL VARIFICATION";
				$headercode = mailheader($var1);
				$footercode = mailfooter();
				$link = "http://".$_REQUEST['url']."/activate.php?key=" .$activation;

				$message .= $headercode;
				$message .= '<tr><td>&nbsp;</td><td><p style="font:22px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:0px 0 0 0;padding:0;color:#000;">Congratulations</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:20px 0 5px 0;padding:0;">Thanks for signing up for your free local guide from TownWizard! Click the email verification link below to complete the guide setup process.</p></td><td>&nbsp;</td></tr>';
				$message .= '<tr><td>&nbsp;</td><td style="text-align: center"><a href='.$link.' target="_blank" style="text-decoration: none; background: #e5292f; padding: 10px; margin: 10px 0px; color: rgb(255, 255, 255); border-radius: 5px; text-transform: capitalize; font: 20px/55px Helvetica Neue,Helvetica,Arial,sans-serif; box-shadow: 0px 1px 2px 2px rgba(0, 0, 0, 0.25);">click here </a></td><td>&nbsp;</td></tr>';
				$message .= '<tr><td>&nbsp;</td><td><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Once You click on above link, Your new local guide will ready! Check out the site link and login information below.</p></td><td>&nbsp;</td></tr>';	
				$message .= '<tr><td height="100">&nbsp;</td><td> 
					<table cellspacing="5"><tbody><tr><td width="180" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Guide Name : </td><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">'.$_REQUEST['gname'].'</td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;vertical-align: top;">Guide Administration URL : </td><td><a target="_blank" href="http://'.$_REQUEST['gname'].'.townwizard.com/administrator" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">http://'.$_REQUEST['gname'].'.townwizard.com/administrator</a></td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Username : </td><td><a href="mailto:'.$_REQUEST['email'].'" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">'.$_REQUEST['email'].'</a></td></tr>
					<tr><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Password : </td><td style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">(password that you specified)</td></tr></tbody></table>
				</td><td>&nbsp;</td></tr>';
				$message .= '<tr><td height="100">&nbsp;</td><td><p style="font:15px Helvetica Neue,Helvetica,Arial,sans-serif;font-weight:bold;margin:7px 0;color:#000000;">We are here to help.</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">You are invited to access TownWizard\'s Customer Self Service Portal. You can access the support site at <a target="_blank" href="http://www.townwizard.org/" style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#1a1a1a;text-decoration:none;">http://www.townwizard.org</a>.</p></td><td>&nbsp;</td></tr>';					$message .= '<tr><td>&nbsp;</td><td><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">Thank you,</p><p style="font:14px Helvetica Neue,Helvetica,Arial,sans-serif;color:#777777;margin:7px 0;padding:0;">The TownWizard Team</p></td><td>&nbsp;</td></tr>';		
				$message .= $footercode;
				
				$headers = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type:text/html;charset=iso-8859-1\r\n";
				$headers .= "From:TownWizard< no-reply@townwizard.com>";
				
			//$updatemail = mail($email, 'TownWizard Signup Email Verification', $message, $headers);
/*			if($updatemail){
				//echo "<br/>Entry updated and mail sent.";
				// Confirmation
				$response=array('status'=>103);
				$callback = $_GET["callback"];
				echo $callback . "(" . json_encode($response) . ")";
				exit;
			}*/	
				$updatemail = sendTwMail($_REQUEST['email'],'TownWizard Signup Email Verification',$message,'operations@townwizard.com');
				if($updatemail == 'SUCCEED'){
					//echo "<br/>Entry Inserted and mail sent.";
					$response	= array('status'=>103);
					$callback	= $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				}else{
					//echo "<br/>Entry Inserted and mail not sent.";
					$response	= array('status'=>200);
					$callback	= $_GET["callback"];
					echo $callback . "(" . json_encode($response) . ")";
					exit;
				}
		}else{
			//echo '<div class="errormsgbox">You could not be registered due to a system error. We apologize for any inconvenience.</div>';
			$response=array('status'=>106);
			$callback = $_GET["callback"];
			echo $callback . "(" . json_encode($response) . ")";
			exit;
		}
	}	 			
?> 
