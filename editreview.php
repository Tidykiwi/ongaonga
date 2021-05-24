<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Edit Review</title>
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
  exit; //stop processing the page further
};
 
//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}
 
//retrieve the booking id from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid bookingID</h2>"; //simple error feedback
        exit;
    } 
}
/* the data was sent using a form therefore we use the $_POST instead of $_GET
   check if we are saving data first by checking if the submit button exists in
   the array */
if (isset($_POST['submit']) and !empty($_POST['submit'])
    and ($_POST['submit'] == 'Update')) {
       
	
/* validate incoming data */
    $error = 0; //clear our error flag
    $msg = 'Error: ';       
	
/* bookingID (sent via a form it is a string not a number so we try
   a type conversion!) */
    if (isset($_POST['id']) and !empty($_POST['id']) 
        and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid booking ID '; //append error message
       $id = 0;  
    } 

	
	if (isset($_POST['review']) and !empty($_POST['review']) and is_string($_POST['review'])) {
       
	   $review = cleanInput($_POST['review']); 
	   $review = trim($review);
  
		// Brief states cannot edit review until after booking has taken place
		// Dates need to be compared before review is accepted
		// Get todays date and convert to a string
		$getDate = new DateTime();
		$today = $getDate->format('Y-m-d');
		// Fetch the checkout date of the current booking
		$query = 'SELECT checkout FROM `bookings` WHERE bookingID='.$id;
		$result = mysqli_query($DBC,$query);
		$row = mysqli_fetch_assoc($result);
		$checkout = $row['checkout'];
		
		// Compare the dateTimestamps to see which is more recent 
		if ($today < $checkout){
			$error++; //bump the error flag
			$msg .= "Sorry you cannot access this option until after you checkout on $checkout"; //append eror message
		}
		
	} else {
	$error++; //bump the error flag
	$msg .= "Sorry you review is INVALID"; //append eror message
	}	
        
    
//save the booking data if the error flag is still clear and booking id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE `bookings` SET review=? WHERE bookingID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'si', $review, $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Review updated.</h2>";     
        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
 
}
//locate the booking to edit by using the bookingID
//booking ID is also included in the form for sending it back for saving the data
$query = 'SELECT firstname,lastname,review FROM `bookings` INNER JOIN `customer` ON bookings.customerID=customer.customerID WHERE bookingID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>

	<div id="main">
		<div id="content">
			<h1>Edit/add room review</h1>
			<h2>Review made by <?php echo $row['firstname']," ",$row['lastname'];?></h2>
			
			<form method="POST" action="editreview.php">	
				<input type="hidden" name="id" value="<?php echo $id;?>">
				<p>
					<span>Room review: </span>
					<textarea rows="10" cols="50" id="review" name="review" minlength="0" maxlength="500" placeholder="nothing">
					<?php echo $row['review'];?>	
					</textarea>			
				</p>
				<p>			
					<input type="submit" name="submit" value="Update"><a href="booking.php">[Cancel]</a>
				</p>
			</form>	

<?php 
} else { 
  echo "<h2>No review found</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>	
<?php
echo '</div></div>';
include "footer.php";
?>