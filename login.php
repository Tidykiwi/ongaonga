<?php
include "header.php";
include "menu.php";
?>
<div id="menubar">";
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li><a href="/bnb/index.php">Home</a></li>
		  <li><a href="/bnb/booking.php">Bookings</a></li>
		  <li><a href="/bnb/listrooms.php">Rooms</a></li>
		  <li><a href="/bnb/listcustomers.php">Customers</a></li>
          <li><a href="/bnb/register.php">Register</a></li>
          <li class="selected"><a href="/bnb/login.php">Login / Logout</a></li>
        </ul>
      </div>
<?php
echo '<div id="site_content">';
include "sidebar.php";

echo '<div id="content">';
?>

<?php
include "checksession.php";


 
//simple logout
if (isset($_POST['logout'])) logout();
 
if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
    include "config.php"; //load in any variables
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE) or die();
	
	//validate incoming data - only the first field is done for you in this example - rest is up to you to do
	//firstname
    $error = 0; //clear our error flag
	$msg = 'Error: ';
    if (isset($_POST['username']) and !empty($_POST['username']) and is_string($_POST['username'])) {
      $un = htmlspecialchars(stripslashes(trim($_POST['username'])));  
       $username = (strlen($un)>32)?substr($un,1,32):$un; //check length and clip if too big       
	} else {
       $error++; //bump the error flag
       $msg .= 'Invalid username '; //append error message
       $username = '';  //username
    } 
                    
	//password  - normally we avoid altering a password apart from whitespace on the ends   
    $password = trim($_POST['password']);        
       
	//This should be done with prepared statements!!
    if ($error == 0) {
        $query = "SELECT customerID,password FROM `customer` WHERE email = '$username'";
        $result = mysqli_query($DBC,$query);     
        if (mysqli_num_rows($result) == 1) { //found the user
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_close($DBC); //close the connection once done
			//this line would be added to the registermember.php to make a password hash before storing it
			//$hash = password_hash($password); 
			//this line would be used if our user password was stored as a hashed password
			//if (password_verify($password, $row['password'])) {

			              
            if ($password === $row['password']){ //using plaintext for demonstration only!            
				login($row['customerID'],$username);
			}
			
        } echo "<h2>Login fail</h2>".PHP_EOL;   
    } else { 
		echo "<h2>$msg</h2>".PHP_EOL;
    }      
}

?>
		<h1>Login</h1>
		<form method="POST" action="login.php">
			<p>
				<p>Hint: Username = email (e.g. non@et.ca)</p>
				<label for="username">Username: </label>
				<input type="text" id="username" name="username" maxlength="32" autocomplete="off"> 
			</p> 
			<p>
				<p>Hint: Password = password</p>
				<label for="password">Password: </label>
				<input type="password" id="password" name="password" maxlength="32"> 
			</p> 
			<input type="submit" name="login" value="Login">
			<input type="submit" name="logout" value="Logout">   
		</form>

<?php
echo '</div></div>';
include "footer.php";
?>