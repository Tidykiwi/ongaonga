<?php
include "checksession.php";
checkUser();
loginStatus(); 
?><?php
include "header.php";
include "menu.php";
?>

<div id="menubar">
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

include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

//check if the connection was good
if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
    exit; //stop processing the page further
}

//prepare a query and send it to the server
$query = 'SELECT roomID,roomname,roomtype FROM room ORDER BY roomtype';
$result = mysqli_query($DBC,$query);
$rowcount = mysqli_num_rows($result); 
?>
<h1>Room list</h1>
<h2><a href='addroom.php'>[Add a room]</a>
<table border="1">
<thead><tr><th>Room Name</th><th>Type</th><th>Action</th></tr></thead>
<?php

//makes sure we have rooms
if ($rowcount > 0) {  
    while ($row = mysqli_fetch_assoc($result)) {
	  $id = $row['roomID'];	
	  echo '<tr><td>'.$row['roomname'].'</td><td>'.$row['roomtype'].'</td>';
	  echo     '<td><a href="viewroom.php?id='.$id.'">[view]</a>';
	  echo         '<a href="editroom.php?id='.$id.'">[edit]</a>';
	  echo         '<a href="deleteroom.php?id='.$id.'">[delete]</a></td>';
      echo '</tr>'.PHP_EOL;
   }
} else echo "<h2>No rooms found!</h2>"; //suitable feedback
echo "</table>";
mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done



echo '</div></div>';
require_once "footer.php";
?>

  