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
      from = $( "#checkin2" ).datepicker({
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
      to = $( "#checkout2" ).datepicker({
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
	<script>
	function searchResult(date1, date2) {
	
		if (date1.length==0 || date2.length==0) {
		return;
	}
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
		//take JSON text from the server and convert it to JavaScript objects
		//mbrs will become a two dimensional array of our customers much like 
		//a PHP associative array
		var mbrs = JSON.parse(this.responseText);              
		var tbl = document.getElementById("tblcustomers"); //find the table in the HTML
      
	  
		//clear any existing rows from any previous searches
		//if this is not cleared rows will just keep being added
		var rowCount = tbl.rows.length;
		for (var i = 1; i < rowCount; i++) {
         //delete from the top - row 0 is the table header we keep
         tbl.deleteRow(1); 
		}      
      
		//populate the table
		//mbrs.length is the size of our array
		for (var i = 0; i < mbrs.length; i++) {
			var rid    = mbrs[i]['roomID'];
			var rn    = mbrs[i]['roomname'];
			var rt    = mbrs[i]['roomtype'];
			var bs    = mbrs[i]['beds'];
         
			//create a table row with three cells  
			tr = tbl.insertRow(-1);
			var tabCell = tr.insertCell(-1);
				tabCell.innerHTML = rid; //roomID
			var tabCell = tr.insertCell(-1);
				tabCell.innerHTML = rn; //roomname
			var tabCell = tr.insertCell(-1);
				tabCell.innerHTML = rt; //roomtype
			var tabCell = tr.insertCell(-1);
				tabCell.innerHTML = bs; //beds 
			}
		}
	}
	//call our php file that will look for a customer or customers matchign the seachstring
	xmlhttp.open("GET","bookingsearch.php?d1="+date1+"&d2="+date2, true);
	xmlhttp.send();
}
</script>
</head>
<?php
include "menu.php";
?>
<div id="menubar">";
        <ul id="menu">
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li class="selected"><a href="/bnb?index.php">Home</a></li>
          <li><a href="/bnb/booking.php">Bookings</a></li>
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
include "content.php";
?>


			<h1>Search for room availability</h1>
			<p>
				<form onsubmit="searchResult(this.checkin2.value, this.checkout2.value); return false">
					<label for="checkin2">Checkin date: </label><input type="text" id="checkin2" placeholder="yyyy-mm-dd" pattern="[0-9]{4}[-][0-9]{2}[-][0-9]{2}" autocomplete="off" required><br><br>
					<label for="checkout2">Checkout date: </label><input type="text" id="checkout2" placeholder="yyyy-mm-dd" pattern="[0-9]{4}[-][0-9]{2}[-][0-9]{2}" autocomplete="off" required><br><br>
					<input type="submit" name="submit" value="Search">
				</form>
			</p>
			<table id="tblcustomers" border="1">
				<thead><tr><th>Room#</th><th>Room Name</th><th>Room Type</th><th>Beds</th></tr></thead>
			</table><br>
			<h2>Register to make a booking</h2>
			

<?php
echo '</div></div>';
include "footer.php";
?>
