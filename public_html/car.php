<?php
	session_start();
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
		$pid = $_POST['pid'];
		$openSeats = $_POST['openSeats'];
		$make = htmlspecialchars($_POST['make']);
		$model = htmlspecialchars($_POST['model']);
		$color = htmlspecialchars($_POST['color']);
		$license_num = htmlspecialchars($_POST['license_num']);

		#Check to see if the car exists in the Cars table, if not, add it to the table, then tie it to the person
		$test = $conn->prepare("SELECT * FROM Car WHERE license_num = ?;");
		$test->bind_param('s', $license_num);
		$test->execute();
		$test->store_result();

		#echo "$test->num_rows";

		if ($test->num_rows == 0) {
			$result = $conn->prepare("INSERT INTO Car (openSeats, make, model, color, license_num) VALUES (?,?,?,?,?);");
			$result->bind_param('issss', $openSeats, $make, $model, $color, $license_num);

			if ($result->execute()) {
				//echo $result->affected_rows()." Car added successfully!";
				$result2 = $conn->prepare("INSERT INTO person_has_car VALUES (?,?);");
				$result2->bind_param('is', $pid, $license_num);

				if ($result2->execute()) {
					//echo $num->affected_rows. "Car added successfully to person";
				}
				else {
					echo "Person does not exist";
				}
				$result2->close();
			}
			else {
				echo "Car NOT added successfully";
			}
			$result->close();
		}
		#################### If it exists, ONLY tie the car to a Person who drives it #############################
		else {
			$result2 = $conn->prepare("INSERT INTO person_has_car VALUES (?,?);");
			$result2->bind_param('is', $pid, $license_num);

			if ($result2->execute()) {
				//echo $result2->affected_rows(). "Car added successfully to person";
			}
			else {
				echo "Person does not exist";
			}
			$result2->close();
		} // end else 

		$test->close();
	}

	###Remove function for Cars
	if ($_POST['action']=='remove'){
		$license_num = $_POST['license_num'];
		$pid = $_POST['pid'];

		$result = $conn->prepare("DELETE FROM Car WHERE (license_num = ?) LIMIT 1;");
		$result->bind_param('s', $license_num);
		if ($result->execute()) {
			echo $result->affected_rows()." Car was removed successfully!";
		}
		else {
			echo "Car was NOT removed successfully";
		}

		$query = "SELECT count(pid) as total FROM person_has_car WHERE ('license_num' = ?);";
		$result2 = $conn->prepare($query); 
		$result2->bind_param('s', $license_num);
		$param = $result2->execute();
		if ($param) {
			$result2->bind_result($col1);

			$result2->fetch();
			$result2->close();
			if ($col1 == 0 ){
				$stmt = $conn->prepare("DELETE FROM Car WHERE license_num = ? LIMIT 1");
				$stmt->bind_param('s', $license_num);
				if($stmt->execute()) {
					echo "\nCar deleted from Car table";
				}
				else {
					echo "There were problems deleting from the Car table";
				}
				$stmt->close();
			}
			else {
				echo "The Car exists for other people still";
			}
		}
		$results->close();
	}

	################################## We don't need an edit function for cars, they will just add a new car or delete one #####
	/*if ($_POST['action']=='edit'){
		$license_num = $_POST['license_num'];  ### Car ID 
		$openSeats = $_POST['openSeats'];
		$make = htmlspecialchars($_POST['make']);
		$model = htmlspecialchars($_POST['model']);
		$color = htmlspecialchars($_POST['color']);
		$license_num = htmlspecialchars($_POST['license_num']);

		$result = $conn->prepare("UPDATE Car SET openSeats=?, make=?, model=?, color=?, license_num=? WHERE (license_num = ?);");
		$result->bind_param('isssss', $openSeats, $make, $model, $color, $license_num, $license_num);

		if ($result->execute()) {
			echo $result->affected_rows()."Car was edited";
		}
		else {
			echo "Car was NOT edited";
		}
	}*/

	if ($_POST['action'] == 'get') {
		$result = $conn->prepare("SELECT color, make, model, license_num, openSeats FROM Car;");

		$result->execute();
		$result->bind_result($color, $make, $model, $license_num, $openSeats);
		while ($result->fetch()) {
	        array_push($returnObject, array("Color"=>$color, "Make"=>$make, "Model"=>$model,"License_num"=>$license_num,"Open_Seats"=>$openSeats));
	        //printf ("color: %s make: %s model: %s license_num: %s openSeats: %s\n <br>", $color, $make, $model, $license_num, $openSeats);
	    }
		$result->close();
		echo json_encode($returnObject);

	}

	if ($_POST['action'] == 'get_person_cars') {
		$pid = $_POST['pid'];

		$query = "SELECT count(*) as total FROM person_has_car WHERE ('pid' = ?);";
		$result = $conn->prepare($query); 
		$result->bind_param('i', $pid);
		$result->execute();
		$result->bind_result($col);
		$result->fetch();
		$result->close();
		if($col == 0) {
			echo "No cars were found for this person";
		}
		else {
			$result = $conn->prepare("SELECT firstName, lastName, color, make, model, license_num, openSeats FROM Car INNER JOIN 
				person_has_car INNER JOIN Person WHERE firstName = ? AND lastName = ?;");
			$result->bind_param('ss', $firstName, $lastName);

			if ($result->execute()) {
				$result->bind_result($color, $make, $model, $license_num, $openSeats);
				while ($result->fetch()) {
			        array_push($returnObject, array("Color"=>$color, "Make"=>$make, "Model"=>$model,"License_num"=>$license_num,"Open_Seats"=>$openSeats));
			        //printf ("color: %s make: %s model: %s license_num: %s openSeats: %s\n <br>", $color, $make, $model, $license_num, $openSeats);
			    }
				$result->close();
				echo json_encode($returnObject);
			}
			else {
				echo "Something went wrong when searching for cars by this person...";
			}
		}
	}

	$conn->close();
?>
