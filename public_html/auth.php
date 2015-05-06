<?php
	include_once("sql/dbinfo.php");

	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$p = $_POST;
	$action = $p['action'];
	$username = $p['username'];
	$password = sha1($p['password']);

	if($action=='add'){
		$pid = intval($p['pid']);

		$query = 'SELECT * FROM Authenticate WHERE userName=?';
				
		$test = $conn->prepare($query);

		$test->bind_param('s', $username);
		if($test->execute()){
			$test->store_result();
	
			if($test->num_rows > 0){
				echo 'ERROR: that username is already in use!';
				exit;
			}
			$test->close();
	
			$sql = 'INSERT INTO Authenticate (userName, password, pid) VALUES (?,?,?)';
			
			if($stmt = $conn->prepare($sql)){
				$stmt->bind_param("ssi", $username, $password, $pid);
				$stmt->execute();
				$stmt->close();

				setcookie('loggedin', true, time()+3600*24*14);
				setcookie('username', $username, time()+3600*24*14);
			} else {
				echo "Database failure, please try again later";
			}

		} else {
			echo 'ERROR: db error testing username';
		}
	}
	$conn->close();
?>
