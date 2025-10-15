			    {if $smarty.get.s ne "" and $smarty.get.id ne ""}
			    <div class="login-page">
				<div class="outer-border-wrapper" style="border-top: 1px solid #d8dbdd;">
				    <form id="password-recovery-form" class="recovery-form user-form" method="post" action="">
                                            <article>
                                            	<h3 class="content-title">
                                            		<i class="icon-user"></i> {lang_entry key="backend.recovery.recovery.password"}
                                            	</h3>
                                            	<div class="line"></div>
                                            </article>
                                            {if $error_message ne ""}{$error_message}{elseif $notice_message ne ""}{$notice_message}{/if}
                                            {if $tpl_error_max eq ""}
				    
					<div class="">
					    <span class="label-signin">{lang_entry key="frontend.global.username"}: </span>
					    <span class="input-signin">{if $global_section eq "frontend"}{$recovery_username['uname']}{else}{$recovery_username}{/if}</span>
					</div>
					<div class="">
					    <span class="label-signin">{lang_entry key="recovery.forgot.new.password"}: </span>
					    <span class="input-signin"><input type="password" id="recover-password-input" name="recovery_forgot_new_password" class="text-input" /></span>
					</div>
					<div class="">
					    <span class="label-signin">{lang_entry key="recovery.forgot.retype.password"}: </span>
					    <span class="input-signin"><input type="password" id="reenter-password-input" name="recovery_forgot_retype_password" class="text-input" /></span>
					</div>
					<div class="form-buttons">
					    <span class="input-signin"></span>
					    <span class="input-signin">
					    	<button class="search-button form-button" name="reset_password" id="reset-password-button" type="submit" value="1">{lang_entry key="frontend.global.submit"}</button>
					    </span>
					</div>
				    </form>
				    	    {/if}
				</div>
			</div>
			    {else}
				{if ($password_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_l value=1}
				{elseif ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_l value=3} {/if}
				{if ($username_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_r value=2}
				{elseif ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_r value=4} {/if}
				<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?onload=myCallBack&render=explicit" async defer></script>
				<script type="text/javascript">
      var recaptcha1;
      var recaptcha2;
      var myCallBack = function() {ldelim}
      {if ($allow_password_recovery eq "1" and $global_section eq "frontend") or ($backend_password_recovery eq "1" and $global_section eq "backend")}
      	{if ($password_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}
        recaptcha1 = grecaptcha.render('recaptcha1', {ldelim}
          'sitekey' : '{$recaptcha_site_key}',
          'theme' : 'light'
        {rdelim});
        {/if}
      {/if}
      {if ($allow_username_recovery eq "1" and $global_section eq "frontend") or ($backend_username_recovery eq "1" and $global_section eq "backend")}
        {if ($username_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}
        recaptcha2 = grecaptcha.render('recaptcha2', {ldelim}
          'sitekey' : '{$recaptcha_site_key}',
          'theme' : 'light'
        {rdelim});
        {/if}
      {/if}
      {rdelim};
    </script>


				<div class="">
                                    <div class="outer-border-wrapper" id="recover-password-mask">
                                        <form id="password-recovery-form" class="user-form" action="" method="post">
                                        {if ($allow_password_recovery eq "1" and $global_section eq "frontend") or ($backend_password_recovery eq "1" and $global_section eq "backend")}
                                            <article>
                                            	<h3 class="content-title">
                                            		<i class="icon-user"></i> {if $global_section eq "frontend"}{lang_entry key="recovery.forgot.password"}{else}{lang_entry key="backend.recovery.forgot.password"}{/if}
                                            	</h3>
                                            	<div class="line"></div>
                                            	<span class="label-signin" style="line-height:20px">{if $global_section eq "frontend"}{lang_entry key="recovery.forgot.pass.txt"}{else}{lang_entry key="backend.recovery.forgot.pass.txt"}{/if}{if $password_recovery_captcha eq "1"}{lang_entry key="recovery.verif.code.txt"}{/if}{lang_entry key="recovery.forgot.pass.txt1"}</span>
                                            </article>
                                            <div id="recover-password-response" class=""></div>
                                            <div class="inner-wrapper center">
                                                
                                                    <div class="row">
                                                        <span class="label-signin">{lang_entry key="frontend.global.username"}: </span>
                                                        <span class="input-signin"><input type="text" id="recover-password-input" name="rec_username" class="text-input" {$left_disabled} /></span>
                                                    </div>
                                                    {if ($password_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}
                                                    	<div class="row" style="margin-top: 10px;">
                                                        	<span class="label-signin"></span>
                                                        	<span class="input-signin"><div id="recaptcha1" style="margin-top: 10px; margin-bottom: 15px; transform:scale(0.99);-webkit-transform:scale(0.99);transform-origin:0 0;-webkit-transform-origin:0 0;"></div></span>
                                                    	</div>
                                                    {/if}
                                                    <div class="clearfix"></div>
                                                    <div class="">
                                                        <span class="label-signin"></span>
                                                        <span class="input-signin">
                                                            <button class="search-button form-button" name="recover_password" id="recover-password-button" type="button" value="1" {$left_disabled}>{lang_entry key="frontend.global.submit"}</button>
                                                        </span>
                                                    </div>
                                                </form>
                                           </div>
                                       </div>
                                        {else}
                                            <article>
                                            	<h3 class="content-title">
                                            		<i class="icon-user"></i> {lang_entry key='recovery.disabled.password'}
                                            	</h3>
                                            	<div class="line"></div>
                                            </article>
                                        {/if}        
                                    </div>

                                    
                                        <div class="outer-border-wrapper" id="recover-username-mask">
                                        <form id="username-recovery-form" class="user-form" action="" method="post">
                                            {if ($allow_username_recovery eq "1" and $global_section eq "frontend") or ($backend_username_recovery eq "1" and $global_section eq "backend")}
                                            	<article>
                                            		<h3 class="content-title"><i class="icon-user"></i> {if $global_section eq "frontend"}{lang_entry key="recovery.forgot.username"}{else}{lang_entry key="backend.recovery.forgot.username"}{/if}</h3>
                                            		<div class="line"></div>
                                            		<span class="label-signin" style="line-height:20px">{if $global_section eq "frontend"}{lang_entry key="recovery.forgot.user.txt"}{else}{lang_entry key="backend.recovery.forgot.user.txt"}{/if}{if $username_recovery_captcha eq "1"}{lang_entry key="recovery.verif.code.txt"}{/if}{lang_entry key="recovery.forgot.user.txt1"}</span>
                                            	</article>
                                            	<div id="recover-username-response" class=""></div>
                                                <div class="inner-wrapper center">
                                                        <div class="">
                                                            <span class="label-signin">{lang_entry key="frontend.global.email"}: </span>
                                                            <span class="input-signin"><input type="text" id="recover-username-input" name="rec_email" class="text-input" {$right_disabled} /></span>
                                                        </div>
                                                        {if ($username_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}
                                                        	<div class="row" style="margin-top: 10px;">
                                                        		<span class="label-signin"></span>
                                                        		<span class="input-signin"><div id="recaptcha2" style="margin-top: 10px; margin-bottom: 15px; transform:scale(0.99);-webkit-transform:scale(0.99);transform-origin:0 0;-webkit-transform-origin:0 0;"></div></span>
                                                    		</div>
                                                        {/if}
                                                        <div class="clearfix"></div>
                                                        <div class="">
                                                            <span class="label-signin"></span>
                                                            <span class="input-signin">
                                                                <button class="search-button form-button" name="recover_username" id="recover-username-button" type="button" value="1" {$right_disabled}>{lang_entry key="frontend.global.submit"}</button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </form>
                                             </div>
                                            {else}
                                            <article>
                                            	<h3 class="content-title">
                                            		<i class="icon-user"></i> {lang_entry key='recovery.disabled.username'}
                                            	</h3>
                                            	<div class="line"></div>
                                            </article>
                                            {/if}
                                        </div>
                                </div>
                            {/if}
