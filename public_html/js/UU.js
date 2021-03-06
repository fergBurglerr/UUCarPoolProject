var geocoder = new google.maps.Geocoder();

$(document).ready(function () {
    
    $("#navBar").tabs();

    loginForm();

    $('#homeTab').click(function(){
	window.location = "http://uuchurch.net";
    });

    $('#announceTab').click(function(){
	loading('#announce');
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
	loading('#event');
	$.post('event.php',
	{
		action:"get",
		offset:0
	}, function(json){
		$("#event").html("<div id=\"events\"></div>");
		console.log(json);
		$.each(JSON.parse(json), function(idx, obj){
			var datePattern = /[0-9]{4}-[0-1][0-9]-[0-3][0-9]/;
			var timePattern = /[0-9]{2}:[0-9]{2}/;
			$("#events").append("<h3>" + obj.Name + "\t|\t" + "<i>" + obj.Type + "</i>" + "</h3>");
			$("#events").append("<div id=\"event" + idx + "\"></div>");
			$("#event" + idx).append("<p>Start Time: " + datePattern.exec(obj.Start) + " at " + timePattern.exec(obj.Start) + "</i>" + "</p>");
			$("#event" + idx).append("<p>End Time: " + datePattern.exec(obj.End) + " at " + timePattern.exec(obj.End) + "</i>" + "</p>");
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
    
    $('#groupTab').click(function(){
	$('#group').html('');
	$.post('group.php',
	{
		action:"get",
		offset:0
	}, function(json){
		$("#group").html("<div id=\"groups\"></div>");
		//console.log(json);
		$.each(JSON.parse(json), function(idx, obj){
			var datePattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
			var timePattern = /[0-9]{2}:[0-9]{2}/;
			$("#groups").append("<h3>" + obj.Name + "Lead by" + obj.Sponsor + "</h3>");
		});
		/*$("#groups").accordion({
			collapsible: true
		});*/
		});
	});

    $('#loginTab').click(function(){
	loading('#login');
	loginForm();
    });

	$('#eventForm').click(function(){
    	loading('#manageEventForm');
    	eventForm();
    });

    $('#groupForm').click(function(){
    	loading('#manageGroupsForm');
    	groupForm();
    });
    
    $('#announcementForm').click(function(){
    	loading('#createAnnouncementForm');
    	announcementForm();
    });

    $('#carpoolTab').click(function(){
		loading('#carpool');
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
				//$('#login').html('Loading...');
				loading('#login');
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
		loading('#login');
		location.reload(true);
	});
}

function loginForm(){
	loading('#login');
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
	loading('#login');
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
	loading('#carpool');
	$.get('carForm.php',
	{
	},function(form){
		$('#carpool').html(form);
	});
}

function carpoolTab(){
	loading('#carpool');
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
					var optionString = '<option value="' + obj.eid + '">' + obj.Name + ' on ' + datePattern.exec(obj.Start) + ' at ' + timePattern.exec(obj.Start) + '</option>';
					$('#driveEvent').append(optionString);
					$('#rideEvent').append(optionString);
				});
			});

		});
	} else {
		$('#carpool').html('<h3>Please log in or register to use the carpool web app!</h3>');
	}
}

function driveForm(){
	var eid = $('#driveEvent').val();
	var pid = getCookie('pid');
	$.post('event.php',
	{
		action:'remove_need_ride',
		eid:eid,
		pid:pid
	},function(resp){

	});
	
	$.post('event.php',
	{
		action:'can_drive',
		eid:eid,
		pid:pid
	}, function(resp){
		alert(resp);
	});
}

