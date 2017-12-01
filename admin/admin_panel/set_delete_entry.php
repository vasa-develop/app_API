<?php
require "functions.php";
$response=array();
$id=$_POST['id'];
$type=$_POST['type'];
if($type=='user')
{
	$query="delete from flauntq_user where id='$id' limit 1";
}
else if($type=='flaunt')
{
	$query="delete from flauntq_user_flaunts where id='$id' limit 1";
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

