	function detectBrowser() {
		var useragent = navigator.userAgent;
		var mapdivMap = document.getElementById("mapHolder");
	
		if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1 ) {
			//mapdivMap.style.width = '100%';
			//mapdivMap.style.height = '100%';
			mapdivMap.style.width = '450px';
			mapdivMap.style.height = '300px';
		} else {
			mapdivMap.style.width = '450px';
			mapdivMap.style.height = '300px';
		}
	};
	function getLocation()
	{
		if (navigator.geolocation)
		{
			navigator.geolocation.getCurrentPosition(showPosition,showError);
		}
		else{x.innerHTML="Geolocation is not supported by this browser.";}
	}

	function showPosition(position)
	{
		lat=position.coords.latitude;
		lon=position.coords.longitude;
		latlon=new google.maps.LatLng(lat, lon)
		mapHolder=document.getElementById('mapHolder')
		pointGPS=document.getElementById('gps')
		mapHolder.style.height='250px';
		mapHolder.style.width='500px';

		var myOptions={
			center:latlon,zoom:14,
			mapTypeId:google.maps.MapTypeId.ROADMAP,
			mapTypeControl:false,
			navigationControlOptions:{style:google.maps.NavigationControlStyle.SMALL}
		};
		var map=new google.maps.Map(document.getElementById("mapHolder"),myOptions);
		var marker=new google.maps.Marker({position:latlon,map:map,title:"You are here!"});
		//pointGPS.innerHTML=lat+", "+lon;
		pointGPS.value=lat+","+lon;
	}

	function initializeMap(gpsCoords)
	{

		var div = $('#gps').val();
		gpsCoords
		var n = div.lastIndexOf(',');
		var Lati = div.substring(0, n);
		var Longi = div.substring(n + 1);
		var myCenter=new google.maps.LatLng(Lati,Longi);

		var mapProp = {
	  		center:myCenter,
	  		zoom:14,
			mapTypeId:google.maps.MapTypeId.ROADMAP
		};
		var map=new google.maps.Map(document.getElementById("mapHolder"), mapProp);
		var marker=new google.maps.Marker({
			position:myCenter,
		});

		marker.setMap(map);	
	}
	function initializeTestMap(gpsCoords)
	{
		$('#mapHolder').gmap().bind('init', function(ev, map) {
			$('#mapHolder').gmap('addMarker', {'position': gpsCoords, 'bounds': true}).click(function() {
				$('#mapHolder').gmap('openInfoWindow', {'content': 'Hello World!'}, this);
			});
		});	
	}	
	function showError(error)
	{
		switch(error.code) 
		{
			case error.PERMISSION_DENIED:
				x.innerHTML="User denied the request for Geolocation."
				break;
			case error.POSITION_UNAVAILABLE:
				x.innerHTML="Location information is unavailable."
				break;
			case error.TIMEOUT:
				x.innerHTML="The request to get user location timed out."
				break;
			case error.UNKNOWN_ERROR:
				x.innerHTML="An unknown error occurred."
				break;
		}
	}

$("#mapPage").live("pageinit", function() {
	// http://jquery-ui-map.googlecode.com/svn/trunk/demos/jquery-google-maps-basic-example.html
	var gpsCoords = "-34.13932,18.43230";
	if(gpsCoords.length > 0) {
		detectBrowser();
		initializeTestMap(gpsCoords);
	}	
});
		
$("#calendarPage").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/eventdata.php", {}, function(res) {
	$.get("http://www.stylus.co.za/cts/eventdata.php", {}, function(res) {	
		$.mobile.hidePageLoadingMsg();
		var s = "";
		
		for(var i=0; i<res.length; i++) {
			s+= "<li class=\"ui-button\">";
			s+= "<a href='event-detail.html?id=" + res[i].id + "'><p class=\"ui-li-aside ui-li-desc\"><strong>" + res[i].startTime + ", " + res[i].startDate + "</strong></p>";
			s+= "<h3>" + res[i].name + "</h3>";
			s+= "<p>" + res[i].description + "</p>";
			s+= "</a>";
			s+= "</li>";
		}
		$("#eventList").html(s);
		$("#eventList").listview("refresh");
	},"json");
 
});

$("#resultsPage").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/eventdata.php", {}, function(res) {
	$.get("http://www.stylus.co.za/cts/eventdata.php", {}, function(res) {	
		$.mobile.hidePageLoadingMsg();
		var s = "";
		
		for(var i=0; i<res.length; i++) {
			s+= "<li class=\"ui-button\">";
			s+= "<a href='result.html?id=" + res[i].id + "'><p class=\"ui-li-aside ui-li-desc\"><strong>" + res[i].startDate + "</strong></p>";
			s+= "<h3>" + res[i].name + "</h3>";
			s+= "<p>" + res[i].description + "</p>";
			s+= "</a>";
			s+= "</li>";
		}
		$("#resultsList").html(s);
		$("#resultsList").listview("refresh");
	},"json");
});

