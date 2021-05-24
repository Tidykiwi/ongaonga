<?php

include 'quantum.php';
QuantumPHP::$MODE = 1;

//Our customer search/filtering engine
include "config.php"; //load in any variables
$DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE) or die();

$d1 = $_GET['d1'];
$d2 = $_GET['d2'];

QuantumPHP::add($d1);
QuantumPHP::add($d2);
QuantumPHP::send();

$searchresult = '';
if (isset($d1) and !empty($d1) and strlen($d1) < 31 || isset($d2) and !empty($d2) and strlen($d2) < 31) {

	//prepare a query and send it to the server using our search string as a wildcard on surname
    
	$query = "SELECT roomID,roomname,roomtype,beds FROM `room` WHERE roomID NOT IN (SELECT roomID FROM `bookings` WHERE checkin >= '$d1' AND checkout <= '$d2')";
	
	$result = mysqli_query($DBC,$query);
    $rowcount = mysqli_num_rows($result); 
        //makes sure we have customers
    if ($rowcount > 0) {  
		
        $rows=[]; //start an empty array
        
        //append each row in the query result to our empty array until there are no more results                    
        while ($row = mysqli_fetch_assoc($result)) {   
            $rows[] = $row; 
        }
		
        //take the array of our 1 or more customers and turn it into a JSON text
        $searchresult = json_encode($rows);
        //this line is cruicial for the browser to understand what data is being sent
        header('Content-Type: text/json; charset=utf-8');
    } else echo "<tr><td colspan=3><h2>No rooms available!</h2></td></tr>";
} else echo "<tr><td colspan=3> <h2>Invalid search query</h2>";
mysqli_free_result($result); //free any memory used by the query
mysqli_close($DBC); //close the connection once done

QuantumPHP::add($searchresult);
QuantumPHP::send();

echo  $searchresult;

?>