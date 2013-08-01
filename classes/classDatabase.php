<?php
/*
- Constructor - The constructor of the class bears the same name as the class and is called when a new instance of the class is created.
In our constructor, we will make the connection to the database server and select the proper database. The constructor will accept a database name,
username, and password as parameters. 
- query Function - This function will be named query and we will be used to run any SQL statement given to it. It will return a resource id.
This function will be used by other functions of the class and it will also be called from outside the class. 
- fetch Function - This function, named fetch, will accept a string containing a SQL statement and will return an array containing the results from the query. 
-  getone Function - The getone function will return the value of the first column on the first row return by the SQL statement passed to it.
If the SQL statement does not yield any results, it will return FALSE. 
*/

abstract class DB_Class {  

    var $conn;
	var $host = 'localhost';
	var $dbase = 'styluscs_guestbook';
	var $user = 'styluscs_david';
	var $pass = 'kfAGZu3r';

	var $serverTZ = "Africa/Johannesburg"; // the servers timezone
	var $localTZ = "Africa/Johannesburg"; // the clients timezone
	
	var $email_address = 'david@stylus.co.za';
	var $product_name = 'Cape Town Surfski 2013';		
	
	// all the tables
	public $tableEvents = 'ctsEvents';
	public $tableSponsors = 'ctsSponsors';
	public $tableResults = 'ctsResults';
	public $tableLog = 'ctsLog';
	
    function __construct()
	{ 
		date_default_timezone_set('Africa/Johannesburg');
		$this->conn = mysql_connect ($this->host, $this->user, $this->pass) 
            or die ("Unable to connect to Database Server"); 

        mysql_select_db ($this->dbase, $this->conn) 
            or die ("Could not select database");
    } 

    function query($sql)
	{ 
        $result = mysql_query ($sql, $this->conn) or die ("Invalid query: ".$sql.' '. mysql_error()); 
        return $result; 
    } 

    // return the ID of the new record
    function queryNew($sql)
	{ 
        $result = mysql_query ($sql, $this->conn) or die ("Invalid query: ".$sql.' '. mysql_error()); 
        if($result)
		{
			return mysql_insert_id();
		}
		return false;
    } 
	
    function fetch($sql) { 
        $data = array(); 
        $result = $this->query($sql);
        while($row = mysql_fetch_assoc($result)) { 
            $data[] = $row; 
        } 
        return $data; 
    } 

    function getone($sql) { 
        $result = $this->query($sql); 
        if(mysql_num_rows($result) == 0) 
            $value = FALSE; 
        else 
            $value = mysql_result($result, 0); 
        return $value; 
    } 
	
	function deleteone($sql)
	{
		$result = $this->query($sql);
	}
}
?>
