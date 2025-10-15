<div class="login-margins-off">
<div class="vs-column full fit">
<div class="login-page">
<div class="tabs tabs-style-line">
	{insert name=advHTML id="37"}
    <h1 class="content-title"><i class="icon-user"></i> {lang_entry key="frontend.global.account"}</h1>
    <nav>
        <ul>
            <li onclick="window.location='{$main_url}/{href_entry key="signin"}'"><a href="#section-topline-1" class="icon icon-enter" rel="nofollow"><span>{lang_entry key="frontend.global.signin"}</span></a></li>
            <li class="tab-current"><a href="#section-topline-2" class="icon icon-signup" rel="nofollow"><span>{lang_entry key="frontend.global.signup"}</span></a></li>
            <li onclick="window.location='{$main_url}/{href_entry key="service"}/{href_entry key="x_recovery"}'"><a href="#section-topline-3" class="icon icon-support" rel="nofollow"><span>{lang_entry key="frontend.global.recovery"}</span></a></li>
        </ul>
    </nav>
    <div class="clearfix"></div>
    <div class="content-wrap">
        <section id="section-topline-1">
        </section>
        <section id="section-topline-2" class="content-current">
            <div class="">
                {include file="tpl_frontend/tpl_auth/tpl_register.tpl"}
            </div>
        </section>
        <section id="section-topline-3">
        </section>
    </div><!-- /content -->
</div><!-- /tabs -->
	{insert name=advHTML id="38"}
</div>
</div>
</div>

<script type="text/javascript">
{include file="tpl_backend/tpl_signinjs.tpl"}
</script>
