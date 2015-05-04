<?php 
include("sql/dbinfo.php");

//$conn = mysql_connect($host, $user, $pass);
// if (!$conn) {
//     die('Could not connect: ' . mysql_error());
// }

// Create connection
$conn = new mysqli($host, $user, $pass, $db);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
//insert
if(strcmp($_POST['action'],"insert")==0){
	$result = $conn->prepare("INSERT INTO Event (eventName,startTime,endTime,description,eventType) 
	VALUES (?,?,?,?,?)");
	$result->bind_param('sssss',$eventName,$startTime,$endTime,$description,$eventType);

	$eventName=$_POST['eventName'];
	$startTime=$_POST['startTime'];
	$endTime = $_POST['endTime'];
	$description = $_POST['description'];
	$eventType = $_POST['test'];

	$result->execute();
	
	printf("%d Row inserted.\n", $result->affected_rows);
	$result->close();

	if (strcmp($_POST['out_church'], "out")==0) {
		$query = 'SELECT eid FROM Event WHERE $eventName = ? AND $startTime = ? AND $endTime = ? AND $eventType = ?';
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param('ssss' $eventName, $startTime, $endTime, $eventType);
			$result = $stmt->execute();
			$stmt->store_result();
			$row = $result->fetch_assoc();
			$eid = $row["eid"];
			$result->free();
			$stmt->close();
	
			$stmt = $conn->prepare("INSERT INTO event_out_of_church VALUES (?);");
			$stmt->bind_param('i', $eid);
			if ($stmt->execute()) {
				echo "Event was designated as an out of church event";
			}
			$stmt->close();
		}
	}
	else {
		$query = 'SELECT eid FROM Event WHERE $eventName = ? AND $startTime = ? AND $endTime = ? AND $eventType = ?';
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param('ssss' $eventName, $startTime, $endTime, $eventType);
			$result = $stmt->execute();
			$stmt->store_result();
			$row = $result->fetch_assoc();
			$eid = $row["eid"];
			$result->free();
			$stmt->close();

			$stmt = $conn->prepare("INSERT INTO event_in_church VALUES (?);");
			$stmt->bind_param('i', $eid);
			if ($stmt->execute()) {
				echo "Event was designated as an in church event or could not be chosen as an out of church event";
			}
			$stmt->close();
		}
	}
}
//edit
if(strcmp($_POST['action'],"edit")==0){
	echo "update";
	$result = $conn->prepare("UPDATE Event SET eventName=? WHERE eid=?");
	echo $result->prepare_error;
	$result->bind_param('si',$edit,$eventid);

	$option=$_POST['option'];
	$edit=$_POST['edit'];
	$eventid=$_POST['eid'];
	$result->execute();
	printf("%d Row updated.\n", $result->affected_rows);

	$result->close();
}

//remove
if(strcmp($_POST['action'], "delete")==0){
	$result = $conn->prepare("DELETE FROM Event WHERE eid=?");
	$result->bind_param('i',$eventid);

	$eventid=$_POST['eid'];
	if($result->execute())
		printf("%d Row Deleted.\n", $result->affected_rows);

	$result->close();
}

//getevent 
if(strcmp($_POST['action'],"get")==0){
	$returnObject=array();
	$result = $conn->prepare("SELECT eid,eventName,startTime,endTime,description,eventType FROM Event ORDER BY startTime limit 10 offset ?");
	$result->bind_param('i',$offset);

	$offset=0;
	$result->execute();
	$result->bind_result($eid,$name,$start,$end,$description,$type);
	while ($result->fetch()) {
		array_push($returnObject, array("Name"=>$name, "Start"=>$start,"End"=>$end,"Description"=>$description,"Type"=>$type));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    	echo json_encode($returnObject);

}

if(strcmp($_POST['action'],"can_drive")==0){
	$eid = $_POST['eid'];
	$pid = $_POST['pid'];

	$stmt = $conn->prepare("INSERT INTO person_drives_for_event VALUES (?,?)");
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have signed up to drive for the event";
	}
	else {
		echo "Something went wrong with your sign up";
	}

	$stmt->close();
}

if(strcmp($_POST['action'],"remove_can_drive")==0) {
	$eid = $_POST['eid'];
	$pid = $_POST['pid'];

	$stmt = $conn->prepare("DELETE FROM person_drives_for_event WHERE pid = ? AND eid = ?");
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have been removed from the driving list";
	}
	else {
		echo "You could not be removed from the driving list for some reason";
	}

	$stmt->close();
}

if(strcmp($_POST['action'],"need_ride")==0){
	#$eventName = $_POST['eventName'];
	#$firstName = $_POST['firstName'];
	#$lastName = $_POST['lastName'];
	#$houseNumber = $_POST['houseNumber'];
	#$street = $_POST['street'];
	#$city = $_POST['city'];
	#$state = $_POST['state'];
	#$zipcode = $_POST['zipcode'];

	$pid=$_POST['pid'];
	$eid=$_POST['eid'];

	$stmt = $conn->prepare("INSERT INTO person_needs_ride_for_event VALUES (?, ?)");
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have signed up for needing a ride to the event";
	}
	else {
		echo "Something went wrong with your sign up";
	}

	$stmt->close();
}

