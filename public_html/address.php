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
	if ($_POST['action']=='add_address_to_person'){
		$houseNumber = htmlspecialchars($_POST['houseNumber']);
		$suiteNumber = htmlspecialchars($_POST['suiteNumber']);
		$street = htmlspecialchars($_POST['street']);
		$city = htmlspecialchars($_POST['city']);
		$zipcode = htmlspecialchars($_POST['zipcode']);

		$test = $conn->prepare("SELECT * FROM Address WHERE houseNumber = ? AND suiteNumber = ? AND street = ? 
			AND city = ? AND zipcode = ?;");
		$test->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);
		$test->execute();
		$test->store_result();

		if ($test->num_rows == 0) { //checks to see if the number already exists in the table
			
			$num = $conn->prepare("INSERT INTO Address (houseNumber, suiteNumber, street, city, zipcode) VALUES (?,?,?,?,?);");
			$num->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);

			if ($num->execute()) {
				$query = 'SELECT aid FROM Address WHERE houseNumber = ? AND suiteNumber = ? AND street = ? AND
				city = ? AND zipcode = ?';
				if ($stmt = $conn->prepare($query)) {
					$stmt->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);
					$result = $stmt->execute();
					$stmt->store_result();
					$row = $result->fetch_assoc();
					$aid = $row["aid"];
					$result->free();
					$stmt->close();
				}
				else {
					echo "Address could not be added";
					exit;
				}

				$result2 = $conn->prepare("INSERT INTO person_lives_at_address VALUES (?,?);");
				$result2->bind_param('ii', $aid, $pid);

				$pid=$_POST['pid'];

				if ($result2->execute()) {
					echo $num->affected_rows. "Address added successfully";
				}
				else {
					echo "Person does not exist";
				}
				$result2->close();
			}
			else {
				echo "Address NOT added successfully";
			}
			$num->close();
		} // end if 
		else { // if the number exists, it ONLY adds a tuple to the person_has_phone table 
			$stmt = $conn->prepare('SELECT aid FROM Address WHERE houseNumber=? AND suiteNumber=? AND street=? AND city=? AND zipcode=?');
			$stmt->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);

			$result = $stmt->execute();
			$stmt->store_result();
			$row = $result->fetch_assoc();
			$aid = $row["aid"];
			$result->free();
			$stmt->close();
			
			$pid = $_POST['pid'];
			$result2 = $conn->prepare("INSERT INTO person_lives_at_address VALUES (?,?);");
			$result2->bind_param('ii', $aid, $pid);

			if ($result2->execute()) {
				echo $result2->affected_rows(). "Address added successfully";
			}
			else {
				echo "Person does not exist";
			}
			$result2->close();
		} // end else 
		$test->close();
	}

	###Remove function for Address
	if ($_POST['action']=='remove'){
		$aid = $_POST['aid'];
		$pid = $_POST['pid'];

		$result = $conn->prepare("DELETE FROM person_lives_at_address WHERE (aid = ? AND pid = ?) LIMIT 1;");
		$result->bind_param('ii', $aid, $pid);
		if ($result->execute()) {
			echo $result->affected_rows." Address was removed successfully!";
		}
		else {
			echo "Address was NOT removed successfully";
		}

		$query = "SELECT count(pid) as total FROM person_lives_at_address WHERE ('aid' = ?);";
		$result2 = $conn->prepare($query); 
		$result2->bind_param('i', $aid);
		$param = $result2->execute();
		if ($param) {
			$result2->bind_result($col1);

			$result2->fetch();
			$result2->close();
			if ($col1 == 0 ){
				$stmt = $conn->prepare("DELETE FROM Address WHERE $aid = ? LIMIT 1");
				$stmt->bind_param('i', $aid);
				if($stmt->execute()) {
					echo "\n$Address deleted from Email table";
				}
				else {
					echo "There were problems deleting from the Address table";
				}
				$stmt->close();
			}
			else {
				echo "The Address exists for other people still";
			}
		}

		$result->close();
	}

	###Edit function for Address
	/*if ($_POST['action']=='edit') {
		$aid = $_POST['aid']; #Address ID 
		$pid = $POST['pid'];

		$result = $conn->prepare("UPDATE person_lives_at_address SET aid = ? WHERE (pid = ? AND aid = ?)");
		$result->bind_param('ii', $aid, $pid);

		if ($result->execute()) {
			echo $result->affected_rows()."Address was edited";
		}
		else {
			echo "Address was NOT edited";
		}
	}*/

	if ($_POST['action'] == 'get') {
		$returnObject=array();
		#$offest=$_POST['aid'];
		#$offset=0;
		$result = $conn->prepare("SELECT houseNumber, suiteNumber, street, city, zipcode FROM Address");
		#$result->bind_param('i',$offset);

		#$offset=0;
		$result->execute();
		$result->bind_result($houseNumber, $suiteNumber, $street, $city, $zipcode);
		while ($result->fetch()) {
			array_push($returnObject, array("House_number"=>$houseNumber, "Suite"=>$suiteNumber,"Street"=>$street,"city"=>$city,"Zipcode"=>$zipcode));
			//printf ("houseNumber: %s suiteNumber: %s street: %s city: %s zipcode: %s\n <br>", $houseNumber, $suiteNumber, $street, $city, $zipcode);
	    }
    	echo json_encode($returnObject);

	$result->close();
	}

	if ($_POST['action'] == 'add') {
		$houseNumber = htmlspecialchars($_POST['houseNumber']);
		$suiteNumber = htmlspecialchars($_POST['suiteNumber']);
		$street = htmlspecialchars($_POST['street']);
		$city = htmlspecialchars($_POST['city']);
		$zipcode = htmlspecialchars($_POST['zipcode']);

		$test = $conn->prepare("SELECT * FROM Address WHERE $houseNumber = ? AND $suiteNumber = ? AND $street = ? 
			AND $city = ? AND $zipcode = ?;");
		$test->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);
		$test->execute();
		$test->store_result();

		if ($test->num_rows == 0) { //checks to see if the number already exists in the table
			
			$num = $conn->prepare("INSERT INTO Address VALUES (?,?,?,?,?);");
			$num->bind_param('iisss', $houseNumber, $suiteNumber, $street, $city, $zipcode);

			if ($num->execute()) {
				echo "Address was added successfully to the table!!!";
			}
			else {
				echo "Could not add the address to the address table!!!";
			}
			$num->close();
		}
		$test->close();
	}
	//Add an address to event
	if(strcmp($_POST['action'],"add_address_to_event")==0) {
		$eid = $_POST['eid'];
		$aid = $_POST['aid'];

		$stmt = $conn->prepare("INSERT INTO event_has_address VALUES (?, ?);");
		$stmt->bind_param('ii', $aid, $eid);
		if ($stmt->execute()) {
			echo "Address was added to event successfully!";
		}
		else {
			echo "Address could NOT be added to event";
		}
	}

	$conn->close();
?>
