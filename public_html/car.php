<?php
	session_start();
	###PHP functions to add, modify, delete, and add assign cars to people
	include("sql/dbinfo.php");

	###Create connection
	$conn = new mysqli($host, $user, $pass, $db);
	###Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	###Insert function for Cars 
	if (strcmp($_GET['action'],'add')==0){
		$result = $conn->prepare("INSERT INTO Car (openSeats,make,model,color,license_num) 
		VALUES (?,?,?,?,?)");
		
		$openSeats=4;
		$make="Chevy";
		$model="Cavalier";
		$color="Green";
		$license_num="ge8 3ds";
		$result->bind_param('issss',$openSeats,$make,$model,$color,$license_num);

		if ($result->execute()) {
			echo "$result->insert_id";
			$cid = $result->insert_id;
			$result2 = $conn->prepare("INSERT INTO person_has_car (pid, cid) VALUES (?,?);");
			$result2->bind_param('ii', $pid, $cid);
			$pid=$_SESSION['pid'];

			if ($result2->execute()) {
				echo "New Car created successfully!!!";
			}
			$result2->close();
		}
		else {
		echo "Something went wrong with insert function";
		}
		$result->close();
	}

	###Remove function for Cars
	if (strcmp($_GET['action'],'remove')==0){
		$result = $conn->prepare("DELETE FROM Car WHERE cid=?");
		$result->bind_param('i',$carid);

		$carid= 1;//$_POST['cid'];
		if($result->execute())
			printf("%d Row Deleted.\n", $result->affected_rows);
		
		$result->close();
	}

	###Edit function for Cars
	###Change number of open seats
	if (strcmp($_GET['action'],'edit')==0){
		echo "update";
		$result = $conn->prepare("UPDATE Car SET openSeats=? WHERE cid=?");
		echo $result->prepare_error;
		$result->bind_param('ii',$openSeats,$carid);

		$openSeats=$_POST['seats'];
		$carid=$_POST['cid'];
		$result->execute();
		printf("%d Row updated.\n", $result->affected_rows);

		$result->close();
	}

	if ($_POST['action'] == 'get') {
		$result = $conn->prepare("SELECT color, make, model, license_num, openSeats FROM Car;");

		#$offset=0;
		$result->execute();
		$result->bind_result($color, $make, $model, $license_num, $openSeats);
		while ($result->fetch()) {
	        array_push($returnObject, array("Color"=>$color, "Make"=>$make, "Model"=>$model,"License_num"=>$license_num,"Open_Seats"=>$openSeats));
	        //printf ("color: %s make: %s model: %s license_num: %s openSeats: %s\n <br>", $color, $make, $model, $license_num, $openSeats);
	    }
		$result->close();
		echo json_encode($returnObject);

	}

	$conn->close();
?>