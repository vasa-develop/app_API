<?php

if(isset($_POST["user_name"]) && isset($_POST["password"]))
{	
	include("constant.php");
	
	
	$user_name = $_POST["user_name"];
	$password = $_POST["password"];
	
	
	// type = 1 -> table i_user
	// type = 2 -> table i_group
	
	require_once 'db_connect.php';
	$db = new DB_CONNECT();
	if($DB_TRUNCATE_CRED_USERNAME==$user_name && $DB_TRUNCATE_CRED_PASSWORD==$password) {
				
		$delete_all = false;
		
		if($delete_all) {
				
			$query	= " truncate table $TABLE_USER_FOLLOW ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_SPAM ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_USER_SPAM ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_RESPONSE ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_RESPONSE_ACTION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_RESPONSE_COMMENT ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ASK_RESPONSE_SPAM ";
			$result = mysql_query($query);
			
			
			$query	= " truncate table $TABLE_CHAT_ROOM ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_CONVERSATION ";
			$result = mysql_query($query);
			
			
			$query	= " truncate table $TABLE_SUGGESTION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_SUGGESTION_COMMENT ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_SUGGESTION_FAV ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_SUGGESTION_SPAM ";
			$result = mysql_query($query);
			
			
			$query	= " truncate table $TABLE_USER_ACTION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_CREDITS ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_SUGGESTION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_FOLLOW ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_INTEREST ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_PIC ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_USER_QUERY ";
			$result = mysql_query($query);
			
			
			$query	= " truncate table $TABLE_NOTIFICATION ";
			$result = mysql_query($query);
			
						
			// THESE ARE NOT USED			
			$query	= " truncate table $TABLE_QUESTION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_ANSWER ";
			$result = mysql_query($query);
			
			
			
			
			// DATA NOT TO BE CLEARED OTHERWISE
			//$query	= " truncate table $TABLE_USER ";
			//$result = mysql_query($query);
			
			//$query	= " truncate table $TABLE_GCM ";
			//$result = mysql_query($query);
			
			
			
			// TABLES NOT TO BE CLEARED
			// OR THESE SHOULD BE PREFILLED WHEN DATABASE IS SETUP
			/*			
			$query	= " truncate table $TABLE_CREDIT ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_PROFESSION ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_TYPE ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_INTEREST ";
			$result = mysql_query($query);
			
			$query	= " truncate table $TABLE_SETTING ";
			$result = mysql_query($query);
			*/
			
			
			echo "EVERYTHING GOT DELETED .. !!";
			
		}
		else {
			echo "You got fooled. Nothing got deleted. If you are tech guy, you know how to do it..!!";
		}
		
				
	}
	else {
		
		echo "TRYING TO HACK..?? ";
	}
}

?>

<html>
<body>
  
  <h2>TRUNCATE DATABASE<br></h2>
  <h4>This is to truncate the data from database. Be careful before doing this, as once done it cannot be reverted back.<br><br></h4>
  
  <form action="<?php $_PHP_SELF ?>" method="POST">
  
  user_name: <input type="text" name="user_name" />
  password: <input type="password" name="password" />
  <input type="submit" />
  </form>
</body>
</html>
