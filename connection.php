<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
</head>

<body>

<?php 
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'bikerfi';

// Establish a connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);

// Check the connection
if (!$conn) {
    die('Could not connect: ' . mysqli_connect_error());
}
 'Connected successfully<br>';

// Select the database
if (!mysqli_select_db($conn, $dbname)) {
    die('Could not select database: ' . mysqli_error($conn));
} else {
     'Database selected successfully<br>';
}

// Close the connection (optional; typically used at the end of the script)
// mysqli_close($conn);
?>
</body>
</html>
