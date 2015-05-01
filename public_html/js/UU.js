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

}

function register(){

}
