tinymce.init({
        selector: '.h-editable',
        inline: true,
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent',
        menubar: false
});

tinymce.init({
        selector: '.d-editable',
        menu: {
		file: {title: 'File', items: 'newdocument'},
		edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
		format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
		newmenu: {title: 'Uploads', items: 'insertlive insertvideo insertimage insertaudio insertdoc'},
		tools: {title: 'View', items: 'visualblocks code'}
        },
        menubar: 'file edit format tools newmenu',
        inline: true,
        plugins: [
		'advlist autolink lists link image charmap anchor',
		'searchreplace visualblocks code fullscreen',
		'insertdatetime media table contextmenu paste',
		'textcolor colorpicker'
        ],
        toolbar: 'undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist | link',

        setup: function (editor) {
        if (typeof(lm) != "undefined") {
	editor.addMenuItem('insertlive', {
	        text: 'Insert Broadcast',
	        icon: 'media',
	        context: 'newmenu',

	        onclick: function () {
		url = _u + "&do=insert&t=live";
		$.fancybox({type: "ajax", margin: 50, minWidth: "50%", href: url, height: "auto", autoHeight: "true", autoResize: "true", autoCenter: "true"});
	        }
	});
	}
	if (typeof(vm) != "undefined") {
	editor.addMenuItem('insertvideo', {
	        text: 'Insert Video',
	        icon: 'media',
	        context: 'newmenu',

	        onclick: function () {
		url = _u + "&do=insert&t=video";
		$.fancybox({type: "ajax", margin: 50, minWidth: "50%", href: url, height: "auto", autoHeight: "true", autoResize: "true", autoCenter: "true"});
	        }
	});
	}
	if (typeof(im) != "undefined") {
	editor.addMenuItem('insertimage', {
	        text: 'Insert Picture',
	        icon: 'image',
	        content: 'newmenu',

	        onclick: function () {
		url = _u + "&do=insert&t=image";
		$.fancybox({type: "ajax", margin: 50, minWidth: "50%", href: url, height: "auto", autoHeight: "true", autoResize: "true", autoCenter: "true"});
	        }
	});
	}
	if (typeof(am) != "undefined") {
	editor.addMenuItem('insertaudio', {
	        text: 'Insert Audio',
	        icon: 'media',
	        content: 'newmenu',

	        onclick: function () {
		url = _u + "&do=insert&t=audio";
		$.fancybox({type: "ajax", margin: 50, minWidth: "50%", href: url, height: "auto", autoHeight: "true", autoResize: "true", autoCenter: "true"});
	        }
	});
	}
	if (typeof(dm) != "undefined") {
	editor.addMenuItem('insertdoc', {
	        text: 'Insert Document',
	        icon: 'books',
	        content: 'newmenu',

	        onclick: function () {
		url = _u + "&do=insert&t=doc";
		$.fancybox({type: "ajax", margin: 50, minWidth: "50%", href: url, height: "auto", autoHeight: "true", autoResize: "true", autoCenter: "true"});
	        }
	});
	}
        }
});