	{if $fb_auth eq "1" or $gp_auth eq "1"}
		$("input[name=auth_username]").keydown(function(e) {ldelim} if (e.keyCode == 13) {ldelim} e.preventDefault(); e.stopPropagation(); $("#auth-register-submit").click(); {rdelim} {rdelim});
		$("input[name=auth_username]").keyup(function(e) {ldelim}
                    var _val = $(this).val();
                    $.ajax({ldelim}
                        url: '{$main_url}/f_modules/m_frontend/m_auth/{href_entry key="signup"}?do=auth_register',
                        cache: false,
                        type: "POST",
                        dataType: "json",
                        data: {ldelim} auth_confirm: 0, auth_username:  _val, auth_userid: "{$smarty.get.u|sanitize}" {rdelim}
                        {rdelim}).complete(function(response) {ldelim}
                            data = response.responseText;

                            var error = '<span class="err-red"><i class="icon-notification"></i> ' + data.replace('error:', '') + '</span>';
                            var notice = '<span class="conf-green"><i class="icon-check"></i> Available</span>';

                            if (data.substr(0, 6) == 'error:') {ldelim}
                                $(".auth-username-check-response").html(error);
                            {rdelim} else {ldelim}
                                $(".auth-username-check-response").html(notice);
                            {rdelim}
                    {rdelim});
            {rdelim});
            $("#auth-register-submit").click(function(e) {ldelim}
                e.preventDefault();
                var t = $(this);
                $("input[name=auth_confirm]").val("1");
                $.post("{$main_url}/f_modules/m_frontend/m_auth/{href_entry key="signup"}?do=auth_register", $("#auth-register-form").serialize(), function (data) {ldelim}
                    var error = '<span class="err-red"><i class="icon-notification"></i> ' + data.replace('error:', '') + '</span>';
                    var notice = '<span class="conf-green"><i class="icon-check"></i> Available</span>';

                    if (data.substr(0, 6) == 'error:') {ldelim}
                        $(".auth-username-check-response").html(error);
                    {rdelim} else {ldelim}
                        $("#auth-register-form input").prop("disabled", true);
                        $("#auth-register-form .auth-username-check-btn").prop("disabled", true);

                        $(".auth-username-check-response").html(notice);
                        $.fancybox.close();
                        location.reload();
                    {rdelim}
                {rdelim});
            {rdelim});
	{/if}
