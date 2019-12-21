jQuery(document).ready(function($){
	Dropzone.autoDiscover = false;

	blkddu.obj = jQuery("#media-uploader").dropzone({
		url: blkddu.upload,
		acceptedFiles	: 'image/*',
		dictRemoveFile	: blkddu.remove_text,
		maxFilesize		: 2,
		dictFileTooBig	: blkddu.file_too_big,
		success: function (file, response) {
			console.log('Response: '+response);
			file.previewElement.classList.add("dz-success");
			file['attachment_id'] = response; // push the id for future reference
			//var ids = jQuery('#media-ids').val() + ',' + response;
			var img = '<input type="hidden" id="img-'+response+'" name="images[]" class="images-input" value="'+response+'">';
			jQuery('#images').append(img);
			
			//khusus untuk upload image di halaman submit property
			//console.log(rvbparams.submitFormChanged);
			if( typeof rvbparams !== 'undefined'){
				rvbparams.submitFormChanged = true;
			}
			//console.log(rvbparams.submitFormChanged);
			
			//jQuery('#media-ids').val(ids);
			//console.log('ids: '+ ids );
		},
		error: function (file, response) {
			console.log(response);
			file.previewElement.classList.add("dz-error");
			var errorContainer = file.previewElement.getElementsByClassName("dz-error-message")[0];
			errorContainer.getElementsByTagName('span')[0].innerHTML = response;
		},
		// update the following section is for removing image from library
		addRemoveLinks: true,
		removedfile: function(file) {
			var attachment_id = file.attachment_id;        
			jQuery.ajax({
				type: 'POST',
				url: blkddu.delete,
				data: {
					media_id : attachment_id
				},
				success	: function(){
					$('#img-'+attachment_id).remove();
				},
				error	: function(e, ee){
					console.log(ee);
				}
			});
			var _ref;
			return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
		}
	});
	
});