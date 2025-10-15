{if $signin_captcha_be eq "1"}<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>{/if}
		    <div class="outer-border-wrapper">
			<div class="inner-wrapper center">
                            {if ($global_section eq "frontend" and $frontend_signin_section eq "1") or ($global_section eq "backend" and $backend_signin_section eq "1")}
			    <form id="signin-form" class="user-form" method="post" action="{if $global_section eq "frontend"}{$main_url}/{href_entry key="signin"}{if $smarty.get.next ne ""}?next={$smarty.get.next|sanitize}{/if}{/if}">
				<article>
					<h3 class="content-title"><i class="icon-user"></i> {if $global_section eq "frontend"}{lang_entry key="frontend.signin.text8"}{else}{lang_entry key="backend.login.intro.text"}{/if}</h3>
					<div class="line"></div>
				</article>
				{$error_message}
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.username"}: </span>
				    <span class="input-signin"><input {$disabled_input} type="text" class="text-input login-input" name="frontend_signin_username" /></span>
				</div>
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.password"}: </span>
				    <span class="input-signin"><input {$disabled_input} type="password" class="text-input" name="frontend_signin_password" onclick="this.select();" /></span>
				</div>
				{if ($global_section eq "frontend" and $login_remember eq "1") or ($global_section eq "backend" and $backend_remember eq "1")}
				<div class="row">
					<span class="label-signin no-top-margin icheck-box"><input {$disabled_input} type="checkbox" name="signin_remember" value="1" {if $smarty.post.signin_remember}checked="checked"{/if} /></span>
					<span class="input-signin top-padding2">{lang_entry key="frontend.signin.remember"}</span>
				</div>
				{/if}
				{if $signin_captcha_be eq "1"}
                                <div class="captcha-row">
                                    <span class="label-signin"></span>
                                    <span class="input-signin"><div class="g-recaptcha" data-sitekey="{$recaptcha_site_key}" style="transform:scale(0.99);-webkit-transform:scale(0.99);transf
                                </div>
                                {/if}
				<div class="clearfix"></div>
				<div class="row form-buttons">
				    <span class="label-signin"></span>
				    <span class="input-signin"><button {$disabled_input} class="search-button form-button" value="1" name="frontend_global_submit"><span>{lang_entry key="frontend.global.signin"}</span></button></span>
				</div>
			    </form>
                            {else}
				<article>
					<h3 class="content-title"><i class="icon-user"></i> {lang_entry key='frontend.signin.text11'}</h3>
					<div class="line"></div>
				</article>
                            {/if}
			</div>
		    </div>
