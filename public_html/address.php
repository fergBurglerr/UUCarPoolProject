<?php
// PHP functions to add, modify, delete, and add assign addresses to people 
	include("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	###Insert function for Address 
	if (strcmp($_POST['action'],'add')==0){
		$houseNumber = htmlspecialchars($_POST['houseNumber']);
		$suiteNumber = htmlspecialchars($_POST['suiteNumber']);
		$street = htmlspecialchars($_POST['street']);
		$city = htmlspecialchars($_POST['city']);
		$zipcode = htmlspecialchars($_POST['zipcode']);

		$result = $conn->prepare("INSERT INTO Address (houseNumber, suiteNumber, street, city, zipcode) VALUES (?,?,?,?,?);");
		$result->bind_param('ssssi', $houseNumber, $suiteNumber, $street, $city, $zipcode);

		if ($result->execute()) {
			echo $result->affected_rows()."Address added successfully!";
		}
		else {
			echo "Address was NOT added successfully";
		}
	}

	###Remove function for Address
	if (strcmp($_POST['action']=='remove'){
		$aid = $_POST['aid'];

		$result = $conn->prepare("DELETE FROM Address WHERE (aid = ?) LIMIT 1;");
		$result->bind_param('i', $aid);
		if ($result->execute()) {
			echo $result->affected_rows()." Address was removed successfully!";
		}
		else {
			echo "Address was NOT removed successfully";
		}
	}

	###Edit function for Address
	if (strcmp($_POST['action'],'edit')==0) {
		$aid = $_POST['aid']; #Address ID 
		$houseNumber = htmlspecialchars($_POST['houseNumber']);
		$suiteNumber = htmlspecialchars($_POST['suiteNumber']);
		$street = htmlspecialchars($_POST['street']);
		$city = htmlspecialchars($_POST['city']);
		$zipcode = htmlspecialchars($_POST['zipcode']);

		$result = $conn->prepare("UPDATE Address SET houseNumber=?, suiteNumber=?, street=?, city=?, zipcode=? WHERE (aid = ?);");
		$result->bind_param('ssssii', $houseNumber, $suiteNumber, $street, $city, $zipcode, $aid);

		if ($result->execute()) {
			echo $result->affected_rows()."Address was edited";
		}
		else {
			echo "Address was NOT edited";
		}
	}

	if (strcmp($_POST['action'],'get')==0) {
		#$offest=$_POST['aid'];
		#$offset=0;
		$result = $conn->prepare("SELECT houseNumber, suiteNumber, street, city, zipcode FROM Address;");
		#$result->bind_param('i',$offset);

		#$offset=0;
		$result->execute();
		$result->bind_result($houseNumber, $suiteNumber, $street, $city, $zipcode);
		while ($result->fetch()) {
	        printf ("houseNumber: %s suiteNumber: %s street: %s city: %s zipcode: %s\n <br>", $houseNumber, $suiteNumber, $street, $city, $zipcode);
	    }
	}

	$result->close();
	$conn->close();
?>