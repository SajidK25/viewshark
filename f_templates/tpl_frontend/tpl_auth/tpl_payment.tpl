	{assign var="ro" value='readonly="readonly"'}
	{if $error_message ne ""}{$error_message}{elseif $notice_message ne ""}{$notice_message}{/if}
	<div class="login-page payment-page">
    <div id="signup-content-wrapper" class="">
	<div class="pane-left50 font83">
	    <div class="left-inner-push">
	        <div class="">{if $smarty.session.signup_username ne "" and $smarty.session.renew_id eq ""}{assign var=l_disabled value='disabled="disabled"'}{/if}
		    <div class="">
			<form id="signup-form-left" action="" method="post" class="user-form entry-form-class">
	<article>
		<h3 class="content-title"><i class="icon-coin"></i> {if $smarty.session.renew_id eq ""}{lang_entry key="frontend.pkinfo.summary"}{else}{lang_entry key="frontend.pkinfo.renew"}{/if}</h3>
		<div class="line"></div>
	</article>
			    <div id="continue-mask" class="wdmax">
				<div><span class="label-signin">{lang_entry key="frontend.pkinfo.pkname"}: <span class="pk-text">{$pk_info[0].pk_name}</span></span></div>
				<div><span class="label-signin">{lang_entry key="frontend.pkinfo.pkprice"}: <span class="pk-text">{$pk_info[0].pk_priceunit}{$pk_info[0].pk_price}{if $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 365 or $pk_info[0].pk_period eq 30} {$pk_periodtext}{/if}</span></span></div>
				<div class="row selector">
				    {if $pk_info[0].pk_price ne 0 and $smarty.get.u ne ""}
					    {assign var="per_value" value="{lang_entry key="frontend.pkinfo.pksubperiod"}"}
					    {if $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 30}{assign var="per_value" value="{lang_entry key="frontend.global.monthly"}"}{elseif $pk_info[0].pk_period eq 365}{assign var="per_value" value="{lang_entry key="frontend.global.yearly"}"}{else}{if $pk_periodtext eq ""}{assign var="per_value" value="{$pk_info[0].pk_period} {lang_entry key="frontend.global.days"}"}{else}{assign var="per_value" value="{$pk_periodtext}"}{/if}{/if}
					    
					    <div><span class="label-signin" id="pack-period-tmp">{lang_entry key="frontend.pkinfo.pksubperiod"}: <span class="pk-text">{$per_value}</span></span></div>
					    <input type="hidden" {$ro} name="pk_period_tmp" class="login-input" id="pack-period-tmp-off" value="{lang_entry key="frontend.pkinfo.pksubperiod"}: {$per_value}" />
                                            <input type="hidden" {$ro} name="pk_period" class="login-input no-display" id="pack-period" value="1" />

					{if $pk_info[0].pk_period eq 30 or $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 365}
					    <select name="pk_period_sel" class="wd100 renew-select" onchange="$('#pack-period').val(this.value); $('#pack-period-tmp').html('{lang_entry key="frontend.pkinfo.pksubperiod"}: <span class=&quot;pk-text&quot;>'+this.value+' {if $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 30}{lang_entry key="frontend.global.month"}{elseif $pk_info[0].pk_period eq 365}{lang_entry key="frontend.global.years"}{else}{if $pk_periodtext eq ""}{$pk_info[0].pk_period} {lang_entry key="frontend.global.days"}{else}{$pk_periodtext}{/if}{/if}'+'</span>');">{$pk_period_opts}</select>
					{/if}
				    {else}
				    	<div><span class="label-signin">{lang_entry key="frontend.pkinfo.pksubperiod"}: <span class="pk-text">{$pk_info[0].pk_period} {lang_entry key="frontend.global.days"}</span></span></div>
					<input type="hidden" {$ro} name="pk_free_tmp" class="login-input" value="{lang_entry key="frontend.pkinfo.pksubperiod"}: {$pk_info[0].pk_period} {lang_entry key="frontend.global.days"}" />
				    {/if}
				</div>
			    {if $smarty.get.p ne "" and $smarty.get.u ne "" and $pk_info[0].pk_price ne 0 and $discount_codes eq 1}
				<div class="row">
				    <input type="text" onfocus="if(this.value == '{lang_entry key="frontend.pkinfo.discount"}') { this.value = ''; }" onblur="if(this.value == '') { this.value = '{lang_entry key="frontend.pkinfo.discount"}'; }" name="frontend_pkinfo_discount" class="login-input" id="pack-dsc" value="{lang_entry key="frontend.pkinfo.discount"}" />
				</div><br>
			    {/if}
			    {if $paypal_payments eq 1 or $moneybookers_payments eq 1}
				<div class="row top-padding10">
				    <span class="label-signin"></span>
				    <span class="input-signin"><button class="search-button form-button continue-button{if $smarty.session.renew_id ne "" and $smarty.get.p eq "" and $smarty.get.u eq ""} hidden{/if}" {if $pk_info[0].pk_price eq 0}value="1"{/if} name="{if $pk_info[0].pk_price ne 0}signup_continue{else}signup_finalize{/if}" id="{if $pk_info[0].pk_price ne 0}signup-continue{else}signup-finalize{/if}" type="{if $pk_info[0].pk_price ne 0}button{else}submit{/if}" {if $smarty.session.renew_id ne "" and $smarty.get.p eq "" and $smarty.get.u eq ""}disabled="disabled"{/if}><span>{if $pk_info[0].pk_price ne 0}{lang_entry key="frontend.global.continue"}{else}{lang_entry key="frontend.signup.update"}{/if}</span></button></span>
				</div>
			    {/if}
				<input type="hidden" name="pk_id" value="{$smarty.get.p|sanitize}" />
				<input type="hidden" name="usr_id" value="{$smarty.get.u|sanitize}" />
			    </div>
			    <div class="left-float wdmax">
				<div id="continue-response"></div>
			    </div>
			</form>
		    </div>
		</div>
	    </div>
	</div>

	{assign var="ro" value='readonly="readonly"'}
	{assign var="renew" value="1"}
	<div class="pane-right50 font83">
	    <div class="right-inner-push">
		<div class="">
		    <div class="center">
			<form id="signup-form-right" action="" method="post" class="entry-form-class user-form">
			    <div id="different-sub" class="">
	<article>
		<h3 class="content-title"><i class="icon-coin"></i> {if $smarty.session.renew_id eq "" or ($smarty.get.p ne "" and $smarty.get.u ne "")}{lang_entry key="frontend.pkinfo.diffsub"}{else}{if $smarty.get.t eq ""}{lang_entry key="frontend.pkinfo.renew.text"}{else}{lang_entry key="frontend.pkinfo.diffsub"}{/if}{/if}</h3>
		<div class="line"></div>
	</article>
				<div class="row">
				    <div class="left-float wd300">{include file="tpl_frontend/tpl_auth/tpl_payment_packlist.tpl"}</div>
				</div>
				<br>
				<div class="row left-float top-padding15">
				    <span class="label-signin"></span>
				    <span class="input-signin"><button class="search-button form-button apply-button" name="signup_packchange" id="signup-packchange" type="submit"><span>{lang_entry key="frontend.global.apply"}</span></button></span>
				</div>
				<input type="hidden" id="frontend_membership_id" name="frontend_membership_id" value="{$smarty.get.u|sanitize}" />
			    </div>
			</form>
		    </div>
		</div>
	    </div>
	</div>
    </div>
    </div>
	<script type="text/javascript">
	var main_url = '{$main_url}/';
	    $("#signup-continue").bind("click", function () {ldelim}
		$("#continue-mask").mask(" ");
		$.post(main_url + "{href_entry key="signup"}/payment?do=continue", $("#signup-form-left").serialize(),
		function(data){ldelim}
		    $("#continue-response").html(data);
		    $("#continue-mask").unmask();
		{rdelim});
	    {rdelim});
	</script>
