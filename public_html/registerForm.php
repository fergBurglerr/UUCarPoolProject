<div id="register">
	<form id="registerForm">
		<h3>Login info</h3>
		<p>Desired Username:<input type="text" id="username" maxlength="15" placeholder="username"></p>
		<p>Desired Password:<input type="password" id="password" placeholder="password"></p>
		<p>Repeat Password:<input type="password" id="password2" placeholder="password"></p>
		<br>
		<h3>Address:</h3>
		<p>House number:<input type="number" id="houseNumber" placeholder="House Number"> Suite number (opt):<input type="number" id="suiteNumber" placeholder="Suite Number"></p>
		<p>Street:<input type="text" id="street" maxlength="126" placeholder="Street Name"></p>
		<p>City:<input type="text" id="city" value="Columbia" maxlength="126"></p>
		<p>Zip: <input type="text" id="zip" value="65201" maxlength="5"></p>
		<br>
		<h3>Contact Info:</h3>
		<p>First name: <input type="text" id="firstname" maxlength="62" placeholder="first"> Last name: <input type="text" id="lastname" maxlength="62" placeholder="last"></p>
		<h6>E-mail required, phone optional</h6>
		<p>E-mail: <input type="text" id="email" placeholder="email@provider.com" maxlength="254"></p>
		<p>Phone number: <input type="text" id="phone" placeholder="##########" maxlength=10><span class="small">format: ##########</span></p>
		<button type="button" onclick="register()">Register</button>
	</form>
	<br>
</div>
