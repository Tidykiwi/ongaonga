<?php
include "checksession.php";
checkUser();
loginStatus(); 
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
  <title>Delete Room</title>
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
          <li class="selected"><a href="/bnb/listrooms.php">Rooms</a></li>
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

//insert DB code from here onwards
//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//function to clean input but not validate type and content
function cleanInput($data) {  
  return htmlspecialchars(stripslashes(trim($data)));
}

//retrieve the Roomid from the URL
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
        echo "<h2>Invalid Room ID</h2>"; //simple error feedback
        exit;
    } 
}

//the data was sent using a formtherefore we use the $_POST instead of $_GET
//check if we are saving data first by checking if the submit button exists in the array
if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {     
    $error = 0; //clear our error flag
    $msg = 'Error: ';  
//RoomID (sent via a form it is a string not a number so we try a type conversion!)    
    if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
       $id = cleanInput($_POST['id']); 
    } else {
       $error++; //bump the error flag
       $msg .= 'Invalid Room ID '; //append error message
       $id = 0;  
    }        
    
//save the Room data if the error flag is still clear and Room id is > 0
    if ($error == 0 and $id > 0) {
        $query = "DELETE FROM `room` WHERE roomID=?";
        $stmt = mysqli_prepare($DBC,$query); //prepare the query
        mysqli_stmt_bind_param($stmt,'i', $id); 
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);    
        echo "<h2>Room details deleted.</h2>";     
        
    } else { 
      echo "<h2>$msg</h2>".PHP_EOL;
    }      

}

//prepare a query and send it to the server
//NOTE for simplicity purposes ONLY we are not using prepared queries
//make sure you ALWAYS use prepared queries when creating custom SQL like below
$query = 'SELECT * FROM `room` WHERE roomID='.$id;
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Room details preview before deletion</h1>
<?php

//makes sure we have the Room
if ($rowcount > 0) {  
    echo "<fieldset><legend>Room detail #$id</legend><dl>"; 
    $row = mysqli_fetch_assoc($result);
    echo "<dt>Room name:</dt><dd>".$row['roomname']."</dd>".PHP_EOL;
    echo "<dt>Description:</dt><dd>".$row['description']."</dd>".PHP_EOL;
    echo "<dt>Room type:</dt><dd>".$row['roomtype']."</dd>".PHP_EOL;
    echo "<dt>Beds:</dt><dd>".$row['beds']."</dd>".PHP_EOL; 
    echo '</dl></fieldset>'.PHP_EOL;  
   ?><form method="POST" action="deleteroom.php">
     <h2>Are you sure you want to delete this Room?</h2>
     <input type="hidden" name="id" value="<?php echo $id; ?>">
     <input type="submit" name="submit" value="Delete">
     <a href="listrooms.php">[Cancel]</a>
     </form>
<?php    
} else echo "<h2>No Room found, possbily deleted!</h2>"; //suitable feedback

mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done
?>
</table> 

<?php
echo '</div></div>';
include "footer.php";
?>