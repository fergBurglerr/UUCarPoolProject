<button type='button' onclick='carpoolTab()' class='small'>Back</button>
<h3>Register a car!</h3>
<form id='carRegister'>
	<p>Car make: <input type="text" id="make" maxlength="62" placeholder="make"><span class="small">Ex. Toyota</span></p>
	<p>Car model:<input type="text" id="model" maxlength="62" placeholder="model"><span class="small">Ex. Camry</span></p>
	<p>Car color:<input type="text" id="color" maxlength="19" placeholder="color"></p>
	<br>
	<p>Number of seats:<input type="number" id="numberOfSeats" value="4"></p>
	<p>License plate:<input type="text" id="licensePlate" maxlength="19" value="MO:"><span class="small">Ex. MO:123ABC</span></p>
	<br>
	<button type="button" onclick="registerCar()">Register car</button>
</form>
