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

	$stmt = $conn->prepare("INSERT INTO person_drives_for_event VALUES (?,?)";);
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have signed up to drive for the event";
	}
	else {
		echo "Something went wrong with your sign up";
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

	$stmt = $conn->prepare("INSERT INTO person_needs_ride_for_event VALUES (?, ?)";);
	$stmt->bind_param('ii', $pid, $eid);

	if ($stmt->execute()) {
		echo "You have signed up for needing a ride to the event";
	}
	else {
		echo "Something went wrong with your sign up";
	}

	$stmt->close();
}

if(strcmp($_POST['action'],"find_drivers")==0)
	$returnObject=array();
	$eventName = $_POST['eventName'];

	$result = $conn->prepare("SELECT eventName, firstName, lastName, openSeats FROM Person P INNER JOIN person_has_car phc INNER JOIN
		person_drives_for_event pde INNER JOIN Event E WHERE E.eventName = ?";);
	$result->bind_param('s', $eventName);

	$result->execute();
	$result->bind_result($eventName, $firstName, $lastName, $openSeats);
	while ($result->fetch()) {
		array_push($returnObject, array("Event"=>$eventName, "FirstName"=>$firstName,"LastName"=>$lastName,"OpenSeats"=>$openSeats));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    echo json_encode($returnObject);
}

if(strcmp($_POST['action'],"find_riders")==0)
	$returnObject=array();
	$eventName = $_POST['eventName'];

	$result = $conn->prepare("SELECT eventName, firstName, lastName, houseNumber, street, city, state, zipcode FROM Person P INNER JOIN person_lives_at_address pla INNER JOIN
		Address INNER JOIN person_needs_ride_for_event pnr INNER JOIN Event E WHERE E.eventName = ?";);
	$result->bind_param('s', $eventName);

	$result->execute();
	$result->bind_result($eventName, $firstName, $lastName, $houseNumber, $street, $city, $state, $zipcode);
	while ($result->fetch()) {
		array_push($returnObject, array("Event"=>$eventName, "FirstName"=>$firstName,"LastName"=>$lastName,"HouseNumber"=>$houseNumber, "Street"=>$street, "City"=>$city, "State"=>$state, "Zipcode"=>$zipcode));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    echo json_encode($returnObject);
}

$result->close();

$conn->close();
?>
