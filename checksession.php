<?php
session_start();

//function to check if the user is logged else send to the login page 
function checkUser() {
    $_SESSION['URI'] = '';    
    if ($_SESSION['loggedin'] == 1)
       return TRUE;
    else {
        $_SESSION['URI'] = 'http://ongaongabnb.unaux.com'.$_SERVER['REQUEST_URI']; //save current url for redirect     
        header('Location: http://ongaongabnb.unaux.com/bnb/login.php', true, 303); 
		echo "<h2>You need to login first</h2>";
    }       
}
 
//just to show we are logged in
function loginStatus() {
    $un = $_SESSION['username'];
    if ($_SESSION['loggedin'] == 1)     
        echo "<h2>Logged in as $un</h2>";
    else
        echo "<h2>Logged out</h2>";            
}
 
//log a user in
function login($id,$username) {
   //simple redirect if a user tries to access a page they have not logged in to
   if ($_SESSION['loggedin'] == 0 and !empty($_SESSION['URI']))        
        $uri = $_SESSION['URI'];          
   else { 
     $_SESSION['URI'] =  'http://ongaongabnb.unaux.com/bnb/booking.php';         
     $uri = $_SESSION['URI'];           
   }  
   
   $_SESSION['loggedin'] = 1;        
   $_SESSION['userid'] = $id;   
   $_SESSION['username'] = $username; 
   $_SESSION['URI'] = ''; 
   header('Location: '.$uri, true, 303);        
}

// pass customerID of logged in user when function is called
// Used in the make a booking form
function currentUser() {
	return  $_SESSION['userid'];
}
 
 
     $passID = $_SESSION['userid'];
 
 
//simple logout function
function logout(){
  $_SESSION['loggedin'] = 0;
  $_SESSION['userid'] = -1;        
  $_SESSION['username'] = '';
  $_SESSION['URI'] = '';
  header('Location: http://ongaongabnb.unaux.com/bnb/login.php', true, 303);    
}
?>