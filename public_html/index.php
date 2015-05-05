<!DOCTYPE html>
<html>
<head>
	<title>UUCC Carpool WebApp</title>
	<link rel="stylesheet" type="text/css" href="css/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="css/UU.css">
	<script src="js/jquery-1.11.2.min.js"></script>
	<script src="js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="js/UU.js"></script>
</head>
<body>
	<h3 id="pageHeader">Unitarian Universalist Church of Columbia, Missouri</h3>
	<div id="navBar">
		<ul>
			<li id="loginTab"><a href="#login">Login/out</a></li>
			<li id="homeTab"><a href="#home">Home</a></li>
			<li id="eventTab"><a href="#event">Events</a></li>
			<li id="announceTab"><a href="#announce">Announcements</a></li>
			<li id="groupTab"><a href="#group">Groups</a></li>
			<li id="carpoolTab"><a href="#carpool">Carpooling</a></li>
			<li id="adminTab"><a href="#admin">Admin</a></li>	
		</ul>
		<div id="login">
			login
		</div>
		<div id="home">
			Home
		</div>
		<div id="event">
			Events
		</div>
		<div id="announce">
			Announcements
		</div>
		<div id="group">
			Groups
		</div>
		<div id="carpool">
			Carpool
		</div>
		<div id="admin">
			<div id="adminForms">
				<h3>Create an Event</h3>
				<div id="createEventForm">
					<form id="eventForm">
						Title<input type="text" name="eventName"><br>
						Start Date<input type="text" name="startDate"><br>
						Start Time<input type="text" name="startTime"><br>
						End Date<input type="text" name="endDate"><br>
						End Time<input type="text" name="endTIme"><br>
						House #<input type="text" name="eventHouseNumber"><br>
						Suite Number<input type="text" name="eventSuiteNumber"><br>
						Street<input type="text" name="eventStreet"><br>
						City<input type="text" name="eventCity"><br>
						Zipcode<input type="text" name="eventZipcode"><br>
						Description<br>
					</form>
					<textarea name="eventDescription" form="eventForm">Type description here...</textarea>
				</div>
				<h3>Create an Announcement</h3>
				<div id="createAnnouncementForm">
					<form>
					
					</form>
				</div>
				<h3>Manage Groups</h3>
				<div id="manageGroupsForm">
					<form>
					
					</form>
				</div> 
			</div>
		</div>
	</div>
	
	
	<?php

	?>

</body>
