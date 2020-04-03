(function( $ ) {
	$.fn.addLoadingLayer = function(options){
		var settings = $.extend({
            size: "fa-3x",
        }, options );
		
		var spinner = typeof adminpage != 'undefined' ? '<span class="load spinner is-active"></span>' : '<i class="load fa fa-spinner fa-pulse '+settings.size+'"></i>';
		
		var layer = "<div class='loading-data'>"+spinner+"</div>";
		this.append(layer);
		this.css('position','relative');
		return this;
	};
	
	$.fn.removeLoadingLayer = function(){
		this.find('.loading-data').remove();
		return this;
	};
}( jQuery ));

jQuery(document).ready(function($){
	if($('.rvb-datepicker').length){
		$('.rvb-datepicker').datepicker({
			dateFormat: "yy-mm-dd",
		});
	}
	
	if($('#report-filter').length){
		$('#report-filter #find-accomodation').blur(function(){
			if($(this).val() == ''){
				$('#report-filter input[name="accomodation_id"]').val('');
			}
		});
	}
	
	if($('#find-accomodation').length){
		 $( "#find-accomodation" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					type: 'GET',
				  url: ajaxurl,
				  dataType: "jsonp",
				  data: {
					'action': 'find_accomodation',
					q		: request.term,
				  },
				  success: function( data ) {
					response( data );
				  },
				  error : function(e, ee, eee){
					  console.log(e);
					  console.log(ee);
					  console.log(eee);
				  }
				});
			},
			minLength: 3,
			select: function( event, ui ) {
				event.preventDefault();
				console.log(ui);
				$('#find-accomodation').val(ui.item.value);
				$('input[name="accomodation_id"]').val(ui.item.id);
			}
		});
	}
	
	if($('#rvb_hd_properties_search').length){
		bind_remove_hd_property();
		
		$('#rvb_hd_add').click(function(){
			var property_id = $('#rvb_hd_properties_search').val();
			console.log( property_id );
			
			if( property_id ){
				var element = "<li><input type='hidden' name='rvb_hd_properties[]' value='"+$('#rvb_hd_properties_search_id').val()+"'>"
							+ property_id + " <span class='remove'>&times;</span>"
							+ "</li>";
				
				$('#rvb_hd_properties').append( element );
				bind_remove_hd_property();
				$('#rvb_hd_properties_search').val('');
				$('#rvb_hd_properties_search_id').val('');
			}
		});
		
		 $( "#rvb_hd_properties_search" ).autocomplete({
			source: function( request, response ) {
				$.ajax({
					type: 'GET',
				  url: ajaxurl,
				  dataType: "jsonp",
				  data: {
					'action': 'find_hd_accomodation',
					q		: request.term,
				  },
				  success: function( data ) {
					response( data );
				  },
				  error : function(e, ee, eee){
					  console.log(e);
					  console.log(ee);
					  console.log(eee);
				  }
				});
			},
			minLength: 3,
			select: function( event, ui ) {
				event.preventDefault();
				console.log(ui);
				$('#rvb_hd_properties_search').val(ui.item.value);
				$('#rvb_hd_properties_search_id').val(ui.item.id);
			}
		});
	}
	
	function bind_remove_hd_property(){
		$('#rvb_hd_properties li .remove').unbind().click(function(){
			$(this).parent().remove();
		});
	}
	
	if($('#send-booking-link').length){
		$('#send-booking-link').submit(function(e){
			e.preventDefault();
			$(this).addLoadingLayer();
			
			var ajaxData = {
					'action'	: 'send_booking_link',
					'c_email'	: $('input[name="c_email"]').val(),
					'c_name'	: $('input[name="c_name"]').val(),
					'check-in'	: $('input[name="check-in"]').val(),
					'check-out'	: $('input[name="check-out"]').val(),
					'accomodation_id'	: $('input[name="accomodation_id"]').val(),
				};
				
			$.ajax({
				url		: ajaxurl,
				data	: ajaxData,
				type	: 'POST',
				success	: function(e){
					console.log(e);
					
					$('#wpbody-content .wrap').prepend(e);
					$('#send-booking-link')[0].reset();
				},
				error	: function(e, ee){
					console.log(ee);
				},
				complete : function(){
					$('#send-booking-link').removeLoadingLayer();
				}
			});
		});
	}
	
	if($('form#send-email-blast').length){
		var progressInterval;
		$('form#send-email-blast').submit(function(e){
			e.preventDefault();
			startProgressBar();
			
			var hotDeals = [];
			$('.hot-deals:checked').each(function () {
				   hotDeals.push($(this).val());
			  });
			var ajaxData = {
					'action'	: 'send_email_blast_ajax',
					'email_title'	: $('input[name="email_title"]').val(),
					'email_text'	: $('#email_text').val(),
					'subject'	: $('input[name="email_subject"]').val(),
					'hot_deals'	: hotDeals,
					'test_email'	: $('#test-email').val(),
				};
			
			console.log(ajaxData);
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				cache	: false,
				success	: function(e){
					//console.log(JSON.parse(e));
					console.log(e);
				}
			});
		});
		
		$('#test-email').blur(function(){
			if($(this).val() != ''){
				$('#submit-email-blast').val('Send Test Email');
			}else{
				$('#submit-email-blast').val('Send');
			}
		});
	}
	
	function startProgressBar(){
		var progressbar = $( "#progressbar" ),
		progressLabel = $( ".progress-label" );
		progressbar.show();
		
		progressbar.progressbar({
		  value: false,
		  change: function() {
			progressLabel.text( progressbar.progressbar( "value" ) + "%" );
		  },
		  complete: function() {
			progressLabel.text( "Complete!" );
			clearInterval(progressInterval);
			$.ajax({
				url		: ajaxurl,
				data	: {'action': 'reset_progress'},
			});
		  }
		});
		
		progressInterval = setInterval( function(){
			$.getJSON( progressPath + '?_=' + new Date().getTime(), function( data ) {
				console.log(data);
				progressbar.progressbar( "value", data.progress );
			});
		}, 1000 );
	}
	
	bindSetOwnerPaid();
	function bindSetOwnerPaid(){
		if($('.set-owner-paid').length){
			$('.set-owner-paid').click(function(){
				var theButton = $(this),
					wrapper = $('.finance-report');
					
					wrapper.addLoadingLayer();
					
				var ajaxData = {
						'action'		: 'set_owner_paid',
						'booking_id'	: theButton.data('bookingid'),
					};
					
				$.ajax({
					url		: ajaxurl,
					type	: 'POST',
					data	: ajaxData,
					success	: function(e){
						theButton.after(e);
						theButton.remove();
						bindSetBackOwnerNotPaid();
					},
					complete: function(e){
						wrapper.removeLoadingLayer();
					},
					error	: function(e, ee){
						console.log(ee);
					}
					
				});
			});
		}
	}
	
	bindSetBackOwnerNotPaid();
	function bindSetBackOwnerNotPaid(){
		if($('.undo-owner-paid').length){
			$('.undo-owner-paid').click(function(){
				var theButton = $(this),
					wrapper = $('.finance-report');
					
					wrapper.addLoadingLayer();
					
				var ajaxData = {
						'action'		: 'set_back_owner_not_paid',
						'booking_id'	: theButton.data('bookingid'),
					};
					
				$.ajax({
					url		: ajaxurl,
					type	: 'POST',
					data	: ajaxData,
					success	: function(e){
						theButton.after(e);
						theButton.prev().remove();
						theButton.remove();
						bindSetOwnerPaid();
					},
					complete: function(e){
						wrapper.removeLoadingLayer();
					},
					error	: function(e, ee){
						console.log(ee);
					}
					
				});
			});
		}
	}
	
	/*
		Graphs js
	*/
	
	if($('#popular-area-bar').length){
		var popularAreaBarObj = null;
		generatePopularAreaBar();
		
		$('#popular-area-stat-submit').click(function(){
			generatePopularAreaBar($('#pa-from').val(), $('#pa-until').val());
		});
	}
	
	function generatePopularAreaBar(from, until){
		from = (typeof from !== 'undefined') ? from : '';
		until = (typeof until !== 'undefined') ? until : '';
		
		$('#popular-area-wrapper').addLoadingLayer();
		
		$.ajax({
			url			: ajaxurl,
			data		: {'action': 'get_popular_area_stat_data', 'from': from, 'until':until},
			method		: 'POST',
			dataType	: 'json',
			success		: function(e){
				//console.log(e);
				if(popularAreaBarObj){
					updateData(popularAreaBarObj, e.labels, e.dataset);
				}else{
					var ctx = document.getElementById("popular-area-bar").getContext("2d");
					popularAreaBarObj = new Chart(ctx, {
									type: 'bar',
									data: {
										labels		: e.labels,
										datasets	: e.dataset,
									},
									options: {
										responsive: true,
										legend: {
											position: 'top',
										},
										scales: {
											yAxes: [{
												ticks: {
													beginAtZero: true
												}
											}]
										}
									}
								});
				}
				
				
				$('#popular-area-wrapper').removeLoadingLayer();
			},
			error	: function(e, ee){
				console.log(ee);
			}
		});
	}
	
	if($('#popular-property-bar').length){
		var popularPropertyBarObj = null;
		generatePopularPropertyBar();
		
		$('#popular-property-stat-submit').click(function(){
			generatePopularPropertyBar($('#pp-from').val(), $('#pp-until').val());
		});
	}
	
	function generatePopularPropertyBar(from, until){
		from = (typeof from !== 'undefined') ? from : '';
		until = (typeof until !== 'undefined') ? until : '';
		
		$('#popular-property-wrapper').addLoadingLayer();
		
		$.ajax({
			url			: ajaxurl,
			data		: {'action': 'get_popular_property_stat_data', 'from': from, 'until':until},
			method		: 'POST',
			dataType	: 'json',
			success		: function(e){
				//console.log(e);
				if(popularPropertyBarObj){
					updateData(popularPropertyBarObj, e.labels, e.dataset);
				}else{
					var ctx = document.getElementById("popular-property-bar").getContext("2d");
					popularPropertyBarObj = new Chart(ctx, {
									type: 'bar',
									data: {
										labels		: e.labels,
										datasets	: e.dataset,
									},
									options: {
										responsive: true,
										legend: {
											position: 'top',
										},
										scales: {
											yAxes: [{
												ticks: {
													beginAtZero: true
												}
											}]
										}
									}
								});
				}
				
				
				$('#popular-property-wrapper').removeLoadingLayer();
			},
			error	: function(e, ee){
				console.log(ee);
			}
		});
	}
	
	if($('#sales-achievement-graph').length){
		var salesAchievementObj = null;
		generateSalesAchievementGraph();
		$('#sales-achievement-submit').click(function(){
			generateSalesAchievementGraph($('#sa-year').val(), $('#sa-type').val());
		});
	}
	
	function generateSalesAchievementGraph(year, type){
		year = (typeof year !== 'undefined') ? year : '';
		type = (typeof type !== 'undefined') ? type : '';
		
		$('#sales-achievement-wrapper').addLoadingLayer();
		$.ajax({
			url			: ajaxurl,
			data		: {'action': 'get_sales_achievement_graph_data', 'year': year, 'type': type},
			method		: 'POST',
			dataType	: 'json',
			success		: function(e){
				//console.log(e);
				if(salesAchievementObj){
					updateData(salesAchievementObj, e.labels, e.dataset);
				}else{
					var config = {
								type: 'line',
								data: {
									labels	: e.labels,
									datasets: e.dataset,
								},
								options: {
									title:{
										text: "Sales Achievement"
									},
									scales: {
										xAxes: [{
											display: true,
										}],
										yAxes: [{
											display: true,
											ticks: {
												beginAtZero: true,
												callback: function(value, index, values) {
													return '$ ' + accounting.formatNumber(value,0,'.',',');

												}
											}
										}]
									},
									tooltips : {
										callbacks: {
											label : function (tooltipItem, data){
												var theDataSet = data.datasets[tooltipItem.datasetIndex];
												/* console.log(theDataSet.label);
												console.log(tooltipItem); */
												
												return theDataSet.label + ' : $ ' +accounting.formatNumber(tooltipItem.yLabel,0,'.',',');
											}
										}
									}
								}
							};
					var ctx = document.getElementById("sales-achievement-graph").getContext("2d");
					salesAchievementObj = new Chart(ctx, config);
				}
				$('#sales-achievement-wrapper').removeLoadingLayer();
			},
			error		: function(e){
				console.log(e);
			}
		});
	}
		
	
	function updateData(chart, labels, data, type) {
		//type = (typeof type !== 'undefined') ? type : '';
		
		chart.data.labels = labels;
		chart.data.datasets = data;
		
		if(typeof type !== 'undefined'){
			chart.type = type;
		}
		
		chart.update();
	}
	
	/*
		Graphs js end
	*/
	
	$('input#rvb_bedrooms').change(function(){
		var bedrooms = $(this).val(),
			room_form_exist = $('.the-room').length;
			
		if(room_form_exist){
			var diff = parseInt(bedrooms) - parseInt(room_form_exist);
			
			//Jika ada pengurangan jumlah bedroom, hapus form sleeping arangement berdasarkan diff
			if(diff < 0){
				$('.the-room').slice(diff).remove();
				return;
			}
		}
		
		var ajaxData = {
			'action'	: 'render_sleeping_arrangement_form',
			'bedrooms'	: bedrooms,
			'start_from': room_form_exist,
			'attr'		: 'required', //applied to the bed_type dropdown
		};
		
		$.ajax({
			url		: ajaxurl,
			type	: 'POST',
			data	: ajaxData,
			success	: function(e){
				$('#sleeping-arrangement .inside').append(e);
				
			},
			error	: function(e, ee){
				console.log(ee);
			}
		});
	});
	
});