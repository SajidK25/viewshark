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
                u = '{$main_url}/{href_entry key="tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rp ne ""}rp={$smarty.get.rp|sanitize}{/if}&t='+type{if $smarty.get.f ne ""}+'&f={$smarty.get.f|sanitize}'{/if}{if $smarty.get.g ne ""}+'&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}'{/if};
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
                		rep = '{if $smarty.get.f ne ""}&f={$smarty.get.f|sanitize}{/if}';
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
                        case "today":
                        case "yesterday":
                        case "thisweek":
                        case "lastweek":
                        case "thismonth":
                        case "lastmonth":
                        case "thisyear":
                        case "lastyear":
                        case "last3months":
                        case "last6months":
                        	window.location = '{$main_url}/{href_entry key="tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rp ne ""}rp={$smarty.get.rp|sanitize}{/if}&t='+$('.view-mode-type.active').attr('rel-t')+'&f='+type{if $smarty.get.g ne ""}+'&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}'{/if}{if $smarty.get.fk ne ""}+'&fk={$smarty.get.fk|sanitize}'{/if}{if $smarty.get.uk ne ""}+'&uk={$smarty.get.uk|sanitize}'{/if}{if $smarty.get.tab ne ""}+'&tab={$smarty.get.tab|sanitize}'{/if};
                        break;
                        case "date":
                        	$('.rpick{$file_type}').hide();
                        	dp.open();
                        break;
                        case "range":
                        	dp.close();
                        	$('.rpick{$file_type}').toggle();
                        break;
                {rdelim}
        {rdelim});

        function setrdate(dp){ldelim}
        	var d = new Date(dp.state.start);
        	var ds = d.getFullYear() + '-' + ("0"+(d.getMonth()+1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
        	$('#custom-date-start').val(ds);

        	d = new Date(dp.state.end);
        	var de = d.getFullYear() + '-' + ("0"+(d.getMonth()+1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
        	$('#custom-date-end').val(de);

        	var u = '{$main_url}/{href_entry key="tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rp ne ""}rp={$smarty.get.rp|sanitize}{/if}&t='+$('.view-mode-type.active').attr('rel-t')+'&f=range';
		u += '{if $smarty.get.g ne ""}&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}{/if}';
		u += '{if $smarty.get.fk ne ""}&fk={$smarty.get.fk|sanitize}{/if}';
		u += '{if $smarty.get.uk ne "" and $smarty.get.fk eq ""}&uk={$smarty.get.uk|sanitize}{/if}';
		u += '{if $smarty.get.tab ne ""}&tab={$smarty.get.tab|sanitize}{/if}';

		if (dp.state.end) {ldelim}
			$('#custom-date').val("");
        		$('#custom-date-form').attr('action', u).submit();
        	{rdelim}
        {rdelim}

        function setdate(datestring){ldelim}
        	var d = new Date(datestring);
        	var ds = d.getFullYear() + '-' + ("0"+(d.getMonth()+1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2);
        	var u = '{$main_url}/{href_entry key="tokens"}?{if $smarty.get.a ne ""}a={$smarty.get.a|sanitize}{elseif $smarty.get.g ne ""}g={$smarty.get.g|sanitize}{elseif $smarty.get.rp ne ""}rp={$smarty.get.rp|sanitize}{/if}&t='+$('.view-mode-type.active').attr('rel-t')+'&f=date';
        	u += '{if $smarty.get.g ne ""}&c={if $smarty.get.c ne ""}{$smarty.get.c|sanitize}{elseif $smarty.post.custom_country ne ""}{$smarty.post.custom_country|sanitize}{else}xx{/if}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}{/if}';
        	u += '{if $smarty.get.fk ne ""}&fk={$smarty.get.fk|sanitize}{/if}';
        	u += '{if $smarty.get.uk ne "" and $smarty.get.fk eq ""}&uk={$smarty.get.uk|sanitize}{/if}';
        	u += '{if $smarty.get.tab ne ""}&tab={$smarty.get.tab|sanitize}{/if}';

		$('#custom-date-start').val("");
		$('#custom-date-end').val("");
        	$('#custom-date').val(ds);
        	$('#custom-date-form').attr('action', u).submit();
        {rdelim}
        function setopen(datestring){ldelim}
        	$('.country-select .flag-dropdown').css({ldelim}'z-index':'0'{rdelim});
        {rdelim}
        function setclose(datestring){ldelim}
        	$('.country-select .flag-dropdown').css({ldelim}'z-index':'auto'{rdelim});
        {rdelim}

	dp = TinyDatePicker('.dpick{$file_type}', {ldelim}mode: 'dp-below'{if $smarty.post.custom_date ne ""},hilightedDate:'{$smarty.post.custom_date|sanitize}'{/if}{rdelim});
        dp.on({ldelim} open: () => setopen(), close: () => setclose(), select: (_, dp) => setdate(dp.state.selectedDate) {rdelim});

	rp = DateRangePicker.DateRangePicker('.rpick{$file_type}', {ldelim}mode: 'dp-below'{rdelim});
        rp.on('statechange', (_, rp) => setrdate(rp));

        const root = document.querySelector('.rpickoff{$file_type}');
        root.addEventListener('focusout', function() {ldelim}{rdelim});
        root.addEventListener('focus', function() {ldelim}{rdelim});
{rdelim});