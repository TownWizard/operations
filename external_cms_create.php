<?php 

global $msg;

if(isset($_REQUEST))
{
	//Calling Function for Validation
	$postcheck = checkPostParameter($_REQUEST);
	
	// if postcheck is false return 400 with the message and if true then proceed ahead
	if($postcheck)
	{	
			//Connecting with Mysql Server
			mysql_connect('localhost', 'root','bitnami');

			//selecting master database
			mysql_select_db("master");

			$id_res = mysql_query("select * from api_caller where id=".$_REQUEST['id']."");

			$output = mysql_fetch_row($id_res);

			//Checking ID and Token in database
			if($output[0] == $_REQUEST['id'] AND $output[1] == $_REQUEST['token'])
			{

				  //Checking ID and Token is active or not in database
				  if($output[2] == TRUE)
				  {
						//Checking Enternal site url is exist or not
						$ext_url = mysql_query("SELECT site_url FROM master WHERE site_url = '".strtolower($_REQUEST['guideexternalurl'])."'");
						
						//if no external url found in database then proceed ahead
						if(mysql_num_rows($ext_url)==0)
						{
							//Checking internal site url is exist or not
							$internal_url = mysql_query("SELECT * FROM master WHERE site_url = '".strtolower($_REQUEST['guideinternalurl']).".townwizard.com'");
							
							//if internal url found in database then proceed ahead
							if(mysql_num_rows($internal_url)>0)
							{
								//calling function for creating external site
								externalSiteCreationSteps($internal_url);					 
							}
							else // if no internal url found in database
							{
								
								header("HTTP/1.0 400 Invalid parameter - Internal site not exists");
								exit;
							}
						}
						else // if external url found in database
						{
							
							header("HTTP/1.0 400 Invalid parameter - External site exists");
							exit;
						}
							
				  }
				  else // if ID and Token are not active
				 {	
					
					header("HTTP/1.0 401 Your ID and Token are not active currently");
			 		exit;
				 }
			}
			else // if ID and Token are not in database
			{ 
				
				header("HTTP/1.0 401 You have entered wrong Access Id or Token");
		 		exit;
			}
	
   	 }
	 else //if postcheck fails
	{
		
		header("HTTP/1.0 400 Invalid parameter - ".$msg."");
		exit;
	}
}
else // if(isset($_REQUEST)) fails
{
		header("HTTP/1.0 400 REQUEST missing");
		exit;
}

//created function for validation
function checkPostParameter($postValue){
	
global $msg;

	if(!ctype_digit($postValue['id'])){
		$msg="Access ID<br/>";
		return false;
	}
	
	if(empty($postValue['token'])){
		$msg="Token Empty<br/>";
		return false;
	}
	
	if(empty($postValue['guideinternalurl'])){
		$msg="Internal site name empty<br/>";
		return false;
	}
	
	if(empty($postValue['guideexternalurl'])){
		$msg="External site name empty<br/>";
		return false;
	}
	
	return true;
}

function externalSiteCreationSteps($internal_url){
	
	$output = mysql_fetch_row($internal_url);
	
	//Insert External URL in our master table
	mysql_query("insert into master(mid,site_url,db_name,db_user,db_password,tpl_folder_name,tpl_menu_folder_name,style_folder_name,partner_folder_name) values('','".strtolower($_REQUEST['guideexternalurl'])."','$output[2]','root','bitnami','$output[5]','$output[6]','v3','$output[8]')");
								
	
	$_REQUEST="";
	header("HTTP/1.0 200 ok - External site created successfully");
	exit;
}

?>