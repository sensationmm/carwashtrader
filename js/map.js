var map;
var myLatLng;
var zoom;
var geocoder;

var image = new google.maps.MarkerImage('/wp-content/themes/carwashtrader/assets/images/map-marker.png',
  new google.maps.Size(30, 38),
  new google.maps.Point(0,0), //origin
  new google.maps.Point(16,37)); //anchor

function initialize(loc, canvasID, hideControls, pins) 
{
	geocoder = new google.maps.Geocoder();
	
	if(loc == 0)
	{
		myLatLng = new google.maps.LatLng(54, -3.8);
		zoom = 6;

		var myOptions = {
		  center: myLatLng,
		  zoom: zoom,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  disableDefaultUI: hideControls
		};
		map = new google.maps.Map(document.getElementById(canvasID), myOptions);
				
		pins = pins.split(';');
		for(i=0; i<pins.length; i++)
		{
			var data = pins[i].split('{!}');
			var coords = data[0].split(',');
			var latlng = new google.maps.LatLng(coords[0], coords[1]);
			var text = data[2];
			placeMarker(latlng, data[1], text, 'true');
		}
	}
	else
	{
		geocoder.geocode( { 'address': loc}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				myLatLng = results[0].geometry.location;
				
				zoom = 10;

				var myOptions = {
				  center: myLatLng,
				  zoom: zoom,
				  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  		  disableDefaultUI: hideControls
				};
				map = new google.maps.Map(document.getElementById(canvasID), myOptions);
			
				placeMarker(myLatLng, 'You are here', '', 'false');
				
				if(typeof pins != 'undefined') {
					pins = pins.split(';');
					for(i=0; i<pins.length; i++)
					{
						var data = pins[i].split('{!}');
						var coords = data[0].split(',');
						var latlng = new google.maps.LatLng(coords[0], coords[1]);
						var text = data[2];
						placeMarker(latlng, data[1], text, 'true');
					}
				}
					
			}
			else{
				myLatLng = new google.maps.LatLng(54, -3.8);
				zoom = 10;
		
				var myOptions = {
				  center: myLatLng,
				  zoom: zoom,
				  mapTypeId: google.maps.MapTypeId.ROADMAP,
				  disableDefaultUI: hideControls
				};
				map = new google.maps.Map(document.getElementById(canvasID), myOptions);
				
				pins = pins.split(';');
				for(i=0; i<pins.length; i++)
				{
					var data = pins[i].split('{!}');
					var coords = data[0].split(',');
					var latlng = new google.maps.LatLng(coords[0], coords[1]);
					var text = data[2];
					placeMarker(latlng, data[1], text, 'true');
				}
			}	
		});
	}
				

}

function placeMarker(latLng, title, text, useImage)
{
	if(useImage == 'true')
	{
		shadowOpt = '';
		imageOpt = image;
		zindex = 1;
	}
	else
	{
		shadowOpt = '';
		imageOpt = '';
		zindex = 2;
	}
	
	var marker = new google.maps.Marker({
        position: latLng,
        map: map,
		icon: imageOpt,
		shadow: shadowOpt,
		zIndex: zindex
    });
	
	var infowindow = new google.maps.InfoWindow({
		content: '<b>'+title+'</b><br />'+text
	});

	google.maps.event.addListener(marker, 'click', function() {
	  	infowindow.open(map,marker);
	});
}