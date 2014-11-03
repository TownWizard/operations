<?php
require "../ses-php/aws-sdk/aws-autoloader.php";
use Aws\Ses\SesClient;

function sendTwMail($toEmail,$subject, $message, $source){

	$client = SesClient::factory(array(
	                   'key'    => 'AKIAIXZYQ3DUUDXLQRBA',
	                   'secret' => '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',
	                   'region' => 'us-east-1'
	                   ));

	//Now that you have the client ready, you can build the message
	$msg = array();
	$msg['Source'] = $source;

	//ToAddresses must be an array
	$msg['Destination']['ToAddresses'][0] = $toEmail;

	$msg['Message']['Subject']['Data'] = $subject;
	$msg['Message']['Subject']['Charset'] = "UTF-8";

	$msg['Message']['Body']['Html']['Data'] = $message;
	$msg['Message']['Body']['Html']['Charset'] = "UTF-8";


	try{
	    $result = $client->sendEmail($msg);
	    //save the MessageId which can be used to track the request
		$msg_id = $result->get('MessageId');
		return 'SUCCEED';
	} catch (Exception $e) {
	    //An error happened and the email did not get sent
	    //echo "<p>*** error *** <p>";
	    //echo($e->getMessage());
	    return $e->getMessage();
	}
}
?>