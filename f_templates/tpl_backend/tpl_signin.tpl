{if ($password_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_l value=1}
{elseif ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_l value=3} {/if}
{if ($username_recovery_captcha eq "1" and $global_section eq "frontend")} {assign var=extra_r value=2}
{elseif ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}{assign var=extra_r value=4} {/if}

<div class="login-page">
<center><div><a href="{$main_url}/{href_entry key="home"}" id="flogo"></a></div></center>
<br>
<div class="tabs tabs-style-line">
    <nav>
        <ul>
            <li><a href="#section-topline-1" class="icon icon-enter"><span>{lang_entry key="frontend.global.signin"}</span></a></li>
            <li><a href="#section-topline-3" class="icon icon-support"><span>{lang_entry key="frontend.global.recovery"}</span></a></li>
        </ul>
    </nav>
    <div class="content-wrap">
        <section id="section-topline-1">
            <div class="">
                {include file="tpl_backend/tpl_auth/tpl_signin_loginbox.tpl"}
            </div>
        </section>
        <section id="section-topline-3">
            <div class="">
                {include file="tpl_backend/tpl_auth/tpl_recovery.tpl"}
            </div>
        </section>
    </div><!-- /content -->
</div><!-- /tabs -->
</div>

<script type="text/javascript">
{include file="tpl_backend/tpl_signinjs.tpl"}
</script>