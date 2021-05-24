<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>View Booking</title>
  <meta name="description" content="Ongaonga Bed & Breakfast" />
  <meta name="keywords" content="Bed & Breakfast" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="style/style.css" title="style" />
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
<div id="main">

<?php
include "menu.php";
?>
<div id="menubar">";
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li><a href="/bnb/index.php">Home</a></li>
          <li class="selected"><a href="/bnb/booking.php">Bookings</a></li>
          <li><a href="/bnb/listrooms.php">Rooms</a></li>
		  <li><a href="/bnb/listcustomers.php">Customers</a></li>
		  <li><a href="/bnb/register.php">Register</a></li>
          <li><a href="/bnb/login.php">Login / Logout</a></li>
        </ul>
      </div>
<?php
echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>
<?php
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);
 
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit;
}



//retrieve the customerID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid customerID</h2>"; //simple error feedback
        exit;
    } 
}

$query = 'SELECT contactnumber,bookingID,roomname,checkin,checkout,extras,review FROM `bookings` INNER JOIN `customer` ON bookings.customerID=customer.customerID INNER JOIN `room` ON bookings.roomID=room.roomID WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
/* turnoff PHP to use some HTML - this quicker to do than php echos*/

?>

	<div id="main">
		<div id="content">
			<h1>View Booking Details</h1>
			<?php

//makes sure we have the member
if ($rowcount > 0) {  
	echo "<fieldset><legend>Details - Booking #$id</legend><dl>"; 
	$row = mysqli_fetch_assoc($result);
	echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
	echo "<dt>Checkin date:</dt><dd>".$row['checkin']."</dd>".PHP_EOL;
	echo "<dt>Checkout date:</dt><dd>".$row['checkout']."</dd>".PHP_EOL;
	echo "<dt>Contact number:</dt><dd>".$row['contactnumber']."</dd>".PHP_EOL;
	echo "<dt>Extras:</dt><dd>".$row['extras']."</dd>".PHP_EOL;
	echo "<dt>Room review:</dt><dd>".$row['review']."</dd>".PHP_EOL;
	echo '</dl></fieldset>'.PHP_EOL;  
  
} else echo "<h2>No bookings found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>		
<?php
echo '</div></div>';
include "footer.php";
?>