$(document).ready(function() {ldelim}
    {if $signup_captcha eq "1"}
        $("#reload-captcha").bind("click", function () {ldelim}
            $("#c-image").attr("src", main_url + "{href_entry key="captcha"}?ts=" + new Date().getTime());
        {rdelim});
    {/if}
    
    {if $signup_username_availability eq "1"}
        $("#signup-username").keyup(function(){ldelim}
            if (document.getElementById("signup-username").value!='') {ldelim}
                $("#signup-username").mask(" ");
                $.post(main_url + "{href_entry key="signup"}?do=ucheck", $("#register-form").serialize(), function(data) {ldelim}
                    $("#check-response").html(data);
                    $("#signup-username").unmask();
                {rdelim});
            {rdelim} else {ldelim}
                $("#check-response").html("");
            {rdelim}
        {rdelim});
    {/if}

    {if ($password_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_password_recovery_captcha eq "1" and $global_section eq "backend")}
        $("#reload-left-captcha").bind("click", function () {ldelim}
            $("#l-image").attr("src", main_url + "{href_entry key="captcha"}?extra={$extra_l}&amp;ts=" + new Date().getTime());
        {rdelim});
    {/if}
    
    {if ($username_recovery_captcha eq "1" and $global_section eq "frontend") or ($backend_username_recovery_captcha eq "1" and $global_section eq "backend")}
        $("#reload-right-captcha").bind("click", function () {ldelim}
            $("#r-image").attr("src", main_url + "{href_entry key="captcha"}?extra={$extra_r}&amp;ts=" + new Date().getTime());
        {rdelim});
    {/if}
    
    $("#recover-password-button").bind("click", function(){ldelim}
        if (document.getElementById("recover-password-input").value!='') {ldelim}
            $("#recover-password-mask").mask(" ");
            $.post(full_url + "{if $global_section eq "backend"}{href_entry key="be_service"}{else}{href_entry key="service"}{/if}/{href_entry key="x_recovery"}?t=1{if $global_section eq "backend"}&a=1{/if}", $("#password-recovery-form").serialize(), function(data){ldelim}
                $("#recover-password-response").html(data);
                $("#recover-password-mask").unmask();
                if (typeof($("#error-message").html()) == "undefined") {ldelim}
                	grecaptcha.reset(recaptcha1);
                {rdelim}
            {rdelim});
        {rdelim}
    {rdelim});
    
    $("#recover-username-button").bind("click", function(){ldelim}
        if (document.getElementById("recover-username-input").value!='') {ldelim}
            $("#recover-username-mask").mask(" ");
            $.post(full_url + "{if $global_section eq "backend"}{href_entry key="be_service"}{else}{href_entry key="service"}{/if}/{href_entry key="x_recovery"}?t=2{if $global_section eq "backend"}&a=1{/if}", $("#username-recovery-form").serialize(), function(data) {ldelim}
                $("#recover-username-response").html(data);
                $("#recover-username-mask").unmask();
                if (typeof($("#error-message").html()) == "undefined") {ldelim}
                	grecaptcha.reset(recaptcha2);
                {rdelim}
            {rdelim});
        {rdelim}
    {rdelim});
    
{rdelim});