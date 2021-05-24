<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Ongaonga Bed & Breakfast</title>
  <meta name="description" content="Ongaonga Bed & Breakfast" />
  <meta name="keywords" content="Bed & Breakfast" />
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="style/style.css" title="style" />
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>	
	<script>
	$( function() {
		var minDate = new Date();
      from = $( "#checkin" ).datepicker({
		minDate: minDate,
		maxDate: "+3y",
        defaultDate: "+1w",
        changeMonth: true,
		dateFormat: "yy-mm-dd",
        numberOfMonths: 1
        })
        .on( "change", function() {
        to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#checkout" ).datepicker({
		minDate: "+1d",
		maxDate: "+3y +1d",
        defaultDate: "+1w",
        changeMonth: true,
		dateFormat: "yy-mm-dd",
        numberOfMonths: 1
		})
		.on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
		});
	  
 
		function getDate( element ) {
		var date;
		try {
			date = $.datepicker.parseDate("yy-mm-dd", element.value );
		} catch( error ) {
			date = null;
		}
 
		return date;
		}
	} );
	</script>
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
 
//customerID
	// fetch customerID from checksession.php
	$custID = currentUser();
	  
 
$query = 'SELECT bookingID,roomname,checkin,checkout,firstname,lastname FROM `bookings` INNER JOIN `customer` ON bookings.customerID=customer.customerID INNER JOIN `room` ON bookings.roomID=room.roomID WHERE customer.customerID='.$custID;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
/* turnoff PHP to use some HTML - this quicker to do than php echos*/
?>
			<h1>Current bookings</h1>
			<h2>Booking count <?php echo $rowcount;?></h2>
			<h2><a href="makebooking.php"><b>[Make  a Booking]</b></a></h2>
			
			<form method="POST" action="#"><br>
				<table border="1">
				<thead><tr><th>Booking (room, dates)</th><th>Customer</th><th>Action</th></tr></thead>
					<?php
 
//makes sure we have bookings
if ($rowcount > 0) {  
    
	while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['bookingID'];	
	  echo '<tr><td>'.$row['roomname'].', '.$row['checkin'].' / '.$row['checkout'].'</td>';
	  echo '<td>'.$row['lastname'].', '.$row['firstname'].'</td>';
      echo     '<td><a href="viewbooking.php?id='.$row['bookingID'].'">[view]</a>';
	  echo     '<a href="editbooking.php?id='.$id.'">[edit]</a>';
	  echo     '<a href="editreview.php?id='.$id.'">[manage reviews]</a>';
	  echo     '<a href="deletebooking.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No bookings found!</h2>"; //suitable feedback
 
mysqli_free_result($result); 
mysqli_close($DBC);
?>
										
				</table>							
			</form>

<?php
echo '</div></div>';
include "footer.php";
?>