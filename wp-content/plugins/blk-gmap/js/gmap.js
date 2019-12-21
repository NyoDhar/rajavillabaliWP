jQuery(document).ready(function(){
	if(jQuery('#blk-map-wrapper').length>0){
		var marker = null;
		initMap();
	}
	
	function initMap() {
		var point;
		if(jQuery('#pinpoint').val()!=''){
			point = jQuery('#pinpoint').val();
			point = point.replace(/\(/g,'');
			point = point.replace(/\)/g,'');
			//point = point.split('|');
		}
		//console.log(point);
		var center = {lat: -8.7093285, lng: 115.1822915};
		if(point) {
			var p = point.split(',');
			center = {lat: Number(p[0]), lng: Number(p[1])};
		}
		
		var map = new google.maps.Map(document.getElementById('blk-map'), {
			center: center,
			zoom: 14,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			gestureHandling: 'greedy',
			fullscreenControl: false,
		});
		
		//Search Box code start
		var input = document.getElementById('pac-input');
		var searchBox = new google.maps.places.SearchBox(input);
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		// Bias the SearchBox results towards current map's viewport.
		map.addListener('bounds_changed', function() {
			searchBox.setBounds(map.getBounds());
		});
		
		var markers = [];
		// [START region_getplaces]
		// Listen for the event fired when the user selects a prediction and retrieve
		// more details for that place.
		searchBox.addListener('places_changed', function() {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}

			// Clear out the old markers.
			markers.forEach(function(marker) {
				marker.setMap(null);
			});
			markers = [];
			
			// For each place, get the icon, name and location.
			var bounds = new google.maps.LatLngBounds();
			places.forEach(function(place) {
				var icon = {
					url: place.icon,
					size: new google.maps.Size(571, 571),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};

				// Create a marker for each place.
				markers.push(new google.maps.Marker({
					map: map,
					icon: icon,
					title: place.name,
					position: place.geometry.location
				}));

				if (place.geometry.viewport) {
				// Only geocodes have viewport.
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
			});
			
			map.fitBounds(bounds);

		}); //Search Box code end
		
		map.addListener('click', function(event) {
			if(marker) marker.setMap(null);
			
			if( useCircleMarker ){
				marker = addCircleRadius(event.latLng, map);
				jQuery('#pinpoint').val(marker.getCenter());
			}else{
				marker = addMarker(event.latLng, map);
				jQuery('#pinpoint').val(marker.getPosition());
			}
		});
		
		if(jQuery('#pinpoint').val()!=''){
			if( useCircleMarker ){
				marker = addCircleRadius({lat:Number(p[0]), lng:Number(p[1])},map);
			}else{
				marker = addMarker({lat:Number(p[0]), lng:Number(p[1])},map);
			}
		}
		
		disableEnterButton();
	}
	
	function addMarker(loc,map){
		var marker = new google.maps.Marker({
			position: loc,
			map: map,
			draggable:true
		});
		marker.addListener('dragend', function(event){
			jQuery('#pinpoint').val(marker.getPosition());
		});

		return marker;
	}
	
	function addCircleRadius(loc, map){
          var cityCircle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: loc,
            radius: 600,
			draggable:true
          });
		  
		  cityCircle.addListener('dragend', function(event){
			jQuery('#pinpoint').val(cityCircle.getCenter());
		});

		return cityCircle;
	}
	
	function disableEnterButton(){
		jQuery('#pac-input').keydown(function(event){
			if(event.keyCode == 13) {
				event.preventDefault();
				return false;
			}
		});
	}

});