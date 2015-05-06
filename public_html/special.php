<?php
// PHP functions to add, modify, delete, and add assign addresses to people 
	include("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	/////////////////////////////////////////// Special queries //////////////////////////////////////////////

	#Query 1 (Find all driver names and number of seats in their car)
	if(strcmp($_POST['action'],"find_drivers_and_spots")==0) {
		$returnObject=array();
		$stmt = $conn->query("SELECT P.firstName, P.lastName, C.openSeats 
			FROM Person P INNER JOIN person_has_car INNER JOIN Car C");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_events_by_group")==0) {
		#Query 2 (Find all events from a certain Group)
		
		$gid = $_POST['gid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT E.eventName FROM Event E INNER JOIN Group G WHERE E.gid = ?;");
		$stmt->bind_param('i', $gid);
		$stmt->execute();
		$stmt->bind_result($eventName);
		while ($stmt->fetch()) {
			array_push($returnObject, array("Event_Name"=>$eventName));
	    }
    	echo json_encode($returnObject);
    	$stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_in_group")==0) {
		#Query 3 (Find the names of all people from are in the "young adults" group)

		$gid = $_POST['gid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT P.firstName, P.lastName FROM Person P INNER JOIN group_has_members INNER JOIN Group G WHERE G.gid = ?");
		$stmt->bind_param['i', $gid];
		$stmt->execute();
		$stmt->bind_result($firstName, $lastName);
		while ($stmt->fetch()) {
			array_push($returnObject, array("FirstName"=>$firstName, "LastName"=>$lastName));
		}
		echo json_encode($returnObject);
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_who_need_ride")==0) {
		#Query 4 (Find the names of all people who need a ride to the event named "test_name")
		
		$eid = $_POST['eid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT P.firstName, P.lastName FROM Person P INNER JOIN person_needs_ride_for_event INNER JOIN Event E WHERE E.eid = ?");
		$stmt->bind_param('i', $eid);
		$stmt->execute();
		$stmt->bind_result($firstName, $lastName);
		while ($stmt->fetch()) {
			array_push($returnObject, array("FirstName"=>$firstName, "LastName"=>$lastName));
		}
		echo json_encode($returnObject);
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_personal_email")==0) {
		#Query 5 (Find the email addresses from all People)-----??? Check this one ???
		
		$pid = $_POST['pid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT E.address FROM Person P
		 INNER JOIN Email E INNER JOIN person_has_email WHERE P.pid = ?"); // this one might be wrong (check the joining of the tables)
		$stmt->bind_param('i', $pid);
		$stmt->execute();
		$stmt->bind_result($address);
		while ($stmt->fetch()) {
			array_push($returnObject, array("Email"=>$address));
		}
		echo json_encode($returnObject);
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_names_of_ooc_events")==0) {
		#Query 6 (Find all names of all out of Church Events)

		$eid = $_POST['eid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT E.name FROM Events E INNER JOIN event_out_of_church INNER JOIN event_has_address INNER JOIN Address A WHERE event_out_of_church.eid = ?");
		$stmt->bind_param('i', $eid);
		$stmt->execute();
		$stmt->bind_result($event_name);
		while ($stmt->fetch()) {
			array_push($returnObject, array("Name"=>$event_name));
		}
		echo json_encode($returnObject);
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_attending_event")==0) {
		#Query 7 (Find the name and person ID of all people who are going to "Happy Hour" who are not in the "Adult" group)

		$eventName = $_POST['eventName'];
		$groupName = $_POST['groupName'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT P.firstName, P.lastName, P.pid FROM Person P INNER JOIN person_goes_to_event INNER JOIN Event E WHERE E.eventName LIKE ? AND P.pid NOT IN (SELECT P.pid FROM Person P INNER JOIN group_has_members INNER JOIN GROUP G WHERE G.name LIKE ?)");
		$stmt->bind_param('ss', $eventName, $groupName);
		if ($stmt->execute()) {
			$stmt->bind_result($firstName, $lastName, $person_id);
			while ($stmt->fetch()) {
				array_push($returnObject, array("FirstName"=>$firstName, "LastName"=>$lastName, "person_id"=>$person_id));
			}
			echo json_encode($returnObject);
		}
		else {
			echo "Could not find group or event by name";
		}
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_without_email_or_phone")==0) {
		#Query 8 (Find the names of all people who DO NOT have an email or a phone number)

		$returnObject = array();
		$stmt = $conn->query("SELECT P.firstName, P.lastName FROM Person P WHERE P.pid NOT IN (SELECT P.pid FROM person_has_email) OR P.pid NOT IN (SELECT P.pid FROM person_has_phone)");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_at_all_events")==0) {
		#Query 9 (Find the names of all people who attend ALL events)

		$returnObject = array();
		$stmt = $conn->query("SELECT P.firstName, P.lastName FROM Person P WHERE NOT EXISTS (SELECT E.eid FROM Events E WHERE E.eid NOT IN (SELECT Pae.eid FROM person_goes_to_event Pae WHERE Pae.pid = P.pid))");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_people_at_all_events")==0) {
		#Query 10 (Find all announcements from the "Religious Studies" group)

		$gid = $_POST['gid'];
		$returnObject = array();
		$stmt = $conn->prepare("SELECT aid, content, aDate FROM Announcement INNER JOIN group_has_announcements gha INNER JOIN Group G WHERE G.gid = ?");
		$stmt->bind_param('i', $gid);
		$stmt->bind_result($aid, $content, $aDate);
		while ($stmt->fetch()) {
			array_push($returnObject, array("aid"=>$aid, "content"=>$content, "aDate"=>$aDate));
		}
		echo json_encode($returnObject);
		$stmt->close();
	}

	$conn->close();
?>