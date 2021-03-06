<?php 
include("sql/dbinfo.php");

// Create connection
$conn = new mysqli($host, $user, $pass, $db);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

//sign up account #addperson
if(strcmp($_POST['action'],"insert")==0){
	$firstName = $_POST['firstName'];
	$LastName = $_POST['lastName'];
	$email = $_POST['emailAddress'];
	
	$stmt = $conn->prepare("SELECT * FROM Person WHERE firstName=? AND LastName=? AND emailAddress=?");
	$stmt->bind_param('sss', $firstName, $LastName, $email);
	$stmt->execute();
	$stmt->store_result();
	if($stmt->num_rows > 0){
		exit;
	}

	$result = $conn->prepare("INSERT INTO Person (firstName,LastName,emailAddress) 
	VALUES (?,?,?)");
//	$result->bind_param('sss',$firstName,$LastName,$email);

	//$firstName= $_POST['firstName'];
	//$LastName=$_POST['lastName'];
	//$email = $_POST['emailAddress'];

	$result->bind_param('sss',$firstName,$LastName,$email);

	if ($result->execute()) {
		echo "You have signed up to drive for the event";
	}
	else {
		echo "Something went wrong with your sign up".mysql_error();
	}
	
	printf("\n%d Row inserted.\n", $result->affected_rows);
	$result->close();
}
//get people
if(strcmp($_POST['action'],"get")==0){
	$returnObject=array();
	$result = $conn->prepare("SELECT pid,firstName,LastName,emailAddress FROM Person WHERE pid=?");
	$result->bind_param('i',$pid);

	$pid=$_POST['pid'];
	if ($result->execute()) {
	$result->bind_result($pid,$firstname,$LastName,$email);
	while ($result->fetch()) {
		array_push($returnObject, array("First"=>$firstname, "Last"=>$LastName,"Email"=>$email));

        //printf ("id: %s name: %s start time: %s end time: %s description: %s type: %s\n <br>", $eid, $name,$start,$end,$description,$type);
    }
    	echo json_encode($returnObject);
	}
	else {
		echo "Something went wrong with your search";
	}

}
//delete
if(strcmp($_POST['action'], "delete")==0){
	$result = $conn->prepare("DELETE FROM Person WHERE pid=?");
	$result->bind_param('i',$personid);

	$personid=$_POST['pid'];
	if($result->execute())
		printf("%d Row Deleted.\n", $result->affected_rows);
	$result->close();
}

//get pid
if(strcmp($_POST['action'], "getPid")==0){
	$result = $conn->prepare("SELECT pid FROM Person WHERE emailAddress=?");
	$result->bind_param('s', $email);

	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];

	if($result->execute()){
		$pid;
		$result->bind_result($pid);
		$result->fetch();
		echo $pid;
	} else {
		echo "Person doesn't exist!";
	}
}
?>
