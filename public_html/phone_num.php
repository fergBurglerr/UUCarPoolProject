<?php
	###PHP functions to add, modify, delete, and add assign phone numbers to people
	include("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	###Insert function for numbers 
	if ($_POST['action']=='add'){
		$number = $_POST['number']; // the phone number 
		$pid = $_POST['pid'];  //ID of the person the phone number is tied to 
		#$pid = 1;
		#$number = '1112223338';

		$test = $conn->prepare("SELECT * FROM Phone WHERE number = ?;");
		$test->bind_param('s', $number);
		$test->execute();
		$test->store_result();

		#echo "$test->num_rows";

		if ($test->num_rows == 0) { //checks to see if the number already exists in the table
			
			#echo "THIS WORKED!!!!!!!!!!!!1";

			$num = $conn->prepare("INSERT INTO Phone VALUES (?);");
			$num->bind_param('s', $number);

			if ($num->execute()) {
				$result2 = $conn->prepare("INSERT INTO person_has_phone VALUES (?,?);");
				$result2->bind_param('si', $number, $pid);

				if ($result2->execute()) {
					echo $num->affected_rows. "Phone number added successfully";
				}
				else {
					echo "Person does not exist";
				}
				$result2->close();
			}
			else {
				echo "Phone number NOT added successfully";
			}
			$num->close();
		} // end if 
		else { // if the number exists, it ONLY adds a tuple to the person_has_phone table 
			$result2 = $conn->prepare("INSERT INTO person_has_phone VALUES (?,?);");
			$result2->bind_param('si', $number, $pid);

			if ($result2->execute()) {
				echo $result2->affected_rows(). "Phone number added successfully";
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
		$number = $_POST['number'];
		$pid = $_POST['pid'];

		#$number = '1112223333';
		#$pid = 1;

		$result = $conn->prepare("DELETE FROM person_has_phone WHERE (phone_number = ?) AND (pid = ?) LIMIT 1;");
		$result->bind_param('si', $number, $pid);
		if ($result->execute()) {
			echo $result->affected_rows." Phone Number was removed successfully!";
		}
		else {
			echo "Phone Number was NOT removed successfully";
		}

		$query = "SELECT count(pid) as total FROM person_has_phone WHERE ('phone_number' = ?);";
		$result2 = $conn->prepare($query); 
		$result2->bind_param('s', $number);
		$param = $result2->execute();
		if ($param) {
			$result2->bind_result($col1);

			$result2->fetch();
			$result2->close();
			if ($col1 == 0 ){
				$stmt = $conn->prepare("DELETE FROM Phone WHERE phone_number = ? LIMIT 1");
				$stmt->bind_param('s', $number);
				if($stmt->execute()) {
					echo "\n$number Number deleted from Phone table";
				}
				else {
					echo "There were problems deleting from the Phone table";
				}
				$stmt->close();
			}
			else {
				echo "The number exists for other people still";
			}
		}

		$result->close();
	}

	###Edit function for numbers
	#if ($_POST['action']=='edit') {
		#$bad_num = $_POST['bad_num'];
		#$good_num = $_POST['good_num'];
		#$pid = $_POST['pid'];

		$bad_num = '2223334444';
		$good_num = '1234567890';
		$pid = 1;

		$stmt = $conn->prepare("UPDATE person_has_phone SET phone_number = ? WHERE pid = ? AND phone_number = ?");
		$stmt->bind_param('sis', $good_num, $pid, $bad_num);
		if ($stmt->execute()) {
			$stmt->close();
			$query = "SELECT count(pid) as total FROM person_has_phone WHERE ('phone_number' = ?);";
			$result = $conn->prepare($query); 
			$result->bind_param('s', $bad_num);
			if ($result->execute()) {
				$result->bind_result($col1);

				$result->fetch();
				$result->close();
				if ($col1 != null && $col1 == 0 ){
					$stmt = $conn->prepare("DELETE FROM Phone WHERE phone_number = ? LIMIT 1");
					$stmt->bind_param('s', $bad_num);
					if($stmt->execute()) {
						echo "\n$number Number deleted from Phone table";
					}
					else {
						echo "There were problems deleting from the Phone table";
					}
					$stmt->close();
				}
				else {
					echo "The number exists for other people still";
				}
			}
		}
		else {
			echo "Something went wrong with updating the phone number";
			$stmt->close();
		}
	#}

	###Get function for numbers
	#if ($_POST['action']=='get') {
		
	#}

	$conn->close();
?>