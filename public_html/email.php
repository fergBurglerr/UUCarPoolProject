<?php
	###PHP functions to add, modify, delete, and add assign phone numbers to people
	include_once("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	###Insert function for numbers 
	if ($_POST['action']=='add'){
		$address = $_POST['email']; // the phone number 
		$pid = $_POST['pid'];  //ID of the person the phone number is tied to 
		#$pid = 1;
		#$number = '1112223338';

		$test = $conn->prepare("SELECT * FROM Email WHERE address = ?;");
		$test->bind_param('s', $address);
		$test->execute();
		$test->store_result();

		#echo "$test->num_rows";

		if ($test->num_rows == 0) { //checks to see if the number already exists in the table
			
			#echo "THIS WORKED!!!!!!!!!!!!1";

			$num = $conn->prepare("INSERT INTO Email VALUES (?);");
			$num->bind_param('s', $address);

			if ($num->execute()) {
				$result2 = $conn->prepare("INSERT INTO person_has_email VALUES (?,?);");
				$result2->bind_param('si', $address, $pid);

				if ($result2->execute()) {
					echo $num->affected_rows. "Email added successfully";
				}
				else {
					echo "Person does not exist";
				}
				$result2->close();
			}
			else {
				echo "Email NOT added successfully";
			}
			$num->close();
		} // end if 
		else { // if the number exists, it ONLY adds a tuple to the person_has_phone table 
			$result2 = $conn->prepare("INSERT INTO person_has_email VALUES (?,?);");
			$result2->bind_param('si', $address, $pid);

			if ($result2->execute()) {
				echo $result2->affected_rows(). "Email added successfully";
			}
			else {
				echo "Person does not exist";
			}
			$result2->close();
		} // end else 
		$test->close();
	}

	###Remove function for numbers 
	if ($_POST['action']=='remove') {
		$address = $_POST['address'];
		$pid = $_POST['pid'];

		#$number = '1112223333';
		#$pid = 1;

		$result = $conn->prepare("DELETE FROM person_has_email WHERE (address = ?) AND (pid = ?) LIMIT 1;");
		$result->bind_param('si', $address, $pid);
		if ($result->execute()) {
			echo $result->affected_rows." Email was removed successfully!";
		}
		else {
			echo "Email was NOT removed successfully";
		}

		$query = "SELECT count(pid) as total FROM person_has_email WHERE ('address' = ?);";
		$result2 = $conn->prepare($query); 
		$result2->bind_param('s', $address);
		$param = $result2->execute();
		if ($param) {
			$result2->bind_result($col1);

			$result2->fetch();
			$result2->close();
			if ($col1 == 0 ){
				$stmt = $conn->prepare("DELETE FROM Email WHERE address = ? LIMIT 1");
				$stmt->bind_param('s', $address);
				if($stmt->execute()) {
					echo "\n$Email deleted from Email table";
				}
				else {
					echo "There were problems deleting from the Email table";
				}
				$stmt->close();
			}
			else {
				echo "The Email exists for other people still";
			}
		}

		$result->close();
	}

	###Edit function for numbers
	if ($_POST['action']=='edit') {
		$bad_email = $_POST['bad_email'];
		$good_email = $_POST['good_email'];
		$pid = $_POST['pid'];

		#$good_num = '9998887777';
		#$bad_num = '2223334444';
		#$pid = 1;

		$query = "SELECT count(*) as total FROM Email WHERE ('address' = ?);";
		$result = $conn->prepare($query); 
		$result->bind_param('s', $good_email);
		$result->execute();
		$result->bind_result($col);
		$result->fetch();
		$result->close();
		if($col == 0) {
			$num = $conn->prepare("INSERT INTO Email VALUES (?);");
			$num->bind_param('s', $good_email);

			if ($num->execute()) {
				echo "Email was added to Phone table";
			}
			else {
				echo "Email could not be added to Phone table";
			}
			$num->close();
		}

		$stmt = $conn->prepare("UPDATE person_has_email SET address = ? WHERE pid = ? AND address = ?;");
		$stmt->bind_param('sis', $good_email, $pid, $bad_email);
		if ($stmt->execute()) {
			echo "$bad_email updated to $good_email";
			$stmt->close();
			$query = "SELECT count(pid) as total FROM person_has_email WHERE ('address' = ?);";
			$result = $conn->prepare($query); 
			$result->bind_param('s', $bad_email);
			if ($result->execute()) {
				$result->bind_result($col1);

				$result->fetch();
				$result->close();
				if ($col1 == 0 ){
					$stmt = $conn->prepare("DELETE FROM Email WHERE address = ? LIMIT 1");
					$stmt->bind_param('s', $bad_email);
					if($stmt->execute()) {
						echo "\n Email deleted from Phone table";
					}
					else {
						echo "There were problems deleting from the Email table";
					}
					$stmt->close();
				}
				else {
					echo "The Email exists for other people still";
				}
			}
		}
		else {
			echo "Something went wrong with updating the Email number";
			printf("Error: %s.\n", $stmt->error);
			$stmt->close();
		}
	}

	###Get function for numbers
	if ($_POST['action']=='get') {
		$pid = $_POST['pid'];
		#$pid = 1;
		$returnObject=array();

		$result = $conn->prepare("SELECT firstName, lastName, address FROM Person INNER JOIN person_has_email WHERE Person.pid = ?;");
		$result->bind_param('i',$pid);

		$result->execute();
		$result->bind_result($firstName, $lastName, $address);
		while ($result->fetch()) {
			array_push($returnObject, array("FirstName"=>$firstName,"LastName"=>$lastName,"Email"=>$address));

	        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
	    }
	    	echo json_encode($returnObject);
	    $result->close();
	}

	$conn->close();
?>
