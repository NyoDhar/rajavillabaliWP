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
	
	$.fn.digits = function(options){
		
		var settings = $.extend({
            precision	: 0,
			thousand 	: '.',
			decimal		: ',',
        }, options );
		
		this.keyup(function(){
			var number = $(this).val();
			$(this).val(accounting.formatNumber(number,settings.precision,settings.thousand,settings.decimal));
		});
		
		return this;
	}
	
	$.fn.addMsg = function(options){
		
		var settings = $.extend({
            msg				: 'Please fill in this field.',
			position 		: 'left',
			theInput		: '',
			callbackFunc	: null,
        }, options );
		
		var msgBox = $('<div class="msgbox msgbox-'+settings.position+'">' + 
					'<span>!</span> ' + settings.msg +
					'</div>');
					
		var offset = this.offset();
		var boxSize = this.outerWidth();
		console.log( parseInt(offset.top) );
		//var thebox = this.find('.msgbox');
		
		/* this.append(msgBox);
		
		
		
		var thebox = this.find('.msgbox');
		var boxSize = (settings.position == 'left' || settings.position == 'right') ? thebox.outerWidth() : thebox.outerHeight(),
			boxPosition = (settings.position == 'left' || settings.position == 'top') ? '-' : '';
		thebox.css(settings.position, boxPosition + (boxSize + 5 ) + 'px'); */
		
		$('body').append(msgBox);
		msgBox.css('left', ( offset.left + boxSize + 5 ) + 'px');
		msgBox.css('top', offset.top + 'px');
		
		//$('body').stop().animate({scrollTop: ( offset.top - 90 )}, 500, 'swing', function(){ alert('scrolled');});
		$('html, body').stop().animate({scrollTop: offset.top - 200 }, 500, 'swing' );
		
		/* setTimeout(function(){
			thebox.fadeOut(400, function(){
				$(this).remove();
			});
		}, 5000); */
		
		if(settings.theInput != ''){
			$(settings.theInput).focus(function(){
				msgBox.remove();
			});
		}else{
			this.focus(function(){
				msgBox.remove();
			});
		}
		
		if(settings.callbackFunc){
			settings.callbackFunc(msgBox);
		}
		/* if(this.is(':visible')){
			this.focus(function(){
				msgBox.remove();
			});
		}else{
			this.change(function(){
				msgBox.remove();
			});
		} */
		
		
		return this;
	}
	
}( jQuery ));

