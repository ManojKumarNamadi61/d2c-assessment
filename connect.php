<?php
session_start();
$conn=mysqli_connect('localhost','root','','d2c');
if(!$conn)
{
	echo "connection failed";
}
?>