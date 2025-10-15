				<div class="top-padding10">
				    <article>
				    	<h3 class="content-title"><i class="iconBe-coin"></i> {lang_entry key="frontend.pkinfo.pktotal"}</h3>
				    	<div class="line"></div>
				    </article>
				    <div class="row">
					<span class="label-signin">{lang_entry key="frontend.pkinfo.pktotal"}: </span>
					<span class="input-signin"><span class="blued">{$pk_info[0].pk_priceunit}{$pk_totalprice}</span> {if $smarty.post.pk_period}{lang_entry key="frontend.global.for"}{/if} <span class="blued">{if $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 30 or $pk_info[0].pk_period eq 365}{$smarty.post.pk_period|sanitize}{/if}</span> {if $pk_info[0].pk_period eq 31 or $pk_info[0].pk_period eq 30}{if $smarty.post.pk_period eq "1"}{lang_entry key="frontend.global.mont"}{else}{lang_entry key="frontend.global.month"}{/if}{elseif $pk_info[0].pk_period eq 365}{if $smarty.post.pk_period eq "1"}{lang_entry key="frontend.global.year"}{else}{lang_entry key="frontend.global.years"}{/if}{else}<span class="blued">{if $pk_periodtext eq ""}{$pk_info[0].pk_period} {if $pk_info[0].pk_period eq "1"}{lang_entry key="frontend.global.day"}{else}{lang_entry key="frontend.global.days"}{/if}{else}{$pk_periodtext}{/if}</span>{/if}</span>
				    </div>
				    <br>
				    <div class="row top-padding10">
					<span class="label-signin"></span>
					<span class="input-signin"><button class="search-button form-button apply-button" name="signup_complete" id="signup-complete" type="button"><span>{lang_entry key="frontend.global.confirm"}</span></button></span>
				    </div>
				</div>
				<script type="text/javascript">
				var main_url = '{$main_url}/';
				    $("#signup-complete").bind("click", function () {ldelim}
					$(".col1").mask(" ");
					$.post(main_url + "{href_entry key="signup"}/payment?do=process", $("#signup-form-left").serialize(),
					function(data) {ldelim}
					    $("#continue-response").html(data);
					{rdelim});
				    {rdelim});
				</script>
