	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.members.entry1.sub1.paid" entry_id="ct-entry-details1" input_name="backend_menu_members_entry1_sub1_paid" input_value=$paid_memberships bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.members.entry1.sub3" entry_id="ct-entry-details3" input_name="backend_menu_members_entry1_sub3" input_value=$discount_codes bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.members.entry1.sub1.subs" entry_id="ct-entry-details2" input_name="backend_menu_members_entry1_sub1_subs" input_value=$user_subscriptions bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="text" entry_title="backend.menu.members.entry1.sub1.rev" entry_id="ct-entry-details4" input_name="backend_menu_members_entry1_sub1_rev" input_value=$sub_shared_revenue bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="text" entry_title="backend.menu.members.entry1.sub1.threshold" entry_id="ct-entry-details10" input_name="backend_menu_members_entry1_sub1_threshold" input_value=$sub_threshold bb=1}
	    {generate_html bullet_id="ct-bullet12" input_type="text" entry_title="backend.menu.members.entry1.tok1.threshold" entry_id="ct-entry-details12" input_name="backend_menu_members_entry1_tok1_threshold" input_value=$token_threshold bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="partner_requirements" entry_title="backend.menu.pt.requirements" entry_id="ct-entry-details11" input_name="backend_menu_pt_requirements" input_value="" bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet5" input_type="text" entry_title="backend.menu.members.entry1.sub1.pp.mail" entry_id="ct-entry-details5" input_name="backend_menu_members_entry1_sub1_pp_mail" input_value=$paypal_email bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.members.entry1.sub1.pp.test" entry_id="ct-entry-details6" input_name="backend_menu_members_entry1_sub1_pp_test" input_value=$paypal_test bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="text" entry_title="backend.menu.members.entry1.sub1.pp.sb.mail" entry_id="ct-entry-details7" input_name="backend_menu_members_entry1_sub1_pp_sb_mail" input_value=$paypal_test_email bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="switch" entry_title="backend.menu.members.entry1.sub1.pplog" entry_id="ct-entry-details8" input_name="backend_menu_members_entry1_sub1_pplog" input_value=$paypal_logging bb=0}
	    {generate_html bullet_id="ct-bullet9" input_type="pp_api" entry_title="backend.menu.members.entry1.sub1.ppapi" entry_id="ct-entry-details9" input_name="backend_menu_members_entry1_sub1_ppapi" input_value=1 bb=0}
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
        <script type="text/javascript">
        $(function(){ldelim}SelectList.init("backend_menu_pt_requirements_type");{rdelim});
        $(document).ready(function () {ldelim}
        	$('#slider-backend_menu_members_entry1_sub1_rev').html('<span class="">You will be sharing <b><span id="sub-share-nr">{$sub_shared_revenue}</span>%</b> from every paid channel subscription.</span>');
        	$('#ct-entry-details4-input').on('keyup', function() {ldelim}var t=$(this).val();if(t<0)$(this).val(0);if(t>100)$(this).val(100);$('#sub-share-nr').text($(this).val());{rdelim});
        {rdelim});
        </script>

