<html>
    <head>
	<title>##TITLE##</title>
	<style>img, a { border: 0px !important; }</style>
    </head>
    <body style="background-color: #FFFFFF; margin: 20px; padding: 0px;">
	<div id="main-wrapper" style="float: left; width: 100%; min-height: 400px;">
	    <div id="logo-wrapper" style="float: left; width: 100%;">##LOGO##</div>
	    <div id="message-content" style="width: 100%; float: left; display: block;">
		<h2>##H2##</h2>
		<p>{lang_entry key="backend.notif.signup.new"}{$website_shortname}</p>
		<p>
		------------------------------------------------------<br />
		{lang_entry key="backend.notif.signup.user"}: ##U_NAME##<br />
		{lang_entry key="backend.notif.signup.email"}: ##U_EMAIL##<br />
		{lang_entry key="backend.notif.signup.ip"}: ##U_IP##<br />
		------------------------------------------------------
		</p>
		<p style="text-align: center; color: #cccccc; padding-top: 15px;">&copy; ##YEAR## {$website_shortname}, LLC</p>
	    </div>
	</div>
    </body>
</html>