jQuery(function($){
	function updateCoords(coords){
		$('#crop-h').val(coords.h);
		$('#crop-w').val(coords.w);
		$('#crop-x').val(coords.x);
		$('#crop-x2').val(coords.x2);
		$('#crop-y').val(coords.y);
		$('#crop-y2').val(coords.y2);
        // console.log(coords);
    }
    function showPreview(coords) {
		if ( parseInt(coords.w) > 0 ) {
			var fw = 150;
			var fh = 150;
			var rx = fw / coords.w;
			var ry = fh / coords.h;
			// console.log(ry * image.naturalHeight);
			// console.log(ry * coords.y);
			$('#avatar-crop').find('img').each(function(){
				var w = $('#popup-crop .jcrop-holder').width();
				var h = $('#popup-crop .jcrop-holder').height();
				$(this).css({
					width: Math.round(rx * w) + 'px',
					height: Math.round(ry * h) + 'px',
					marginLeft: '-' + Math.round(rx * coords.x) + 'px',
					marginTop: '-' + Math.round(ry * coords.y) + 'px'
				});
			});
		}
	}

	$('#dwqa-upload-user-avatar').on('change', function(){
		var file = this.files[0];
		if(file == 'undefined'){
			return false;
		}

		var formData = new FormData();
		formData.append('action', 'dwqa_upload_avatar');
		formData.append('nonce', dwqa_profile.ajax_nonce);
		formData.append('file', file);

		$.ajax({
			url: dwqa_profile.ajax_url,
			type: "POST",
			data: formData,
			success: function (data) {
				console.log(data);
				if(data.success){
					$('#attachment_file').val(data.data.upload.file);
					var html = '<img src="'+data.data.upload.url+'">';
					$('#popup-crop').html(html);
					$('#avatar-crop').html(html);

					$('#popup-crop').find('img').Jcrop({
				        /*setSelect: [400, 200, 50, 50], */
				        aspectRatio: 1,
				        onSelect: showPreview, 
				        onChange: updateCoords
				    }/*, function(){
				        jcrop_api = this;
				    }*/);
				    $('.dwqa-poup').show();
				    
				}else{
					alert(data.data);
				}
				// alert(msg)
			},
			cache: false,
			contentType: false,
			processData: false
		});

		// e.preventDefault();
	});

	$('#button-crop').on('click', function(){
		$('.dwqa-poup').hide();
		var attachment_file = $('#attachment_file').val();
		var h = $('#crop-h').val();
		var w = $('#crop-w').val();
		var x = $('#crop-x').val();
		var x2 = $('#crop-x2').val();
		var y = $('#crop-y').val();
		var y2 = $('#crop-y2').val();

		var ui_w = $('#popup-crop .jcrop-holder').width();
		var ui_h = $('#popup-crop .jcrop-holder').height();

		$.ajax({
			url: dwqa_profile.ajax_url,
			type: "POST",
			dataType: 'json',
			data: {
				action: 'dwqa_crop_avatar',
				nonce: dwqa_profile.ajax_nonce,
				attachment_file: attachment_file,
				h: h,
				w: w,
				x: x,
				x2: x2,
				y: y,
				y2: y2,
				ui_w: ui_w,
				ui_h: ui_h
			},
			success: function (data) {
				console.log(data);
				if(data.success){
					var html = '<img src="'+data.data.cropped.url+'">';
					$('#dwqa-user-avatar').html(html);
				}else{
					alert(data.data);
				}
				// alert(msg)
			}
		});

	});


	$('#dwqa-upload-user-cover-image').on('change', function(){
		var file = this.files[0];
		if(file == 'undefined'){
			return false;
		}

		var formData = new FormData();
		formData.append('action', 'dwqa_upload_cover_image');
		formData.append('nonce', dwqa_profile.ajax_nonce);
		formData.append('file', file);

		$.ajax({
			url: dwqa_profile.ajax_url,
			type: "POST",
			data: formData,
			success: function (data) {
				console.log(data);
				if(data.success){
					// $('#attachment_file').val(data.data.upload.file);
					var html = '<img src="'+data.data.upload.url+'">';
					$('#dwqa-user-cover-image').html(html);
				}else{
					alert(data.data);
				}
				// alert(msg)
			},
			cache: false,
			contentType: false,
			processData: false
		});

		// e.preventDefault();
	});
});