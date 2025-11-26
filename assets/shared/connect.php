<?php
  $dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$db = "ctrlsave";

	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

	if(!$conn)
	{
		die("Connection Failed. ". mysqli_connect_error());
		echo "can't connect to database";
	}

date_default_timezone_set('Asia/Manila');

  function executeQuery($query){
    $conn = $GLOBALS['conn'];
    return mysqli_query($conn, $query);
  }
?>