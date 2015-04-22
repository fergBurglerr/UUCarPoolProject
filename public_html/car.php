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
		$openSeats = $_POST['openSeats'];
		$make = htmlspecialchars($_POST['make']);
		$model = htmlspecialchars($_POST['model']);
		$color = htmlspecialchars($_POST['color']);
		$license_num = htmlspecialchars($_POST['license_num']);

		$result = $conn->prepare("INSERT INTO Car (openSeats, make, model, color, license_num) VALUES (?,?,?,?,?);");
		$result->bind_param('issss', $openSeats, $make, $model, $color, $license_num);

		if ($result->execute()) {
			echo $result->affected_rows()." Car added successfully!";
		}
		else {
			echo "Car NOT added successfully";
		}
	}

	###Remove function for Cars
	if ($_POST['action']=='remove'){
		$cid = $_POST['cid'];

		$result = $conn->prepare("DELETE FROM Car WHERE (cid = ?) LIMIT 1;");
		$result->bind_param('i', $cid);
		if ($result->execute()) {
			echo $result->affected_rows()." Car was removed successfully!";
		}
		else {
			echo "Car was NOT removed successfully";
		}
	}

	###Edit function for Cars
	if ($_POST['action']=='edit'){
		$cid = $_POST['cid'];  ### Car ID 
		$openSeats = $_POST['openSeats'];
		$make = htmlspecialchars($_POST['make']);
		$model = htmlspecialchars($_POST['model']);
		$color = htmlspecialchars($_POST['color']);
		$license_num = htmlspecialchars($_POST['license_num']);

		$result = $conn->prepare("UPDATE Car SET openSeats=?, make=?, model=?, color=?, license_num=? WHERE (cid = ?);");
		$result->bind_param('issssi', $openSeats, $make, $model, $color, $license_num, $cid);

		if ($result->execute()) {
			echo $result->affected_rows()."Car was edited";
		}
		else {
			echo "Car was NOT edited";
		}
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