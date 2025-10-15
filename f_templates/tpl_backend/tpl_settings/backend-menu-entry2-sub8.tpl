	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet15" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.l" entry_id="ct-entry-details15" input_name="backend_menu_entry1_sub13_guest_browse_l" input_value=$guest_browse_live bb=1}
	    {generate_html bullet_id="ct-bullet16" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.l" entry_id="ct-entry-details16" input_name="backend_menu_entry1_sub13_guest_view_l" input_value=$guest_view_live bb=1}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.v" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub13_guest_browse_v" input_value=$guest_browse_video bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.v" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub13_guest_view_v" input_value=$guest_view_video bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.i" entry_id="ct-entry-details2" input_name="backend_menu_entry1_sub13_guest_browse_i" input_value=$guest_browse_image bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.i" entry_id="ct-entry-details6" input_name="backend_menu_entry1_sub13_guest_view_i" input_value=$guest_view_image bb=1}
            {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.a" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub13_guest_browse_a" input_value=$guest_browse_audio bb=1}
            {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.a" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub13_guest_view_a" input_value=$guest_view_audio bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
            {generate_html bullet_id="ct-bullet13" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.b" entry_id="ct-entry-details13" input_name="backend_menu_entry1_sub13_guest_browse_b" input_value=$guest_browse_blog bb=1}
            {generate_html bullet_id="ct-bullet14" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.b" entry_id="ct-entry-details14" input_name="backend_menu_entry1_sub13_guest_view_b" input_value=$guest_view_blog bb=1}
            {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.d" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub13_guest_browse_d" input_value=$guest_browse_doc bb=1}
            {generate_html bullet_id="ct-bullet8" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.d" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub13_guest_view_d" input_value=$guest_view_doc bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.ch" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub13_guest_browse_ch" input_value=$guest_browse_channel bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.c" entry_id="ct-entry-details10" input_name="backend_menu_entry1_sub13_guest_view_c" input_value=$guest_view_channel bb=1}
	    {generate_html bullet_id="ct-bullet12" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.view.s" entry_id="ct-entry-details12" input_name="backend_menu_entry1_sub13_guest_view_s" input_value=$guest_search_page bb=0}
	    {generate_html bullet_id="ct-bullet11" input_type="switch" entry_title="backend.menu.entry1.sub13.guest.browse.pl" entry_id="ct-entry-details11" input_name="backend_menu_entry1_sub13_guest_browse_pl" input_value=$guest_browse_playlist bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="s-wrap">
		<div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	    <input type="hidden" name="ct_entry" id="ct_entry" value="" />
	</form>
	</div>
        {include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>

