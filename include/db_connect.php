<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

/**
 * A class file to connect to database
 */
class DB_CONNECT {
	
    // constructor
    function __construct() {
        // connecting to database
        $this->connect();
    }
	
    // destructor
    function __destruct() {
        // closing db connection
        $this->close();
    }
	
    /**
     * Function to connect with database
     */
    function connect() {
        //require_once 'db_config.php'; // CORRECT ONE - WORKS BOTH ON LOCALHOST AN SERVER - both files should be in same folder. (Donot use for other files.)
        //require_once './db_config.php';
		require_once 'db_config.php';
		
		// Connecting to mysql database
        $con = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD) or die(mysql_error());
		
        // Selecing database
        $db = mysql_select_db(DB_DATABASE) or die(mysql_error()) or die(mysql_error());
		
        // returing connection cursor
        return $con;
    }
	
    /**
     * Function to close db connection
     */
    function close() {
        // closing db connection
        //mysql_close();
    }
}
 
?>