$(document).ready(function () {
    $("#navBar").tabs();

    $.get('loginForm.php', {loggedin:"false"},
    function(form){
	$('#login').html(form);
    });


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
});

function login(){
	console.log('hit');
	$.post('login.php',
	{
		username:$('#username').val(),
		password:$('#password').val()
	},
	function(data){
		console.log($('#username').val());
	});
}
