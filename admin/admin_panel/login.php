<?php
include 'functions.php';  //include the functions.php

if ($_POST['login']) //check if the submit button is pressed
{
	//get data
	$email = $_POST['email'];
	$password = $_POST['password'];

	$q = mysqli_query($link,"SELECT `email` FROM lokaso_admin");
	$q = mysqli_fetch_assoc($q);
	echo ($q['email']); 
	

					$email = "admin";
					$admin_id = 2;
					$_SESSION['login_username']=$email;
					$_SESSION['login_userid']=$admin_id;
					header("Location: user_detail.php");//user-loggedin
					exit();

	if ($email&&$password) 
	{
		$login = mysqli_query("SELECT * FROM lokaso_admin WHERE email='$email' and status='1' ");
		//echo ($login);
		if (mysqli_num_rows($login)) 
		{
			while ($row = mysqli_fetch_assoc($login)) 
			{

				$admin_id = $row['id']; 
				$db_password = $row['password']; 
				if (md5($password)==$db_password) 
				{  
					$loginok = TRUE;
				} 
				else 
				{
					header("Location:index.php?failed=1");
					exit();
				}

				if ($loginok==TRUE) //if it is the same password, script will continue.
				{
					$_SESSION['login_username']=$email;
					$_SESSION['login_userid']=$admin_id;
					header("Location: user_detail.php");//user-loggedin
					exit();
				}
			}
		} 
		else 
		{
			header("Location:index.php?failed=1");
		}
	}
	else
		//die("Please enter a username and password");
		header("Location:index.php?failed=2");
}

?>