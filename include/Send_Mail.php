<?php

function Send_Mail($fromname,$from,$toname,$to,$subject,$body)
{
	// Set POST variables
	$url = 'https://api.sendgrid.com/api/mail.send.json';
	$api_user = "vishalvandrocid";
	$api_key = "jYh34v!4@ood";

	$fields = array(
	'api_user' => $api_user,
	'api_key' => $api_key,
	'to' => $to,
	'toname' => $toname,
	'fromname' => $fromname,
	'subject' => $subject,
	'html' => $body,
	'from' => $from,
	);

	//return $fields;
	
	
	// Open connection
	$ch = curl_init();

	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_POST, true);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Disabling SSL Certificate support temporarly
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

	// Execute post
	$result = curl_exec($ch);
	if ($result === FALSE) 
	{
		die('Curl failed: ' . curl_error($ch));
	}

	// Close connection
	curl_close($ch);
	return $result;
	
}

?>
