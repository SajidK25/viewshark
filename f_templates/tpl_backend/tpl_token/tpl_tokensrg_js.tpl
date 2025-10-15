$(document).ready(function() {ldelim}
	{if $smarty.get.a ne ""}$("#chart-views-container").mask(""); $("#chart-line-container").mask(""); $("#device-pie-container").mask(""); $("#timeline-container").mask("");
	{elseif $smarty.get.g ne ""}$("#geo-container").mask(""); $("#country-container").mask(""); $("#pagetitle-container").mask(""); $("#country-search-load").mask(""); $("#continent-search-load").mask(""); $("#country-search-dots").mask(""); $("#continent-search-dots").mask("");
	{elseif $smarty.get.rp ne ""}
	{/if}

	$("#view-limits").submit(function(e){ldelim}
		e.preventDefault();

		l1 = parseInt($("#view-limit-min-off").val());
		l2 = parseInt($("#view-limit-max-off").val());

		$("#view-limit-min").val(l1);
		$("#view-limit-max").val(l2);

		$("#custom-date-form").submit();
	{rdelim});
	$(".view-mode-type").click(function(){ldelim}
                t = $(this);
                type = t.attr("rel-t");
                u = '{$backend_url}/{href_entry key="be_tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rp ne ""}rp={$smarty.get.rp|sanitize}{/if}&t='+type{if $smarty.get.f ne ""}+'&f={$smarty.get.f|sanitize}'{/if}{if $smarty.get.g ne ""}+'&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}'{/if};
                u+= '{if $smarty.get.uk ne "" and $smarty.get.fk eq ""}&uk={$smarty.get.uk|sanitize}{/if}';
                u+= '{if $smarty.get.tab ne ""}&tab={$smarty.get.tab|sanitize}{/if}';

                $(".view-mode-type").removeClass("active"); t.addClass("active");
                
                if ($(".content-filters li a.active").attr("rel-t") == "date" || $(".content-filters li a.active").attr("rel-t") == "range") {ldelim}
                	$('#custom-date-form').attr('action', u).submit();
                {rdelim} else {ldelim}
                	window.location = u;
                {rdelim}
        {rdelim});

        $("a.filter-tag").click(function(){ldelim}
                t = $(this);
                type = t.attr("rel-t");
                u = String(window.location);
                rep = '';
                switch (type) {ldelim}
                	case "f":
                		rep = '{if $smarty.get.f ne ""}&t=sub&f={$smarty.get.f|sanitize}{/if}{if $smarty.get.w ne ""}&w={$smarty.get.w|sanitize}{/if}{if $smarty.get.m ne ""}&m={$smarty.get.m|sanitize}{/if}{if $smarty.get.y ne ""}&y={$smarty.get.y|sanitize}{/if}';
                	break;
                	case "r":
                		rep = '{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}';
                	break;
                	case "c":
                		rep = '{if $smarty.get.c ne "" and $smarty.get.c ne "xx"}&c={$smarty.get.c|sanitize}{/if}';
                	break;
                	case "fk":
                		rep = '{if $smarty.get.fk ne ""}&fk={$smarty.get.fk|sanitize}{/if}';
                	break;
                	case "uk":
                		rep = '{if $smarty.get.uk ne ""}&uk={$smarty.get.uk|sanitize}{/if}';
                	break;
                {rdelim}
                if (rep != '') {ldelim}
                	u = u.replace(rep, '');

                	if ($(".content-filters li a.active").attr("rel-t") == "date" || $(".content-filters li a.active").attr("rel-t") == "range") {ldelim}
                		$('#custom-date-form').attr('action', u).submit();
                	{rdelim} else {ldelim}
                		window.location = u;
                	{rdelim}
                {rdelim}
        {rdelim});

        $(".content-filters li a").click(function(){ldelim}
                t = $(this);
                type = t.attr("rel-t");
                ft = $(".view-mode-type.active").attr("rel-t");

                $("#filter-section-"+(ft == 'document' ? 'doc' : ft)+" .content-filters li a").removeClass("active"); t.addClass("active");

                switch (type) {ldelim}
                        case "week":
                        case "month":
                        case "year":
                        	window.location = '{$backend_url}/{href_entry key="be_tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rg ne ""}rg=1{/if}&t='+$('.view-mode-type.active').attr('rel-t')+'&f='+type{if $smarty.get.g ne ""}+'&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}'{/if}{if $smarty.get.fk ne ""}+'&fk={$smarty.get.fk|sanitize}'{/if}{if $smarty.get.uk ne ""}+'&uk={$smarty.get.uk|sanitize}'{/if}{if $smarty.get.tab ne ""}+'&tab={$smarty.get.tab|sanitize}'{/if};
                        break;
                {rdelim}
        {rdelim});

{rdelim});