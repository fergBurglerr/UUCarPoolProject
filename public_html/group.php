<?php 
	include_once("sql/dbinfo.php");

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
	if(strcmp($_POST['action'], "add")==0){
		$gid = $_POST['gid'];
		$name = $_POST['name'];
		$sponsor = $_POST['sponsor'];

		$result = $conn->prepare("INSERT INTO Group(name,sponsor) VALUES (?,?)");
		$result->bind_param('si',$name,$sponsor);

		if($result->execute()) {
			printf("%d Group added.\n", $result->affected_rows);
		}
		else {
			echo false;
		}
		
		
		$result->close();
	}

	//edit
	if(strcmp($_POST['action'], "edit")==0){
		echo "update";
		$result = $conn->prepare("UPDATE Group SET name=?, $sponsor=? WHERE gid=?");
		echo $result->prepare_error;
		$result->bind_param('sii', $name, $sponsor, $gid);

		$name=$_POST['name'];
		$sponsor=$_POST['sponsor'];
		$gid=$_POST['gid'];

		if ($result->execute()) {
			printf("%d Group updated.\n", $result->affected_rows);
		}
		else {
			echo "Could not edit Group Information";
		}

		$result->close();
	}

	//remove
	if(strcmp($_POST['action'], "delete")==0){
		$gid = $_POST['gid'];
		$result = $conn->prepare("DELETE FROM Group WHERE gid=?");
		$result->bind_param('i', $gid);

		if($result->execute()) {
			printf("%d Group Deleted.\n", $result->affected_rows);
		}
		else {
			echo "Could not delete the group";
		}

		$result->close();
	}

	//getevent 
	if(strcmp($_POST['action'],"get")==0){
		$returnObject=array();
		$result = $conn->prepare("SELECT name,sponsor FROM Group");
		#$result->bind_param('i',$offset);

		$result->execute();
		$result->bind_result($name,$sponsor);
		while ($result->fetch()) {
			array_push($returnObject, array("Name"=>$name, "Sponsor"=>$sponsor));
	    }
	    echo json_encode($returnObject);

	    $result->close();
	}

	if(strcmp($_POST['action'],"add_to_group")==0){
		$gid = $_POST['gid'];
		$pid = $_POST['pid'];

		$result = $conn->prepare("INSERT INTO group_has_members VALUES (?,?)");
		$result->bind_param("ii", $gid, $pid);
		if ($result->execute()) {
			echo "User successfully added to group!";
		}
		else {
			echo "User was NOT able to be added to the group";
		}
	}

	if(strcmp($_POST['action'],"remove_from_group")==0){
		$gid = $_POST['gid'];
		$pid = $_POST['pid'];
	
		$result = $conn->prepare("DELETE FROM group_has_members WHERE gid = ? AND pid = ?)");
		$result->bind_param("ii", $gid, $pid);	
		if ($result->execute()) {
			echo "User successfully removed to group!";
		}
		else {
			echo "User was NOT able to be removed to the group";
		}
	}

	$conn->close();
?>