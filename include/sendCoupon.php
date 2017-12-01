<?php

	include("constant.php");
	require_once './db_connect.php';
	$db = new DB_CONNECT();	
	$response = array();

if (isset($_POST["user_id"]) && isset($_POST["coupon_id"])) {

    $user_id 		= $_POST["user_id"];
	$coupon_id 		= $_POST["coupon_id"];
	
	date_default_timezone_set('Asia/Kolkata');
	$date = date("Y-m-d H:i:s");
	
	//INSERT INTO USER COUPON TABLE
	$query	= "SELECT * FROM `test`";
	$result = mysql_query($query);
	if($result)
	{
		$row = mysql_fetch_array($result);
		echo $row['id'];
		echo $row['name'];
		echo $row['date'];
		
	}
	
}
?>
<html>
<body>
  <form action="<?php $_PHP_SELF ?>" method="POST">
  user_id: <input type="text" name="user_id" />
  coupon_id: <input type="text" name="coupon_id" />
   
    <input type="submit" />
  </form>
</body>
</html>