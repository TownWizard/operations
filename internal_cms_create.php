<?php 
global $msg,$twwebroot,$twwebtmp;
$twwebroot='/twweb';
$twwebtmp='/twweb/tmp'; 



if(isset($_REQUEST))
{
	//Calling Function for Validation 
	$postcheck = checkPostParameter($_REQUEST);
	
	//if true then proceed ahead and if postcheck is false return 400 with the message
	if($postcheck)
	{
			//Connecting with Mysql Server
			$link1 = mysql_connect('localhost', 'root','bitnami');
			
			# Condition to check, if server connection succesfully established or not.
			if (!$link1){
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
							//Checking site url is exist or not
							$check_url = mysql_query("SELECT site_url FROM master WHERE site_url = '".$_REQUEST['guideinternalurl'].".townwizard.com'");
							
							//if no url found in database then proceed ahead
							if(mysql_num_rows($check_url)==0)
							{
								//calling function for creating internal site
								internalSiteCreationSteps();
						 
							}
							else // if url found in database
							{	
								$msg="Site URL exists";
								send_error_email($msg);
								header("HTTP/1.0 400 Invalid parameter - ".$msg."");
								exit;
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
	 else // if($postcheck) fails
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
		$msg="Token empty";
		return false;
	}
	
	if(empty($postValue['guideinternalurl'])){
		$msg="Internal site name empty";
		return false;
	}
	
	if(!filter_var($postValue['email'], FILTER_VALIDATE_EMAIL)){
		$msg="Email address format wrong";
		return false;
	}
	
	if(!ctype_alnum($postValue['guidezipcode'])){ 
		$msg="Location code is not alphanumeric";
		return false;
	}
	
	if(strtolower($postValue['dunit'])!='miles')
	{
		if(strtolower($postValue['dunit'])!='km')
		{ 
			$msg="Distance unit should be 'KM' or 'Miles' ";
			return false;
		}
	}
	
	if(strtolower($postValue['wunit'])!='f')
	{ 
		if(strtolower($postValue['wunit'])!='c')
		{ 		
			$msg="Weather unit should be 'f' for farenhit or 'c' for celsius";
			return false;
		}
	}
	
	if(strtolower($postValue['dformat'])!='mmdd')
	{ 
		if(strtolower($postValue['dformat'])!='ddmm')
		{ 		
			$msg="Date format should be 'mmdd' or 'ddmm'";
			return false;
		}
	}
	
	if($postValue['tformat']!='12')
	{ 
		if($postValue['tformat']!='24')
		{ 		
			$msg="Time format should be '12' or '24'";
			return false;
		}
	}
	
	if(!ctype_alpha($postValue['language'])){
		$msg="Language should be alphabetic letter";
		return false;
	}

	return true;
}

//created function for creating site
function internalSiteCreationSteps()
{
			// adjusting parameter for database
			$_REQUEST['wunit']             = strtolower($_REQUEST['wunit']);
			$_REQUEST['dformat']           = strtolower($_REQUEST['dformat']);
			$_REQUEST['language']          = strtolower($_REQUEST['language']);
			
			
			if($_REQUEST['dunit']=='miles')
			{
				$_REQUEST['dunit'] = ucfirst($_REQUEST['dunit']);
			}
			else{
				$_REQUEST['dunit'] = strtoupper($_REQUEST['dunit']);
			}
			
			if($_REQUEST['wunit']=='f')
			{
				$_REQUEST['wunit'] = 's';
			}
			else{
				$_REQUEST['wunit'] = 'm';
			}
			
			if($_REQUEST['dformat']=='mmdd')
			{
				$_REQUEST['dformat'] = '%m/%d';
			}
			else{
				$_REQUEST['dformat'] = '%d/%m';
			}
			global $twwebtmp;
			// Changing directory to twweb/tmp 
			if(chdir($twwebtmp))
			{
				// remove previous database backup of particular languge
				// and then export database of particular language
				if($_REQUEST[language] == 'english')
				{
					shell_exec('rm masterdefaultv3.sql');
					$res = shell_exec('mysqldump -u root -pbitnami masterdefaultv3 > masterdefaultv3.sql');
					//$res = shell_exec('mysqldump -u root -pbitnami masterdefaultv3 > masterdefaultv3.sql');
					//$rep = exec('mysqldump -u root -pbitnami masterdefaultv3 > masterdefaultv3.sql',$response);
					//print $rep;
					//print_r($response);
					
				}else{
					shell_exec('rm masterdefault'.strtolower($_REQUEST[language]).'v3.sql');
					shell_exec('mysqldump -u root -pbitnami masterdefault'.strtolower($_REQUEST[language]).'v3 > masterdefault'.strtolower($_REQUEST[language]).'v3.sql');  
				}
				
			}
			else{ //Internal server error - could not change path to twweb/tmp";
				
				$msg="Internal server error - could not change path to root/tmp";
				send_error_email($msg);
				header("HTTP/1.0 500 Internal server error - ".$msg."");
		 		exit;
			}
			
			global $msg,$twwebroot;
			//copy masterdefaultv3 images to new partner
			shell_exec('cp -r '.$twwebroot.'/v3/partner/masterdefaultv3 '.$twwebroot.'/v3/partner/'.$_REQUEST[guideinternalurl].'');
			
			
			// Changing directory to images/phocagallery
			if(chdir(''.$twwebroot.'/v3/partner/'.$_REQUEST[guideinternalurl].'/images/phocagallery'))
			{
				shell_exec('rm .htaccess');
			
				//Creating new htaccess file for new partner
				$myFile = ".htaccess";
				$fh = fopen($myFile, 'w');
				
				if($fh){
					$stringData = "
					RewriteEngine On
					# rewrite _T files to thumbnails folder!
					# Bhavan - few changes are made for V2
					#	(1) redirecting to partner specific phocagallery/thumbs
					#	(2) Override is allowed in apache vhost file
					#RewriteRule ^(.*)_t\.(.*)$ /images/phocagallery/thumbs/phoca_thumb_s_$1.$2 [L]
					RewriteRule ^(.*)_t\.(.*)$ /partner/$_REQUEST[guideinternalurl]/images/phocagallery/thumbs/phoca_thumb_s_$1.$2 [L]
					#RewriteRule ^(.*)_t\.jpg$ /images/phocagallery/thumbs/phoca_thumb_s_$1.jpg [L]
					#RewriteRule ^(.*)_t\.png$ /images/phocagallery/thumbs/phoca_thumb_s_$1.png [L]
					#RewriteRule ^(.*)_t\.pjpg$ /images/phocagallery/thumbs/phoca_thumb_s_$1.pjpg [L]
					#RewriteRule ^(.*)_t\.jpeg$ /images/phocagallery/thumbs/phoca_thumb_s_$1.jepg [L]
					#RewriteRule ^(.*)_t\.pjpeg$ /images/phocagallery/thumbs/phoca_thumb_s_$1.pjepg [L]

					# rewrite normal files to large thumbnail
					#RewriteRule ^thumbs\/phoca_thumb_[slm]_(.*) $1 [L]

					#RewriteCond  %{REQUEST_URI} !thumbs\/phoca_thumb_[slm]_(.*) 
					#RewriteRule ^(.*)$ /images/phocagallery/thumbs/phoca_thumb_l_$1 [L]

					RewriteCond  %{REQUEST_URI} !thumbs\/phoca_thumb_[slm]_(.*)
					RewriteRule ^(.*)$ thumbs/phoca_thumb_l_$1 [L]";
					
					if(fwrite($fh, $stringData))
					{
						fclose($fh);
					}
					else{ // can't write images/phocagallery/.htaccess file
						$msg="Internal server error - can't write .htaccess file";
						send_error_email($msg);
						header("HTTP/1.0 500 ".$msg."");
		 				exit;
					}
				}
				else{ // path:partner/[guideinternalurl]/images/phocagallery
						$msg="Internal server error - can't open .htaccess file";
						send_error_email($msg);
						header("HTTP/1.0 500 ".$msg."");
		 				exit;
				}
				
				// Changing directory to images/phocagallery/image_uploader
				if(chdir('image_uploader'))
				{
						shell_exec('rm .htaccess');
						
						$myFile = ".htaccess";
						$fh = fopen($myFile, 'w');
						
						if($fh){
							$stringData = "
							RewriteEngine On
							# rewrite _T files to thumbnails folder!
							# Bhavan - few changes are made for V2
							#       (1) redirecting to partner specific phocagallery/image_uploader/thumbs
							#       (2) Override is allowed in apache vhost file 
							#RewriteRule ^(.*)_t\.(.*)$ /images/phocagallery/image_uploader/thumbs/phoca_thumb_s_$1.$2 [L]
							RewriteRule ^(.*)_t\.(.*)$ /partner/$_REQUEST[guideinternalurl]/images/phocagallery/image_uploader/thumbs/phoca_thumb_s_$1.$2 [L]

							RewriteCond %{HTTP_USER_AGENT} thumbs! [NC]
							RewriteCond  %{REQUEST_URI} !thumbs\/phoca_thumb_[slm]_(.*)
							RewriteRule ^(.*)$ thumbs/phoca_thumb_l_$1 [L]";
						
							if(fwrite($fh, $stringData))
							{
								fclose($fh);
							}
							else{ // can't write images/phocagallery/image_uploader/.htaccess file
								$msg="Internal server error - can't write .htaccess file";
								send_error_email($msg);
								header("HTTP/1.0 500 ".$msg."");
				 				exit;
							}
						}
						else{ // path:partner/[guideinternalurl]/images/image_uploader
							$msg="Internal server error - can't open .htaccess file";
							send_error_email($msg);
							header("HTTP/1.0 500 Can't open .htaccess file");
			 				exit;
						}
						
						global $twwebtmp;
						// Changing directory to root/tmp
						if(chdir($twwebtmp))
						{
								//calling function for database related updates
								databaseInsertSteps();
						}
						else{
							//echo "HTTP/1.0 500 Internal server error - could not change path to root/tmp";
							$msg="Internal server error - could not change path to root/tmp";
							send_error_email($msg);
							header("HTTP/1.0 500 Internal server error - ".$msg."");
					 		exit;
						}
				}
				else{ // Internal server error - could not change path to parnter/images/phocagallery/image_uploader";
					$msg="Internal server error - could not change path to parnter/images/phocagallery/image_uploader";
					send_error_email($msg);
					header("HTTP/1.0 500 Internal server error - ".$msg."");
					exit;
				}
			}
			else{ //Internal server error - could not change path to parnter/images/phocagallery";
				
				$msg="Internal server error - could not change path to parnter/images/phocagallery";
				send_error_email($msg);
				header("HTTP/1.0 500 Internal server error - ".$msg."");
		 		exit;
			}
				
			
			
}

//created function for database related updates
function databaseInsertsteps(){
	
	//connecting to mysql
	$link = mysql_connect('localhost', 'root', 'bitnami');
	
	# Condition to check, if server connection succesfully established or not.
	if (!$link){
		$msg="Could not connect Mysql";
		send_error_email($msg);
		header("HTTP/1.0 402 Could not connect Mysql - ".$msg."");
 		exit;
	}
				
	//creating new database of new partner
	$new_db_name = str_replace( array( '-', '.','!','>','<','#'),'',strtolower($_REQUEST['guideinternalurl']));
	
	$create_db = mysql_query("create database `".$new_db_name."`");
		
	if (!$create_db){
		$msg="Could not create database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not create database - ".$msg."");
 		exit;
	}		
	//selecting new parnter database
	$select_db = mysql_select_db($new_db_name);
	
	if (!$select_db){
		$msg="Could not select database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not select database - ".$msg."");
 		exit;
	}
						       
	//Import database into partner's database
	if($_REQUEST[language] == 'english')
	{
		shell_exec('mysql -u root -pbitnami '.$new_db_name.' < masterdefaultv3.sql');  
	}
	else{
		shell_exec('mysql -u root -pbitnami '.$new_db_name.' < masterdefault'.strtolower($_REQUEST[language]).'v3.sql');  
	}
				
	//updating page global table
	$query_output1 = mysql_query("UPDATE jos_pageglobal SET site_name ='".ucfirst($_REQUEST[guideinternalurl])."', email ='".$_REQUEST[email]."', googgle_map_api_keys ='', location_code ='".$_REQUEST[guidezipcode]."', beach ='".ucfirst($_REQUEST[guideinternalurl])."', photo_mini_slider_cat ='Events', photo_upload_cat ='Events', facebook ='', iphone ='http://itunes.apple.com/us/app/townwizard/id507216232?mt=8&uo=4', android ='', Header_color='#00BAE8', distance_unit ='".$_REQUEST[dunit]."', weather_unit ='".$_REQUEST[wunit]."', twitter ='',date_format ='".$_REQUEST[dformat]."',time_format ='".$_REQUEST[tformat]."', youtube ='',time_zone ='".$_REQUEST[timezone]."' WHERE id='1'");	
		
	if (!$query_output1){
		$msg="Could not update jos_pageglobal table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update jos_pageglobal table of database - ".$msg."");
 		exit;
	}
		
	//updating com-shines parameters
	$query_output2 = mysql_query("UPDATE jos_components SET `params`= 'phocaimagecat=1 
				phocavideocat=2 
				phocauser=65 
				todayarticle= 
				aboutarticle=
				townname=".ucfirst($_REQUEST[guideinternalurl])." 
				zip=".$_REQUEST[guidezipcode]." 
				email=".$_REQUEST[email]."' 
				WHERE `jos_components`.`id` =41");

	if (!$query_output2){
		$msg="Could not update com_shines table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update com_shines table of database - ".$msg."");
 		exit;
	}
	
	//calling for function for getting longitude and latitude
	$val = getLnt($_REQUEST[guidezipcode]);
		 	
	//updating jevlocation parameters
	$query_output3 = mysql_query("UPDATE jos_components SET `params` = 'loc_own=19
				max_art=5
				loc_global=19
				selectfromall=0
				commondefault=1
				showimage=1
				upimageslevel=24
				maxupload=1000000
				imagew=420
				imageh=268
				thumbw=120
				thumbh=90
				no_thumbanil=1500000
				googlemaps=http://maps.google.com
				googlemapskey=
				redirecttodirections=0
				long=$val[lng]
				lat=$val[lat]
				zoom=12
				showmap=0
				showfilters=0
				deforder=1
				template=locations.xml
				custinlist=0
				usecats=0
				importlocations=0
				showpriority=0
				locadmin=62
				anonselect=1
				anoncreate=0
				anonpublished=0
				notifysubject=New Anonymous Location Submission
				notifymessage=
				hybrid=0
				googledomain1=
				googledomainurl1=
				googledomainkey1=
				googledomain2=
				googledomainurl2=
				googledomainkey2=
				googledomain3=
				googledomainurl3=
				googledomainkey3=
				ignorefiltermodule=0
				modlatest_inccss=1
				layout=
				modlatest_useLocalParam=1
				modlatest_CustFmtStr=
				modlatest_MaxEvents=10
				modlatest_Mode=3
				modlatest_Days=30
				modlatest_NoRepeat=0
				modlatest_DispLinks=1
				modlatest_DispYear=0
				modlatest_DisDateStyle=0
				modlatest_DisTitleStyle=0
				modlatest_LinkToCal=0
				modlatest_LinkCloaking=0
				modlatest_SortReverse=0'
				WHERE `jos_components`.`id` =43");										

	if (!$query_output3){
		$msg="Could not update jos_jev_location table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update jos_jev_location table of database - ".$msg."");
 		exit;
	}
	
	//Changing partner folder name for media manager
	$query_output4 = mysql_query("UPDATE jos_components SET `params`= 'upload_extensions=bmp,csv,doc,epg,gif,ico,jpg,odg,odp,ods,odt,pdf,png,ppt,swf,txt,xcf,xls,BMP,CSV,DOC,EPG,GIF,ICO,JPG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS,zip
				upload_maxsize=10000000
				file_path=partner/".$_REQUEST[guideinternalurl]."/images
				image_path=partner/".$_REQUEST[guideinternalurl]."/images/stories
				restrict_uploads=1
				allowed_media_usergroup=3
				check_mime=1
				image_extensions=bmp,gif,jpg,png
				ignore_extensions=
				upload_mime=image/jpeg,image/gif,image/png,image/bmp,application/x-shockwave-flash,application/msword,application/excel,application/pdf,application/powerpoint,text/plain,application/x-zip
				upload_mime_illegal=text/html
				enable_flash=0' 
				WHERE `jos_components`.`id` =19");

	if (!$query_output4){
		$msg="Could not update media manager table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update media manager table of database - ".$msg."");
 		exit;
	}
	
	//Updating rsform table for changing email id
	$query_output5 = mysql_query("UPDATE jos_rsform_forms SET AdminEmailTo='".$_REQUEST[email]."'");


	if (!$query_output5){
		$msg="Could not update jos_rsforms table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update jos_rsforms table of database - ".$msg."");
 		exit;
	}
	
	//updating partner folder name in JCE Editor
	$query_output6 = mysql_query("UPDATE jos_jce_groups SET `params`= 'editor_width=
				editor_height=
				editor_theme_advanced_toolbar_location=top
				editor_theme_advanced_toolbar_align=center
				editor_skin=default
				editor_skin_variant=default
				editor_inlinepopups_skin=clearlooks2
				advcode_toggle=1
				advcode_editor_state=1
				advcode_toggle_text=[show/hide]
				editor_relative_urls=0
				editor_invalid_elements=
				editor_extended_elements=
				editor_event_elements=a,img
				code_allow_javascript=0
				code_allow_css=0
				code_allow_php=0
				code_cdata=1
				editor_theme_advanced_blockformats=h3,h4
				editor_theme_advanced_fonts_add=
				editor_theme_advanced_fonts_remove=
				editor_theme_advanced_font_sizes=8pt,10pt,12pt,14pt,18pt,24pt,36pt
				editor_dir=partner/".$_REQUEST[guideinternalurl]."/images/stories
				editor_max_size=1024
				editor_upload_conflict=
				editor_preview_height=550
				editor_preview_width=750
				editor_custom_colors=
				browser_dir=
				browser_max_size=
				browser_extensions=xml=xml;html=htm,html;word=doc,docx;powerpoint=ppt;excel=xls;text=txt,rtf;image=gif,jpeg,jpg,png;acrobat=pdf;archive=zip,tar,gz;flash=swf;winrar=rar;quicktime=mov,mp4,qt;windowsmedia=wmv,asx,asf,avi;audio=wav,mp3,aiff;openoffice=odt,odg,odp,ods,odf
				browser_extensions_viewable=html,htm,doc,docx,ppt,rtf,xls,txt,gif,jpeg,jpg,png,pdf,swf,mov,mpeg,mpg,avi,asf,asx,dcr,flv,wmv,wav,mp3
				browser_upload=1
				browser_upload_conflict=
				browser_folder_new=1
				browser_folder_delete=1
				browser_folder_rename=1
				browser_file_delete=1
				browser_file_rename=1
				browser_file_move=1
				media_use_script=0
				media_strict=1
				media_version_flash=9,0,124,0
				media_version_windowsmedia=5,1,52,701
				media_version_quicktime=6,0,2,0
				media_version_realmedia=7,0,0,0
				media_version_shockwave=11,0,0,458
				paste_dialog_width=450
				paste_dialog_height=400
				paste_strip_class_attributes=all
				paste_remove_spans=1
				paste_retain_style_properties=
				paste_remove_styles=1
				paste_remove_empty_paragraphs=1
				paste_remove_styles_if_webkit=0
				spellchecker_engine=googlespell
				spellchecker_languages=English=en
				spellchecker_pspell_mode=PSPELL_FAST
				spellchecker_pspell_spelling=
				spellchecker_pspell_jargon=
				spellchecker_pspell_encoding=
				spellchecker_pspellshell_aspell=/usr/bin/aspell
				spellchecker_pspellshell_tmp=/tmp' 
				WHERE name ='All Users'");
				
	if (!$query_output6){
		$msg="Could not update jce editor table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update jce editor table of database - ".$msg."");
 		exit;
	}

        //Updating email and pass for admin user
        if(isset($_REQUEST['password']) AND $_REQUEST['password']!=''){
            $password = $_REQUEST['password'];
            $query_output7 = mysql_query("UPDATE jos_users SET `username` = '".$_REQUEST[email]."',email='".$_REQUEST[email]."',password='".md5($password)."' WHERE id=62");
        }else{
            $query_output7 = mysql_query("UPDATE jos_users SET `username` = '".$_REQUEST[email]."',email='".$_REQUEST[email]."',password='".md5($_REQUEST[guideinternalurl].'123')."' WHERE id=62");
        }
        
	if (!$query_output7){
		$msg="Could not update user table of database `".$new_db_name."`";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not update user table of database - ".$msg."");
 		exit;
	}

	//selecting master db
	$selct_master = mysql_select_db("master");
	
	if (!$selct_master){
		$msg="Could not select master database";
		send_error_email($msg);
		header("HTTP/1.0 400 Could not select master database - ".$msg."");
 		exit;
	}

	//Insert new partner in our master table
	if($_REQUEST[language] == 'english')
	{
		if($_REQUEST['id']=='2'){
			$insert_for_eng = mysql_query("insert into master(mid,site_url,db_name,db_user,db_password,tpl_folder_name,partner_type,style_folder_name,partner_folder_name) values('','".$_REQUEST[guideinternalurl].".townwizard.com','".$new_db_name."','root','bitnami','default','free','v3','".$_REQUEST[guideinternalurl]."')");
		}else{
			$insert_for_eng = mysql_query("insert into master(mid,site_url,db_name,db_user,db_password,tpl_folder_name,partner_type,style_folder_name,partner_folder_name) values('','".$_REQUEST[guideinternalurl].".townwizard.com','".$new_db_name."','root','bitnami','default','paid','v3','".$_REQUEST[guideinternalurl]."')");
		}
		if (!$insert_for_eng){
			$msg="Could not insert into master table";
			send_error_email($msg);
			header("HTTP/1.0 400 Could not insert into master table - ".$msg."");
			exit;
		}
		
	}
	else{
		if($_REQUEST['id']=='2'){
			$insert_for_other = mysql_query("insert into master(mid,site_url,db_name,db_user,db_password,tpl_folder_name,partner_type,style_folder_name,partner_folder_name) values('','".$_REQUEST[guideinternalurl].".townwizard.com','".$new_db_name."','root','bitnami','default".strtolower($_REQUEST[language])."','free','v3','".$_REQUEST[guideinternalurl]."')");
		}else{
			$insert_for_other = mysql_query("insert into master(mid,site_url,db_name,db_user,db_password,tpl_folder_name,partner_type,style_folder_name,partner_folder_name) values('','".$_REQUEST[guideinternalurl].".townwizard.com','".$new_db_name."','root','bitnami','default".strtolower($_REQUEST[language])."','paid','v3','".$_REQUEST[guideinternalurl]."')");
		}
		if (!$insert_for_other){
			$msg="Could not insert into master table";
			send_error_email($msg);
			header("HTTP/1.0 400 Could not insert into master table - ".$msg."");
	 		exit;
		}
	}
	
	$msg="Internal site created successfully";
	send_success_email($msg);
    header("HTTP/1.0 200 ok - ".$msg."");
    exit;
}

function send_error_email($msg)
{
	$to = "operations@townwizard.com". ", ";
	$to .= "support@townwizard.com";
	$subject = "".$_REQUEST['guideinternalurl']. "-internal site creation failed";
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
	$to = "operations@townwizard.com". ", ";
	$to .= "support@townwizard.com";
	$subject = "".$_REQUEST['guideinternalurl']. "-internal site creation succeed";
	$message = "<div>".$msg." for guide ".$_REQUEST['guideinternalurl']."<br/><br/>Thanks!</div>";
	$from = "Townwizard-Operations";
	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type:text/html;charset=iso-8859-1' . "\r\n";	
	$headers .= "From:" . $from;
	mail($to,$subject,$message,$headers);
	return TRUE;
}

// Getting latitude and longitude from zip code
function getLnt($zip){
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=
".urlencode($zip)."&sensor=false";
$result_string = file_get_contents($url);
$result = json_decode($result_string, true);
$result1[]=$result['results'][0];
$result2[]=$result1[0]['geometry'];
$result3[]=$result2[0]['location'];
return $result3[0];
}


?>
