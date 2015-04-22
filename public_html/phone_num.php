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
	#if ($_POST['action']=='add'){
		#$number = $_POST['number']; // the phone number 
		#$pid = $_POST['pid'];  //ID of the person the phone number is tied to 
		$pid = 1;
		$number = 1112223336;

		$test = $conn->prepare("SELECT * FROM Phone WHERE phone = ?;");
		$test->bind_param('s', $number);
		$test->execute();
		$test->store_result();

		echo "$test->num_rows";

		if ($test->num_rows == 0) { //checks to see if the number already exists in the table
			
			echo "THIS WORKED!!!!!!!!!!!!1";

			$num = $conn->prepare("INSERT INTO Phone VALUES (?);");
			$num->bind_param('s', $number);

			if ($num->execute()) {
				$result2 = $conn->prepare("INSERT INTO person_has_phone VALUES (?,?);");
				$result2->bind_param('si', $number, $pid);

				if ($result2->execute()) {
					echo $num->affected_rows(). "Phone number added successfully";
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
	#}

	###Remove function for numbers 
	if ($_POST['action']=='remove') {
		$number = $_POST['number'];
		$pid = $_POST['pid'];

		$result = $conn->prepare("DELETE FROM person_has_phone WHERE (number = ?) AND (pid = ?) LIMIT 1;");
		$result->bind_param('si', $number, $pid);
		if ($result->execute()) {
			echo $result->affected_rows()." Phone Number was removed successfully!";
		}
		else {
			echo "Phone Number was NOT removed successfully";
		}
		$result->close();
	}

	###Edit function for numbers
	if ($_POST['action']=='edit') {

	}

	###Get function for numbers
	#if ($_POST['action']=='get') {
		
	#}

	$conn->close();
?>