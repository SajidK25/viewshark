	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry2.sub4.IPaccess" entry_id="ct-entry-details7" input_name="backend_menu_entry2_sub4_IPaccess" input_value=$website_ip_based_access bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="textarea" entry_title="backend.menu.entry2.sub4.IPlist" entry_id="ct-entry-details8" input_name="backend_menu_entry2_sub4_IPlist" input_value="{insert name=getListContent from='ip-access' assign=iplist}{$iplist}" bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry2.sub4.offmode" entry_id="ct-entry-details1" input_name="backend_menu_entry2_sub4_offmode" input_value=$website_offline_mode bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="offline_slides" entry_title="backend.menu.entry2.sub4.offmsg" entry_id="ct-entry-details2" input_name="backend_menu_entry2_sub4_offmsg" input_value=$website_offline_message bb=0}
            {generate_html bullet_id="ct-bullet3" input_type="text" entry_title="backend.menu.entry2.sub4.offuntil" entry_id="ct-entry-details3" input_name="backend_menu_entry2_sub4_offuntil" input_value=$offline_mode_until bb=1}
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
        <script type="text/javascript">{literal}$(document).ready(function(){$("a.sml-add").click(function(){lid=document.getElementById("url-entry-details-list").childElementCount;lid+=1;nht=ht.replace(/#NR#/g,lid).replace(/#V1#/g, '').replace(/#V2#/g, '').replace(/#V3#/g, '');$("#url-entry-details-list").append(nht);});});{/literal}</script>