$("#resultPage").live("pagebeforeshow", function() {
	var page = $(this);
	var query = page.data("url").split("?")[1];
	var id = query.split("=")[1];
	console.log("Getting remote detail for "+id);
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/eventdata.php", {rid:id}, function(res) {
	$.get("http://www.stylus.co.za/cts/eventdata.php", {rid:id}, function(res) {
		$.mobile.hidePageLoadingMsg();
		$("h1",page).text(res[0].eventName);
		var s = "<p>" + res[0].eventDescription + "</p>";
			s += "Date: " + res[0].startTime + ", " + res[0].startDate + "<br/>";
			s += "Venue: "+res[0].eventVenue + "</p>";
			if(res[0].resultPos !== null){
				s += "<table data-role=\"table\" id=\"result-table\" class=\"ui-responsive table-stripe\" data-mode=\"columntoggle\">";
				s += "<thead><th><abbr title='Position'>Pos</abbr></th><th data-priority='2'><abbr title='Race No'>No</th><th>Name</th><th>Time</th><th data-priority='1'>Category</th></thead>";
				s += "<tbody>";
				$(jQuery.parseJSON(JSON.stringify(res))).each(function() {  
					var pos = this.resultPos;
					var no = this.resultRaceNo;
					var name = this.resultRaceName;
					var time = this.resultRaceTime;
					var cat = this.resultRaceCat;
					s += "<tr><td>" + pos + "</td><td>" + no + "</td><td>" + name + "</td><td>" + time + "</td><td>" + cat + "</td></tr>";
				});			
				
				s += "</tbody>";
				s += "</table>";
 			} else {
 				s += "<div class=\"ui-bar ui-bar-e\"><p>No results</p></div>";
 			}
 			if(res[0].eventImage !== null) s += "<p class=\"image\"><img src='images/" + res[0].eventImage + "'></p>";
			
			$("#resultContent").html(s).trigger( "create" );
			
	},"json");
});

$("#weatherPage").live("pagebeforeshow", function() {

	var page = $(this);
	var query = page.data("url").split("?")[1];
	 
	var weatherCityCode = '3369157'; // Cape Town
	var appID = 'aa05fb30074ff517833db8ba123597bf';
	var unit = 'metric';
	var KELVIN = 273.15;
	if(unit === 'metric') var KELVIN = 0;
	    	
	function getWeather(callback) {

	    var openweathermapURL = 'http://api.openweathermap.org/data/2.1/weather/city?id=' + weatherCityCode + '&APPID=' + appID + '&units=' + unit;
	    $.ajax({
	        type: "GET",
	        url: openweathermapURL,
	        dataType: "jsonp",
			success: callback
	    });
	}

	// get data:
	getWeather(function (data) {
	    console.log('weather data received');

		$("h1",page).text(data.name);
		
		var tempVal = data.main.temp;
		var tempMin = Math.round(data.main.temp_min - KELVIN);
		var tempMax = Math.round(data.main.temp_max - KELVIN);
		var tempCel = tempVal - KELVIN;
		
		var s = "";
		s += "<p>Humidity: " + data.main.humidity + "%</p>";
		s += "<p>Pressure: " + data.main.pressure + "<abbr title='hectopascal'>hPa</abbr></p>";
		s += "<p>Temp: " + Math.round(tempCel) + "&deg;C (" + tempMin + "&deg;C - " + tempMax + "&deg;C)</p>";
		s += "<p>Cloud: " + data.wind.speed + "</p>";
		s += "<p>" + data.weather[0]['description'] + "</p>";
		s += "<p>Wind:</p>";
		s += "<ul><li>Wind speed: " + data.wind.speed + "</li>";
		s += "<li>Wind gust: " + data.wind.gust + "</li>";
		s += "<li>Wind deg: " + data.wind.deg + "</li></ul>";
		s += "<p><img title='" + data.weather.icon + "' src='http://openweathermap.org/img/w/" + data.weather[0]['icon'] + ".png' /></p>";
		
		s += "<p>Last updated: " + data.date + "</p>";
		s += "<p><a href='" + data.url + "' title='' target='_blank'>openweathermap.org</a></p>";
		
		$("#weatherReport").html(s);
	});
});

