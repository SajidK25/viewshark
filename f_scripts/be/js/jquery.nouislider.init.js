$(document).ready(function(){
	$(document).on("dblclick", ".noUi-handle.noUi-handle-lower", function (){
		var slider = $(this).parent().parent().parent().parent().find(".noUi-target.noUi-ltr.noUi-horizontal.noUi-background");
		
		$(this).parent().parent().parent().parent().find("input").val(function() {
			var _val = this.defaultValue;
			slider.val(_val);

			return _val;
		});
	});
});