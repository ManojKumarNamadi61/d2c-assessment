<?php 
include 'connect.php';   //importing sql connection file
$c1="SELECT * FROM bookstatus";
$run=mysqli_query($conn,$c1);
$booked=array(); //Set a array to store booked seats
$remain=array(); //set a array to store remain seats in a row
if($run)
{
	while($res=$run->fetch_assoc()) //running in loop
	{
		array_push($remain,$res['remaining']);  //adding the remaining seats in remain array
		$str=trim($res['booknum']); //returning booked numbers in a row to string
		if(strlen($str)>0)
		{
			$tmp=explode(' ',$str); //splitting the string into array
			$booked=array_merge($booked,$tmp); //merged the tmp array with booked array
		}
	}
}

if(isset($_POST['submit'])) //onclick book
{
	$count=$_POST['tcount']; //count from user
	$name=$_POST['name'];  //name from user
	$email=$_POST['email']; //email from user
	if($count>7 || $count<0)    //check the ticket count is between 1-7 only
		$error="Ticket count will be 1-7 only"; //otherwise through a error
	elseif(array_sum($remain)<$count)
	{
		$error="No tickets. We will meet in next trip";    //check the seats available to book or not
	}
	else
	{
		$bookingarr=array();   //array to save booking seat numbers
		$rowarr=array();       //array to save the in which row the seat is booked
		
		//loop the remaining seat count from the remain array, if the user ticket count is equal to or less than the 
		//seats from the remaining seats available in the row. Book the row.
		if(count($bookingarr)==0)   
		{
			for($key=0;$key<count($remain);$key++)
			{
				if($remain[$key]==0) //if the row is fully booked skip the loop
					continue;
				if($remain[$key]>=$count) //check the ticket count is less than or equal to remaining seats in array
				{
					for($x=$key*7+1;$x<=($key+1)*7;$x++)
					{
						if(!in_array($x, $booked))    //book the seat numbers which are not booked or available in the already booked array in that row
						{
							array_push($bookingarr,$x);   //push the seat number
							array_push($rowarr,$key+1);   //push the row number
						}

						if(count($bookingarr)==$count || $x>80)   //if ticket count is completed or bookingarr count is equal to user ticket count. Break loop
							break;
					}
				}
				if(count($bookingarr)==$count) //if ticket count is completed or bookingarr count is equal to user ticket count. Break loop
					break;
			}
		}

		// if first case not passed.
		// loop the remaining seats from the remain array, if the any of the next two row is available to book to create the ticket count.
		//Book that row.
		if(count($bookingarr)==0)
		{
			for($key=0;$key<count($remain)-1;$key++)
			{
				if($remain[$key]==0) //check the ticket count is less than or equal to remaining seats in array
					continue;
				if($remain[$key]+$remain[$key+1]>=$count) //check the current row and next row is sufficent to book the tickets
				{
					for($x=$key*7+1;$x<=($key+1)*7;$x++)
					{
						if(!in_array($x, $booked)) //book the seat numbers which are not booked or available in the already booked array in that row
						{
							array_push($bookingarr,$x); //push the seat number
							array_push($rowarr,$key+1); //push the row number
						}

						if(count($bookingarr)==$count || $x>80) //if ticket count is completed or bookingarr count is equal to user ticket count. Break loop
							break;
					}
				}
				if(count($bookingarr)==$count) //if ticket count is completed or bookingarr count is equal to user ticket count. Break loop
					break;
			}
		}
		//if all the above the cases are not possible.
		// then users the tickets from the vacant rows.

		if(count($bookingarr)==0)
		{
			for($key=0;$key<=11;$key++)
			{
				if($remain[$key]==0)
					continue;
				for($x=$key*7+1;$x<=($key+1)*7;$x++)
				{
					if(!in_array($x, $booked))
					{
						array_push($bookingarr,$x);
						array_push($rowarr,$key+1);
					}

					if(count($bookingarr)==$count || $x>80)
						break;
				}
				if(count($bookingarr)==$count)
					break;
			}
		}
		//DATA INSERTION IN DATABASE
		
		
		$bookedString=""; //current booked seats in a string
		foreach($bookingarr as $b)
			$bookedString=$bookedString.strval($b)." ";  //save all the seats in a string 
		$query="INSERT INTO bookdetails VALUES ('$count','$name','$email','$bookedString');";  //insert the name, count of seats, email, booked seats.

		
		//print_r($rowarr);
		for($x=0;$x<count($rowarr);$x++) //loop the rowarr to update the booked details in bookingstatus data.
		{
			$q2="SELECT * from bookstatus where row='$rowarr[$x]'";  //get the details for the row
			$r=mysqli_query($conn,$q2);   //firing the sql query
			$rere=$r->fetch_assoc();      //creating into array
			$upString=$rere['booknum'];   //already booked seats in that row
			$remainCount=$rere['remaining'];  //remaining seats count
			$remainCount=$remainCount-1;      //remaining seats after booking the ticket in the row
			$upString=$upString.$bookingarr[$x]." ";  //updated booked seats in the row
			
			$q3="UPDATE bookstatus SET booknum='$upString',remaining='$remainCount' WHERE row='$rowarr[$x]';"; //update the database with new updated booked details

			$fire=mysqli_query($conn,$q3); //firing the query
			if(!$fire)
			{
				echo "failed";
			}
		}
		$fire=mysqli_query($conn,$query); 
		if($fire)
		{
			$_SESSION['msg']="Successfully Booked. Your Seats are $bookedString"; //if all set create a session msg to return the booked elements
			header("location:index.php");   //refresh the page back
		}
	}
}



