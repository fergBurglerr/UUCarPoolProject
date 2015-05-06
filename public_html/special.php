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

	if(strcmp($_POST['action'],"find_events_attended_by_person")==0) {
		#Query 11 (Find all events that "Pastor Roth" attended )

		$returnObject = array();
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$stmt = $conn->prepare("SELECT eventName FROM Event E INNER JOIN person_goes_to_event INNER JOIN Person P WHERE P.firstName LIKE ? AND P.lastName LIKE ?");
		$stmt->bind_param('ss', $firstName, $lastName);
		if ($stmt->execute()) {
			$stmt->bind_result($eventName);
			while ($stmt->fetch()) {
				array_push($returnObject, array("eventName"=>$eventName));
			}
			echo json_encode($returnObject);
		}
		else {
			echo "Cannot find any events for that person";
		}
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_events_attended_by_person")==0) {
		#Query 12 (Find a list of all people who have a ride to the " ")

		$returnObject = array();
		$eventName = $_POST['eventName'];
		$stmt = $conn->prepare("SELECT P.firstName P.lastName FROM Person P INNER JOIN  driver_drives_rider ddr INNER JOIN Event E WHERE P.pid = ddr.pid_rider AND E.eventName LIKE ?");
		$stmt->bind_param('s', $eventName);
		if ($stmt->execute()) {
			$stmt->bind_result($firstName, $lastName);
			while ($stmt->fetch()) {
				array_push($returnObject, array("FirstName"=>$firstName, "LastName"=>$lastName));
			}
			echo json_encode($returnObject);
		}
		else {
			echo "Could not find that event";
		}
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_events_attended_by_person")==0) {
		#Query 13 (Return the number of people who attended the "Bible Study" on 4-13-2012)
		
		$returnObject = array();
		$eventName = $_POST['eventName'];
		$startTime = $_POST['startTime'];
		$stmt = $conn->prepare("SELECT E.eventName, count(pid) AS number_of_people FROM person_goes_to_event pae INNER JOIN Event E WHERE E.eventName LIKE ? AND E.startTime = ? GROUP BY (E.eventName, E.startTime)");
		$stmt->bind_param('s', $eventName, $startTime);
		if ($stmt->execute()) {
			$stmt->bind_result($eventName, $number_of_people);
			while ($stmt->fetch()) {
				array_push($returnObject, array("eventName"=>$eventName, "number"=>$number_of_people));
			}
			echo json_encode($returnObject);
		}
		else {
			echo "Cound not find that event";
		}
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_drivers_for_event")==0) {
		#Query 14 (Find how many people can drive to "Bible Study" on 3-12-2013)

		$returnObject = array();
		$eventName = $_POST['eventName'];
		$startTime = $_POST['startTime'];
		$stmt = $conn->prepare("SELECT count(person_drives_for_event.pid) AS drivers FROM person_drives_for_event INNER JOIN Event E WHERE E.eventName = ? AND E.startTime = ? GROUP BY (person_drives_for_event.eid)");
		$stmt->bind_param('ss', $eventName, $startTime);
		if ($stmt->execute()) {
			$stmt->bind_result($drivers);
			while ($stmt->fetch()) {
				array_push($returnObject, array("drivers"=>$drivers));
			}
			json_encode($returnObject);
		}
		else {
			echo "Cannot find this event";
		}
		$stmt->close();
	}

	if(strcmp($_POST['action'],"find_person_attened_most_events")==0) {
		#Query 15 (Find the name of the person who attended the most events)

		$returnObject = array();
		$stmt = $conn->query("SELECT P.firstName, P.lastName FROM Person P INNER JOIN person_goes_to_event pae GROUP BY (pae.pid) ORDER BY count(pae.eid) DESC LIMIT 10");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_underaged_people")==0) {
		#Query 16 (Find a list of all people under the age of 18 "not adults")

		$stmt = $conn->query("SELECT P.name FROM Person P WHERE P.age < 18");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_events_attendance")==0) {
		#Query 17 (Find a list of all events in order of attendence)

		$stmt = $conn->query("SELECT E.eventName FROM Event E INNER JOIN person_goes_to_event pge GROUP BY pge.eid ORDER BY count(pge.pid) DESC");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_average_attendance")==0) {
		#Query 18 (Find the average attendance at all events)

		$stmt = $conn->query("SELECT avg(Ph.pid) AS average FROM person_goes_to_event Ph INNER JOIN Event E");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_most_attened_events")==0) {
		#Query 19 find the event that the most people attended
		$stmt = $conn->query("SELECT E.eventName, count(pge.pid) FROM Event E INNER JOIN person_goes_to_event pge GROUP BY (pge.eid) ORDER BY count(pge.pid) DESC LIMIT 10");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	if(strcmp($_POST['action'],"find_most_attened_events")==0) {
		#Query 20 (Find the names of everyone who has driven to AT LEAST one event)

		$stmt = $conn->prepare("SELECT DISTINCT P.firstName, P.lastName FROM Person P INNER JOIN person_drives_for_event pde;");
		while($row = $stmt->fetch_array(MYSQL_ASSOC)) {
            $returnObject[] = $row;
	    }
	    echo json_encode($returnObject);
	    $stmt->close();
	}

	$conn->close();
?>