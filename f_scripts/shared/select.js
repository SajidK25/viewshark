var SelectList = {
    fn: {
        prepare: function (name) {
        	var extra_span1 = '';
        	var extra_span2 = '';
        	if (name == 'ipp_jump' || name == 'ipp_select') {
        		var text = (name == 'ipp_select' ? 'Items per page' : (name == 'ipp_jump' ? 'Jump to page' : ''));
        		extra_span1 = '<span rel="tooltip" title="' + text + '">';
        		extra_span2 = '</span>';
        	}
            var select = $('<div class="select-box" id="select-box-' + name + '"/>');
            var html = '<div class="trigger" id="trigger-' + name + '">' + extra_span1 + $("option:selected", "select[name='" + name + "']").html() + extra_span2 + '</div>';
            html += '<ul class="choices" id="choices-' + name + '">';

            $('option', "select[name='" + name + "']").each(function () {
                var $option = $(this);
                var value = $option.val();
                var text = $option.html();
                
                if ($option.is(":selected")) {
                    html += '<li data-value="' + value + '" style="display: none;">' + text + '</li>';
                } else {
                    html += '<li data-value="' + value + '">' + text + '</li>';
                }
            });

            html += '</ul>';
            select.html(html).insertBefore("select[name='" + name + "']");
        },
        
        showHide: function (name) {
            $('#trigger-' + name, '#select-box-' + name).unbind().bind('click', function () {
                var $trigger = $(this);
                var list = $trigger.next();

                if (list.is(':hidden')) {
                    list.slideDown(300, function() {
                    	$('#ct-wrapper').bind('click', bodyHideSelect);
                    	return;
                    });
                } else {
                    list.slideUp(300, function() {
                    	$('#ct-wrapper').unbind('click', bodyHideSelect);
                    	return;
                    });
                }
            });
        },

        select: function (name) {
            var $trigger = $('#trigger-' + name);
            var $select = $("select[name='" + name + "']");
            var $view = $('#view');

            $('li', '#choices-' + name).on('click', function () {
                var $li = $(this);
                var value = $li.html();

                $("#choices-" + name + " li").show();
                $("#choices-" + name + " li").each(function () {
                    var text = $(this).text();
                    if (text == value) {
                        $(this).hide();
                    }
                });

                $trigger.text($('<div/>').html(value).text());
                $li.parent().slideUp(300, function () {
                    $select.changeVal($li.data('value'));
                    $view.trigger('click');
                    
                });
            });
        }
    },
    init: function (name) {
        for (var method in this.fn) {
            this.fn[method](name);
        }

    }
};

$.fn.changeVal = function (v) {
    return $(this).val(v).trigger("change");
}

function bodyHideSelect() {
	$("#paging-top .choices").each(function(){
			if ($(this).attr("id") === "choices-ipp_select" && $(this).css("display") === "block") {
				$(this).slideUp(300, function() { $('#ct-wrapper').unbind('click', bodyHideSelect); return; });

				return;
			}
	});
}
function bodyHideSelect2() {
		$("#paging-top .choices").each(function(){
			if ($(this).attr("id") === "choices-ipp_select" && $(this).css("display") === "block") {
				$(this).slideUp(300, function() { $('#ct-wrapper').unbind('click', bodyHideSelect); return; });

				return;
			}
		});
		$(".entry-form-class .choices").each(function(){
			var choice = $(this);

			alert(choice.attr("id"));
			
			if (choice.attr("id") !== $(this).find(".choices").attr("id")) {
			
			if ($(this).find(".choices").css("display") === "block") {
				choice.slideUp(300, function() { $('#ct-wrapper').unbind('click', bodyHideSelect); return; });

				return;
			}
			}
		});
}