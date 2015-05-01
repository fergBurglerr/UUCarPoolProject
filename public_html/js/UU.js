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
			$("#Announcements").append("<h3>" + obj.Date + "</h3>");
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
		$('#event').html('<div id="events"></div>');
		$('#events').html(json);
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
	} else if ((!(email)) && (!(phone))){
		alert("An e-mail address or phone number is required!");
	} else {
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
			phone:phone
		},
		function(data){
			
		});
	}
}
