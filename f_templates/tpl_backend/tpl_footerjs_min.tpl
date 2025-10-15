	<script type="text/javascript">var section = '{$page_display}';var current_url  = '{$main_url}/{$backend_access_url}/';var menu_section = '{href_entry getKey="be_settings"}';</script>
        <script type="text/javascript" src="{$javascript_url_be}/init0.min.js"></script>
{if $smarty.session.ADMIN_NAME ne ""}
	<script type="text/javascript" src="{$javascript_url_be}/init1.min.js"></script><script type="text/javascript" src="{$javascript_url_be}/init2.min.js"></script><script type="text/javascript">new gnMenu(document.getElementById('gn-menu'));</script>
        <script type="text/javascript">{literal}var menuLeft = document.getElementById('cbp-spmenu-s1'),menuBottom = document.getElementById('cbp-spmenu-s4'),showLeftPush = document.getElementById('showLeftPush'),body = document.body;showBottom.onclick = function () {classie.toggle(this, 'active');classie.toggle(menuBottom, 'cbp-spmenu-open');disableOther('showBottom');};showLeftPush.onclick = function () {classie.toggle(this, 'active');classie.toggle(body, 'cbp-spmenu-push-toright');classie.toggle(menuLeft, 'cbp-spmenu-open');disableOther('showLeftPush');jQuery(window).resize();};function disableOther(button) {if (button !== 'showBottom') {classie.toggle(showBottom, 'disabled');}if (button !== 'showLeftPush') {classie.toggle(showLeftPush, 'disabled');}}{/literal}</script>
        {include file="tpl_backend/tpl_menupaneljs.tpl"}
{/if}
	{insert name="loadbejsplugins"}
	<script type="text/javascript">
	$(document).on("click", ".messages_holder", function() {ldelim}notif_url = '{$backend_url}/{href_entry key="be_dashboard"}?s=notif';$.fancybox({ldelim} type: "ajax", minWidth: "90%", minHeight: "75%", margin: 20, href: notif_url, wrapCSS: "notifications" {rdelim});{rdelim});$(document).ready(function() {ldelim}$("#new-notifications-nr").load('{$backend_url}/{href_entry key="be_dashboard"}?s=new');{rdelim});
	$(document).ready(function(){ldelim}{literal}jQuery(document).on({click: function(e){if($(e.target).hasClass("fancybox-opened"))$("a.fancybox-close").click();}},".fancybox-overlay");{/literal}{rdelim});
	</script>