jQuery(document).ready(function($){
	
	var is_mobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	
	$("#masthead").sticky({ topSpacing: 0 });
	
	if(!is_mobile){
		$(".bookbox").sticky({ topSpacing: 105 });
	}
	
	if(is_mobile){
		$('.mobile-menu-open').click(function(){
			$('.main-navigation').css('left', '0px');
			$('body, #page').css('overflow', 'hidden');
		});
		
		$('.mobile-close-menu').click(function(){
			$('.main-navigation').css('left', '-100%');
			$('body, #page').css('overflow', 'auto');
		});
		
		if($('.open-filter').length){
			$('.open-filter').click(function(e){
				e.preventDefault();
				$('.property-filter').css('top', '0px');
				$('body, #page').css('overflow', 'hidden');
			});
			
			$('.close-filter').click(function(){
				$('.property-filter').css('top', '-100%');
				$('body, #page').css('overflow', 'auto');
			});
		}
		
		if($('.open-bookbox ').length){
			$('.open-bookbox ').click(function(e){
				e.preventDefault();
				$('.bookbox').css('top', '0px');
				$('body, #page').css('overflow', 'hidden');
			});
			
			$('.close-bookbox').click(function(){
				$('.bookbox').css('top', '-100%');
				$('body, #page').css('overflow', 'auto');
			});
		}
	}
	
	if($('#login-register-homeowner').length){
		$('form#login-register-form-homeowner').submit(function(e){
			e.preventDefault();
			var loaderWrapper = $('#login-register-homeowner .inner-window');
			loaderWrapper.addLoadingLayer();
			
			var ajaxData = {
						'action'	: 'rvb_account_action_register',
						'type'		: 'owner',
						'username'	: $(this).find('input[name="username"]').val(),
						'email'		: $(this).find('input[name="email"]').val(),
						'password'	: $(this).find('input[name="password"]').val(),
						'confirm_password'	: $(this).find('input[name="confirm-password"]').val(),
						'name'		: $(this).find('input[name="name"]').val(),
						'phone'		: $(this).find('input[name="phone"]').val(),
					};
			console.log(ajaxData);
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					console.log(e);
					var r = e.split('|');
					if(r[0] == 'success'){
						//alert('berhasil');
						loaderWrapper.find('.loading-data').prepend(r[1]);
						if(r[2] != ''){
							window.location.replace(r[2]);
						}else{
							location.reload();
						}
						
					}else{
						loaderWrapper.find('.errors').html(r[1]);
						loaderWrapper.removeLoadingLayer();
					}
				},
				error	: function(e, ee){
					console.log(ee);
				}
			});
		});
	}
	
	// Login for user and homeowner, register for user
	if($('#login-register').length){
		$('#open-login-register').click(function(e){
			e.preventDefault();
			$('#login-register').css('display', 'flex');
		});
		
		var theForm = $('#login-register-form');
		$('#create-account').click(function(e){
			e.preventDefault();
			$('.register').show();
			$('.login').hide();
			theForm.data('act', 'register');
		});
		
		$('#sign-in').click(function(e){
			e.preventDefault();
			$('.register').hide();
			$('.login').show();
			theForm.data('act', 'login');
		});
		
		theForm.submit(function(e){
			e.preventDefault();
			var loaderWrapper = $('#login-register .inner-window');
			loaderWrapper.addLoadingLayer();
			
			var ajaxData = {
						'action'	: 'rvb_account_action_' + $(this).data('act'),
						'type'		: 'user',
						'username'	: $(this).find('input[name="username"]').val(),
						'email'		: $(this).find('input[name="email"]').val(),
						'password'	: $(this).find('input[name="password"]').val(),
						'confirm_password'	: $(this).find('input[name="confirm-password"]').val()
					};
			console.log(ajaxData);
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					console.log(e);
					var r = e.split('|');
					if(r[0] == 'success'){
						//alert('berhasil');
						loaderWrapper.find('.loading-data').prepend(r[1]);
						location.reload();
					}else{
						loaderWrapper.find('.errors').html(r[1]);
						loaderWrapper.removeLoadingLayer();
					}
				},
				error	: function(e, ee){
					console.log(ee);
				}
			});
		});
	}
	
	if($('#logout').length){
		$('#logout').click(function(e){
			e.preventDefault();
			$('body').addLoadingLayer();
				$.ajax({
					url		: ajaxurl,
					type	: 'POST',
					data	: {'action': 'rvb_account_action_logout'},
					success	: function(e){
						window.location.replace(e);
						//location.reload();
					},
					error	: function(e, ee){
						console.log(ee);
					}
				});
		});
	}
	
	if($('.popup-window').length){
		$('.popup-window #close-window').click(function(){
			$('.popup-window').css('display', 'none');
		});
	}
	
	
	if($('.rvb-datepicker').length){
		$('.rvb-datepicker').datepicker({
			dateFormat: "d MM yy",
		});
	}
	
	if($('.open-change-search').length){
		$('.open-change-search').click(function(e){
			e.preventDefault();
			if($('.change-search').is(':visible')){
				$('.change-search').slideUp();
				$(this).find('span').text($(this).data('text'));
			}else{
				$('.change-search').slideDown();
				$(this).find('span').text($(this).data('cancel'));
			}
		});
	}
	
	/* function init_owl() {
		$(".owl-carousel[data-carousel=owl]").each( function(){
			var config = {
				loop: false,
				nav: $(this).data( 'nav' ),
				dots: false, //$(this).data( 'pagination' ),
				items: 4,
				navSpeed: 800,
				navText: ['<i class="fa fa-chevron-left" aria-hidden="true"></i>', '<i class="fa fa-chevron-right" aria-hidden="true"></i>']
			};
		
			var owl = $(this);
			if( $(this).data('items') ){
				config.items = $(this).data( 'items' );
			}
			if( $(this).data('loop') ){
				config.loop = true;
			}
			if ($(this).data('margin')) {
				config.margin = $(this).data('margin');
			} else {
				config.margin = 30;
			}
			if ($(this).data('large')) {
				var desktop = $(this).data('large');
			} else {
				var desktop = config.items;
			}
			if ($(this).data('medium')) {
				var medium = $(this).data('medium');
			} else {
				var medium = config.items;
			}
			if ($(this).data('smallmedium')) {
				var smallmedium = $(this).data('smallmedium');
			} else {
				var smallmedium = config.items;
			}
			if ($(this).data('extrasmall')) {
				var extrasmall = $(this).data('extrasmall');
			} else {
				var extrasmall = 2;
			}
			if ($(this).data('verysmall')) {
				var verysmall = $(this).data('verysmall');
			} else {
				var verysmall = 1;
			}
			config.responsive = {
				0:{
					items:verysmall
				},
				320:{
					items:extrasmall
				},
				768:{
					items:smallmedium
				},
				980:{
					items:medium
				},
				1280:{
					items:desktop
				}
			}
			if ( $('html').attr('dir') == 'rtl' ) {
				config.rtl = true;
			}
			$(this).owlCarousel( config );
			// owl enable next, preview
			var viewport = jQuery(window).width();
			var itemCount = jQuery(".owl-item", $(this)).length;

			if(
				(viewport >= 1280 && itemCount <= desktop) //desktop
				|| ((viewport >= 980 && viewport < 1280) && itemCount <= medium) //desktop
				|| ((viewport >= 768 && viewport < 980) && itemCount <= smallmedium) //tablet
				|| ((viewport >= 320 && viewport < 768) && itemCount <= extrasmall) //mobile
				|| (viewport < 320 && itemCount <= verysmall) //mobile
			) {
				$(this).find('.owl-prev, .owl-next').hide();
			}
		} );
	}
	
	setTimeout(function(){
		init_owl();
	}, 50); */
	
	/* $('.property-gallery-index .thumb-link').each(function(e){
		$(this).click(function(event){
			event.preventDefault();
			$('.property-gallery-preview-owl').trigger("to.owl.carousel", [e, 800, true]);
			
			return false;
		});
	});
	$('.property-gallery-preview-owl').on('changed.owl.carousel', function(event) {
		setTimeout(function(){
			var index = 0;
			$('.property-gallery-preview-owl .owl-item').each(function(i){
				if ($(this).hasClass('active')){
					index = i - 3;
				}
			});
			
			$('.property-gallery-index .thumb-link').removeClass('active');
			$('.property-gallery-index .owl-item').eq(index).find('.thumb-link').addClass('active');
		},50);
	}); */
	
	if($('.open-submit-form a').length){
		$('.open-submit-form a').click(function(e){
			e.preventDefault();
			/* e.preventDefault();
			$('.submit-property').show();
			$('body').css('overflow-y', 'hidden'); */
			if($('#login-register-homeowner').length){
				$('#login-register-homeowner').css('display', 'flex');
			}else{
				alert('Go to your account area');
			}
		});
		
		/* $('.submit-property-form .head .close').click(function(){
			$('.submit-property').hide();
			$('body').css('overflow-y', 'auto');
		}); */
	}
	
	if($('#open-inquiry').length){
		
		$('#open-inquiry').click(function(e){
			e.preventDefault();
			$('#inq-villa-link').val(inqVillaLink);
			$('#inq-villa-name').val(inqVillaName);
			$('#inq-check-in').val($('.bookbox input.mphb_room_type_check_in_datepicker').val());
			$('#inq-check-out').val($('.bookbox .mphb-check-out-date-wrapper input.mphb-datepick').val());
			
			$('#inquiry-form').show();
			$('body').css('overflow-y', 'hidden');
		});
		
		$('#inquiry-form .head .close').click(function(){
			$('#inquiry-form').hide();
			$('body').css('overflow-y', 'auto');
		});
	}
	
	//will be sent to customer on inquiry sent successfully
	document.addEventListener( 'wpcf7mailsent', function( event ) {
		if ( '3184' == event.detail.contactFormId ) {
			console.log($('#inq-villa-name').val());
			var ajaxData = {
						'action'			: 'inquiry_thankyou_email',
						'to'				: $('#inq-email').val(),
						'name'				: $('#inq-full-name').val(),
						'phone'				: $('#inq-phone').val(),
						'message'			: $('#inq-message').val(),
						'villa-name'		: $('#inq-villa-name').val(),
						'villa-link'		: $('#inq-villa-link').val(),
						'check-in'			: $('#inq-check-in').val(),
						'check-out'			: $('#inq-check-out').val(),
					};
			
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					console.log(e);
				},
				error	: function( jqxhr, status){
					console.log(status);
				}
			});
		}
	}, false );
	
	if($('.photos').length){
		$('.photos').magnificPopup({
		  delegate: 'a', // child items selector, by clicking on it popup will open
		  type: 'image',
		  gallery:{enabled:true},
		  // other options
		});
	}
	
	if($('.the-review-form').length){
		$('#commentform').submit(function(e){
			e.preventDefault();
			$(this).addLoadingLayer();
			var ajaxData = {
							'action'			: 'rvb_add_new_review',
							'accomodation_id'	: $('#accomodation-id').val(),
							'customer_name'		: $('#author').val(),
							'email'				: $('#email').val(),
							'comment'			: $('#comment').val(),
							'rating'			: $('input[name="rating"]:checked').val(),
						};
			
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					$('#rvb-comment-notice').html(e);
				},
				complete: function(){
					$('#commentform').removeLoadingLayer();
				},
				error	: function(e, ee){
					console.log(ee);
				}
			});
		});
	}
	
	if($('.expand-att').length){
		$('.expand-att').click(function(e){
			e.preventDefault();
			
			$(this).prev('.mphb_room_type_facility').find('li:nth-child(n+3)').css('display', 'list-item');
			$(this).css('display', 'none');
			$(this).next('.collaps-att').css('display', 'initial');
		});
		
		$('.collaps-att').click(function(e){
			e.preventDefault();
			
			$(this).prev().prev().find('li:nth-child(n+3)').css('display', 'none');
			$(this).css('display', 'none');
			$(this).prev('.expand-att').css('display', 'initial');
		});
	}
	
	if($('#submit-listing').length){
		
		$('.buttons .next').click(function(){
			
			/* var theForm = $('#submit-property');
			var valid = theForm[0].reportValidity(); */
			var valid = submitPropertyValidation($(this).data('current'));
			console.log(valid);
			
			if(!valid) return;
		
			if(rvbparams.submitFormChanged){
				saveProperty( nextStep, $(this) );
			}else{
				nextStep($(this));
			}
			
		});
		
		$('.buttons .prev').click(function(){
			
			$("#step-" + $(this).data('current')).addClass('hide').removeClass('show');
			$("#step-" + $(this).data('step')).addClass('show').removeClass('hide');
			
			window.scrollTo(0, 0);
		});
		
		
		$('.property-price-input').change(function(e){

			var val = parseInt( $(this).val().replace('.','') );
			var fee = val * rvbparams.fee / 100,
				totalPrice = val + fee;
			
			var season = $(this).data('season');
			
			//Set total Price on christmast & new year and high season
			if(season == 'high'){
				$('#property-price-high').val( totalPrice );
				$('#property-price-christmast-season').val( totalPrice );
			}
			
			//Set total Price on low season
			if(season == 'low'){
				$('#property-price-low').val( totalPrice );
			}
			
			$('#ota-earning-'+season+' .the-price span').html( accounting.formatNumber( fee, 0, '.', ',') );
			$('#ota-charge-'+season+' .the-price span').html( accounting.formatNumber( totalPrice, 0, '.', ',') );
			
		});
		
		$('#submit-property').on('change', ':input', function(e){ 
			//':input' selector get all form fields even textarea, input, or select
			rvbparams.submitFormChanged = true;
		});
		
		/* $('#submit-property textarea#description').change(function(){
			alert('changed');
		}); */
		
		$('a.edit-info').click(function(e){
			e.preventDefault();
			
			$("#step-8").addClass('hide').removeClass('show');
			$("#step-" + $(this).data('step')).addClass('show').removeClass('hide');
			
			window.scrollTo(0, 0);
		});
		
		//Submit property for review
		$('#submit-it').click(function(){
			if(!$('#confirm-info').is(':checked')){
				$('#confirm-info-text').addMsg({
					position	: 'right',
					msg			: 'Please confirm if property location and property name are correct.',
					theInput	: '#confirm-info',
				});
				
				return;
			}
			
			$('#step-8').addLoadingLayer();
			
			var ajaxData = {
				'action'	: 'submit_property_for_review',
				'post_id'	: $('#submit-property').data('postid'),
				'post_status'	: $('#submit-property').data('poststatus'),
			};
			
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					var result = e.split('|');
					
					if(result[0] == 'success'){
						$("#step-9").find('span.step-title').html(result[1]);
						$("#step-9").find('#content-msg').html(result[2]);
						
						$("#step-8").addClass('hide').removeClass('show');
						$("#step-9").addClass('show').removeClass('hide');
					}else{
						alert(result[1]);
					}
					
					$('#step-8').removeLoadingLayer();
				},
				error	:	function (e, ee){
					console.log(ee);
				}
			});
			
		});
		
		if($('a.remove-uploaded-photo').length){
			$('a.remove-uploaded-photo').click(function(e){
				e.preventDefault();
				var theImg = $(this).parent();
				
				theImg.addLoadingLayer();
				
				var ajaxData = {
					'action'	: 'remove_property_photo',
					'image_id'	: $(this).data('imgid'),
					
				};
				
				$.ajax({
					url		: ajaxurl,
					type	: 'POST',
					data	: ajaxData,
					success	: function(e){
						var result = e.split('|');
						if(result[0] == 'success'){
							rvbparams.submitFormChanged = true;
							theImg.fadeOut(400, function(){
								$(this).remove();
							});
						}else{
							theImg.removeLoadingLayer();
							alert(result[1]);
						}
						
					},
					error	: function(e, ee){
						console.log(ee);
					}
				});
			});
		}
	}
	
	function nextStep(obj){
		/* $("#step-" + $(this).data('current')).addClass('hide').removeClass('show');
		$("#step-" + $(this).data('step')).addClass('show').removeClass('hide'); */
		
		$("#step-" + obj.data('current')).addClass('hide').removeClass('show');
		$("#step-" + obj.data('step')).addClass('show').removeClass('hide');
		
		if(obj.data('step') == 8){
			reviewProperty();
		}
		
		window.scrollTo(0, 0);
	}
	
	function saveProperty( callbackFunc = null, nextButtonObj = null ){
		
		console.log('save property');
		
		var theForm = $('#submit-property');
		
		var formData = new FormData( theForm[0] );
		
		formData.append('action', 'rvb_save_property');
		formData.append('post_id', theForm.data('postid'));
		
		//console.log($('#description').val());
		if ($("#wp-description-wrap").hasClass("tmce-active")){
			//console.log(tinyMCE.activeEditor.getContent());
			formData.append('description', tinyMCE.activeEditor.getContent());
		}else{
			console.log( $('#description').val());
			formData.append('description', $('#description').val());
		}
		
		
		//for test purpose - prevent property from saving to database
		/* callbackFunc(nextButtonObj);
		return; */
		
		console.log(formData.get('images'));
		
		theForm.addLoadingLayer();
		
		
		
		$.ajax({
			url		: ajaxurl,
			data	: formData,
			type	: 'POST',
			processData: false,
			contentType: false,
			success	: function(e){
				console.log(e);
				
				var result = e.split('|');
				if(result[0] == 'success'){
					
					theForm.data('postid', result[1]);
					
					if(callbackFunc){
						callbackFunc(nextButtonObj);
					}
				}else{
					theForm.append(e);
				}
				
				rvbparams.submitFormChanged = false;
				theForm.removeLoadingLayer();
			},
			error	: function(e, ee){
				console.log(ee);
			}
			
		});
	}
	
	function submitPropertyValidation( step ){
		var valid = true;
		console.log(step);
		
		switch( step ){
			case 1 :
				var pname = $('#name'),
					bedrooms = $('#bedrooms'),
					capacity = $('#capacity'),
					land_size = $('#land-size');
					
				if( pname.val() == ''){
					pname.addMsg({position: 'right'});
					return false;
				}
				
				if( bedrooms.val() == ''){
					bedrooms.addMsg({position: 'right'});
					return false;
				}
				
				if( capacity.val() == ''){
					capacity.addMsg({position: 'right'});
					return false;
				}
				
				if( land_size.val() == ''){
					land_size.addMsg({position: 'right'});
					return false;
				}
				
			break;
			
			case 2	:
				var area = $('#area'),
					pinpoint = $('#pinpoint');
				
				if( area.val() == '0'){
					area.addMsg({position: 'right'});
					return false;
				}
				
				if( pinpoint.val() == ''){
					$('#blk-map-wrapper').addMsg({
													position	: 'right', 
													msg			: 'Please select your location on the map',
													theInput	: '#pac-input',
													
												});
					return false;
				}
			break;
			
			case 3 :
				if($('.images-input').length == 0){
					$('#media-uploader').addMsg({
						position	: 'right', 
						msg			: 'Please upload some photos of your property',
						callbackFunc	: function(msgBox){
											blkddu.obj.on('drop', function(){
												msgBox.remove();
											});	
										}
					});
					
					return false;
				}
			break;
			
			case 5 :
				var price_high = $('#price-high-season-input');
				if( price_high.val() == ''){
					price_high.addMsg({position: 'right'});
					return false;
				}
				
				var price = $('#price-low-season-input');
				if( price.val() == ''){
					price.addMsg({position: 'right'});
					return false;
				}
				
			break;
			
			case 6 :
				var cencelPolicies = $('.cancel-policies');
				if( cencelPolicies.find('input[type="radio"]:checked').length == 0 ){
					cencelPolicies.addMsg({
						position	: 'right',
						msg			: 'Please select one of the cancelation policies',
						theInput	: '.cancel-policies input[type="radio"]',
					});
					return false;
				}
				
			break;
			
			case 7 :
				var rvb_property_contact_phone = $('#rvb_property_contact_phone'),
					rvb_property_contact_email = $('#rvb_property_contact_email'),
					rvb_property_contact_new_booking_email = $('#rvb_property_contact_new_booking_email');
					
				if( rvb_property_contact_phone.val() == '' ){
					rvb_property_contact_phone.addMsg({
						position	: 'right',
					});
					return false;
				}
				
				if( rvb_property_contact_email.val() == '' ){
					rvb_property_contact_email.addMsg({
						position	: 'right',
					});
					return false;
				}
				
				if( rvb_property_contact_new_booking_email.val() == '' ){
					rvb_property_contact_new_booking_email.addMsg({
						position	: 'right',
					});
					return false;
				}
				
			break;
		}
		
		return valid;
	}
	
	function reviewProperty(){
		$('#area-review').html($('select#area option:selected').text());
		
		var mapReview = $('#blk-map-wrapper');
		mapReview.find('#pac-input').remove();
		$('#map-review').html(mapReview.html());
		
		$('#property-name-review').html($('#name').val());
		
		var details = reviewText.propertyDetail;
		details = details.replace('#bedrooms', $('#bedrooms').val());
		details = details.replace('#guest', $('#capacity').val());
		details = details.replace('#land_size', $('#land-size').val());
		$('#property-detail-review').html(details);
		
		var photos = reviewText.photos;
		photos = photos.replace('#photos', $('.images-input').length );
		$('#photos-review').html(photos);
		
		var ammenities_text = '';
		$('ul.ammenities').each(function(){
			var ammenities = [];
			$(this).find('input[type="checkbox"]:checked').each(function(){
				ammenities.push($(this).next().text());
			});
			
			ammenities_text += '<p><b>' + $(this).data('name') + ' : </b>' + ammenities.join(', ') + '</p>';
		});
		
		$('#ammenties-review').html(ammenities_text);

		var house_rule = [];
		$('ul.house-rules input[type="checkbox"]:checked').each(function(){
			house_rule.push($(this).next().text());
		});
		
		$('#house-rules-review').html( house_rule.join(', ') );
		
		$('#cancel-policy-review').html( $('ul.cancel-policies input[type="radio"]:checked').next().text() );
		
		var propertyContact = reviewText.contact_phone + ' : ' + $('#rvb_property_contact_phone').val()
								+ '<br>' + reviewText.contact_email + ' : ' + $('#rvb_property_contact_email').val()
								+ '<br>' + reviewText.booking_email + ' : ' + $('#rvb_property_contact_new_booking_email').val();
		
		$('#contact-review').html(propertyContact);
		
	}
	
	if($('form#change-password').length){
		$('form#change-password').submit(function(e){
			e.preventDefault();
			
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			if(formData.get('new-password') != formData.get('confirm-password')){
				$('#confirm-password').addMsg({
					msg	: 'New password and confirm password does not match',
					position: 'right',
				});
			
				return;
			}
			
			formData.append('action', 'rvb_do_change_user_password');
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					
					var result = e.split('|');
					if(result[0] == 'success'){
						alert(result[1]);
						theForm[0].reset();
					}else{
						alert(result[1]);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	if($('form#change-user-info').length){
		$('form#change-user-info').submit(function(e){
			e.preventDefault();
			
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			/* if(formData.get('new-password') != formData.get('confirm-password')){
				$('#confirm-password').addMsg({
					msg	: 'New password and confirm password does not match',
					position: 'right',
				});
			
				return;
			} */
			
			formData.append('action', 'rvb_do_change_user_info');
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					
					var result = e.split('|');
					if(result[0] == 'success'){
						alert(result[1]);
						//theForm[0].reset();
					}else{
						alert(result[1]);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	if($('#change-booking').length){
		$('#change-booking form').submit(function(e){
			e.preventDefault();
			
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			formData.append('action', 'rvb_do_change_booking');
			formData.append('booking_id', theForm.data('bid'));
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					
					var result = e.split('|');
					alert(result[1]);
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
			
		});
		
		$('#check-in-date-input').change(function(){
			var theForm = $('#change-booking form');
			var ajaxData = {
				'action'		: 'rvb_get_check_out_date_change',
				'booking_id'	: theForm.data('bid'),
				'check-in'		: $(this).val(),
			};
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: ajaxData,
				type	: 'POST',
				success	: function(e){
					
					$('span#checkout-date').html(e.data.checkout);
					
					if(!e.success){
						alert(e.data.message);
						$('#change-booking form input[type="submit"]').attr('disabled', 'disabled');
					}else{
						$('#change-booking form input[type="submit"]').removeAttr('disabled');
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	if($('#see-cancel-policy').length){
		$('#see-cancel-policy').click(function(e){
			e.preventDefault();
			$('#cancel-policy-content').css('display', 'flex');
		});
	}
	
	if($('#cancel-booking').length){
		$('#cancel-booking form').submit(function(e){
			e.preventDefault();
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			formData.append('action', 'rvb_cancel_the_booking');
			formData.append('booking_id', theForm.data('bid'));
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					//var result = JSON.parse(e);
					
					if(e.success){
						alert(e.data.msg);
						window.location.replace(e.data.redirect_to);
					}else{
						alert(e.data.msg);
						theForm.removeLoadingLayer();
					}
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
			
		});
	}
	
});