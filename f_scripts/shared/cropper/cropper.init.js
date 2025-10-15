if (!HTMLCanvasElement.prototype.toBlob) {
        Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
	value: function (callback, type, quality) {
	        type = 'image/jpeg';
	        quality = 1;

	        var binStr = atob(this.toDataURL(type, quality).split(',')[1]),
		len = binStr.length,
		arr = new Uint8Array(len);

	        for (var i = 0; i < len; i++) {
		arr[i] = binStr.charCodeAt(i);
	        }

	        callback(new Blob([arr], {type: type || 'image/jpeg'}));
	}
        });
}

function pad(num, size) {
        return ('000000000' + num).substr(-size);
}

String.prototype.chunk = function (n) {
        var ret = [];
        for (var i = 0, len = this.length; i < len; i += n) {
	ret.push(this.substr(i, n))
        }

        return ret
};

$(document).ready(function() {
        var $image = $(".cropper img");
        
        $image.cropper({
	aspectRatio: 512 / 85,
	resizable: true,
	rotatable: true,
	responsive: true,
	minCropBoxWidth: Math.round(2048),
	minCropBoxHeight: Math.round(340),
	done: function (data) {
	}
        });
        
        
        var $inputImage = $('#fedit-image');
        var URL = window.URL || window.webkitURL;
        var blobURL;

        if (URL) {
	$inputImage.change(function () {
	        var files = this.files;
	        var file;

	        if (!$image.data('cropper')) {
		return;
	        }

	        if (files && files.length) {
		file = files[0];

		if (/^image\/\w+$/.test(file.type)) {
		        blobURL = URL.createObjectURL(file);
		        $image.one('built.cropper', function () {
			URL.revokeObjectURL(blobURL);
		        }).cropper('reset').cropper('replace', blobURL);
		} else {
		        $body.tooltip('Please choose an image file.', 'warning');
		}
	        }
	});
        } else {
	$inputImage.prop('disabled', true).parent().addClass('disabled');
        }


        $("a[class='c-zoomin']").click(function () {
        	$image.cropper('zoom', '.1');
        });
        $("a[class='c-zoomout']").click(function () {
        	$image.cropper('zoom', '-.1');
        });
        $("a[class='c-moveup']").click(function () {
        	$image.cropper('move', '0', '-10');
        });
        $("a[class='c-movedown']").click(function () {
        	$image.cropper('move', '0', '10');
        });
        $("a[class='c-moveleft']").click(function () {
        	$image.cropper('move', '-10', '0');
        });
        $("a[class='c-moveright']").click(function () {
        	$image.cropper('move', '10', '0');
        });


        $("button[id='done']").click(function () {
	if (typeof ($(".cropper-container").html()) != "undefined") {
	        $image.cropper('getCroppedCanvas').toBlob(function (blob) {
		var reader = new window.FileReader();
		reader.readAsDataURL(blob);
		reader.onloadend = function () {
		        base64data = reader.result.replace('png', 'jpeg');
		        $("input[name='crop_data']").val(base64data);
		        
		        $("#fedit-image-form").mask(" ");
		        jQuery.post(current_url + menu_section + "?s=channel-menu-entry3&do=save", $("#fedit-image-form").serialize(), function (data) {
			
			window.location = current_url + menu_section + "?r=confirm";
			
			return false;
		        });

		}
	        });
	} else {
	}
        });
        
        
        
        $("button[id='update']").click(function () {
	if (typeof ($(".cropper-container").html()) != "undefined") {
	        $image.cropper('getCroppedCanvas').toBlob(function (blob) {
		var reader = new window.FileReader();
		reader.readAsDataURL(blob);
		reader.onloadend = function () {
		        t = $("#crop-string").val();
		        base64data = reader.result.replace('png', 'jpeg');
		        $("input[name='crop_data']").val(base64data);
		        
		        $(".fancybox-inner").mask(" ");
		        jQuery.post(current_url + menu_section + "?s=channel-menu-entry3&do=save_crop&t=" + t, $("#fedit-image-form").serialize(), function (data) {
			if (t > 24) {
			        $(".fancybox-inner #upload-response").html(data);
			        $(".fancybox-inner").unmask("");
			} else {
			        window.location = current_url + menu_section + "?r=confirm";
			
			        return false;  
			}
		        });
		}
	        });
	} else {
	}
        });
        
        
        $("button[id='default']").click(function () {
	t = $("#crop-string").val();
	$(".fancybox-inner").mask(" ");
	jQuery.post(current_url + menu_section + "?s=channel-menu-entry3&do=save_default&t=" + t, $("#fedit-image-form").serialize(), function (data) {
	        $("#channel-own-photos img").removeClass("own");
	        $("#channel-own-photos #li-" + t + " img").addClass("own");
	        $(".fancybox-inner #upload-response").html(data);
	        $(".fancybox-inner").unmask("");
	});
        });
});