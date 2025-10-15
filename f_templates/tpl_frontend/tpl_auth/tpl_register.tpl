			{if $signup_captcha eq "1"}<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>{/if}
                    <div class="outer-border-wrapper">
			<div class="inner-wrapper center">
                            {if $global_signup eq "0" or $do_disable eq "yes"}
				<article>
                                        <h3 class="content-title"><i class="icon-user"></i> {$disabled_signup_message}</h3>
                                        <div class="line"></div>
                                </article>
                            {else}
			    <form id="register-form" class="user-form entry-form-class" method="post" action="">
			    <br>
                                {if $error_message ne ""}{$error_message}{elseif $notice_message ne ""}{$notice_message}{/if}
                                {if $notice_message eq ""}
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.username"}: </span>{if $signup_username_availability eq "1"}<span id="check-response"></span>{/if}
				    <span class="input-signin"><input type="text" id="signup-username" class="text-input login-input" name="frontend_signin_username" value="{if $smarty.post.frontend_signin_username}{$smarty.post.frontend_signin_username|sanitize}{/if}"></span>
				</div>
                                <div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signup.emailadd"}: </span>
				    <span class="input-signin"><input type="text" class="text-input login-input" name="frontend_signup_emailadd" value="{if $smarty.post.frontend_signup_emailadd}{$smarty.post.frontend_signup_emailadd|sanitize}{/if}"></span>
				</div>
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.password"}: </span>
				    <span class="input-signin"><input type="password" class="text-input" name="frontend_signup_setpass" onclick="this.select();" onfocus="if(this.value == '{lang_entry key="frontend.signin.password"}1') { this.value = ''; }" onblur="if(this.value == '') { this.value = '{lang_entry key="frontend.signin.password"}1'; }" {if $signup_password_meter eq "1"}onkeyup="updatePasswordStrength_new(this,'passwdRating',{ldelim} 'text':2 {rdelim});"{/if} /><a href="" rel="nofollow" class="showp"><i class="icon-eye"></i></a><a href="" rel="nofollow" class="hidep no-display"><i class="icon-eye-blocked"></i></a></span>
                                    {if $signup_password_meter eq "1"}
                                        <div class="row no-top-padding">
                                            <div class="label-signup"></div>
                                            <div class="input-signup">
                                                <div id="passwdRating">
                                                    <div id="pass_meter" class="pass_meter"><div class="pass_meter_base"></div></div>
                                                    <div id="ps-rating"></div>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
				</div>
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signup.setpassagain"}: </span>
				    <span class="input-signin"><input type="password" class="text-input" name="frontend_signup_setpassagain" onclick="this.select();" /><a href="" rel="nofollow" class="showp"><i class="icon-eye"></i></a><a href="" rel="nofollow" class="hidep no-display"><i class="icon-eye-blocked"></i></a></span>
				</div>
				{if $paid_memberships eq "1"}
					{include file="tpl_frontend/tpl_auth/tpl_payment_packlist.tpl"}
				{/if}
				{if $signup_captcha eq "1"}
				<div class="captcha-row">
				    <span class="label-signin"></span>
				    <span class="input-signin"><div class="g-recaptcha" data-sitekey="{$recaptcha_site_key}" style="transform:scale(0.99);-webkit-transform:scale(0.99);transform-origin:0 0;-webkit-transform-origin:0 0;"></div></span>
				</div>
				{/if}
				<div class="row form-buttons">
				    <span class="label-signin"></span>
				    <span class="input-signin">
				    	<button type="submit" class="search-button form-button" value="1" name="frontend_global_submit" style="width:100%"><span>{lang_entry key="frontend.signup.create"}</span></button>
				    </span>
				</div>
			    </form>
				<div class="row form-buttons">
				    <span class="label-signin"></span>
				    <span class="input-signin">
				    	{if $fb_auth eq "1" or $gp_auth eq "1"}
				    	<div class="hr"><div class="inner">{lang_entry key="frontend.global.or"}</div></div>
				    	{if $smarty.get.u eq ""}
				    	<center>
				    		{$fb_register}
				    		{$gp_register}
				    	</center>
				    	{else}
				    		{$fb_register}
				    		{$gp_register}
				    		<a href="#upopup" id="inline" class="hidden" rel="nofollow"></a>
				    		<div id="upopup" style="display: none;">
				    			<div class="lb-margins">
				    				<article>
				    					<h3 class="content-title"><i class="icon-user"></i>{lang_entry key="frontend.signup.fbusername"}</h3>
				    					<div class="line"></div>
				    				</article>
				    				<form id="auth-register-form" class="entry-form-class">
				    					<h4><i class="icon-info"></i> {lang_entry key="frontend.signup.fbcomplete"}</h4>
				    					<input name="auth_confirm" type="hidden" value="0">
				    					<input name="auth_userid" type="hidden" class="form-control" placeholder="" value="{if $smarty.session.fb_user['id'] gt 0}{$smarty.session.fb_user['id']}{elseif $smarty.session.gp_user['id'] gt 0}{$smarty.session.gp_user['id']}{/if}">
				    					<input name="auth_username" type="text" class="form-control" placeholder="" value="">
				    					<div class="auth-username-check-response"></div>
				    					<span class="input-signin">
				    						<button class="search-button form-button auth-check-button auth-username-check-btn" value="1" name="frontend_global_check" type="button"><span>{lang_entry key="frontend.global.check"}</span></button>
				    						<button class="search-button form-button apply-button" name="auth-submit-register" id="auth-register-submit" type="button">{lang_entry key="frontend.global.submit"}</button>
				    					</span>
				    				</form>
				    			</div>
				    		</div>
				    	{/if}
				    	{/if}
				    </span>
				</div>
				{/if}
                            {/if}
			</div>
		    </div>