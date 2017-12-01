<?php
session_start();
unset($_SESSION['login_username']);
unset($_SESSION['login_userid']);
unset($_SESSION['login_userstate']);
unset($_SESSION['login_usercity']);
//if(session_destroy())
{
	header("Location: index.php");
}
?>