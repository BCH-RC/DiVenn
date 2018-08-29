<?php
$servername = "10.84.2.163";
$username = "divennuser";
$password = "divenn";
$db_name = "divenn_db";

// Create connection
$conn =  mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

//$conn = mysql_connect($servername, $username, $password) or die (mysql_error());
//mysql_select_db($db_name, $conn) or die (mysql_error());

//$dbs = new PDO("mysql:dbname=$db_name;host=$servername", $username,$password);

?>