function rideForm(){
	var eid = $('#rideEvent').val();
	var pid = getCookie('pid');
	var e = $('#rideEvent option:selected').text();
	loading('#carpool');

	$.post('event.php',
	{
		action:'remove_can_drive',
		eid:eid,
		pid:pid
	}, function(resp){
		$.get('rideForm.php',
		{
		}, function(form){
			$('#carpool').html(form);
			$('#eventHeader').html(e);
			$.post('latlong.php',
			{
				action:'get_lat_long_from_pid',
				pid:pid
			}, function(riderJson){
				var riderJsonParsed = JSON.parse(riderJson);
				var riderLat = riderJsonParsed.latitude;
				var riderLong= riderJsonParsed.longitude;
				$.post('event.php',
				{
					action:'find_drivers',
					eid:eid
				},function(json){
					var something = false;
					var jsonParsed = JSON.parse(json);
					$.each(jsonParsed, function(idx, obj){
						$.post('latlong.php',
						{
							action:'get_lat_long_from_pid',
							pid:obj.pid
						}, function(driverJson){
							var driverJsonParsed = JSON.parse(driverJson);
							driverLat=driverJsonParsed.latitude;
							driverLong=driverJsonParsed.longitude;

							var r = 3963; //miles
							var lat1=Math.radians(riderLat);
							var lat2=Math.radians(driverLat);
							var dlat=Math.radians(driverLat-riderLat);
							var dlng=Math.radians(driverLong-riderLong);
							var a = Math.sin(dlat/2) * Math.sin(dlat/2) + Math.cos(lat1) * Math.cos(lat2) * Math.sin(dlng/2) * Math.sin(dlng/2);
							var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
							var d = r*c;

							jsonParsed[idx].distance=d;
							if(idx == JSON.parse(json).length - 1){
								jsonParsed.sort(function(a,b){ return parseFloat(a.distance) + parseFloat(b.distance)});

								$.each(jsonParsed, function(idx, obj){
								something = true;
								$('#drivers').append('<tr><td>' + obj.firstname + '</td><td>' + obj.lastname + '</td><td>' + Math.round(obj.distance*100)/100 + '</td><td><a href="mailto:' + obj.email + '">' + obj.email + '</td></tr>');
								});
								if(!(something)){
									alert('It looks like no one has signed to drive for this event yet, please check back later!');
								}

							}
						});
					});
				});
			});
			
		});
	});
	
	$.post('event.php',
	{
		action:'need_ride',
		eid:eid,
		pid:pid
	},function(resp){
		//alert(resp);
	});
}

function groupForm(){
	loading('#manageGroupsForm');
	$.get('groupform.php',{},
	function(form){
		$('#manageGroupsForm').html(form);
		
	});
}

function eventForm(){
	loading('#manageEventForm');
	$.get('manageEventForm.php',{},
	function(form){
		$('#manageEventForm').html(form);
		
	});
}
function announcementForm(){
	loading('#createAnnouncementForm');
	$.get('announcementForm.php',{},
	function(form){
		$('#createAnnouncementForm').html(form);
	});
}

