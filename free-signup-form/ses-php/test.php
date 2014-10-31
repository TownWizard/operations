<?php

echo "start";

//require "aws.phar";
require "aws-sdk/aws-autoloader.php";

use Aws\Ses\SesClient;

$client = SesClient::factory(array(
                                   'key'    => 'AKIAIXZYQ3DUUDXLQRBA',
                                   'secret' => '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',
                                   'region' => 'us-east-1'
                                   ));


echo "<p>client:";

//Now that you have the client ready, you can build the message
$msg = array();
$msg['Source'] = "operations@townwizard.com";
//ToAddresses must be an array
$msg['Destination']['ToAddresses'][0] = 'bhavan@salzinger.com';
$msg['Destination']['CcAddresses'][0] = 'bhavan_shah@yahoo.com';
$msg['Destination']['CcAddresses'][1] = 'bhavan.shah@gmail.com';

$msg['Message']['Subject']['Data'] = "Text only subject";
$msg['Message']['Subject']['Charset'] = "UTF-8";

$msg['Message']['Body']['Text']['Data'] ="Text data of email";
$msg['Message']['Body']['Text']['Charset'] = "UTF-8";
$msg['Message']['Body']['Html']['Data'] ="HTML Data of email<br />";
$msg['Message']['Body']['Html']['Charset'] = "UTF-8";

try{
    $result = $client->sendEmail($msg);

    //save the MessageId which can be used to track the request
    $msg_id = $result->get('MessageId');
    echo("<p>MessageId: $msg_id");

    //view sample output
    print_r($result);
} catch (Exception $e) {
    //An error happened and the email did not get sent
    echo "<p>*** error *** <p>";
    echo($e->getMessage());
}
//view the original message passed to the SDK
print_r($msg);

echo "<p>done";

?>
