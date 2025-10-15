<html>
    <head>
	<title>##TITLE##</title>
	<style>img, a { border: 0px !important; }</style>
    </head>
    <body style="background-color: #FFFFFF; margin: 20px; padding: 0px;">
	<div id="main-wrapper" style="float: left; width: 100%; min-height: 400px;">
	    <div id="logo-wrapper" style="float: left; width: 100%;">##LOGO##</div>
	    <div id="message-content" style="width: 100%; float: left; display: block;">
                <p>##USER## {lang_entry key="mail.invite.email.txt1"}</p>
                <p>
                    <ul>
                        <li>{lang_entry key="mail.invite.email.txt2"}</li>
                        <li>{lang_entry key="mail.invite.email.txt3"}</li>
                        <li>{lang_entry key="mail.invite.email.txt4"}</li>
                        <li>{lang_entry key="mail.invite.email.txt5"}</li>
                    </ul>
                </p>
                <p>{lang_entry key="mail.invite.email.txt6"}</p>
                <p><a href="##LINK##">##LINK##</a></p>
                <p>{lang_entry key="email.notif.general.txt1"} {$website_shortname}!</p>
                <p>{lang_entry key="email.notif.general.txt2"} {$website_shortname} {lang_entry key="email.notif.general.txt3"}</p>
		<p style="text-align: center; color: #cccccc; padding-top: 15px;">&copy; ##YEAR## {$website_shortname}, LLC</p>
	    </div>
	</div>
    </body>
</html>