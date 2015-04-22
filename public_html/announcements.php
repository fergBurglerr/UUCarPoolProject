<?PHP

include("sql/dbinfo.php");//including my database connection
$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
	echo"<p>Connection to database failed...</p>".$conn->connect_error;
}


##### Function to add a new announcement and adds a tuple into the event_has_announcement table to tie
##### the announcement with an event 
if ($_POST['action']=='add'){

	$message = htmlspecialchars($_POST['text']);
	#$message = htmlspecialchars("This worked fine!!!"); ### test data 
	$eid = $_POST['eid']; ### Event ID for the event in which the announcement is made for 
	#$eid = 1; ### test data 

	if (strlen($message) < 2047) { //checks that the message isn't too long 
		$query = 'INSERT INTO Announcement (content, aDate) VALUES (?, ?);';
		$stmt = $conn->prepare($query);
		$stmt->bind_param("ss", $message, date("Y-m-d H:i:s"));
		if ($stmt->execute()) { // after the announcement is valid, tie it to the event that it is for 
			echo "$stmt->insert_id";
			$aid = $stmt->insert_id; //Grabs the most recent aid created 
			$stmt2 = $conn->prepare("INSERT INTO event_has_announcements (aid, eid) VALUES (?,?);");
			$stmt2->bind_param('ii', $aid, $eid);

			if ($stmt2->execute()) { //print out a success message if both work successfully 
				echo "New announcement created successfully!!!";
			}
			else {
				echo "The event does not exist";
			}
		}
		else {
			echo "Something went wrong with insert function";
		}
		$stmt->close();
		$stmt2->close();
	}
	else {
		die("Content of message is too long!!!");
	}

}

##### Function to edit an announcement
else if ($_POST['action']=='edit'){
	$message = htmlspecialchars($_POST['content']);  #this post and the one below would be hidden fields
	#$message = htmlspecialchars('The old the old message'); 
	$announcement_key = $_POST['announcement_primary_key'];  #this would also be a hidden field 
	#$announcement_key = 3;

	$query = 'UPDATE Announcement SET content=?, aDate=? WHERE (aid = ?);';
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ssi", $message, date("Y-m-d H:i:s"), $announcement_key);
	if ($stmt->execute()) {
		echo "The announcement was updated";
	}
	else {
		echo "Something went wrong with the edit function";
	}
	$stmt->close();
}

##### Function to remove an announcement
else if ($_POST['action']=='remove'){
	$announcement_id = htmlspecialchars($_POST['announcement_id']);
	#$announcement_id = htmlspecialchars('1');

	$query = 'DELETE FROM Announcement WHERE (aid = ?) LIMIT 1;';
	$stmt = $conn->prepare($query);
	$stmt->bind_param("i", $announcement_id);
	if ($stmt->execute()) {
		echo "Successfully removed announcement!!!";
	}
	else {
		echo "Something went wrong with remove function";
	}
	$stmt->close();
}

##### Retrieves all announcements 
#else if ($_POST['action']=='get'){  #This all works perfectly 
	$query = 'SELECT content, aDate FROM Announcement;';
	$stmt = $conn->prepare($query);
	$stmt->execute();
	$stmt->bind_result($content, $aDate);
	while($stmt->fetch())
		{
			echo $content. ": " . $aDate;
			echo "<br />";
			echo "$row";
			echo $row;
		}
#}

#else {
	#echo"There was an error with the POST request, please contact Joe since it is probably his fault";
#}
$conn->close();
?>