?>
<!DOCTYPE html>
<html>
<head>
	<title>Ticket System</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  	<style>
  		label,h4 {
  			color: white;	
  		}
  	</style>
</head>
<body style="background: 
		linear-gradient(rgba(196, 102, 0, 0.6), rgba(155, 89, 182, 0.6));">
<div style="text-align: center;">

<h6>D2C Presents RTBS</h6>
<h1 style=" font-family: fantasy;">RAILWAY TICKET BOOKING SYSTEM</h1>
</div>

<br>
<br>
<br>
<br>
<div class="container">
<!-- COACH BLUE PRINT -->
		<div style="border:solid 1px BLACK; background-color:; padding: 5px; padding-left: 40px" class="col-lg-6">
			<u style="color: pink"><b><h4 style="text-align: center; padding-top: -30px;">C O U C H</h4></b></u>
			<?php 
			for($x=1;$x<=80;$x++) 
			{ 
				if(in_array($x, $booked)) 
					echo '<i class="glyphicon glyphicon-check" style="color:white; font-size:18px"></i>';
				else
					echo '<i class="glyphicon glyphicon-unchecked"  style="font-size:18px"></i>';
				?>
				<?php if($x%7==0) echo "<br>"; else echo "&nbsp; &nbsp; &nbsp; &nbsp;   &nbsp; &nbsp; &nbsp;"; ?> 
			<?php	}  ?>
		</div>

		<!-- BOOKING FORM -->
		<!-- collect the details from the user and post to backend -->
		<div class="col-lg-6">
			<div>
				<form action="" method="POST">
					<div class="form-group">
						<div style=" padding: px;">
						<?php //if any error occour on user details giving. return the error message.
						if(isset($error)) { ?>
							<div class="alert alert-danger" role="alert"> <?php echo $error; ?></div>
						<?php } ?>
						<label>Book Your Tickets</label>
						<input type="number" name="tcount" placeholder="Number of tickets 1-7" class="form-control" required><br>
						<label>Date</label><br>
						<input type="date" id="journey" name="journey">
						<br><br>
			
						<label>Name</label>
						<input type="text" name="name" class="form-control" placeholder="Name..." required><br>
						<label>Email address</label>
						<input type="email" name="email" class="form-control" placeholder="Email address..."><BR>
						<center><input type="submit" name="submit" value="Book" class="btn btn-primary" ></center>
						</div>
					</div>
				</form>
	</div></div></div>
	<div class="container" style="text-align: center; font-size: 20px">
		<?php  //if session msg is set. print the session msg(Booked status)
			if(isset($_SESSION['msg'])) { ?>
				<div class="alert alert-success" role="alert"> <?php echo $_SESSION['msg'];?></div>
			<?php } ?></div>
	
	<br>
	<!-- RESETTING THE DATABASE COMPLETLY-->
	<div class="alert alert-secondary" role="alert">
  	<center>Click on reset to empty all the bookings and Get new Couch....</center>
  	<br>
  	<center><form action="reset.php" method="POST"><input type="submit" name="reset" value="reset" class="btn btn-primary"></form></center>
  	</div>
</body>	

</html>