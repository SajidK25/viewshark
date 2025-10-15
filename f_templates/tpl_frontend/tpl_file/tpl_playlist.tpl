	{if $smarty.get.l ne ""}
	    {assign var=file_type value="l"}
	    {assign var=u_key value="{$smarty.get.l|sanitize}"}
	    {assign var=f_key value="sort-live"}
        {elseif $smarty.get.v ne ""}
	    {assign var=file_type value="v"}
	    {assign var=u_key value="{$smarty.get.v|sanitize}"}
	    {assign var=f_key value="sort-video"}
	{elseif $smarty.get.i ne ""}
	    {assign var=file_type value="i"}
	    {assign var=u_key value="{$smarty.get.i|sanitize}"}
	    {assign var=f_key value="sort-image"}
	{elseif $smarty.get.a ne ""}
	    {assign var=file_type value="a"}
	    {assign var=u_key value="{$smarty.get.a|sanitize}"}
	    {assign var=f_key value="sort-audio"}
	{elseif $smarty.get.d ne ""}
	    {assign var=file_type value="d"}
	    {assign var=u_key value="{$smarty.get.d|sanitize}"}
	    {assign var=f_key value="sort-doc"}
	{elseif $smarty.get.b ne ""}
	    {assign var=file_type value="b"}
	    {assign var=u_key value="{$smarty.get.b|sanitize}"}
	    {assign var=f_key value="sort-blog"}
	{/if}
	<script type="text/javascript">
            var current_url  = '{$main_url}/';
            var menu_section = '{href_entry key="playlist"}?{$file_type}={$u_key}&for={$f_key}';
            var fe_mask      = 'on';
        </script>
	{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}
	<div class="wdmax" id="ct-wrapper">
            {generate_html type="playlist_details_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        </div>
