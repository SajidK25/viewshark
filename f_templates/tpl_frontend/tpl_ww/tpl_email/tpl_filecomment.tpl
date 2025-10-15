<html>
    <head>
	<title>##TITLE##</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style>img, a { border: 0px !important; } #message-content { line-height: 18px } #message-content img { width: 18px !important; height: 18px !important; display:inline-block; } </style>
    </head>
    <body style="background-color: #FFFFFF; margin: 20px; padding: 0px;">
	<div id="main-wrapper" style="float: left; width: 100%; min-height: 400px;">
	    <div id="logo-wrapper" style="float: left; width: 100%;">##LOGO##</div>
	    <div id="message-content" style="width: 100%; float: left; display: block;">
		<p><a href="##CHURL##">##USER##</a> {if $comm_type eq "file_comment_reply"}{lang_entry key="post.comment.reply.file.tpl"}{else}{lang_entry key="post.comment.file.tpl"}{/if} ##FTITLE##</p>
		<p style="font-weight: bold;">##COMMENT##</p>
		<p></p>
		<p style="text-align: center; color: #cccccc; padding-top: 15px;">&copy; ##YEAR## {$website_shortname}, LLC</p>
	    </div>
	</div>
    </body>
</html>