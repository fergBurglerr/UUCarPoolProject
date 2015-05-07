<form id="eventForm">
	Title<input type="text" id="eventName"><br>
	Type<input type="text" id="eventType"><br>
	Start Date<input type="text" id="startDate">(YYYY-MM-DD)<br>
	Start Time<input type="text" id="startTime">(HH:MM:SS)<br>
	End Date<input type="text" id="endDate">(YYYY-MM-DD)<br>
	End Time<input type="text" id="endTime">(HH:MM:SS)<br>
	House #<input type="text" id="eventHouseNumber"><br>
	Suite Number<input type="text" id="eventSuiteNumber"><br>
	Street<input type="text" id="eventStreet"><br>
	City<input type="text" id="eventCity"><br>
	Zipcode<input type="text" id="eventZipcode"><br>
	Description<br>
	<textarea id="eventDescription" form="eventForm">Type description here...</textarea><br>
	<button type="button" onclick="makeEvent()">Create Event</button>
</form>