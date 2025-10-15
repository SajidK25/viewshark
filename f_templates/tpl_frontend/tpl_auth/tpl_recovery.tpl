{if $smarty.get.s eq "" and $smarty.get.id eq ""}

{if ($password_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_l value=1}
{elseif ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_l value=3} {/if}
{if ($username_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_r value=2}
{elseif ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_r value=4} {/if}

<div class="login-margins-off">
<div class="vs-column full fit">
<div class="login-page">
<div class="tabs tabs-style-line">
	{insert name=advHTML id="39"}
    <h1 class="content-title"><i class="icon-user"></i> {lang_entry key="frontend.global.account"}</h1>
    <nav>
        <ul>
            <li onclick="window.location='{$main_url}/{href_entry key="signin"}'"><a href="#section-topline-1" class="icon icon-enter" rel="nofollow"><span>{lang_entry key="frontend.global.signin"}</span></a></li>
            <li onclick="window.location='{$main_url}/{href_entry key="signup"}'"><a href="#section-topline-2" class="icon icon-signup" rel="nofollow"><span>{lang_entry key="frontend.global.signup"}</span></a></li>
            <li class="tab-current"><a href="#section-topline-3" class="icon icon-support" rel="nofollow"><span>{lang_entry key="frontend.global.recovery"}</span></a></li>
        </ul>
    </nav>
    <div class="content-wrap">
        <section id="section-topline-1">
        </section>
        <section id="section-topline-2">
        </section>
        <section id="section-topline-3" class="content-current">
            <div>
                {include file="tpl_frontend/tpl_auth/tpl_recovery_form.tpl"}
            </div>
        </section>
    </div><!-- /content -->
</div><!-- /tabs -->
	{insert name=advHTML id="40"}
</div>
</div>
</div>


<script type="text/javascript">
{include file="tpl_backend/tpl_signinjs.tpl"}
</script>
{else}
	{include file="tpl_frontend/tpl_auth/tpl_recovery_form.tpl"}
{/if}