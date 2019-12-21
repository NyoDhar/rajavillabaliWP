(function($){
	$.fn.wpmediauploader = function(options) {
		var settings = $.extend({
            title		: 'Select or Upload Media Of Your Chosen Persuasion',
			multiple	: true,
			fieldsName	: 'wpmediauploader-imgs',
			force_array_field_name : '',
        }, options );
		
		var frame,
		metaBox = this; // Your meta box id here
		//metabox.append('<input type="button" value="Upload Images" class="upload_images_button"><div id="thumbs"></div>');
		
		if(settings.multiple || settings.force_array_field_name){
			settings.fieldsName += '[]';
		}
		
		if(settings.multiple){
			var imgContainer = metaBox.find( '#thumbs');
		}
		
		var addImgLink = metaBox.find('.ez_upload_button');
		
		addImgLink.on( 'click', function( event ){
			event.preventDefault();
			// If the media frame already exists, reopen it.
			if ( frame ) {
				frame.open();
				return;
			}
			
			// Create a new media frame
			frame = wp.media({
				title: settings.title,
				button: {
					text: 'Use this media'
				},
				multiple: settings.multiple  // Set to true to allow multiple files to be selected
			});
			
			// When an image is selected in the media frame...
			frame.on( 'select', function() {
			  // Get media attachment details from the frame state
				var selections = frame.state().get('selection');
				var append='';
				if(settings.multiple){
					selections.map(function(attachment){
						attachment = attachment.toJSON();
						var thumbnail = attachment.sizes.thumbnail ? attachment.sizes.thumbnail : attachment.sizes.full;
						append += '<div class="thumb">' + 
								'<span class="remove">x</span>' +
								'<img src="'+thumbnail.url+'" title="Drag me to change my position">' +
								'<input type="hidden" name="'+settings.fieldsName+'" value="'+attachment.id+'" />' +
								'</div>';
					});
					imgContainer.append( append );
					jQuery('#thumbs .thumb .remove').click(function(){
						jQuery(this).parent().remove();
					});
				}else{
					selections.map(function(attachment){
						attachment = attachment.toJSON();
						console.log(attachment.sizes);
						var thumbnail = attachment.sizes.thumbnail ? attachment.sizes.thumbnail : attachment.sizes.full;
						append += '<div class="thumb">' +
								'<img src="'+thumbnail.url+'">' +
								'<input type="hidden" name="'+settings.fieldsName+'" value="'+attachment.id+'" />' +
								'</div>';
					});
					addImgLink.html( append );
				}
			});

			// Finally, open the modal on click
			frame.open();
		});
		
        return this;
    };
}(jQuery));

jQuery(document).ready(function(){
	jQuery( "#thumbs.sortable" ).sortable();
	if(jQuery('#thumbs .thumb .remove').length){
		jQuery('#thumbs .thumb .remove').click(function(){
			jQuery(this).parent().remove();
		});
	}
});