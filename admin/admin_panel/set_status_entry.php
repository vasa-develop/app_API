<?php
require "functions.php";
$response=array();
$id=$_POST['id'];
$status_value=$_POST['status_value'];
$type=$_POST['type'];
if($type=='user')
{
	$query="update lokaso_user set status='$status_value' where id='$id' limit 1";
}
else if($type=='request')
{
	$query="update lokaso_ask set status='$status_value' where id='$id' limit 1";
}
else if($type=='response')
{
	$query="update lokaso_ask_response set status='$status_value' where id='$id' limit 1";
}
else if($type=='discovery')
{
	$query="update lokaso_discovery set status='$status_value' where id='$id' limit 1";
}
else if($type=='ads')
{
	$query="update lokaso_ads set status='$status_value' where id='$id' limit 1";
}
else
{
	$query="";
}

if(mysql_query($query))
{
	$query_status="1";
	$query_message="Success";
}
else
{
	$query_status="0";
	$query_message="Failed";
}

$response['query_status']=$query_status;
$response['query_message']=$query_message;

echo json_encode($response);

