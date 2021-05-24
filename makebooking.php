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
include 'quantum.php';
QuantumPHP::$MODE = 1;

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//the data was sent using a form therefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
//if ($_SERVER["REQUEST_METHOD"] == "POST") { //alternative simpler POST test    
    include "config.php"; //load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

	//validate incoming data
	$error = 0;

	//customerID
	// fetch customerID from checksession.php
	$userid = currentUser();
	$customerID = cleanInput($userid); 

	//roomID
	$roomID = cleanInput($_POST['roomID']); 

	//checkin
	//Prevent the user from MANUALLY entering a date too far into the future
    $checkin = cleanInput($_POST['checkin']);
	if($checkin > '2025-12-31') {
	$error++; // bump the error flag
	$msg .="Sorry but we do not know if we will be here that far into the future!"; // eror message
	}
	
	//Prevent user from manually enteing a checkin or checkout before todays date
	// Get todays date and convert to a string
	$getDate = new DateTime();
	$today = $getDate->format('Y-m-d');
	// Compare todays date with checkin 
	if ($today > $checkin){
		$error++; //bump the error flag
		$msg .= "Sorry but you cannot checkin before $today"; // error message
	} 
	
	//checkout
	//prevent user from selecting same day for checkin & checkout
    $checkout = cleanInput($_POST['checkout']);  
	if ($checkout == $checkin) {
		$error++; // bump the error flag
		$msg .= "Sorry but you cannot checkin and checkout on the same day"; // error message
	}
	
	//prevent user from MANUALLY selecting a checkout that is prior to the checkin  
	if ($checkout < $checkin) {
		$error++; // bump the error flag
		$msg .= "Sorry but you cannot checkout before you checkin"; // error message
	}
	
	//Prevent the user from MANUALLY entering a date too far into the future
    $checkin = cleanInput($_POST['checkin']);
	if($checkout > '2025-12-31') {
	$error++; // bump the error flag
	$msg .="Sorry but we do not know if we will be here that far into the future!"; // eror message
	}
       
	//contactnumber
    $contactnumber = cleanInput($_POST['contactnumber']);  
	   
	//extras
    $extras = cleanInput($_POST['extras']);
	$extras = trim($extras);

	QuantumPHP::add($roomID);
	QuantumPHP::add($checkin);
	QuantumPHP::add($checkout);
	QuantumPHP::add($contactnumber);
	QuantumPHP::add($extras);
	QuantumPHP::send();
	
	//save the member data if the error flag is still clear
    if ($error == 0) {
        $query = "INSERT INTO bookings(customerID,roomID,checkin,checkout,contactnumber,extras) VALUES (?,?,?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'ssssss', $customerID,$roomID,$checkin,$checkout,$contactnumber,$extras); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);   
		
        echo "<h2>Booking saved</h2>";        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }     

	
    mysqli_close($DBC); //close the connection once done
}
?>
		<h1>Make a booking</h1>
			
			<form method="POST" action="makebooking.php">
				<p>
					<span>Room (name,type,beds): </span>
					<select id="roomID" name="roomID" required>
					<option value="1">Kellie, S, 5</option>
					<option value="2">Herman, D, 5</option>
					<option value="3">Scarlett, D, 2</option>
					<option value="4">Jelani, S, 2</option>
					<option value="5">Sonya, S, 5</option>
					<option value="6">Miranda, S, 4</option>
					<option value="7">Helen, S, 2</option>
					<option value="8">Octavia, D, 3</option>
					<option value="9">Gretchen, D, 3</option>
					<option value="10">Bernard, S, 5</option>
					<option value="11">Dacey, D, 2</option>
					<option value="12">Preston, D, 2</option>
					<option value="13">Dane, S, 4</option>
					<option value="14">Cole, S, 1</option>
					</select>
				</p>
				<p>
					<label for="checkin">Checkin date: </label><input type="text" id="checkin" name="checkin" placeholder="yyyy-mm-dd" pattern="[0-9]{4}[-][0-9]{2}[-][0-9]{2}" autocomplete="off" required>
					<label for="checkout">Checkout date: </label><input type="text" id="checkout" name="checkout" placeholder="yyyy-mm-dd" pattern="[0-9]{4}[-][0-9]{2}[-][0-9]{2}" autocomplete="off" required>
				</p>
				<p>
					<label>Contact number: </label>
					<input type="tel" id="contactnumber" name="contactnumber" placeholder="(###) ###-####" pattern="[(][0-9]{3}[)] [0-9]{3}[-][0-9]{4}" autocomplete="off" required>
				</p>
				<p>				
					<span>Booking extras: </span>
					<textarea rows="10" cols="50" id="extras" name="extras" maxlength="250" autocomplete="off"></textarea>			
				</p>
				<p>
					<input type="submit" name="submit" value="Add"><a href="booking.php">[Cancel]</a>
				</p>
			</form>	


<?php
echo '</div></div>';
include "footer.php";
?>