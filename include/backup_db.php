<?php

 
		//require 'db_connect.php';
		//$db = new DB_CONNECT();
		
$tableName  = 'lokaso_user';

//$target_path = 'C:\wamp\www\lokaso_web\include\\';
$target_path = '';
$backupFile = $target_path.'lokaso_dbb.sql';
//$query      = "SELECT * INTO OUTFILE '$backupFile' FROM $tableName";

/*
$query      = "SELECT * from $tableName";
$result = mysql_query($query);

if($result) {
	echo "suc";
}
else {	
	echo "fail";
}
 */
 
 
		require 'db_config.php';

	
	$dbname = DB_DATABASE;
	$dbpass = DB_PASSWORD;
	$dbuser = DB_USER;
	$dbhost = DB_SERVER;
	//$dbhost = "43.225.53.176";//DB_SERVER;
	
$backupFile = $target_path.$dbname.date("Y-m-d-H-i-s").'.sql';
//$command = "C:\wamp\bin\mysql\mysql5.5.20\bin\mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname  > $backupFile";
$command = "mysqldump --opt -h $dbhost -u $dbuser -p $dbpass $dbname  > $backupFile";
//$command = "C:\wamp\bin\mysql\mysql5.5.20\bin\mysqldump -h $dbhost -u $dbuser $dbname  > $backupFile";

	echo "command ".$command;
//system($command);
//echo "resp : ".system($command);

$returnv = -1;
$output = system($command, $returnv);
if($output !== false)
{
    echo "Return value is: " . $returnv . "\r\n";
    echo "Output is:\r\n" . $output . "\r\n";
}
 
 
?>