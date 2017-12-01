<?php
include 'Send_Mail.php';

$from_name  	= $_POST["from_name"];
$from_email  	= $_POST["from_email"];
$to_name  		= $_POST["to_name"];
$to_email  		= $_POST["to_email"];
$subject		= $_POST["subject"];
$table			= $_POST["data"];

echo $mail = Send_Mail($from_name, $from_email, $to_name, $to_email, $subject, $table);

?>