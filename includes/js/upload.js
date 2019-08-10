jQuery(function($){

	var custom_uploader;
	if (custom_uploader) {
		custom_uploader.open();
		return;
	}

	//上传单个图片
	$('body').on("click", '.squirrelzoo_upload', function(e) {	
		e.preventDefault();	// 阻止事件默认行为。

		var input = $('#squirrel_avatar_input');
		var img = $('.squirrelzoo_upload')[0];
		
		custom_uploader = wp.media({
			title: 		'选择图片',
			library: 	{ type: 'image' },
			button: 	{ text: '选择图片' },
			multiple:	false 
		}).on('select', function() {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			input.val(attachment.url);
			img.src=attachment.url;
			$('.media-modal-close').trigger('click');

		}).open();

		return false;
	});

	return false;
});

