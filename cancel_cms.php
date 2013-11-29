<?php 
// based on any(internal or external) url fetch entries
global $msg,$sitename;

if(isset($_REQUEST))
{
	//Calling Function for Validation
	$postcheck = checkPostParameter($_REQUEST);
	
	// if postcheck is false return 400 with the message and if true then proceed ahead
	if($postcheck)
	{	
			//connecting to mysql
			$link = mysql_connect('localhost', 'root', 'bitnami');
			
			# Condition to check, if server connection succesfully established or not.
			if (!$link){
				$msg="Could not connect Mysql";
				send_error_email($msg);
				header("HTTP/1.0 402 Could not connect Mysql - ".$msg."");
				exit;
			}

			//selecting master database
			$select_db1 = mysql_select_db("master");
			
			if (!$select_db1){
				$msg="Could not select database master";
				send_error_email($msg);
				header("HTTP/1.0 400 Could not select database - ".$msg."");
		 		exit;
			}

			$id_res = mysql_query("select * from api_caller where id=".$_REQUEST['id']."");
			
			if (!$id_res){
				$msg="Could not found result from api_caller";
				send_error_email($msg);
				header("HTTP/1.0 400 Could not found result from api_caller - ".$msg."");
		 		exit;
			}
			
			$output = mysql_fetch_row($id_res);

			//Checking ID and Token in database
			if($output[0] == $_REQUEST['id'] AND $output[1] == $_REQUEST['token'])
			{

				  //Checking ID and Token is active or not in database
				  if($output[2] == TRUE)
				  {
						
					if(!empty($_REQUEST['guideinternalurl']))
					{
						global $sitename;
						$sitename = $_REQUEST['guideinternalurl'];
						//calling function for cms cancellation based on internal url
						basedOnInternalSite();
					}
					else // if no internal url set and external url set
					{
						global $sitename;
						$url = explode(".", $_REQUEST['guideexternalurl']);
						$sitename=$url[0].'-dot-'.$url[1];
						//calling function for cms cancellation based on external url
						basedOnExternalSite();
					}
				  }
				  else // if ID and Token are not active
				 {	
					$msg="Your ID and Token are not active currently";
					send_error_email($msg);
					header("HTTP/1.0 401 ".$msg."");
			 		exit;
				 }
			}
			else // if ID and Token are not in database
			{ 
				$msg="You have entered wrong Access Id or Token";
				send_error_email($msg);
				header("HTTP/1.0 401 ".$msg."");
		 		exit;
			}
	
   	 }
	 else //if postcheck fails
	{
		send_error_email($msg);
		header("HTTP/1.0 400 Invalid parameter - ".$msg."");
		exit;
	}
}
else // if(isset($_REQUEST)) fails
{	
		$msg="REQUEST missing";
		send_error_email($msg);
		header("HTTP/1.0 400 ".$msg."");
		exit;
}

//created function for validation
function checkPostParameter($postValue){
	
global $msg;

	if(!ctype_digit($postValue['id'])){
		$msg="Access ID";
		return false;
	}
	
	if(empty($postValue['token'])){
		$msg="Token Empty";
		return false;
	}
	
	if(empty($postValue['guideinternalurl']) and empty($postValue['guideexternalurl'])){
		$msg="Site name empty";
		return false;
	}
	
	return true;
}


function basedOnInternalSite(){
	
		//Checking Internal site url is exist or not
		$int_url = mysql_query("SELECT site_url,db_name FROM master WHERE site_url = '".strtolower($_REQUEST['guideinternalurl']).".townwizard.com'");

		//if internal url found in database then proceed ahead
		if(mysql_num_rows($int_url)>0)
		{
			$int_output = mysql_fetch_row($int_url);
			
			//Checking external site url and other urls are exist or not
			$ext_url = mysql_query("SELECT  site_url,db_name FROM master WHERE db_name = '".$int_output[1]."'" );
			
			if(mysql_num_rows($ext_url)>0)
			{ 
				while($ext_output=mysql_fetch_array($ext_url))
				{
					
					//Update internal and other external URLS in our master table
					$query_output1 = mysql_query("update master SET site_url='TBD-".$ext_output[0]."',db_name='TBD-".$ext_output[1]."' where site_url='".$ext_output[0]."'");
						
					if (!$query_output1){
						$msg="Could not update master table for cancellation";
						send_error_email($msg);
						header("HTTP/1.0 400 Could not update master table for cancellation - ".$msg."");
						exit;
					}
				
				}
				
				$msg="CMS cancelled successfully";
				send_success_email($msg);
				header("HTTP/1.0 200 ok -".$msg."");
				exit;
								 
			}				 
		
		}
		else // if no internal url found in database
		{
			$msg="Internal site not exists";
			send_error_email($msg);
			header("HTTP/1.0 400 Invalid parameter - Internal site not exists");
			exit;
		}
}

function basedOnExternalSite(){
	
		//Checking External site url is exist or not
		$ext_url = mysql_query("SELECT site_url,db_name FROM master WHERE site_url = '".strtolower($_REQUEST['guideexternalurl'])."'");
	
		//if external url found in database then proceed ahead
		if(mysql_num_rows($ext_url)>0)
		{
			$ext_output = mysql_fetch_row($ext_url);
			
			//Checking internal site url and other external urls are exist or not
			$int_url = mysql_query("SELECT  site_url,db_name FROM master WHERE db_name = '".$ext_output[1]."'" );
			
			if(mysql_num_rows($int_url)>0)
			{ 
				while($int_output=mysql_fetch_array($int_url))
				{
					//Update internal and other external URL in our master table
					$query_output2 = mysql_query("update master SET site_url='TBD-".$int_output[0]."',db_name='TBD-".$int_output[1]."' where site_url='".$int_output[0]."'");

					if (!$query_output2){
						$msg="Could not update master table for cancellation";
						send_error_email($msg);
						header("HTTP/1.0 400 Could not update master table for cancellation - ".$msg."");
						exit;
					}					
				}
				$msg = "CMS cancelled successfully";
				send_success_email($msg);
				header("HTTP/1.0 200 ok -".$msg."");
				exit;
								 
			}
							 
		
		}
		else // if no external url found in database
		{
			$msg="External site not exists";
			send_error_email($msg);
			header("HTTP/1.0 400 Invalid parameter - ".$msg."");
			exit;
		}
}

function send_error_email($msg)
{
	global $sitename;
	$to = "operations@townwizard.com". ", ";
	$to .= "support@townwizard.com";
	$subject = "".$sitename. "-cancel cms failed";
	$message = "<div>".$msg." for guide ".$_REQUEST['guideinternalurl']."<br/><br/>Thanks!</div>";
	$from = "Townwizard-Operations";
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";
	$headers .= "From:" . $from;
	mail($to,$subject,$message,$headers);
	return TRUE;
}

function send_success_email($msg)
{
	global $sitename;
	$to = "operations@townwizard.com". ", ";
	$to .= "support@townwizard.com";
	$subject = "".$sitename. "-cancel cms succeed";
	$message = "<div>".$msg." for guide ".$sitename."<br/><br/>Thanks!</div>";
	$from = "Townwizard-Operations";
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";	
	$headers .= "From:" . $from;
	mail($to,$subject,$message,$headers);
	return TRUE;
}

?>