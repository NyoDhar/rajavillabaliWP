jQuery(document).ready(function($){
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
		
		if($('#pac-input').length){
			var input = document.getElementById('pac-input');
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
			var autocomplete = new google.maps.places.Autocomplete(input); //new google.maps.places.SearchBox(input);
			
			autocomplete.bindTo('bounds', map);

			// Set the data fields to return when the user selects a place.
			autocomplete.setFields(
				['address_components', 'geometry', 'icon', 'name']);

			//var infowindow = new google.maps.InfoWindow();
			//var infowindowContent = document.getElementById('infowindow-content');
			//infowindow.setContent(infowindowContent);
			var marker = new google.maps.Marker({
			  map: map,
			  anchorPoint: new google.maps.Point(0, -29),
			  draggable:true
			});
			
			marker.addListener('dragend', function(event){
				jQuery('#pinpoint').val(marker.getPosition());
			});
			
			//autocomplete.setTypes([]);

			autocomplete.addListener('place_changed', function() {
			  //infowindow.close();
			  marker.setVisible(false);
			  var place = autocomplete.getPlace();
			  if (!place.geometry) {
				// User entered the name of a Place that was not suggested and
				// pressed the Enter key, or the Place Details request failed.
				window.alert("No details available for input: '" + place.name + "'");
				return;
			  }

			  // If the place has a geometry, then present it on a map.
			  if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
			  } else {
				map.setCenter(place.geometry.location);
				map.setZoom(17);  // Why 17? Because it looks good.
			  }
			  marker.setPosition(place.geometry.location);
			  marker.setVisible(true);
			  
			  jQuery('#pinpoint').val(marker.getPosition());

			 /*  var address = '';
			  if (place.address_components) {
				address = [
				  (place.address_components[0] && place.address_components[0].short_name || ''),
				  (place.address_components[1] && place.address_components[1].short_name || ''),
				  (place.address_components[2] && place.address_components[2].short_name || '')
				].join(' ');
			  }

			  infowindowContent.children['place-icon'].src = place.icon;
			  infowindowContent.children['place-name'].textContent = place.name;
			  infowindowContent.children['place-address'].textContent = address;
			  infowindow.open(map, marker); */
			});
			//Search Box code end
		}
		
		if(jQuery('#pinpoint').val()!=''){
			if( useCircleMarker ){
				addCircleRadius({lat:Number(p[0]), lng:Number(p[1])},map);
			}else{
				addMarker({lat:Number(p[0]), lng:Number(p[1])},map);
			}
		}
		
	}
	
	function addMarker(loc,map){
		var isDraggable = $('#pac-input').length ? true : false;
		
		var marker = new google.maps.Marker({
			position: loc,
			map: map,
			draggable:isDraggable
		});
		
		if(isDraggable){
			marker.addListener('dragend', function(event){
					jQuery('#pinpoint').val(marker.getPosition());
				});
		}
		
	}
	
	function addCircleRadius(loc, map){
		var isDraggable = $('#pac-input').length ? true : false;
		
          var cityCircle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: loc,
            radius: 600
          });
		  
		  if(isDraggable){
			  marker.addListener('dragend', function(event){
					jQuery('#pinpoint').val(marker.getPosition());
				});
		  }
	}

});