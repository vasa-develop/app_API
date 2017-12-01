<?php
include('functions.php');
$response=array();
$query_status=1;
$query_message='';

$id=(int)$_SESSION['login_userid'];//$_POST['entry_id'];
$current_password=$_POST['current_password'];
$new_password=$_POST['new_password'];
if($id!=0)
{	
	if($new_password!='')
	{		
		$current_password_md5=md5($current_password);
		$new_password_md5=md5($new_password);
		$query1= "SELECT * FROM lokaso_admin where password='$current_password_md5'  ";
		$res1= mysql_query($query1);
		if(mysql_num_rows($res1)>0)
		{
			$row1 = mysql_fetch_array($res1,MYSQL_ASSOC);
			$name=$row1['name'];
			$query2="update lokaso_admin set password='$new_password_md5' where id='$id' ";
			if(mysql_query($query2))
			{
				$query_status=1;
				$query_message='Password change successfully.';
			}
		}
		else
		{
			$query_status=0;
			$query_message='No User found with matching Password.';
		}
	}	
}		

mysql_close();

$response['query_status']=$query_status;
$response['query_message']=$query_message;
echo json_encode($response);
?>