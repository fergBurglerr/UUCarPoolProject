<?PHP

include("dbinfo.php");//including my database connection
$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
	echo"<p>Connection to database failed...</p>".$conn->connect_error;
}


##### Function to add a new announcement 
#if ($_POST['action']=='add'){

	#$message = htmlspecialchars($_POST['text']);
	$message = htmlspecialchars("This worked fine!!!");

	if (strlen($message) < 2047) {
		$query = 'INSERT INTO Announcement (content, aDate) VALUES (?, ?);';
		$stmt = $conn->prepare($query);
		$stmt->bind_param("ss", $message, date("Y-m-d H:i:s"));
		$stmt->execute();

		echo "New announcement created successfully!!!";
		$stmt->close();
	}
	else {
		die("Content of message is too long!!!");
	}

#}

##### Function to edit an announcement
#else if ($_POST['action']=='edit'){

#}

##### Function to remove an announcement
#else if ($_POST['action']=='remove'){

#}

##### Retrieves all announcements 
#else if ($_POST['action']=='get'){

#}

#else {
	#echo"There was an error with the POST request, please contact Joe since it is probably his fault";
#}
mysqli_close($conn);
?>
