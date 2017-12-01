<?php
include('functions.php');
require_once "../../include/constant.php";

$query1="update lokaso_credit set points='$_POST[credit_Signup]' where name='$TAG_CREDIT_SIGNUP' limit 1";
mysql_query($query1);

$query2="update lokaso_credit set points='$_POST[credit_Attend_Query]' where name='$TAG_CREDIT_ATTEND_QUERY' limit 1";
mysql_query($query2);

$query3="update lokaso_credit set points='$_POST[credit_Like_Res]' where name='$TAG_CREDIT_LIKE_RESPONSE' limit 1";
mysql_query($query3);

$query4="update lokaso_credit set points='$_POST[credit_Create_Suggestion]' where name='$TAG_CREDIT_SUGGESTION' limit 1";
mysql_query($query4);

$query4="update lokaso_credit set points='$_POST[credit_Upload_Pic]' where name='$TAG_CREDIT_SUGGESTION_PICTURE' limit 1";
mysql_query($query4);

$query5="update lokaso_credit set points='$_POST[credit_Like_Pic]' where name='$TAG_CREDIT_LIKE_PICTURE' limit 1";
mysql_query($query5);

$query6="update lokaso_credit set points='$_POST[credit_Invite]' where name='$TAG_CREDIT_USER_INVITE' limit 1";
mysql_query($query6);

$query7="update lokaso_credit set points='$_POST[credit_Ask_Query]' where name='$TAG_CREDIT_ASK_QUERY' limit 1";
mysql_query($query7);

$query8="update lokaso_credit set points='$_POST[credit_Follow]' where name='$TAG_CREDIT_FOLLOW' limit 1";
mysql_query($query8);

$query9="update lokaso_credit set points='$_POST[credit_Signup_Invite_from]' where name='$TAG_CREDIT_SIGNUP_INVITE_FROM' limit 1";
mysql_query($query9);

$query10="update lokaso_credit set points='$_POST[credit_Signup_Invite_to]' where name='$TAG_CREDIT_SIGNUP_INVITE_TO' limit 1";
mysql_query($query10);


echo "success : ".$query2;

mysql_close();

?>