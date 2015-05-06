<?php
	include_once("sql/dbinfo.php");
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}
	$p = $_POST;
	$action = $p['action'];
	$houseNumber = $p['houseNumber'];
	$suiteNumber = $p['suiteNumber'];
	$street = $p['street'];
	$city = $p['city'];
	$zip = intval($p['zip']);
	$latitude = doubleval($p['latitude']);
	$longitude = doubleval($p['longitude']);

	if($action=='add'){
		$sql = 'SELECT * FROM Address WHERE houseNumber=? AND suiteNumber=? AND street=? AND city=? AND zipcode=? AND latitude=? AND longitude=?';

		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("ssssidd", $houseNumber, $suiteNumber, $street, $city, $zip, $latitude, $longitude);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0){
				echo "Address already exists!";
				exit;
			}
		} else {
			echo "Database error, try again later";
		}

		$sql = 'INSERT INTO Address (houseNumber, suiteNumber, street, city, zipcode, latitude, longitude) VALUES (?,?,?,?,?,?,?)';

		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("ssssidd", $houseNumber, $suiteNumber, $street, $city, $zip, $latitude, $longitude);
			$stmt->execute();
			$stmt->close();
		}
	}

	$conn->close();
?>
