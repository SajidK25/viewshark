	<script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
	<div class="wdmax entry-list" id="ct-wrapper">
	    <div class="section-top-bar{if $smarty.get.do eq "add"}-add{else} vs-maskx{/if} left-float top-bottom-padding">
                <div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}{include file="tpl_backend/tpl_settings/ct-cancel-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>

            <div class="vs-mask">{generate_html type="be_live_streaming_token" bullet_id="ct-bullet1" entry_id="ct-entry-details1" bb=1}</div>

            {if $smarty.get.do eq "add"}
            <div class="clearfix"></div>
            <div class="section-bottom-bar{if $smarty.get.do eq "add"}-add{else} vs-maskx{/if} left-float top-bottom-padding">
                <div class="clearfix"></div>
                {include file="tpl_backend/tpl_settings/ct-save-top.tpl"}{include file="tpl_backend/tpl_settings/ct-cancel-top.tpl"}
            </div>
            {/if}

	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
	{include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
	<script type="text/javascript">
        $(document).ready(function () {ldelim}
                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                
                        {rdelim});
                {rdelim});
                $('.icheck-box input').on('ifChecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', true); {rdelim});
                $('.icheck-box input').on('ifUnchecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', false); {rdelim});
        {rdelim});
        </script>
