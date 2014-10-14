<?php
ini_set("display_errors",1);

# DEFINE Partner Value in variable
/*$accountName	= '<![CDATA[Yogi %26 Ghorecha Co.]]>';
$firstName		= 'Yogi';
$lastName		= 'Ghorecha';
$email			= 'yogi@yogiinfo.com';
$subject		= 'Yogiinfo';
$signupDate		= '12/9/2014'; // Format 8/8/2014
$zipcode		= '77477';
$contactName	= 'Yogi Ghorecha';*/

$accountName	= '<![CDATA['.$data['first_name'].' '.$data['last_name'].' %26 Co.]]>';
$firstName		= $data['first_name'];
$lastName		= $data['last_name'];
$email			= $data['email'];
$subject		= $data['guide_name'];
$signupDate		= date("m/d/Y", strtotime("now"));  // Format 8/8/2014
$zipcode		= $data['zip'];
$contactName	= $data['first_name']." ".$data['last_name'];


# ***** ACCOUNT MODULE ***** CURL Process
setCurlParameter('Accounts'); // I created this function
$xml = '<Accounts><row no="1"><FL val="Account Name">'.$accountName.'</FL></row></Accounts>';
$query = 'newFormat=2&authtoken=fd05fe57d221aba08aa657f9699fa0ce&scope=crmapi&xmlData='.$xml;
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);// Set the request as a POST FIELD for curl.
$result = curl_exec($ch); // Execute cUrl session
curl_close($ch);
//echo "<b>New Partner Account Added!</b><br>";
//echo $result;
//echo "<br><br>";
# $partnerCreation will SET TRUE if data inserted successfully or FALSE if Failed to insert
$partnerCreation = zohoInsertResponse($result);

# ***** CONTACT MODULE ***** CURL Process 
setCurlParameter('Contacts'); // I created this function
$xml = '<Contacts><row no="1">
<FL val="First Name">'.$firstName.'</FL>
<FL val="Last Name">'.$lastName.'</FL>
<FL val="Email">'.$email.'</FL>
<FL val="Guide Status">Not Terminated</FL>
<FL val="Account Name">'.$accountName.'</FL>
<FL val="Product">Free</FL>
</row></Contacts>';
$query = 'newFormat=2&authtoken=fd05fe57d221aba08aa657f9699fa0ce&scope=crmapi&xmlData='.$xml;
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);// Set the request as a POST FIELD for curl.
$result = curl_exec($ch); //Execute cUrl session
curl_close($ch);
//echo "<b>New Contact Added!</b><br>";
//echo $result;
//echo "<br><br>";
# $contactCreation will SET TRUE if data inserted successfully or FALSE if Failed to insert
$contactCreation = zohoInsertResponse($result);


# ***** SALES ORDERS MODULE ***** CURL Process 
setCurlParameter('SalesOrders'); // I created this functioni
$xml = '<SalesOrders><row no="1">
<FL val="Account Name">'.$accountName.'</FL>
<FL val="Product">Free</FL>
<FL val="Guide Deployment Status">CMS Ready</FL>
<FL val="Subject">'.$subject.'</FL>
<FL val="Guide Signup Date">'.$signupDate.'</FL>
<FL val="Guide City Zip">'.$zipcode.'</FL>
<FL val="Contact Name">'.$contactName.'</FL>
<FL val="Billing Status">Free</FL>
<FL val="Product Details">
<product no="1">
<FL val="Product Id">760086000001604083</FL>
<FL val="Product Name">Free</FL>
<FL val="Unit Price">0</FL>
<FL val="Quantity">1</FL>
<FL val="Total">0</FL>
<FL val="Discount">0</FL>
<FL val="Total After Discount">0</FL>
<FL val="List Price">0</FL>
<FL val="Net Total">0</FL>
</product>
</FL>
</row></SalesOrders>';
$query = 'newFormat=2&authtoken=fd05fe57d221aba08aa657f9699fa0ce&scope=crmapi&xmlData='.$xml;
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);// Set the request as a POST FIELD for curl.
$result = curl_exec($ch); //Execute cUrl session
curl_close($ch);
//echo "<b>New Partner Guide Added!<br></b>";
//echo $result;
//echo "<br><br>";
# $guideCreation will SET TRUE if data inserted successfully or FALSE if Failed to insert
$guideCreation = zohoInsertResponse($result);



/**
* @param Module Name to initiate CURL Process $module
* @param Value of module parameter $value
*/
function setCurlParameter($module,$value = NULL){
	global $ch;
	$ch = curl_init('https://crm.zoho.com/crm/private/xml/'.$module.'/insertRecords?');
	curl_setopt($ch, CURLOPT_VERBOSE, 1);//standard i/o streams
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// Turn off the server and peer verification
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//Set to return data to string ($response)
	curl_setopt($ch, CURLOPT_POST, 1);//Regular post
}

/**
* @param $result : Result from Zoho
* $Return : True if data inserted False if Failed 
*/
function zohoInsertResponse($result){
	if(strpos($result,'Record(s) added successfully') !== false) {
    	return TRUE;
	}else{
		return $result;
	}
}

?>