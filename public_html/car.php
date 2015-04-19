<?php
	###PHP functions to add, modify, delete, and add assign cars to people
	include("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	###Insert function for Cars 
	if ($_POST['action']=='add'){

	}

	###Remove function for Cars
	if ($_POST['action']=='remove'){

	}

	###Edit function for Cars
	if ($_POST['action']=='edit'){

	}

	#if ($_POST['action'] == 'get') {
		$result = $conn->prepare("SELECT color, make, model, license_num, openSeats FROM Car;");

		#$offset=0;
		$result->execute();
		$result->bind_result($color, $make, $model, $license_num, $openSeats);
		while ($result->fetch()) {
	        printf ("color: %s make: %s model: %s license_num: %s openSeats: %s\n <br>", $color, $make, $model, $license_num, $openSeats);
	    }
	#}

	$result->close();
	$conn->close();
?>