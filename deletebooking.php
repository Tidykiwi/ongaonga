<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Delete Booking</title>
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

//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the customerID from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid customerID</h2>"; //simple error feedback
        exit;
    } 
}

//the data was sent using a form therefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
//bookingID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid member ID '; //append error message
       $id = 0;  
    }        
    
//save the member data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM `bookings` WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query	
        mysqli_stmt_bind_param($stmt,'i', $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Booking deleted.</h2>";     
        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      

}

//prepare a query and send it to the server
$query = 'SELECT bookingID,roomname,checkin,checkout FROM `bookings` INNER JOIN `room` ON bookings.roomID=room.roomID WHERE bookingID='.$id;   
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>


	<div id="main">
		<div id="content">
			<h1>Booking preview before deletion</h1>
			<?php

//makes sure we have the member
if ($rowcount > 0) {  
   $row = mysqli_fetch_assoc($result);
   echo "<fieldset><legend>Details - Booking #".$row['bookingID']."</legend><dl>"; 
   
   echo "<dt>Room Name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
   echo "<dt>Checkin date:</dt><dd>".$row['checkin']."</dd>".PHP_EOL;
   echo "<dt>Checkout date:</dt><dd>".$row['checkout']."</dd>".PHP_EOL; 
   echo '</dl></fieldset>'.PHP_EOL;  
   ?><form method="POST" action="deletebooking.php">
     <h2>Are you sure you want to delete this booking?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href="booking.php">[Cancel]</a>
     </form>
<?php    
} else echo "<h2>No bookings found!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
<?php
echo '</div></div>';
include "footer.php";
?>