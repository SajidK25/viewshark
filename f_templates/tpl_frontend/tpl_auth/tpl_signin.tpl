{if $signup_captcha eq "1" or $signin_captcha eq "1"}<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>{/if}
<div class="login-margins-off">
<div class="vs-column full fit">
<div class="login-page">
<div class="tabs tabs-style-line">
	{insert name=advHTML id="39"}
    <h1 class="content-title"><i class="icon-user"></i> {lang_entry key="frontend.global.account"}</h1>
    <nav>
        <ul>
            <li class="tab-current"><a href="#section-topline-1" class="icon icon-enter" rel="nofollow"><span>{lang_entry key="frontend.global.signin"}</span></a></li>
            <li onclick="window.location='{$main_url}/{href_entry key="signup"}'"><a href="#section-topline-2" class="icon icon-signup" rel="nofollow"><span>{lang_entry key="frontend.global.signup"}</span></a></li>
            <li onclick="window.location='{$main_url}/{href_entry key="service"}/{href_entry key="x_recovery"}'"><a href="#section-topline-3" class="icon icon-support" rel="nofollow"><span>{lang_entry key="frontend.global.recovery"}</span></a></li>
        </ul>
    </nav>
    <div class="clearfix"></div>
    <div class="content-wrap">
        <section id="section-topline-1" class="content-current">
            <div class="">
                {include file="tpl_frontend/tpl_auth/tpl_signin_loginbox.tpl"}
            </div>
        </section>
        <section id="section-topline-2">
        </section>
        <section id="section-topline-3">
        </section>
    </div><!-- /content -->
</div><!-- /tabs -->
	{insert name=advHTML id="40"}
</div>
</div>
</div>