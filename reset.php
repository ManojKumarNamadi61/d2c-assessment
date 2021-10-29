<?php 
include 'connect.php';
if(isset($_POST['reset']))
{
	//reset all the data from the database
	$up1="UPDATE `bookstatus` SET `booknum`='',`remaining`='3' WHERE row=12";
	$up2="UPDATE `bookstatus` SET `booknum`='',`remaining`='7' WHERE row<=11";
	$de1="TRUNCATE TABLE bookdetails;";
	$run1=mysqli_query($conn,$up1);
	$run2=mysqli_query($conn,$up2);
	$run3=mysqli_query($conn,$de1);
	if($run1 && $run2 && $run3)
	{
		header("location:index.php");
	}
}
?>