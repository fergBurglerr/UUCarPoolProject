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
	$result->execute();
	printf("%d Row Deleted.\n", $result->affected_rows);

	$result->close();
}

//getevent 
if(strcmp($_POST['action'],"get")==0){
	$result = $conn->prepare("SELECT eid,eventName,startTime,endTime,description,eventType FROM Event ORDER BY startTime limit 10 offset ?");
	$result->bind_param('i',$offset);

	$offset=0;
	$result->execute();
	$result->bind_result($eid,$name,$start,$end,$description,$type);
	while ($result->fetch()) {
        printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
}


$result->close();


$conn->close();
?>