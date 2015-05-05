<?php
	include_once("sql/dbinfo.php");

	$p = $_POST;
	if(!($p['username'])){
		echo 'A username is required!<br>';
		exit;
	}
	if(!(ctype_alnum($p['username']))){
		echo 'A username may only contain letters and number!<br>';
		exit;
	}
	$username = $p['username'];
	$password = sha1($p['password']);

	if(!($p['houseNumber'])){
		echo 'A house number is required!<br>';
		exit;
	}
	$houseNumber = $p['houseNumber'];
	$suiteNumber = $p['suiteNumber'];
	if(!($suiteNumber)){ 
		$suiteNumber = '';
	}
	if(!($p['street'])){
		echo 'Street name required!';
		exit;
	}
	$street = $p['street'];

	if(!($p['zip'])){
		echo 'Zip code required';
		exit;
	}

	$zip = $p['zip'];

	if(!(($p['email'])) && (!($p['phone']))){
		echo 'Phone or E-mail required';
		exit;
	}
	$email = $p['email'];
	$phone = $p['phone'];
	
	if(!($p['latitude'])){
		echo 'Google maps error, cannot find address, please try again';
		exit;
	}

	$latitude = $p['latitude'];

	if(!($p['longitude'])){
		echo 'Google maps error, cannot find address, please try again';
		exit;
	}

	$longitude = $p['longitude'];

	if(!($p['firstname'])){
		echo 'First name cannot be blank!';
		exit;
	}
	$firstname = $p['firstname'];

	if(!($p['lastname'])){
		echo 'Last name cannot be blank!';
		exit;
	}
	$lastname = $p['lastname'];

	//Input validated -- DB section begins here ====================
	
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		//die("Connection failed: ". $conn->connect_error);
		echo 'DB Connection error...';
		exit;
	}
		
	if($email <> null){
		$query = "SELECT * FROM Email WHERE address = ?";
		
		$test = $conn->prepare($query);
		$test->bind_param('s', $email);
		$test->execute();
		
		echo $test->num_rows;
		
		if($test->num_rows==0){
			$query = "INSERT INTO Email VALUES (?)";
			echo $query . ' ';
			/*
			$result = $conn->prepare($query);
			$result = bind_param('s', $email);
			$result->execute();
			
			$result->close();
			*/
		}

		$test->close();
		
	}
	
	$conn->close();
	echo 'Registration successful!';
?>
