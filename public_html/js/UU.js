var geocoder = new google.maps.Geocoder();

$(document).ready(function () {
    
    $("#navBar").tabs();

    loginForm();

    $('#homeTab').click(function(){
	window.location = "http://uuchurch.net";
    });

    $('#announceTab').click(function(){
	$('#announce').html('');
	$.post('announcements.php',
	{
		action:"get"
	}, function(json){
		$("#announce").html("<div id=\"Announcements\"></div>");
		$.each(JSON.parse(json), function(idx, obj){
			var pattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
			$("#Announcements").append("<h3>" + obj.Content.substring(0,10) + "..." + "\t|\t" + pattern.exec(obj.Date) + "</h3>");
			$("#Announcements").append("<div id=\"announcement" + idx + "\"></div>");
			$("#announcement" + idx).append("<p>" + obj.Content + "</p>");
		});
		$("#Announcements").accordion({
			collapsible: true
		});
	});
    });

    $('#eventTab').click(function(){
	$('#event').html('');
	$.post('event.php',
	{
		action:"get",
		offset:0
	}, function(json){
		$("#event").html("<div id=\"events\"></div>");
		console.log(json);
		$.each(JSON.parse(json), function(idx, obj){
			var datePattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
			var timePattern = /[0-9]{2}:[0-9]{2}/;
			$("#events").append("<h3>" + obj.Name + "\t|\t" + "<i>" + obj.Type + "</i>" + "</h3>");
			$("#events").append("<div id=\"event" + idx + "\"></div>");
			$("#event" + idx).append("<p>Start Time: " + datePattern.exec(obj.Start) + "at " + timePattern.exec(obj.Start) + "</i>" + "</p>");
			$("#event" + idx).append("<p>End Time: " + datePattern.exec(obj.End) + "at " + timePattern.exec(obj.End) + "</i>" + "</p>");
			$("#event" + idx).append("<p>" + obj.Description + "</p>");
		});
		$("#events").accordion({
			collapsible: true
		});
	});
    });

    $('#loginTab').click(function(){
	$('#login').html('');
	loginForm();
    });
});

function login(){
	var username = $('#username').val();
	var password = $('#password').val();
	if(!(username.trim())){
		alert("Username field can't be empty!");
	} else if (!(password.trim())){
		alert("Password field can't be empty!");
	} else {
		$.post('login.php',
		{
			username:username,
			password:password
		},
		function(data){
			
		});
	}
}

function loginForm(){
	$.get('loginForm.php', {loggedin:"false"},
	function(form){
		$('#login').html(form);
	});
}

function registerForm(){
	$.get('registerForm.php', {loggedin:"false"},
	function(form){
		$('#login').html(form);
		$('#register').css('background-color', '#EEE');

	});
}

function register(){
	var username = $('#username').val();
	var password = $('#password').val();
	var checkPass=$('#password2').val();
	var houseNumber = $('#houseNumber').val();
	var suiteNumber = $('#suiteNumber').val();
	var street = $('#street').val();
	var city = $('#city').val();
	var zip = $('#zip').val();
	var email = $('#email').val();
	var phone = $('#phone').val();
	var firstname = $('#firstname').val();
	var lastname = $('#lastname').val();
	var googleAddress = houseNumber + " " + street + ", " + city + ", MO, " + zip;
	var latitude=0;
	var longitude=0;
	geocoder.geocode( {'address': googleAddress},function(results, status){
		if(status == google.maps.GeocoderStatus.OK){
			latitude = results[0].geometry.location.lat();
			longitude = results[0].geometry.location.lng();

			if(!(username.trim())){
				alert("Username can't be blank");
			} else if (!(password.trim())){
				alert("Password can't be blank");
			} else if (!(checkPass.trim())){
				alert("Please retype password in second box!");
			} else if (!(password == checkPass)){
				alert("Passwords don't match!");
			} else if (!(houseNumber.trim())){
				alert("House number can't be blank!");
			} else if (!(street.trim())){
				alert("Street name can't be blank!");
			} else if (!(city.trim())){
				alert("City name can't be blank!");
			} else if (!(zip.trim())){
				alert("Zip code can't be blank!");
			} else if (!(zip.length==5)){
				alert("Zip code must be 5 chars long!");
			} else if (!(firstname.trim())){
				alert("First name required");
			} else if (!(lastname.trim())){
				alert("Last name required");
			} else if ((!(email)) && (!(phone))){
				alert("An e-mail address or phone number is required!");
			} else {
				console.log('About to post');
				$.post('register.php',
				{
					username:username,
					password:password,
					houseNumber:houseNumber,
					suiteNumber:suiteNumber,
					street:street,
					city:city,
					zip:zip,
					email:email,
					phone:phone,
					latitude:latitude,
					longitude:longitude,
					firstname:firstname,
					lastname:lastname
				},
				function(data){
					$('#register').html(data);
				});
			}
		}
	});
	//console.log('( ' + latitude + ', ' + longitude + ' )');

	
}

//getCookie function curtosey of w3 schools
function getCookie(cname){
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++){
 		var c = ca[i].trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length)
	}
	return "";
}
