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
			collapsible: true,
			active: false
		});
	});
    });

    $('#eventTab').click(function(){
	$('#event').html('Loading...');
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
			collapsible: true,
			active: false
		});
	});
    });
    
    $("#adminForms").accordion({
    	collapsible: true,
    	active: false
    });
    
    /*$('#groupTab').click(function(){
	$('#group').html('');
	$.post('group.php',
	{
		action:"get",
		offset:0
	}, function(json){
		$("#group").html("<div id=\"groups\"></div>");
		console.log(json);
		$.each(JSON.parse(json), function(idx, obj){
			var datePattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
			var timePattern = /[0-9]{2}:[0-9]{2}/;
			$("#groups").append("<h3>" + obj.Name + "Lead by" + obj.Sponsor + "</h3>");
		});
		$("#groups").accordion({
			collapsible: true
		});
	});
    });*/

    $('#loginTab').click(function(){
	$('#login').html('Loading...');
	loginForm();
    });

    $('#carpoolTab').click(function(){
	$('#carpool').html('Loading...');
	carpoolTab();
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
		$.post('auth.php',
		{
			action:'login',
			username:username,
			password:password
		},
		function(resp){
			if(!(resp)){
				location.reload(true);
			} else {
				alert(resp);
			}
		});
	}
}

function logout(){
	$.get('logout.php',
	{
	},
	function(resp){
		location.reload(true);
	});
}

function loginForm(){
	if(!(getCookie('loggedin'))){
		$.get('loginForm.php', {loggedin:"false"},
		function(form){
			$('#login').html(form);
		});
	} else {
		var button = "<button type='button' onclick='logout()'>Logout</button>";
		$('#login').html(button);
	}
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
			} else if (!(email)){
				alert("An e-mail address is required!");
			} else {
				var good=true;
				//console.log('About to post');
				$.post('people.php',
				{
					action:'insert',
					firstName:firstname,
					lastName:lastname,
					emailAddress:email
					
				},
				function(peopleResponse){
					//console.log('Inserted into people');
					$.post('people.php',
					{
						action:'getPid',
						firstname:firstname,
						lastname:lastname,
						email:email
					},
					function(data){
						//console.log('got pid');
						var pid=data;
						if(phone){
							$.post('phone_num.php',
							{
								action:'add',
								number:phone,
								pid:pid
							},function(resp){
								if(resp)
									alert(resp);
							});
						}
						$.post('auth.php',
						{
							action:'add',
							username:username,
							password:password,
							pid:pid
						},
						function(resp){
							//console.log('Added to Authenticate');
							if(resp){
								alert(resp);
								good=false;
							} else {
							
								$.post('latlong.php',
								{
									action:'add',
									houseNumber:houseNumber,
									suiteNumber:suiteNumber,
									street:street,
									city:city,
									zip:zip,
									latitude:latitude,
									longitude:longitude
								},
								function(resp){
									//console.log('Address added');
									if(resp){
										alert(resp);
										good=false;
									} else {
										$.post('latlong.php',
										{
											action:'getAid',
											houseNumber:houseNumber,
											suiteNumber:suiteNumber,
											street:street,
											city:city,
											zip:zip,
											latitude:latitude,
											longitude:longitude
										},
										function(aid){
											console.log('got aid');
											$.post('latlong.php',
											{
												action:'matchAddressToPerson',
												aid:aid,
												pid:pid
											},
											function(resp){
												//console.log('Person and address linked');
												if(resp)
													alert(resp);
												else
													location.reload(true);
											});
										});
									}
								});
							}
						});
					});
					
				});
			}
		}
	});
	//console.log('( ' + latitude + ', ' + longitude + ' )');

	
}

function registerCar(){
	var pid = getCookie('pid');
	var make =$('#make').val();
	var model=$('#model').val();
	var color=$('#color').val();
	var seats=$('#numberOfSeats').val();
	var license=$('#licensePlate').val();
	if(!(color))
		color = 'other';
	
	if(!(make))
		alert('Make of car required!');
	else if(!(model))
		alert('Model of car required!');
	else if(!(seats))
		alert('Number of seats required!');
	else if(!(license))
		alert('License plate required!');
	else if(!(pid))
		alert('Something went wrong, please log out and back in!');
	else {
		$.post('car.php',
		{
			action:'add',
			pid:pid,
			openSeats:seats,
			make:make,
			model:model,
			color:color,
			license_num:license
		}, function(resp){
			if(resp)
				alert(resp)
			else
				$('#carpool').html('<h4>Registered car successfully!</h4><button type="button" onclick="carpoolTab()">Back</button>');
		});
	}
}

function carForm(){
	$.get('carForm.php',
	{
	},function(form){
		$('#carpool').html(form);
	});
}

function carpoolTab(){
	if(getCookie('loggedin')){
		$.get('carpool.php',
		{
		},
		function(form){
			$('#carpool').html(form);
			$.post('event.php',
			{
				action:"get",
				offset:0
			}, function(json){
				$.each(JSON.parse(json), function(idx, obj){
					var datePattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
					var timePattern = /[0-9]{2}:[0-9]{2}/;
					var optionString = '<option vale="' + obj.eid + '">' + obj.Name + ' on ' + datePattern.exec(obj.Start) + ' at ' + timePattern.exec(obj.Start) + '</option>';
					$('#driveEvent').append(optionString);
					$('#rideEvent').append(optionString);
				});
			});

		});
	} else {
		$('#carpool').html('<h3>Please log in or register to use the carpool web app!</h3>');
	}
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
