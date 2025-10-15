	jQuery(document).ready(function() {
		$("ul.responsive-accordion li").mouseenter(function(){
			t = $(this);
			if (t.parent().parent().parent().attr("id") == "lb-wrapper") {
				return;
			}
			$(this).find(".responsive-accordion-head").addClass("h-msover");
			$(this).find(".responsive-accordion-panel").addClass("p-msover");
		}).mouseleave(function(){
			$(this).find(".responsive-accordion-head").removeClass("h-msover");
			$(this).find(".responsive-accordion-panel").removeClass("p-msover");
		});
		
		
		var _id = "";
                if ($(".fancybox-wrap").width() > 0) {
                        _id = ".fancybox-inner ";
                }
                if ($("#right-side").width() > 0) {
                        _id = "#right-side ";
                }
		jQuery(_id + '.responsive-accordion').each(function(e) {
                	if ($(".fancybox-wrap").width() > 0 || $("#right-side").width() > 0) {
                        	_obj = "";
                	} else {
                		_obj = this;
                	}
				jQuery(_id + '.responsive-accordion-head', _obj).click(function(event) {
					event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
						var	thisAccordion = $(this).parent().parent(),
							thisHead = $(this),
							thisPlus = thisHead.find('.responsive-accordion-plus'),
							thisMinus = thisHead.find('.responsive-accordion-minus'),
							thisPanel = thisHead.siblings('.responsive-accordion-panel');

						thisAccordion.find('.responsive-accordion-plus').show();
						thisAccordion.find('.responsive-accordion-minus').hide();

						thisAccordion.find('.responsive-accordion-head').not(this).removeClass('active');
						thisAccordion.find('.responsive-accordion-panel').not(this).removeClass('active').stop().slideUp('fast');

						if (typeof(thisAccordion.find('.responsive-accordion-panel').html()) == "undefined") {
							return;
						}

						if (thisHead.hasClass('active')) {
							if ($("#ct-wrapper").hasClass("col_panels") && !$("body").hasClass("is-mobile")) {
								var pid = thisHead.next().attr("rel-id");
								$("#"+pid).appendTo(thisHead.next()).removeClass("active");
							}

							thisHead.removeClass('active');
							thisPlus.show();
							thisMinus.hide();
							thisPanel.removeClass('active').stop().slideUp('fast', function(){ldelim}thisresizeDelimiter();{rdelim});
						} else {
							thisHead.addClass('active');
							thisPlus.hide();
							thisMinus.show();

							if ($("#ct-wrapper").hasClass("col_panels") && !$("body").hasClass("is-mobile")) {
								$(".col_panel > div").each(function(e){
									t = $(this).attr("id");
									if (!$(this).hasClass("active")) return;
									$(this).appendTo($(".responsive-accordion-panel[rel-id="+t+"]")).removeClass("active");
									$(".responsive-accordion-panel[rel-id="+t+"]").removeClass("active").removeClass("no-display").hide();
									$(".responsive-accordion-panel[rel-id="+t+"]").prev().removeClass("active");
									$(".responsive-accordion-panel[rel-id="+t+"]").prev().find("i.responsive-accordion-plus").show();
									$(".responsive-accordion-panel[rel-id="+t+"]").prev().find("i.responsive-accordion-minus").hide();
								});

								var a = thisPanel.html();
								thisPanel.html("").attr("rel-id", $(a).attr("id")).addClass("no-display");
								$(a).appendTo('.col_panel').addClass("active");
							}

							thisPanel.addClass('active').stop().slideDown('fast', function(){ldelim}
								thisresizeDelimiter();
								if (thisHead.hasClass("new-message")) {
									thisHead.parent().click();
								}
								thisPanel.css("height", "auto");
							{rdelim});
						}
				});
		});
	});