$("#weatherForecastPage").live("pagebeforeshow", function() {

	var page = $(this);
	var query = page.data("url").split("?")[1];
	 
	var weatherCityCode = '3369157'; // Cape Town
	var appID = 'aa05fb30074ff517833db8ba123597bf';
	var unit = 'metric';
	var KELVIN = 273.15;
	if(unit === 'metric') var KELVIN = 0;
	    	
	function getForecast(callback) {

	    var openforecastURL = 'http://api.openweathermap.org/data/2.5/forecast?id=' + weatherCityCode + '&APPID=' + appID + '&units=' + unit;
	    $.ajax({
	        type: "GET",
	        url: openforecastURL,
	        dataType: "jsonp",
			success: callback
	    });
	}

	// get data:
	getForecast(function (data) {
	    console.log('forecast data received');

		$("h1",page).text(data.city.name + ", " + data.city.country);
		
		var s = "";
		s += "<p>" + data.city.name + ", " + data.city.country + "</p>";
		s += "<table data-role=\"table\" id=\"forecast-table\" class=\"ui-responsive table-stripe\" data-mode=\"columntoggle\">";
		s += "<thead><th>dt</th><th>temp</th><th>humidity</th><th>clouds</th><th>wind</th></thead>";
		s += "<tbody>";
		$(jQuery.parseJSON(JSON.stringify(data.list))).each(function() {  
			var dt = this.dt;
			var dt_txt = this.dt_txt;
			var temp = this.main.temp;
			var tempMin = this.main.temp_min;
			var tempMax = this.main.temp_max;
			var humidity = this.main.humidity;
			var cloud = this.weather.main;
			var clouds = this.clouds.all;
			var windSpeed = this.wind.speed;
			var windDeg = this.wind.deg;
			//var rain = this.rain.speed;
			s += "<tr><td>" + dt_txt + " (" + dt + ")</td><td>" + temp + "&degC (" + tempMin + "&degC - " + tempMax + "&degC)</td><td>" + humidity + "%</td><td>" + cloud + " - " + clouds + "</td><td>" + windSpeed + " - " + windDeg + "</td></tr>";
		});
		s += "</tbody>";
		s += "</table>";
		$("#weatherForecast").html(s);
		$("#forecast-table").refresh;
	});
});

function createContact() {					
    if (navigator.contacts) {
        navigator.notification.alert('message', null, 'title', 'OK');
    } else {
        alert("Not device");
        return;
    }
	var phoneNumbers = [];
	var addresses = [];
	var ims = [];
	var organizations = [];					
	var photos = [];
	var categories = [];
	var urls = [];

	// create a new contact object
	var contact = navigator.contacts.create();
	contact.displayName = $("#sponsorName").val();
	contact.nickname = $("#sponsorName").val();       //specify both to support all devices
	var name = new ContactName();
	name.givenName = $("#sponsorName").val();
	name.familyName = $("#sponsorName").val();
	contact.name = name;

	//contact.birthday = new Date( $("#birthday").val() );
	contact.note = "created by the Phonegap Kitchen Sink";

	// save to device
	contact.save(onSuccess, onError);
}

function onSuccess(contact) {
	$("#contactResults").html("<span class='success'>Save Success</span>");
	gaPlugin.trackEvent( nativePluginResultHandler, nativePluginErrorHandler, "Contacts", "Create", "Success", 1);
};

function onError(contactError) {
	$("#contactResults").append("<span class='error'>Error = " + contactError.code + "</span>");
	gaPlugin.trackEvent( nativePluginResultHandler, nativePluginErrorHandler, "Contacts", "Create", "Error: " +contactError.code, 1);
};

$("#sponsorsPage").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/sponsordata.php", {}, function(res) {
	$.get("http://www.stylus.co.za/cts/sponsordata.php", {}, function(res) {
		$.mobile.hidePageLoadingMsg();
		var s = "";
		if (res.length === 0) {
			s+= "<li>Nothing for you</li>";
		} else {
			$(jQuery.parseJSON(JSON.stringify(res))).each(function() {  
				var sponsorID = this.sponsorID;
				var sponsorName = this.sponsorName;
				var sponsorDescription = this.sponsorDescription;
				var sponsorTel = this.sponsorTel;
				var sponsorIcon = this.sponsorIcon;

				s+= "<li class=\"ui-button\">";
				s+= "<a href='sponsor-detail.html?id=" + sponsorID + "'><img src='images/" + sponsorIcon + "' class='ui-li-thumb'><p class=\"ui-li-aside ui-li-desc\"><strong>" + sponsorTel + "</strong></p>";
				s+= "<h3>" + sponsorName + "</h3>";
				if(sponsorDescription !== null) s+= "<p>" + sponsorDescription + "</p>";
				s+= "</a>";
				s+= "</li>";				
			});			
		}
		$("#sponsorList").html(s);
		$("#sponsorList").listview("refresh");
	},"json");
});

