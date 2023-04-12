<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database = "energibot";

//$conn = mysqli_connect($hostname, $username, $password, $database) or die("Database connection failed");
  //Create Connection
  $conn = new mysqli($hostname,$username,$password,$database);

  //Check Connection
  if($conn->connect_error)
  {
      echo "MySQL Connection Error";
  }

    define("EMAILID",'noreply.kplc@gmail.com'); //Enter your E-mail
    //define('PASSWORD','Airtel!2345'); //Enter your E-mail Account Password
	define('PASSWORD','cfscvlulhgfdtqjo'); //Enter your E-mail Account Password
	
?>
