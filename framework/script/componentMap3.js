/**
 *   Medication For All Framework javascript file ComponentMap3,
 *   Copyright (C) 2012  James M Adams
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Lesser General Public License for more details.
 *
 *   You should have received a copy of the GNU Lesser General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *@package framework
 */

//refer to http://gmaps-samples-v3.googlecode.com/svn/trunk/drivingdirections/directions-demo.html

var Demo
var geocoder;


$(document).ready(function()
{
Demo = {
  // HTML Nodes
  mapContainer: document.getElementById('map-container'),
  dirContainer: document.getElementById('dir-container'),
  fromInput: document.getElementById('from-input'),
  toInput: document.getElementById('to-input'),
  travelModeInput: document.getElementById('travel-mode-input'),
  unitInput: document.getElementById('unit-input'),

  // API Objects
  dirService: new google.maps.DirectionsService(),
  dirRenderer: new google.maps.DirectionsRenderer(),
  map: null,

  showDirections: function(dirResult, dirStatus) {
    if (dirStatus != google.maps.DirectionsStatus.OK) {
      alert('Directions failed: ' + dirStatus);
      return;
    }

    // Show directions
    Demo.dirRenderer.setMap(Demo.map);
    Demo.dirRenderer.setPanel(Demo.dirContainer);
    Demo.dirRenderer.setDirections(dirResult);
  },

  getSelectedTravelMode: function() {
    var value =
        Demo.travelModeInput.options[Demo.travelModeInput.selectedIndex].value;
    if (value == 'driving') {
      value = google.maps.DirectionsTravelMode.DRIVING;
    } else if (value == 'bicycling') {
      value = google.maps.DirectionsTravelMode.BICYCLING;
    } else if (value == 'walking') {
      value = google.maps.DirectionsTravelMode.WALKING;
    } else {
      alert('Unsupported travel mode.');
    }
    return value;
  },

  getSelectedUnitSystem: function() {
    return Demo.unitInput.options[Demo.unitInput.selectedIndex].value == 'metric' ?
        google.maps.DirectionsUnitSystem.METRIC :
        google.maps.DirectionsUnitSystem.IMPERIAL;
  },
/*function codeAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        var myOptions = {
        zoom: 8,
        center: results[0].geometry.location,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        Demo.map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
        Demo.map = new google.maps.Map(Demo.mapContainer, {
          zoom: 13,
          center: latLng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });


      }
    });
  },*/
  getDirections: function() {
    var fromStr = Demo.fromInput.value;
    var toStr = Demo.toInput.value;
    var dirRequest = {
      origin: fromStr,
      destination: toStr,
      travelMode: Demo.getSelectedTravelMode(),
      unitSystem: Demo.getSelectedUnitSystem(),
      provideRouteAlternatives: true
    };
    Demo.dirService.route(dirRequest, Demo.showDirections);
  },
  init: function() {
    geocoder = new google.maps.Geocoder();
    var latLng = new google.maps.LatLng(42.531184, -75.52351490000001);

	var test = geocoder.geocode( { 'address': Demo.toInput.value, 'region': 'us' }, function(results, status)
	 {
		if (status == google.maps.GeocoderStatus.OK) 
		{
			latLng = results[0].geometry.location;
		       // var marker = createMarker(results[0].geometry.location,"<b>{StreetNumber} {Street}, {Suburb}</b><br />{PriceRange}<br />{Title}<br />{Bedrooms} Bedrooms, {Bathrooms} Bathrooms<br />",
		       // "{StreetNumber} {Street}, {Suburb}");
			//alert('gecoding returned a response '+latLng);

			Demo.map = new google.maps.Map(Demo.mapContainer, {
				zoom: 10,
				center: latLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});
     		 } 
		else 
		{
		        alert("Geocode was not successful for the following reason: " + status);
      		}
	});

//alert('l '+latLng);


   // Demo.codeAddress('Binghamton NY');


    // Show directions onload
 //Demo.getDirections();

  }
};
// Onload handler to fire off the app.
google.maps.event.addDomListener(window, 'load', Demo.init);
});
