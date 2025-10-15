                    <div class="outer-border-wrapper">
			<div class="inner-wrapper center">
                            {if $global_signup eq "0" or $do_disable eq "yes"}
                                <div class="">{$disabled_signup_message}</div>
                            {else}
			    <form id="register-form" class="user-form" method="post" action="{if $global_section eq "frontend"}{$main_url}/{href_entry key="signin"}{if $smarty.get.next ne ""}?next={$smarty.get.next|sanitize}{/if}{/if}">
				<div class="top-bottom-padding bold">{if $global_section eq "frontend"}{lang_entry key="frontend.signin.text8"}{else}{lang_entry key="frontend.signup.h1"}{/if}</div>
				<div><br /></div>
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.username"}: </span>{if $signup_username_availability eq "1"}<span id="check-response"></span>{/if}
				    <span class="input-signin"><input type="text" id="signup-username" class="text-input login-input" name="frontend_signin_username"></span>
				</div>
                                <div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signup.emailadd"}: </span>
				    <span class="input-signin"><input type="text" class="text-input login-input" name="frontend_signup_emailadd"></span>
				</div>
				<div class="row">
				    <span class="label-signin">{lang_entry key="frontend.signin.password"}: </span>
				    <span class="input-signin"><input type="password" class="text-input" name="frontend_signup_setpass" onclick="this.select();" onfocus="if(this.value == '{lang_entry key="frontend.signin.password"}1') { this.value = ''; }" onblur="if(this.value == '') { this.value = '{lang_entry key="frontend.signin.password"}1'; }" value="{lang_entry key="frontend.signin.password"}1" {if $signup_password_meter eq "1"}onkeyup="updatePasswordStrength_new(this,'passwdRating',{ldelim} 'text':2 {rdelim});"{/if} /></span>
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
				    <span class="input-signin"><input type="password" class="text-input" name="frontend_signup_setpassagain" onclick="this.select();" /></span>
				</div>
				<div class="row">
				    <span class="label-signin"></span>
				    <span class="input-signin"><button class="search-button form-button" value="1" name="frontend_global_submit"><span>{lang_entry key="frontend.signup.create"}</span></button></span>
				</div>
			    </form>
                            {/if}
			</div>
		    </div>