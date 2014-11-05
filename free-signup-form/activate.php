<!DOCTYPE HTML>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script type="text/javascript">
	$(window).load(function() {
		$('#loading').fadeOut(7000);
	});
</script>
<style> 
.errormsgbox {
	border: 1px solid;
	margin: 0 auto;
	padding:10px 60px;
	background-repeat: no-repeat;
	background-position: 10px center;
	width:450px;
	color: #D8000C;
	background-color: #FFBABA;
	font-size: 130%;
}
</style>
</head>
<body>


<?php

include "emailtemplate.php";

$con = mysql_pconnect("localhost","root","bitnami");
if (!$con){die('Could not connect: ' . mysql_error($con));}
$dblink = mysql_select_db("master");

if (isset($_REQUEST['key']) && (strlen($_REQUEST['key']) == 32)){//The Activation key will always be 32 since it is MD5 Hash
   $key = $_REQUEST['key']; 
   
}else{
	echo '<div class="errormsgbox">Activation key is not Proper.</div>';
	exit;
}

if (isset($key)){
	
	//echo $_REQUEST['key'];

	// Select from database to set the "activation" field
	$sql="SELECT * FROM user_signup WHERE `activation`='$key' LIMIT 1";
	$result = mysql_query($sql);
	$data = mysql_fetch_array($result);


   // Key is Proper and we got data from key
	if($data['activation'] == TRUE){
		
		//Guide Activate or not
		if($data['user_status'] == '0'){
			
			$ctime = time();
			$calculateday = $ctime - $data['time'];
				
			//Checking entries less that 24 hours
			if($calculateday <= 86400){
			
				echo '<div id="loading" style="margin: auto;width: 500px;"><img alt="loading" src="../gear.gif" style="width: 100%;"></div>';

				//calling of creating internal site

				$querystring = "id=2&token=EBDBB91F-BCFE-4f00-AFF5-F33F19A345E8&email=".$data['email']."&password=".$data['password']."&guideinternalurl=".$data['guide_name']."&guidezipcode=".$data['zip']."&timezone=".$data['timezone']."&language=".$data['language']."&tformat=".$data['timeformat']."&dformat=".$data['dateformat']."&wunit=".$data['temperature']."&dunit=".$data['distance'];
		//echo $querystring;

				/** CMS CREATION CODE START*/
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,"http://operations.townwizard.com/internal_cms_create.php");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$querystring);
				$server_output = curl_exec ($ch);
				curl_close ($ch);
				//$server_output = "OK";
				
				/** CMS CREATION CODE END*/

				if ($server_output == TRUE){ 
					$updateuser="UPDATE user_signup SET signup_type = '0', user_status = '1' WHERE id = '".$data['id']."' ";
		         
		        		$result = mysql_query($updateuser);
			  
					  /**  ZOHO INTEGRATION CODE START  */	
					 
					//$accountName	= '<![CDATA['.$data['first_name'].' %26 '.$data['last_name'].' Co.]]>';
					$accountName	= '<![CDATA['.$data['first_name'].' '.$data['last_name'].' %26 Co.]]>';
					$firstName		= $data['first_name'];
					$lastName		= $data['last_name'];
					$email			= $data['email'];
					$subject		= $data['guide_name'];
					$signupDate		= date("m/d/Y", strtotime("now"));  // Format 8/8/2014
					$zipcode		= $data['zip'];
					$contactName	= $data['first_name']." ".$data['last_name'];

					include "zoho-export.php"; 
					
					 /**  ZOHO INTEGRATION CODE END  */			
			
					/**  MAIL PROCESS START  */
					
					$signupdate = gmdate("l jS \of F Y h:i:s A");
					$message2 = "New local guide is ready! Check out the site  link and information below.<br/>\n\n";
					$message2.= "<table cellspacing='5'><tr><td><b>Name : </b></td><td>".$data['first_name']."</td></tr>";
					$message2.= "<tr><td><b>Email Address : </b></td><td>".$data['email']."</td></tr>";
					$message2.= "<tr><td><b>Guide Status : </b></td><td>Not Terminated</td></tr>";
					$message2.= "<tr><td><b>Partners Name : </b></td><td>".$data['first_name']."</td></tr>";
					$message2.= "<tr><td><b>Product : </b></td><td>Free</td></tr>";
					$message2.= "<tr><td><b>Guide Deployment Status : </b></td><td>CMS Ready</td></tr>";
					$message2.= "<tr><td><b>Subject : </b></td><td> ".$data['guide_name']."</td></tr>";
					$message2.= "<tr><td><b>Guide Signup Date : </b></td><td>".$signupdate."</td></tr>";
					$message2.= "<tr><td><b>Guide City Zip : </b></td><td>".$data['zip']."</td></tr>";
					$message2.= "<tr><td><b>Contact Name : </b></td><td>".$data['first_name']."</td></tr>";
					$message2.= "<tr><td><b>Product Name : </b></td><td>Free - External Use</td></tr>";
					$message2.= "<tr><td><b>Qty : </b></td><td>1</td></tr>";
					$message2.= "<tr><td><b>Unit Price : </b></td><td>0</td></tr>";
					$message2.= "<tr><td><b>List Price : </b></td><td>0</td></tr>";
					$message2.= "<tr><td><b>Language : </b></td><td>".$data['language']."</td></tr>";
					$message2.= "<tr><td><b>Distance : </b></td><td>".$data['distance']."</td></tr>";
					$message2.= "<tr><td><b>Time Zone : </b></td><td>".$data['timezone']."</td></tr>";
					$message2.= "<tr><td><b>Date Format : </b></td><td>".$data['dateformat']."</td></tr>";
					# ZOHO STATUS MESSAGE START
					$message2.= "<tr><td colspan=2><b><u>ZOHO STATUS</u></b></td></tr>";
					$message2.= "<tr><td><b>Partner Creation in ZOHO : </b></td><td>".$partnerZohoStatus."</td></tr>";
					$message2.= "<tr><td><b>Contact Creation in ZOHO : </b></td><td>".$contactZohoStatus."</td></tr>";
					$message2.= "<tr><td><b>Guide   Creation in ZOHO : </b></td><td>".$guideZohoStatus."</td></tr>";
					# ZOHO STATUS MESSAGE END
					$message2.= "<tr><td><b>Guide Administration URL : </b></td><td>http://".$data['guide_name'].".townwizard.com/administrator</td></tr></table>";

					$headers2 = "MIME-Version: 1.0\r\n";
					$headers2 .= "Content-type:text/html;charset=iso-8859-1\r\n";
					$headers2 .= "From:TownWizard< no-reply@townwizard.com>";
					
					$subject = "New guide ".$data['gname']."  is created";
					
					$twadminemail2 = "operations@townwizard.com";

					$finalmail2 = mail($twadminemail2, $subject, $message2,$headers2);

				      if($finalmail2){

				            $data[] = "";
				         //   $serverurl = $_SERVER["HTTP_HOST"];

							header('Location:http://'.$data['guide_name'].'.townwizard.com/administrator');
				      }else{
				       echo '<div class="errormsgbox">You could not be registered due to error.Please contact at <b>support@townwizard.com</b></div><br/>';
				      }
					/**  MAIL PROCESS END  */

				}else{echo '<div class="errormsgbox">You could not be registered due to a system error.</div>';}
		
			}else{echo '<div class="errormsgbox">Activation key is Expire.</div>';}

		}else{echo '<div class="errormsgbox">Guide already activated.</div>';}	
		
	}else{echo '<div class="errormsgbox">Activation key is Expire.</div>';}	

	$key="";
 	mysql_close($con);

} else {
	echo '<div class="errormsgbox">Activation key is not proper .</div>';
}

?>
</body>
</html>
