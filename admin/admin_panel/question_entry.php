<?php
include('functions.php');

$id=(int)$_POST['entry_id'];

$interest=$_POST['interest'];
$question=$_POST['question'];


$current_time=date('Y-m-d H:i:s');
if($id!=0)
{
	$query="update lokaso_question set name='$question',interest_id='$interest' where id='$id' ";
}
else
{
	$query="INSERT INTO lokaso_question (name,interest_id,created_date) VALUES ('$question','$interest','$current_time') ";	
}
		
if(!mysql_query($query))
{
	//echo mysql_error();
}

mysql_close();
?>