function createGroup(){
	var groupName=$('#groupName').val();
	var Sponsor=$('#sponsor').val();
	if(!(groupName))
		alert('Group Name required!');
	else if(!(Sponsor))
		alert('Group Sponsor required!');
	else{
		$.post('group.php',
			{
				action:'add',
				
			}, function(resp){
				if(resp)
					alert(resp)
				else
					$('#manageGroupsForm').html('<h4>Registered Group successfully!</h4>');
			});
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

function loading(selector){
	$(selector).html('<img src="img/load.gif" alt="loading..." width="9%" height="16%">');
}

function makeEvent(){
	var eventName = $("#eventName").val().trim();
	var eventType = $("#eventType").val().trim();
	var startDate = $("#startDate").val().trim();
	var startTime = $("#startTime").val().trim();
	var endDate = $("#endDate").val().trim();
	var endTime = $("#endTime").val().trim();
	var houseNumber = $("#eventHouseNumber").val().trim();
	var suiteNumber = $("#eventSuiteNumber").val().trim();
	var street = $("#eventStreet").val().trim();
	var city = $("#eventCity").val().trim();
	var zipcode = $("#eventZipcode").val().trim();
	var description = $("#eventDescription").val().trim();
	var eventLat;
	var eventLong;
	var googleAddress = houseNumber + " " + street + ", " + city + ", MO, " + zipcode;
	var eid;
	
	geocoder.geocode({'address': googleAddress},function(results, status){
		if(status == google.maps.GeocoderStatus.OK){
			eventLat = results[0].geometry.location.lat();
			eventLong = results[0].geometry.location.lng();
		}
	});	
	if(!(eventName)){
		alert("Event name must be present");
	}else if(!(eventType)){
		alert("Event type must be present");
	}else if(!(checkStartDateTime(startDate, startTime))){
		return;
	}else if(!(checkEndDateTime(endDate, endTime))){
		return;
	}else if(!(testAddress(houseNumber, street, city, zipcode))){
		return;
	}else if(!(description)){
		alert("Description is a required field");
		return;
	}else{
		var mysqlStart = startDate + " " + startTime;
		var mysqlEnd = endDate + " " + endTime;
		console.log(mysqlStart);
		$.post('event.php', {
			action:'insert',
			eventName: eventName,
			startTime: mysqlStart,
			endTime: mysqlEnd,
			description: description,
			eventType: eventType
		});
		
		$.post("latlong.php", {
				action:'add',
				houseNumber:houseNumber,
				suiteNumber:suiteNumber,
				street:street,
				city:city,
				zip:zipcode,
				latitude:eventLat,
				longitude:eventLong
		});
		
		var eid;
		var aid;
		
		$.post("event.php", {
			action:'getEid',
			eventName: eventName,
			startTime: mysqlStart,
			endTime: mysqlEnd,
			eventType: eventType
		}, function(json){
			var j = JSON.parse(json);
			eid = j.eid;
		});
		
		$.post("latlong.php", {
			action:'getAid',
			houseNumber:houseNumber,
			suiteNumber:suiteNumber,
			street:street,
			city:city,
			zip:zipcode,
			latitude:eventLat,
			longitude:eventLong
		}, function(json){
			var k = JSON.parse(json);
			aid = k.aid;
		});
		
		$.post('event.php', {
			action:'insert_into_event_has_address',
			aid:aid,
			eid:eid
		});
		
		
		
		
		alert("Event Created!");
		$("#eventName").val('');
		$("#eventType").val('');
		$("#startDate").val('');
		$("#startTime").val('');
		$("#endDate").val('');
		$("#endTime").val('');
		$("#eventHouseNumber").val('');
		$("#eventSuiteNumber").val('');
		$("#eventStreet").val('');
		$("#eventCity").val('');
		$("#eventZipcode").val('');
		$("#eventDescription").val('');
	}
}

function checkStartDateTime(date,time){
	var datePattern = /[0-9]{4}-[0-1][0-9]-[0-3][0-9]/;
	var timePattern = /[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/;
	if(!(date)){
		alert("Start date cannot be empty");
		return false;
	}else if(!(datePattern.test(date))){
		alert("Start date is not in the correct format. Please use the format YYYY-MM-DD");
		return false;
	}else if(!(time)){
		alert("Start time cannot be empty");
		return false;
	}else if(!(timePattern.test(time))){
		alert("Start time is not in the correct format. Please use the format HH:MM:SS");
		return false;
	}
	return true;
}

function checkEndDateTime(date,time){
	var datePattern = /[0-9]{4}-[0-1][0-9]-[0-3][0-9]/;
	var timePattern = /[0-2][0-9]:[0-5][0-9]:[0-5][0-9]/;
	if(!(date)){
		alert("End date cannot be empty");
		return false;
	}else if(!(datePattern.test(date))){
		alert("End date is not in the correct format. Please use the format YYYY-MM-DD");
		return false;
	}else if(!(time)){
		alert("End time cannot be empty");
		return false;
	}else if(!(timePattern.test(time))){
		alert("End time is not in the correct format. Please use the format HH:MM:SS");
		return false;
	}
	return true;
}

function testAddress(house, street, city, zipcode){
	if(!house){
		alert("House number is a required field");
		return false;
	}else if(!street){
		alert("Street is a required field");
		return false;
	}else if(!city){
		alert("City is a required field");
		return false;
	}else if(!zipcode){
		alert("Zipcode is a required field");
		return false;
	}else if(zipcode.length != 5){
		alert("Zipcodes should be 5 digits in length");
		return false;
	}
	return true;
}

Math.radians = function(degrees){
	return degrees * Math.PI / 180;
}
