jQuery(document).ready(function($){
	var is_mobile = false;
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		is_mobile = true;
	}
	
	
	/* if(jQuery('#map').length>0){
		var markers = [];
		initMap();
	} */
	
	if($('.open-map-view').length){
		var markers = [];
		var map = null,
			needReload = false;
		
		$('.open-map-view').click(function(e){
			e.preventDefault();
			//if the map have been init before
			if($('.map-view').length){
				$('.map-view').show();
				
				$('#page, body').css('overflow', 'hidden');
				
			}else{
				$('body').addLoadingLayer();
				var ajaxData = {
							'action'	: 'get_map_data'
						};
						
				$.ajax({
					url			: ajaxurl,
					type		: 'POST',
					data		: ajaxData,
					success		: function(e){
						
						var data = JSON.parse(e);
						$('body').append(data.map_layout);
						$('#close-map').click(function(){
							$(this).parent().hide();
							if(needReload){
								$('body').addLoadingLayer();
								location.reload();
							}else{
								$('#page, body').css('overflow', 'auto');
							}
							
						});
						
						//console.log(e);
						initMap(data.points);
						
						$('#page, body').css('overflow', 'hidden');
					},
					complete	: function(e){
						$('body').removeLoadingLayer();
					},
					error		: function(e, ee){
						console.log(ee);
					}
					
				});
			}
		});
		
		
		
	}
	
	function bindMapPropertyFilter(){
		$('#filter-map form').submit(function(e){
			e.preventDefault();
			
			/* var ajaxData = {
					'action'	: 'filter_property_map',
					'formdata'	: $(this).serialize(),	
				}; */
			
			var formData = new FormData($(this)[0]);
			var queryString = new URLSearchParams( formData ).toString();
			
			formData.append('action', 'filter_property_map');
			
			console.log(formData);
			$('#filter-map').addLoadingLayer();
			
			$.ajax({
				url			: ajaxurl,
				type		: 'POST',
				data		: formData,
				processData: false,
				contentType: false,
				success		: function(e){
					//console.log(e);
					
					var pinpoints = JSON.parse(e);
					removeMarkers();
					jQuery.each(pinpoints,function(index,value){
						if(value.point){

							var marker = addMarker(map, value);
							
						}
					});
					
					var currentUrl = window.location.href.split('?');
					window.history.replaceState("Filter", "Filter", currentUrl[0] + "?" + queryString );
					needReload = true;
					
				},
				complete	: function(e){
					$('#filter-map').removeLoadingLayer();
				},
				error		: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	function initMap( points ) {
		//points = points.replace(/\s+/g, '');
		var pinpoints = points; //JSON.parse(points);
		console.log(pinpoints);
		var center = {lat: -8.4519524, lng: 115.0965568};//{lat: -8.7093285, lng: 115.1822915};
		map = new google.maps.Map(document.getElementById('the-map-view'), {
			center: center,
			zoom: 10,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false,
			gestureHandling: 'greedy',
			fullscreenControl: false,
		});
		
		//Search Box code start
		var input = document.getElementById('pac-input');
		var card = document.getElementById('pac-card');
		//var closebutton = document.getElementById('close-map');
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);
		//map.controls[google.maps.ControlPosition.TOP_RIGHT].push(closebutton);
		var autocomplete = new google.maps.places.Autocomplete(input); //new google.maps.places.SearchBox(input);
		//map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
		
		
		autocomplete.bindTo('bounds', map);

        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'icon', 'name']);

        var infowindow = new google.maps.InfoWindow();
        var infowindowContent = document.getElementById('infowindow-content');
        infowindow.setContent(infowindowContent);
        var marker = new google.maps.Marker({
          map: map,
          anchorPoint: new google.maps.Point(0, -29)
        });
		
		//autocomplete.setTypes([]);

        autocomplete.addListener('place_changed', function() {
          infowindow.close();
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

          var address = '';
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
          infowindow.open(map, marker);
        });
		//Search Box code end
		
		jQuery.each(pinpoints,function(index,value){
			if(value.point){

				var marker = addMarker(map, value);
				
					
				/* jQuery.each(point, function(index,p){
					var longlat = p.split(',');
					var marker = addMarker({lat:Number(longlat[0]), lng:Number(longlat[1])},map,value.id);
					markers.push({'marker': marker,'link':value.post_name});
				}); */
				
			}
		});
		
		bindMapPropertyFilter();
	}
	
	function removeMarkers(){
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(null);
        }
     }
	
	function addMarker( map, data ){
		var point = data.point;
			point = point.replace(/\(/g,'');
			point = point.replace(/\)/g,'');
		var longlat = point.split(',');
		//var loc = {lat:Number(longlat[0]), lng:Number(longlat[1])};
		
		var markerOptions = {
			position: { lat:Number(longlat[0]), lng:Number(longlat[1]) },
			map: map,
			icon : marker_icon
		};

		
		var marker = new google.maps.Marker(markerOptions);
		
		if(data.id){
			/* var theurl = (type=='rent') ? homeurl : 'http://www.balivillasales.com';
			marker.addListener('click', function(event){
				window.open(theurl+'/'+url,'_blank');
			}); */
			
			var windowInfo = null;
			
			
			if(!is_mobile){
				marker.addListener('mouseover', function(event){
					windowInfo = open_marker_info(map, this, data );
				});
			
				marker.addListener('mouseout', function(event){
					windowInfo.close();
				});
				
				marker.addListener('click', function(event){
					window.open( data.url ,'_blank');
				});
			}else{
				marker.addListener('click', function(event){
					windowInfo = open_marker_info(map, this, data );
				});
			}
		}
		
		markers.push(marker);
		
		return marker;
	}
	
	function open_marker_info(map, marker, data){
		var infowindow = new google.maps.InfoWindow();
        var infowindowContent = $('#infowindow-marker');
		
		infowindowContent.find('#villa-image').attr('src', data.image_url);
		infowindowContent.find('#villa-name').html(data.name);
		infowindowContent.find('#guest').html(data.guest);
		infowindowContent.find('#bedroom').html(data.bedroom);
		infowindowContent.find('#land-size').html(data.land_size);
		infowindowContent.find('#price').html(data.start_at + ' ' +data.price);
		infowindowContent.find('#locations').html(data.locations);
		infowindowContent.find('.url').attr('href', data.url );
		//console.log(data.price);
		
        infowindow.setContent( infowindowContent.html() );
		infowindow.open(map, marker);
		return infowindow;
	}

	/* if(jQuery('#singlemap').length>0){
		initSingleMap();
	}
	
	function initSingleMap(){
		var center = {lat: -8.7093285, lng: 115.1822915};
		if(point) {
			point = point.replace(/\(/g,'');
			point = point.replace(/\)/g,'');
			var p = point.split(',');
			center = {lat: Number(p[0]), lng: Number(p[1])};
		}
		
		var map = new google.maps.Map(document.getElementById('singlemap'), {
			center: center,
			zoom: 14,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl:false
		});
		
		var longlat = point.split(',');
		addMarker({lat:Number(longlat[0]), lng:Number(longlat[1])},map);
		
	} */

});