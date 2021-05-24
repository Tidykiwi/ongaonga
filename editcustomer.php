<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Edit Customer</title>
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
          <li><a href="/bnb/booking.php">Bookings</a></li>
          <li><a href="/bnb/listrooms.php">Rooms</a></li>
		  <li class= "selected"><a href="/bnb/listcustomers.php">Customers</a></li>
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

//retrieve the customerid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Customer ID</h2>"; //simple error feedback
        exit;
    } 
}
//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {     
//validate incoming data - only the first field is done for you in this example - rest is up to you do
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
     
//customerID (sent via a form ti is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid Customer ID '; //append error message
       $id = 0;  
    }   
//firstname
       $firstname = cleanInput($_POST['firstname']); 
//lastname
       $lastname = cleanInput($_POST['lastname']);        
//email
       $email = cleanInput($_POST['email']);         
    
//save the customer data if the error flag is still clear and customer id is > 0
    if ($error == 0 and $id > 0) {
        $query = "UPDATE `customer` SET firstname=?,lastname=?,email=? WHERE customerID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'sssi', $firstname, $lastname, $email,$id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>customer details updated.</h2>";     
     
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      
}
//locate the customer to edit by using the customerID
//we also include the customer ID in our form for sending it back for saving the data
$query = 'SELECT customerID,firstname,lastname,email FROM customer WHERE customerID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result);
if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
?>
<h1>Customer Details Update</h1>

<form method="POST" action="editcustomer.php">
  <input type="hidden" name="id" value="<?php echo $id;?>">
  <p>
    <label for="firstname">First Name: </label>
    <input type="text" id="firstname" name="firstname" minlength="1" 
           maxlength="50" required value="<?php echo $row['firstname']; ?>"> 
  </p> 
  <p>
    <label for="lastname">Last Name: </label>
    <input type="text" id="lastname" name="lastname" minlength="1" 
           maxlength="50" required value="<?php echo $row['lastname']; ?>">  
  </p>  
  <p>  
    <label for="email">Email: </label>
    <input type="email" id="email" name="email" maxlength="100" 
           size="50" required value="<?php echo $row['email']; ?>"> 
   </p>

   <input type="submit" name="submit" value="Update"><a href="listcustomers.php">[Cancel]</a>
 </form>
<?php 
} else { 
  echo "<h2>Customer not found with that ID</h2>"; //simple error feedback
}
mysqli_close($DBC); //close the connection once done
?>
<?php
echo '</div></div>';
include "footer.php";
?>