(function( $ ) {
	$.fn.addLoadingLayer = function(options){
		var settings = $.extend({
            size: "fa-3x",
			spinner: true,
        }, options );
		
		var spinner = '';
		
		if(settings.spinner){
			spinner = typeof adminpage != 'undefined' ? '<span class="load spinner is-active"></span>' : '<i class="load fa fa-spinner fa-pulse '+settings.size+'"></i>';
		}
		
		var layer = "<div class='loading-data'>"+spinner+"</div>";
		this.append(layer);
		this.css('position','relative');
		return this;
	};
	
	$.fn.removeLoadingLayer = function(){
		this.children('.loading-data').remove();
		this.css('position','');
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
	
	if($('input.timepicker').length){
		$('input.timepicker').timepicker({});
	}
	
	$(".mphb_sc_search-location>select").treeselect({
		buttontext : 'Any',
		selectedTotal : 2,
	});
	
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
	
	/* function blkPasswordMeter( passInput ){
		var valid = true;
		var upperCase= new RegExp('[A-Z]');
		var numbers = new RegExp('[0-9]');
		
		var pass = passInput.val();
		
		if(pass.length < 8 || !pass.match(upperCase) || !pass.match(numbers)){
			valid = false;
		}
		
		if(!valid){
			passInput.addMsg({
				position	: 'right',
				msg			: 'Your password must be at least 8 characters. <br> It must contain a mixture of upper and lower case letters, and at least one number or symbol.',
			});
		}
		
		return valid;
	} */
	
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
						//location.reload();
						window.location.replace(r[2]);
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
			$('body').css('overflow-y', 'auto');
		});
	}
	
	
	if($('.rvb-datepicker').length){
		$('.rvb-datepicker').datepicker({
			dateFormat: "d MM yy",
		});
	}
	
	if($('.rvb-clasic-datepicker').length){
		$('.rvb-clasic-datepicker').datepicker({
			dateFormat: "yy-mm-dd",
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
				//$('#login-register-homeowner').css('overflow-y', 'auto');
				$('body').css('overflow-y', 'hidden');
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
		bindAttToogleLink();
	}
	
	function bindAttToogleLink(){
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
		
		var pid = getUrlParameter('pid');
		var curStep = getUrlParameter('step');
		
		if(pid && curStep){
			$("#step-1").addClass('hide').removeClass('show');
			$("#step-" + curStep).addClass('show').removeClass('hide');
			
			if(curStep == rvbparams.submissionStep['review']){
				reviewProperty();
			}
		}
		
		$('.buttons .next').click(function(){
			
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
			
			var postID = $('#submit-property').data('postid');
		
			if( postID ){
				var url = rvbparams.propertySubmitUrl;
				url += '?pid='+postID+'&step='+$(this).data('step');
				window.history.replaceState("Filter", "Filter", url );
			}
		
			window.scrollTo(0, 0);
		});
		
		$('.buttons-editing .save-edit').click(function(){
			
			var valid = submitPropertyValidation($(this).data('current'));
			console.log(valid);
			
			if(!valid) return;
		
			if(rvbparams.submitFormChanged){
				$('#submit-listing .inner-form.editing').addLoadingLayer();
				saveProperty( closeEditForm, $(this) );
			}else{
				closeEditForm( $(this) );
			}
			
		});
		
		$('.buttons-editing .cancel-edit').click(function(){
			closeEditForm( $(this) );
		});
		
		function closeEditForm(obj){
			//remove loading layer after property changes is saved
			if(obj.hasClass('save-edit')){
				$('#submit-listing .inner-form.editing').removeLoadingLayer();
				reviewProperty();
			}
			
			$('#step-'+obj.data('current')).removeClass('editing');
			$('body').css('overflow', 'auto');
			
			
		}
		
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
			
			/* $("#step-8").addClass('hide').removeClass('show');
			$("#step-" + $(this).data('step')).addClass('show').removeClass('hide');
			
			window.scrollTo(0, 0); */
			
			$("#step-" + $(this).data('step')).addClass('editing');
			$('body').css('overflow', 'hidden');
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
			
			var review_step = rvbparams.submissionStep['review'],
				next_step = review_step + 1;
			$('#step-'+review_step).addLoadingLayer();
			
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
						$("#step-"+next_step).find('span.step-title').html(result[1]);
						$("#step-"+next_step).find('#content-msg').html(result[2]);
						
						$("#step-"+review_step).addClass('hide').removeClass('show');
						$("#step-"+next_step).addClass('show').removeClass('hide');
					}else{
						alert(result[1]);
					}
					
					$('#step-'+review_step).removeLoadingLayer();
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
		
		$('input#bedrooms').change(function(){
			var bedrooms = $(this).val(),
				room_form_exist = $('.sleeping-arrangement .the-room').length;
				console.log(room_form_exist);
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
				
			};
			
			$('.sleeping-arrangement').addLoadingLayer();
			$.ajax({
				url		: ajaxurl,
				type	: 'POST',
				data	: ajaxData,
				success	: function(e){
					if(room_form_exist){
						$('.sleeping-arrangement').append(e);
					}else{
						$('.sleeping-arrangement').html(e);
					}
					
					$('.sleeping-arrangement').removeLoadingLayer();
				},
				error	: function(e, ee){
					console.log(ee);
				}
			});
		});
		
		//Adding ical
		$('#add-ical').click(function(){
			var iCalWrapper = $('#icals');
			var index = iCalWrapper.find('li:last-child').data('index');
			console.log(index);
				index = typeof index == 'undefined' ? 0 : (index + 1);
			
			var ical = '<li data-index="'+index+'">' + $('#ical-url').val() +
							'<input type="hidden" name="mphb_sync_urls['+index+'][url]" value="'+$('#ical-url').val()+'">'+
							'<span class="delete">&times;</span>'+
						'</li>';
			
			iCalWrapper.append(ical);
			//$('#ical-name').val('');
			$('#ical-url').val('');
			bindRemoveIcal();
		});
		
		$('.breakfast').click(function(){
			if($(this).val() == 'no'){
				$('.bf-cost').removeClass('tmp-hide');
			}else{
				$('.bf-cost').addClass('tmp-hide');
			}
		});
		
	} //Submit Listing End
	
	function bindRemoveIcal(){
		$('#icals li .delete').unbind().click(function(){
			$(this).parent().remove();
		});
	}
	
	function nextStep(obj){
		/* $("#step-" + $(this).data('current')).addClass('hide').removeClass('show');
		$("#step-" + $(this).data('step')).addClass('show').removeClass('hide'); */
		
		$("#step-" + obj.data('current')).addClass('hide').removeClass('show');
		$("#step-" + obj.data('step')).addClass('show').removeClass('hide');
		
		var postID = $('#submit-property').data('postid');
		
		if( postID ){
			var url = rvbparams.propertySubmitUrl;
			url += '?pid='+postID+'&step='+obj.data('step');
			window.history.replaceState("Filter", "Filter", url );
		}
		
		console.log(rvbparams.submissionStep['review']);
		
		if(obj.data('step') == rvbparams.submissionStep['review']){
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
		//console.log(step);
		
		$('input.rvb-required:visible, select.rvb-required:visible').each(function(){
			if($(this).val() == '' || $(this).val() == 0){
				$(this).addMsg({position: 'right'});
				valid = false;
				
			}
		});
		
		if($('#legal-docs').is(':visible')){
			if($('.legal-docs-input').length == 0){
				$('#file-uploader').addMsg({
					position	: 'right', 
					msg			: 'Please upload the required legal documents of your property',
					callbackFunc	: function(msgBox){
										blkddu.fileObj.on('drop', function(){
											msgBox.remove();
										});	
									}
				});
				
				valid = false;
			}
		}
		
		if($('#blk-map-wrapper').is(':visible')){
			if( $('#pinpoint').val() == ''){
				$('#blk-map-wrapper').addMsg({
					position	: 'right', 
					msg			: 'Please select your location on the map',
					theInput	: '#pac-input',
					
				});
				valid = false;
			}
		}
		
		if($('#media-uploader').is(':visible')){
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
				
				valid = false;
			}
		}
		
		if($('#breakfast').is(':visible')){
			if( $('#breakfast input[type="radio"]:checked').length == 0){
				$('#breakfast').addMsg({
					position	: 'right', 
					msg			: 'Please state if it is include breakfast or not',
					theInput	: '#breakfast input[type="radio"]',
					
				});
				
				valid = false;
			}
		}
		
		if($('.cancel-policies').is(':visible')){
			var cencelPolicies = $('.cancel-policies');
			if( cencelPolicies.find('input[type="radio"]:checked').length == 0 ){
				cencelPolicies.addMsg({
					position	: 'right',
					msg			: 'Please select one of the cancelation policies',
					theInput	: '.cancel-policies input[type="radio"]',
				});
				
				valid = false;
			}
		}
		
		/* switch( step ){
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
				var valid = true;
				$('.rvb-required').each(function(){
					if($(this).val() == ''){
						$(this).addMsg({position: 'right'});
						valid = false;
						return false;
					}
				});
				
				return valid;
			break;
			
			case 3	:
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
			
			case 5 :
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
			
			case 6 :
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
			
			case 7 :
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
			
			case 8 :
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
		} */
		
		return valid;
	}
	
	function reviewProperty(){
		
		/*
		 *	Property Information
		*/
		
		$('#property-name-review').html($('#name').val());
		
		var details = reviewText.propertyDetail;
		details = details.replace('#bedrooms', $('#bedrooms').val());
		details = details.replace('#bathrooms', $('#bathrooms').val());
		details = details.replace('#guest', $('#capacity').val());
		details = details.replace('#land_size', $('#land-size').val());
		details = details.replace('#homearea', $('#home-area').val());
		$('#property-detail-review').html(details);
		
		var sa = '';
		$('.sleeping-arrangement .the-room').each(function(){
			let ensuite = $(this).find('input[type="checkbox"]').is(':checked') ? '<i class="fa fa-check-circle-o" aria-hidden="true"></i>' : '<i class="fa fa-times-circle-o" aria-hidden="true"></i>';
			sa += '<div class="col-sm-4">' +
					'<div class="the-sla">' +
						'<span class="att-title">'+$(this).find('label').text()+'</span>' +
						'<ul class="mphb_room_type_facility">' +
							'<li><i class="fa fa-bed" aria-hidden="true"></i> '+$(this).find('select option:selected').text()+'</li>' +
							'<li>' + $(this).find('li:last-child b').text() +
							' ' + ensuite +
							'</li>' +
						'</ul>' +
					'</div>' +
				'</div>';
		});
		
		$('#property-sa-review').html('<div class="row">' + sa + '</div>');
		
		var ammenities_text = '';
		$('.ammenities-inputs').each(function(){
			
			let remain = $(this).find('input[type="checkbox"]:checked').length - 10;
			console.log(remain);
			ammenities_text += '<div class="col-sm-4">'+
									'<span class="att-title">'+$(this).children('label').text()+'</span>'+
									'<ul class="mphb_room_type_facility">';
									
									$(this).find('input[type="checkbox"]:checked').each(function(){
										ammenities_text += '<li><i class="fa fa-check-circle-o" aria-hidden="true"></i> '+$(this).next('label').text()+'</li>';
									});
									
									
						ammenities_text += '</ul>';
							if(remain > 0){
								let showMore = reviewText.show_more.replace('#n', remain);
								ammenities_text += '<a href="#" class="expand-att">'+showMore+'</a>' +
													'<a href="#" class="collaps-att tmp-hide">'+reviewText.show_less+'</a>';
							}
						ammenities_text +='</div>';
		});
		
		$('#ammenties-review').html('<div class="row">' + ammenities_text + '</div>');
		bindAttToogleLink();
		
		let poolText = reviewText.pool_text.replace('#s', $('#pool-type option:selected').text());
			poolText = poolText.replace('#n', $('#pool-size').val())
		
		let miscAmm = '<ul class="mphb_room_type_facility">';
		$('#misc-ammenities input[type="checkbox"]:checked').each(function(){
			miscAmm += '<li><i class="fa fa-check-circle-o" aria-hidden="true"></i> '+$(this).prev('label').text() +'</li>';
		});
		
		miscAmm += '</ul>';
		$('#ammenties-misc-review').html('<p>'+poolText+'</p>' + miscAmm + '<br>');
		
		$('#description-review').html($('textarea#description').val());
		
		/* let legalDocs = $('#legal-docs');
			legalDocs.find('.remove-uploaded-photo').remove();
			legalDocs.find('input').remove();
			legalDocs.find('label').remove(); */
			
		var propertyContact = '<b>Title : </b>' + $('#rvb_property_contact_title option:selected').text()
								+ '<br><b>' + reviewText.contact_name + ' : </b>' + $('#rvb_property_contact_name').val()
								+ '<br><b>' + reviewText.contact_phone + ' : </b>' + $('#rvb_property_contact_phone').val()
								+ '<br><b>' + reviewText.contact_email + ' : </b>' + $('#rvb_property_contact_email').val()
								+ '<br><b>' + reviewText.booking_email + ' : </b>' + $('#rvb_property_contact_new_booking_email').val()
								+ '<br><br><b>' + reviewText.contact_legal + ' : </b><br><br>';
		$('#contact-review').html(propertyContact);
		
		$('#contact-review').addLoadingLayer();
		$.ajax({
			url		: ajaxurl,
			data	: {'action': 'get_villa_legal_docs_view', 'post_id': $('form#submit-property').data('postid')},
			type	: 'POST',
			success	: function(e){
				$('#contact-review').append(e);
				$('#contact-review').removeLoadingLayer();
			},
			error	: function(e, ee){
				console.log(ee);
			}
		});
		
		/*
		 *	Property Location
		*/
		
		$('#address-review').html($('#property-address').val());
		$('#area-review').html($('select#area option:selected').text());
		
		$('#map-review').blkgmap({
			pinpoint : $('#blk-map-wrapper').find('#pinpoint').val(),
		});
		
		$('#landmark-review').html($('#property-landmark').val());
		
		let pViews = [];
		$('#property-views input[type="checkbox"]:checked').each(function(){
			pViews.push($(this).next('label').text());
		});
		$('#views-review').html(pViews.join(', '));
		
		
		/*
		 *	Property Images
		*/
		
		$('#photos-review').addLoadingLayer();
		$.ajax({
			url		: ajaxurl,
			data	: {'action': 'get_villa_photos_view', 'post_id': $('form#submit-property').data('postid')},
			type	: 'POST',
			success	: function(e){
				$('#photos-review').html(e);
				$('#photos-review').removeLoadingLayer();
			},
			error	: function(e, ee){
				console.log(ee);
			}
		});
		
		/*
		 *	Property Price & Availability
		*/
		
		let breakfast = reviewText.breakfast_text + ' ' + $('#breakfast input[type="radio"]:checked').next('label').text();
		if($('#breakfast input[type="radio"]:checked').val() == 'no' && $('#bf-extra-cost').val() != '' ){
			breakfast += '. ' + reviewText.breakfast_extra_text.replace('#n', ( 'USD ' + $('#bf-extra-cost').val() ) );
		}
		
		$('#price-review').html($('#price-charge').html() + '<p>'+breakfast+'</p>');
		$('#ical-review').html($('#ical-sync').html());
		
		
		/*
		 *	House Rules & Cancellation policy
		*/
		
		var house_rule = [];
		$('ul.house-rules input[type="checkbox"]:checked').each(function(){
			house_rule.push($(this).next().text());
		});
		
		$('#house-rules-review').html( house_rule.join(', ') );
		
		let checkinout = '<b>Check-in</b> : '+$('#check-in').val() + '<br><b>Check-out</b> : ' + $('#check-out').val();
		$('#check-inout-review').html(checkinout);
		$('#cancel-policy-review').html( $('ul.cancel-policies input[type="radio"]:checked').next().text() );

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
	
	if($('form#reset-password')){
		$('form#reset-password').submit(function(e){
			e.preventDefault();
			
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			formData.append('action', 'rvb_get_reset_password_link');
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					
					if(e.success){
						alert(e.data.msg);
					}else{
						alert(e.data.msg);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
			
		});
	}
	
	if($('form#do-reset-password')){
		$('form#do-reset-password').submit(function(e){
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
			
			formData.append('action', 'rvb_do_reset_password_link');
			formData.append('uid', theForm.data('uid') );
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					console.log(e);
					
					if(e.success){
						alert(e.data.msg);
						$('#login-register').css('display', 'flex');
					}else{
						alert(e.data.msg);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
			
		});
	}
	
	if($('form#change-bank-info')){
		
		$('form#change-bank-info').submit(function(e){
			e.preventDefault();
			
			var theForm = $(this);
			var formData = new FormData(theForm[0]);
			
			formData.append('action', 'rvb_save_owner_bank_info');
			formData.append('uid', theForm.data('uid') );
			
			if($('#otp-wrapper').is(':visible')){
				var otp = $('input[name="verification"]').val();
				if(otp == '' || otp.length != 6 ){
					$('input[name="verification"]').addMsg({
						position	: 'right',
						msg			: 'Verification code should be in 6 numbers length',
					});
					
					return false;
				}
			}
			
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				success	: function(e){
					
					if(e.success){
						if(e.data.saved){
							alert(e.data.msg);
							$('#otp-wrapper input').val('');
							$('#otp-wrapper').hide();
							$('#bank-fields').removeLoadingLayer();
						}else{
							$('#bank-fields').addLoadingLayer({
								spinner: false,
							});
							$('#otp-wrapper').show();
						}
					}else{
						alert(e.data.msg);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
			
		});
		
		$('#resend-bank-otp').click(function(ev){
			ev.preventDefault();
			
			var theForm = $('form#change-bank-info');
			theForm.addLoadingLayer();
			
			$.ajax({
				url		: ajaxurl,
				data	: {'action': 'resend_bank_change_otp'},
				type	: 'POST',
				success	: function(e){
					
					if(e.success){
						alert(e.data.msg);
					}else{
						alert(e.data.msg);
					}
					
					theForm.removeLoadingLayer();
					
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	if($('form#pp').length){
		$('form#pp img').click(function(){
			$('input[name="pp-input"]').click();
		});
		
		$('input[name="pp-input"]').change(function(){
			var theForm = $('form#pp');
			var formData = new FormData( theForm[0] );
			
			formData.append('action', 'rvb_upload_photo_profile');
			theForm.addLoadingLayer()
			
			//console.log(formData);
			
			$.ajax({
				url		: ajaxurl,
				data	: formData,
				type	: 'POST',
				processData: false,
				contentType: false,
				cache: false,
				success	: function(e){
					console.log(e);
					if(e.success){
						//theForm.find('input[name="uploaded_id_card"]').val(e.data.img_id);
						$('img.user-pp').attr('src', e.data.img_url);
					}else{
						alert(e.data.msg);
					}
					
					theForm.removeLoadingLayer();
				},
				error	: function(e, ee){
					console.log(ee);
				}
				
			});
		});
	}
	
	function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
	
});