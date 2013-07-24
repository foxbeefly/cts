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

		var div = gpsCoords;
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
	var gpsCoords = "-34.13932,18.43230";
	if(gpsCoords.length > 0) {
		detectBrowser();
		initializeTestMap(gpsCoords);
	}	
});
		
$("#calendarPage").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/data.php", {}, function(res) {
	$.get("http://www.stylus.co.za/cts/data.php", {}, function(res) {	
		$.mobile.hidePageLoadingMsg();
		var s = "";
		
		for(var i=0; i<res.length; i++) {
			s+= "<li class=\"ui-button\">";
			s+= "<a href='event-detail.html?id=" + res[i].id + "'><p class=\"ui-li-aside ui-li-desc\"><strong>" + res[i].start + "</strong></p>";
			s+= "<h3>" + res[i].name + "</h3>";
			s+= "<p>" + res[i].description + "</p>";
			s+= "</a>";
			s+= "</li>";
		}
		$("#eventList").html(s);
		$("#eventList").listview("refresh");
	},"json");
 
});

$("#sponsorList").live("pageinit", function() {
	// console.log("Getting remote list");
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/data.php", {}, function(res) {
	$.get("http://www.stylus.co.za/cts/data.php", {}, function(res) {
		$.mobile.hidePageLoadingMsg();
		var s = "";
		
		for(var i=0; i<res.length; i++) {
			s+= "<li class=\"ui-button\">";
			s+= "<a href='event-detail.html?id=" + res[i].id + "'><p class=\"ui-li-aside ui-li-desc\"><strong>" + res[i].start + "</strong></p>";
			s+= "<h3>" + res[i].name + "</h3>";
			s+= "<p>" + res[i].description + "</p>";
			s+= "</a>";
			s+= "</li>";
		}
		$("#eventList").html(s);
		$("#eventList").listview("refresh");
	},"json");
 
});


function createContact() {					
    if (navigator.contacts) {
        navigator.notification.alert(message, null, title, 'OK');
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

$("#detailPage").live("pagebeforeshow", function() {
	var page = $(this);
	var query = page.data("url").split("?")[1];
	var id = query.split("=")[1];
	console.log("Getting remote detail for "+id);
	$.mobile.showPageLoadingMsg();
	//$.get("http://127.0.0.1/cts/data.php", {id:id}, function(res) {
	$.get("http://www.stylus.co.za/cts/data.php", {id:id}, function(res) {
		$.mobile.hidePageLoadingMsg();
		$("h1",page).text(res.name);
		var s = "<p>" + res.description + "</p>";
			s += "<p>Date: " + res.start + "<br/>";
			s += "<p>Venue: "+res.venue + "<br/>";
			s += "<p>GPS: "+res.gps + "<br/>";
			s += "<input type=hidden id=gps value=" + res.gps + ">";
			s += "<p>Price: ";
			if(res.cost == 0) s += "tbc";
			else s+= res.cost;			
 			s+= "<p>";
			s+= "<p><img src='images/" + res.image + "'></p>";
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

$("#sponsorPage").live("pageshow", function() {
	$("#btnCreateContact").off("click").on("click", createContact);
	//$("#btnCreateContact").on("click", function() {	});
});

var app = {
	showAlert: function (message, title) {
	    if (navigator.notification) {
	        navigator.notification.alert(message, null, title, 'OK');
	    } else {
	        alert(title ? (title + ": " + message) : message);
	    }
	},
    initialize: function() {
        var self = this;
        self.showAlert('App Initialized', 'Info');
    }	
}
app.initialize();