$("#restaurantsPage").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/sponsordata.php", {type:"r"}, function(res) {
	$.get("http://www.stylus.co.za/cts/sponsordata.php", {type:"r"}, function(res) {
		$.mobile.hidePageLoadingMsg();
		var s = "";
		if (res.length === 0) {
			s+= "<li>Nothing for you</li>";
		} else {
			$(jQuery.parseJSON(JSON.stringify(res))).each(function() {  
				var sponsorID = this.sponsorID;
				var sponsorName = this.sponsorName;
				var sponsorDescription = this.sponsorDescription;
				var sponsorTel = this.sponsorTel;
				var sponsorIcon = this.sponsorIcon;

				s+= "<li class=\"ui-button\">";
				s+= "<a href='sponsor-detail.html?id=" + sponsorID + "'><img src='images/" + sponsorIcon + "' class='ui-li-thumb'><p class=\"ui-li-aside ui-li-desc\"><strong>" + sponsorTel + "</strong></p>";
				s+= "<h3>" + sponsorName + "</h3>";
				s+= "<p>" + sponsorDescription + "</p>";
				s+= "</a>";
				s+= "</li>";				
			});			
		}
		$("#restaurantList").html(s);
		$("#restaurantList").listview("refresh");
	},"json");
});

$("#sponsorPage").live("pageshow", function() {
	$("#btnCreateContact").off("click").on("click", createContact);
	//$("#btnCreateContact").on("click", function() {	});
});

$("#sponsorPage").live("pagebeforeshow", function() {
	var page = $(this);
	var query = page.data("url").split("?")[1];
	var id = query.split("=")[1];
	console.log("Getting remote detail for "+id);
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/sponsordata.php", {id:id}, function(res) {
	$.get("http://www.stylus.co.za/cts/sponsordata.php", {id:id}, function(res) {
		$.mobile.hidePageLoadingMsg();
		$("h1",page).text(res.name);
		var s = "<p>" + res.description + "</p>";

			if(res.twitter !== null) s += "<p>Twitter: " + res.twitter + "<br/>";
			if(res.url !== null) s += "<p>Web: " + res.url + "<br/>";
			
			if(res.cell !== null) s += "<p><a href=\"sms:" + res.cell + "\">SMS<br/>" + res.cell + "</a></p>";

			//if(res.telephone == 0) s += "tbc";
			//else s+= res.cost;			
 			//s+= "</p>";
 			
 			if(res.image !== null) s += "<div id='logo_image'><img src='images/" + res.image + "'></div>";
			
			s += "<p><a id='btnCreateContact' data-role='button'>Create Contact</a></p>";
			
			$("#detailContent").html(s);
	},"json");
});

$("#detailPage").live("pagebeforeshow", function() {
	var page = $(this);
	var query = page.data("url").split("?")[1];
	var id = query.split("=")[1];
	console.log("Getting remote detail for "+id);
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/eventdata.php", {id:id}, function(res) {
	$.get("http://www.stylus.co.za/cts/eventdata.php", {id:id}, function(res) {
		$.mobile.hidePageLoadingMsg();
		$("h1",page).text(res.name);
		var s = "<p>" + res.description + "</p>";
			s += "<p>Date: " + res.startDate + "<br/>";
			s += "<p>Time: " + res.startTime + "<br/>";
			s += "<p>Venue: " + res.venue + "<br/>";

			if(res.gps !== null) s += "<p>GPS: " + res.gps + "<br/><input type=hidden id=gps value=" + res.gps + ">";
			
			s += "<p id=\"price\">Price: ";
			if(res.cost == 0) s += "tbc";
			else s+= res.cost;			
 			s+= "</p>";
 			
 			if(res.image !== null) s += "<p class=\"image\"><img src='images/" + res.image + "'></p>";
			
			$("#detailContent").html(s);
			var x=document.getElementById("demoHolder");
			//console.log("x = " + x);
	
		var gpsCoords = $('#gps').val();
		if(gpsCoords.length > 0) {
			detectBrowser();
			initializeMap(gpsCoords);
		} else {
			// show map at current position
			// getLocation(); ????????
			console.log("No gpsCoords value....");
		}			
	},"json");
});

var app = {
	showAlert: function (message, title) {
	    if (navigator.notification) {
			navigator.notification.alert(message + ' on Device', null, title, 'OK');
	    } else {
	        alert(title ? (title + ": " + message) : message);
	    }
	},
    initialize: function() {
        var self = this;
        self.showAlert('App Initialized', 'Info');
    }	
}
//app.initialize();