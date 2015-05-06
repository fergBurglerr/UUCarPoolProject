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
				setcookie('pid', $pid, time()+3600*24*14);
			} else {
				echo "Database failure, please try again later";
			}

		} else {
			echo 'ERROR: db error testing username';
		}
	}

	if($action=='login'){
		$sql = 'SELECT pid FROM Authenticate WHERE userName	=? AND password=?';
		if($stmt = $conn->prepare($sql)){
			$stmt->bind_param("ss", $username, $password);
			$stmt->execute();
			$stmt->store_result();

			if($stmt->num_rows > 0){
				$stmt->bind_result($pid);
				$stmt->fetch();
				setCookie('loggedin', true, time()+3600*24*14);
				setCookie('pid', $pid, time()+3600*24*14);
			} else {
				echo "Bad Log in info!";
			}

			$stmt->close();
		} else {
			echo "Database error: please try again later";
		}
	}
	$conn->close();
?>
