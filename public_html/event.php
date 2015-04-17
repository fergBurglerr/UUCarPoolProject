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
if(strcmp($_POST['action'],"insert")==0){

}



//remove


//getevent 


$conn->close();
?>