if(strcmp($_POST['action'],"remove_need_ride")==0) {
	$eid = $_POST['eid'];
	$pid = $_POST['pid'];

	$stmt = $conn->prepare("DELETE FROM person_needs_ride_for_event WHERE pid = ? AND eid = ?");
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have been removed from the riders list";
	}
	else {
		echo "You could not be removed from the riders list for some reason";
	}

	$stmt->close();
}

if(strcmp($_POST['action'],"find_drivers")==0){
	$returnObject=array();
	$eventName = $_POST['eventName'];

	$result = $conn->prepare("SELECT eventName, firstName, lastName, openSeats FROM Person P INNER JOIN person_has_car phc INNER JOIN
		person_drives_for_event pde INNER JOIN Event E WHERE E.eventName = ?");
	$result->bind_param('s', $eventName);

	$result->execute();
	$result->bind_result($eventName, $firstName, $lastName, $openSeats);
	while ($result->fetch()) {
		array_push($returnObject, array("Event"=>$eventName, "FirstName"=>$firstName,"LastName"=>$lastName,"OpenSeats"=>$openSeats));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    echo json_encode($returnObject);
}

if(strcmp($_POST['action'],"find_riders")==0){
	$returnObject=array();
	$eventName = $_POST['eventName'];

	$result = $conn->prepare("SELECT eventName, firstName, lastName, houseNumber, street, city, state, zipcode FROM Person P INNER JOIN person_lives_at_address pla INNER JOIN
		Address INNER JOIN person_needs_ride_for_event pnr INNER JOIN Event E WHERE E.eventName = ?");
	$result->bind_param('s', $eventName);

	$result->execute();
	$result->bind_result($eventName, $firstName, $lastName, $houseNumber, $street, $city, $state, $zipcode);
	while ($result->fetch()) {
		array_push($returnObject, array("Event"=>$eventName, "FirstName"=>$firstName,"LastName"=>$lastName,"HouseNumber"=>$houseNumber, "Street"=>$street, "City"=>$city, "State"=>$state, "Zipcode"=>$zipcode));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    echo json_encode($returnObject);
}

if(strcmp($_POST['action'], "designate_driver")==0) {
	$pid_driver = $_POST['pid_driver'];
	$pid_rider = $_POST['pid_rider'];
	$eid = $_POST['eid'];

	$stmt = $conn->prepare("INSERT INTO driver_drives_rider VALUES (?, ?, ?)");
	$stmt->bind_param('iii', $pid_driver, $pid_rider, $eid);

	if ($stmt->execute()) {
		echo "You have chosen to drive to the event";
	}
	else {
		echo "Something went wrong with your choice";
	}

	$stmt->close();
}

if(strcmp($_POST['action'], "add_event_to_group")==0) {
	$eid = $_POST['eid'];
	$gid = $_POST['gid'];

	$test = $conn->prepare("SELECT gid FROM Event WHERE $eid = ?;");
	$test->bind_param('i', $eid);
	$result = $test->execute();
	$test->store_result();
	$row = $result->fetch_assoc();

	if ($row["gid"] == NULL) {
		$stmt = $conn->prepare("UPDATE Event SET gid = ? WHERE eid = ? AND gid IS NULL;");
		$stmt->bind_param('ii', $gid, $eid);
		if ($stmt->execute()) {
			echo "Group was successfully added as the owner of the event!";
		}
		else {
			echo "Group could not be added as the owner for the event";
		}
		$stmt->close();
	}
	else {
		echo "Event could not be found!!!";
		return;
	}
	$test->close();
}

if (strcmp($_POST['action'], "get_event_address")==0) {
	$returnObject=array();
	$eid = $_POST['eid'];

	$query = 'SELECT aid FROM event_has_address WHERE $eid = ?';
	if ($stmt = $conn->prepare($query)) {
		$stmt->bind_param('i', $eid);
		$result = $stmt->execute();
		$stmt->store_result();
		$row = $result->fetch_assoc();
		$aid = $row["aid"];
		$result->free();
		$stmt->close();

		$result = $conn->prepare("SELECT houseNumber, suiteNumber, street, city, zipcode FROM Address WHERE aid = ?");
		$result->bind_param('i', $aid);
		if ($result->execute()) {
			$result->bind_result($houseNumber, $suiteNumber, $street, $city, $zipcode);
			while ($result->fetch()) {
			array_push($returnObject, array("House_number"=>$houseNumber, "Suite"=>$suiteNumber,"Street"=>$street,"city"=>$city,"Zipcode"=>$zipcode));
			//printf ("houseNumber: %s suiteNumber: %s street: %s city: %s zipcode: %s\n <br>", $houseNumber, $suiteNumber, $street, $city, $zipcode);
	    }
    	echo json_encode($returnObject);
		}
		else {
			echo "Address could not be found!!!";
		}
	}
	else {
		echo "Address could not be added";
		return NULL;
	}

}

$result->close();

$conn->close();
?>
