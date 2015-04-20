$(document).ready(function () {
    $("#navBar").tabs();

    $('#homeTab').click(function(){
	window.location = "http://uuchurch.net";
    });

    $('#announceTab').click(function(){
	$('#announce').html('');
	$.post('announcements.php',
	{
		action:"get"	
	}, function(data){
		$('#announce').html(data);
	});
    });
});
