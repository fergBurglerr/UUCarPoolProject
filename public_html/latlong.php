<?php
	include_once("sql/dbinfo.php");
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
	}
	$p = $_POST;
	$latitude;
	$longitude;
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
				//echo "Address already exists!";
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

	if($action=='getAid'){
		$sql = 'SELECT aid FROM Address WHERE houseNumber=? AND suiteNumber=? AND street=? AND city=? AND zipcode=? AND latitude=? AND longitude=?';

		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("ssssidd", $houseNumber, $suiteNumber, $street, $city, $zip, $latitude, $longitude);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows==0){
				echo "Address not in database";
			} else {
				$stmt->bind_result($aid);
				$stmt->fetch();
				echo $aid;//this is important, throwing aid back to frontend
				$stmt->close();
			}
		} else {
			echo "Database error, try again later";
		}
	}

	if($action=='matchAddressToPerson'){
		$aid=intval($p['aid']);
		$pid=intval($p['pid']);
		$sql = 'INSERT INTO person_lives_at_address (aid, pid) VALUES (?,?)';

		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("ii", $aid, $pid);
			$stmt->execute();
			$stmt->close();
		} else {
			echo "Database error: Please try again later!";
		}
	}

	if($action=='get_lat_long_from_pid'){
		$pid=intval($p['pid']);
		$sql = 'SELECT A.latitude, A.longitude FROM Address A INNER JOIN person_lives_at_address plaa USING (aid) WHERE plaa.pid=?';

		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("i", $pid);
			$stmt->execute();
			$stmt->bind_result($lat, $long);
			$stmt->fetch();
			$returnObject = array("latitude"=>$lat, "longitude"=>$long);
			echo json_encode($returnObject);
			$stmt->close();
		} else {
			echo "Database error: Please try again later!";
		}
	}
	
	$conn->close();
?>
