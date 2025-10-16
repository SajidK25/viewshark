<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/* file comments */
class VChannelComments extends VView
{
    private static $db_cache;
    private static $user_id;
    public static $file_key;
    private static $ch_cfg;

    public function __construct()
    {
        self::$db_cache = false; //change here to enable caching

        $adr = self::$filter->clr_str($_SERVER['REQUEST_URI']);

        if (strpos($adr, self::$href['channel']) !== false) {
            $c  = new VChannel;
            $ch = true;

            $user_id = (int) VChannel::$user_id;
        } elseif (isset($_GET['c'])) {
            $ch = true;

            $user_id = (int) $_GET['c'];
        }

        if ($user_id == 0) {
            return;
        }

        self::$user_id  = $user_id;
        self::$file_key = $user_id;

        $ch           = self::getChannelConf($user_id);
        self::$ch_cfg = unserialize($ch[0]['ch_cfg']);
    }
    private static function getChannelConf($id)
    {
        $db = self::$db;

        $rs = $db->execute(sprintf("SELECT `ch_title`, `ch_tags`, `ch_type`, `ch_cfg` FROM `db_accountuser` WHERE `usr_id`='%s' AND `usr_status`='1' LIMIT 1;", $id));

        return $rs->getrows();
    }
    /* comments section */
    public function commLayout(string $type, int $vuid, $msg_arr = '', $like_arr = '')
    {
        $cfg            = self::$cfg;
        $language       = self::$language;
        $class_filter   = self::$filter;
        $class_database = self::$dbc;
        $href           = self::$href;
        $section        = self::$section;
        $f_key          = (int) self::$user_id;
        $vuid           = $vuid == 0 ? self::$user_id : $vuid;

        $cfg['file_comment_spam'] = 1;

        $islogged = (int) $_SESSION['USER_ID'] > 0 ? true : false;

        /* js stuff */
        $ht_js = 'var comm_sec = "' . VHref::getKey("see_comments") . '";';
        $ht_js .= 'var comm_url = "' . $cfg['main_url'] . '/"+comm_sec+"?' . $type[0] . '=' . $f_key . '"; var m_loading = "";';
        /* pagination links */
        $page_nr = (int) $_GET['page'] > 1 ? (int) $_GET['page'] : 1;

        /* mouse over comments */
        $ht_js .= '$(document).on({mouseover: function(e){var c_key = $(this).find(".comm-actions-hkey:first").text();if(typeof $(".ce-"+c_key).html()!=="undefined"){$(this).find("#comm-actions2-over"+c_key).addClass("no-display");return}$(this).find("#comm-actions2-over"+c_key).removeClass("no-display"); $(this).addClass("comment-bg-on");}},".comment_h");';
        $ht_js .= '$(document).on({mouseout: function(e){var c_key = $(this).find(".comm-actions-hkey:first").text();if(typeof $(".ce-"+c_key).html()!=="undefined"){$(this).find("#comm-actions2-over"+c_key).addClass("no-display");return}$(this).find("#comm-actions2-over"+c_key).addClass("no-display");$(this).removeClass("comment-bg-on");}},".comment_h");';
        //done
        /* click to sort comments */
        $ht_js .= '$(".sort-comments").click(function(e){';
        $ht_js .= 'e.preventDefault();var t = $(this);$("#comm-sort").val(t.attr("href").replace("#", ""));var frm = $("#comm-post-form").serialize();';
        $ht_js .= ';$("#comment-load").mask("");';
        $ht_js .= '$.post(comm_url+"&do=comm-sort", frm, function(data){';
        $ht_js .= '$("#comment-load").html(data);'; // $(".comm-input-action").val("");';
        $ht_js .= 'if(v=="top-comments"){$(".response_holder .c-pinned").each(function(){c=$(this).parent().parent().parent().clone(true);c.insertAfter($(".comments_activity:first")).removeClass("response");});}'; //////////////////////
        $ht_js .= '$("#comment-load").unmask();';
        $ht_js .= '});';
        $ht_js .= '});';
        /* if logged in */
        if ($islogged) {
            $ht_js .= '$(".comm-cancel-action").click(function(){$(this).closest("form").find("textarea").attr("style", "height:auto");$(".comm-switch2").addClass("comm-hide");$("#comm-char-remaining").html("' . $cfg['file_comment_max_length'] . '");});';
            /* update comment limit */
            //$ht_js .= '$(".comm-input-action").keyup(function(){var rem = ' . $cfg['file_comment_max_length'] . '-$(this).val().length; $("#comm-char-remaining").html(rem > 0 ? rem : 0); if(rem < 1){$("#comm-char-remaining").parent().addClass("err-red");}else{$("#comm-char-remaining").parent().removeClass("err-red");}});';
            //$ht_js .= '$(document).on("keyup", ".comm-reply-action", function (){var rid = $(this).attr("id").substr(2); var rem = ' . $cfg['file_comment_max_length'] . '-$(this).val().length; $("#comm-char-remaining"+rid).html(rem > 0 ? rem : 0); if(rem < 1){$("#comm-char-remaining"+rid).parent().addClass("err-red");}else{$("#comm-char-remaining"+rid).parent().removeClass("err-red");}});';
            /* click to post comment */
            $ht_js .= '$(".post-comment-button").click(function(){';
            $ht_js .= 'if($(".comm-input-action").val() != ""){';
            $ht_js .= 'var t = $(this); t.find("i").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-post", $("#comm-post-form").serialize(), function(data){';
            $ht_js .= '$("#comment-load").html(data);$(".comm-input-action").val(""); t.find("i").removeClass("spinner icon-spinner");';
            $ht_js .= '});}});';
            $ht_js .= 'var pag="&page=' . $page_nr . '";';

            /* click on comment actions menu */
            $ht_js .= '$(".single-comm-action").click(function(){if($(this).next().next().hasClass("no-display")){$(this).addClass("topnav-button-active");$(this).next().next().removeClass("no-display");}else{$(this).removeClass("topnav-button-active");$(this).next().next().addClass("no-display");}});';
            /* reply to comm */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().next().text();';
            $ht_js .= 'var ct = "' . $cfg['file_comment_max_length'] . '";
				var ht = \'<div class="comment-reply cr-\'+f1+\'"><span id="cp-pos\'+f1+\'" class="cp-pos">0</span><div class="comment_left_char tfr"><p class="rem_char reply_char"><span class="greyed-out comm-switch\'+f1+\'"><span id="comm-char-remaining\'+f1+\'">\'+(ct-($(".comm-own"+f1+">a").html().length+3))+\'</span> ' . $language['view.files.comm.char'] . '</span></p></div><form class="entry-form-class-off" id="comm-reply-form\'+f1+\'"><input type="hidden" name="comm_reply" value="\'+f1+\'" /><input type="hidden" name="comm_type" value="' . $class_filter->clr_str($type) . '" /><input type="hidden" name="comm_uid" value="' . (int) $vuid . '" /><input type="hidden" name="file_comm_sort" value="\'+$("#comm-sort").val()+\'" /><textarea name="file_comm_text" id="r-\'+f1+\'" class="textarea-input comm-reply-action" rows="1" cols="1">@\'+$(".comm-own"+f1+">a").attr("rel-usr").trim()+\' </textarea><div class="comm-switch\'+f1+\'"><div class="comments_actions main_comments_actions" style="display:flex"><div id="comment-emotes\'+f1+\'" class="comment-emotes"><i class="icon-smiley"></i></div><a class="comm-cancel-action comm-cancel-reply link cancel-trigger" href="javascript:;" onclick="$(&quot;.cr-\'+f1+\'&quot;).replaceWith(&quot;&quot;);">' . $language['frontend.global.cancel'] . '</a><button onfocus="blur();" value="1" type="button" class="search-button reply-comment-button symbol-button" id="pr-\'+f1+\'" name="post_file_comment"><span>' . $language['frontend.global.reply'] . '</span></button></div></div></form></div>\';';
            $ht_js .= 'if(!$(".cr-"+f1).html()){$(ht).insertAfter($(this).parent().parent().parent().next())} $("#c-menu"+f1+".single-comm-action").click();';
            $ht_js .= '
			const textarea = document.getElementById("r-"+f1);
			textarea.addEventListener("focus", () => {
				const currentValue = textarea.value;
				textarea.value = "";
				textarea.value = currentValue;
				textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
			});
			textarea.addEventListener("input", () => {
				textarea.style.height = "auto";
				textarea.style.height = (textarea.scrollHeight+1) + "px";
			});

			textarea.focus();
			';
            /* reply emoji */
            if ($cfg['comment_emoji'] == 1) {
                $ht_js .= '

			const pickerOptions = {
				theme: $("body").hasClass("dark") ? "dark" : "light", emojiButtonSize:30, emojiSize:20, perLine:12, skinTonePosition:"search", previewPosition:"none", autoFocus:true, dynamicWidth: true, onEmojiSelect: function(emoji){
					const textarea = document.getElementById("r-"+f1)
					const cursorPosition = textarea.selectionEnd
					const start = textarea.value.substring(0, textarea.selectionStart)
					const end = textarea.value.substring(textarea.selectionStart)
					const text = start + emoji.native + end
					textarea.value = text
					textarea.focus()
					textarea.selectionEnd = cursorPosition + emoji.native.length
				}
			}
			const picker = new EmojiMart.Picker(pickerOptions);
			$("#comment-emotes"+f1).append(picker);

			if(window.innerWidth<500){
				$("#comment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21)));
				$(".posted-comments > .comments_activity > .response_holder #comment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+72)));
			} else {
				$("#comment-emotes"+f1+" em-emoji-picker").css("left", "39px");
				$(".posted-comments > .comments_activity > .response_holder#comment-emotes"+f1+" em-emoji-picker").css("left", "39px");
			}
			window.addEventListener("resize",function(){
			if(window.innerWidth<500){
				$("#comment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21)));
				$(".posted-comments > .comments_activity > .response_holder #comment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+72)));
			} else {
				$("#comment-emotes"+f1+" em-emoji-picker").css("left", "39px");
				$(".posted-comments > .comments_activity > .response_holder#comment-emotes"+f1+" em-emoji-picker").css("left", "39px");
			}
			});

			$(document).on({click: function(e){
			var $div = $("#comment-emotes"+f1+" em-emoji-picker");var $targetElement = $(e.target);var excludedDivIds = ["comment-emotes"+f1]; var excludedTextareaId = "r-"+f1;
			var isClickInsideDiv = $div.find($targetElement).length > 0;
			var isExcludedDiv = excludedDivIds.some(function(excludedId) {return $("#" + excludedId).find($targetElement).length > 0;});
			var isExcludedTextarea = $targetElement.is("#" + excludedTextareaId);
			if (!isClickInsideDiv && !isExcludedDiv && !isExcludedTextarea){$("#comment-emotes"+f1+" em-emoji-picker").css("display", "none");$("#comment-emotes"+f1+" i").removeClass("icon-smiley2").addClass("icon-smiley")}
			}});
			';
            }
            $ht_js .= '}},".comm-reply");'; //recheck

            /* edit comm */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text();';
            $ht_js .= 'var ct = "' . $cfg['file_comment_max_length'] . '";
				var ht = \'<div class="comment-edit ce-\'+f1+\'"><span id="cpe-pos\'+f1+\'" class="cp-pos">0</span><div class="comment_left_char tfr"><p class="rem_char reply_char"><span class="greyed-out ecomm-switch\'+f1+\'"><span id="ecomm-char-remaining\'+f1+\'">\'+(ct-($(".comm-own"+f1+">a").html().length+3))+\'</span> ' . $language['view.files.comm.char'] . '</span></p></div><form class="entry-form-class-off" id="comm-edit-form\'+f1+\'"><input type="hidden" name="comm_edit" value="\'+f1+\'" /><input type="hidden" name="comm_type" value="' . $class_filter->clr_str($type) . '" /><input type="hidden" name="comm_uid" value="' . (int) $vuid . '" /><textarea name="file_comm_text" id="e-\'+f1+\'" class="textarea-input comm-edit-action" rows="1" cols="1">\'+$(".comm-own"+f1+":first").next().text().trim()+\'</textarea><div class="comm-switch\'+f1+\'"><div class="comments_actions main_comments_actions" style="display:flex"><div id="ecomment-emotes\'+f1+\'" class="comment-emotes"><i class="icon-smiley"></i></div><a class="comm-cancel-action comm-cancel-edit link cancel-trigger" href="javascript:;" onclick="$(&quot;.ce-\'+f1+\'&quot;).replaceWith(&quot;&quot;);$(&quot;.comm-own\'+f1+\', #' . $type . '-comment\'+f1+\' .comm-body, #comm-actions2-over\'+f1+\', .comment-rating\'+f1+\', #comm-actions-main\'+f1+\'&quot;).show();">' . $language['frontend.global.cancel'] . '</a><button onfocus="blur();" value="1" type="button" class="search-button edit-comment-button symbol-button" id="pe-\'+f1+\'" name="post_file_comment"><span>' . $language['frontend.global.save'] . '</span></button></div></div></form></div>\';';
            $ht_js .= 'if(!$(".ce-"+f1).html()){$(".cr-"+f1).replaceWith("");$(".comm-own"+f1+", #' . $type . '-comment"+f1+" .comm-body, #comm-actions-main"+f1+", #comm-actions2-over"+f1+", .comment-rating"+f1).hide();$(ht).insertAfter("#comm-actions2-over"+f1);} $("#c-menu"+f1+".single-comm-action").click();';
            $ht_js .= '
			const textarea = document.getElementById("e-"+f1);
			textarea.addEventListener("focus", () => {
				const currentValue = textarea.value;
				textarea.value = "";
				textarea.value = currentValue;
				textarea.selectionStart = textarea.selectionEnd = textarea.value.length;
			});
			textarea.addEventListener("input", () => {
				textarea.style.height = "auto";
				textarea.style.height = (textarea.scrollHeight+1) + "px";
			});

			textarea.focus();
			';
            /* reply emoji */
            if ($cfg['comment_emoji'] == 1) {
                $ht_js .= '

			const pickerOptions = {
				theme: $("body").hasClass("dark") ? "dark" : "light", emojiButtonSize:30, emojiSize:20, perLine:12, skinTonePosition:"search", previewPosition:"none", autoFocus:true, dynamicWidth: true, onEmojiSelect: function(emoji){
					const textarea = document.getElementById("e-"+f1)
					const cursorPosition = textarea.selectionEnd
					const start = textarea.value.substring(0, textarea.selectionStart)
					const end = textarea.value.substring(textarea.selectionStart)
					const text = start + emoji.native + end
					textarea.value = text
					textarea.focus()
					textarea.selectionEnd = cursorPosition + emoji.native.length
				}
			}
			const picker = new EmojiMart.Picker(pickerOptions);
			$("#ecomment-emotes"+f1).append(picker);

			if(window.innerWidth<500){
				$("#ecomment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21)));
				$(".posted-comments > .comments_activity > .response_holder #ecomment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+72)));
			} else {
				$("#ecomment-emotes"+f1+" em-emoji-picker").css("left", "39px");
				$(".posted-comments > .comments_activity > .response_holder#ecomment-emotes"+f1+" em-emoji-picker").css("left", "39px");
			}
			window.addEventListener("resize",function(){
			if(window.innerWidth<500){
				$("#ecomment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21)));
				$(".posted-comments > .comments_activity > .response_holder #ecomment-emotes"+f1+" em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+72)));
			} else {
				$("#ecomment-emotes"+f1+" em-emoji-picker").css("left", "39px");
				$(".posted-comments > .comments_activity > .response_holder#ecomment-emotes"+f1+" em-emoji-picker").css("left", "39px");
			}
			});

			$(document).on({click: function(e){
			var $div = $("#ecomment-emotes"+f1+" em-emoji-picker");var $targetElement = $(e.target);var excludedDivIds = ["ecomment-emotes"+f1]; var excludedTextareaId = "e-"+f1;
			var isClickInsideDiv = $div.find($targetElement).length > 0;
			var isExcludedDiv = excludedDivIds.some(function(excludedId) {return $("#" + excludedId).find($targetElement).length > 0;});
			var isExcludedTextarea = $targetElement.is("#" + excludedTextareaId);
			if (!isClickInsideDiv && !isExcludedDiv && !isExcludedTextarea){$("#ecomment-emotes"+f1+" em-emoji-picker").css("display", "none");$("#ecomment-emotes"+f1+" i").removeClass("icon-smiley2").addClass("icon-smiley")}
			}});
			';
            }
            $ht_js .= '}},".comm-edit");'; //recheck

            /* click outside of emoji picker */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var $div = $("#ntm em-emoji-picker");var $targetElement = $(e.target);var excludedDivIds = ["comment-emotes","main-comments-actions"]; var excludedTextareaId = "textarea-content";';
            $ht_js .= 'var isClickInsideDiv = $div.find($targetElement).length > 0;';
            $ht_js .= 'var isExcludedDiv = excludedDivIds.some(function(excludedId) {return $("#" + excludedId).find($targetElement).length > 0;});';
            $ht_js .= 'var isExcludedTextarea = $targetElement.is("#" + excludedTextareaId);';
            $ht_js .= 'if (!isClickInsideDiv && !isExcludedDiv && !isExcludedTextarea){if(!$("#ntm em-emoji-picker").is(":hidden")){$("#ntm em-emoji-picker").css("display", "none");$("#comment-emotes i").removeClass("icon-smiley2").addClass("icon-smiley")}}';
            $ht_js .= '}});'; //done
            /* changing emoji picker theme light/dark */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'if(!$("body").hasClass("dark")){
				setTimeout(function(){
				$("em-emoji-picker").each(function(){
				const emojiPicker = this;//document.querySelector("em-emoji-picker");
				const shadowRoot = emojiPicker.shadowRoot || emojiPicker.attachShadow({ mode: "open" });
				const sectionElement = shadowRoot.querySelector("section");
				sectionElement.setAttribute("data-theme", "dark");
				});
				},1000);
				}else{
				setTimeout(function(){
				$("em-emoji-picker").each(function(){
				const emojiPicker = this;//document.querySelector("em-emoji-picker");
				const shadowRoot = emojiPicker.shadowRoot || emojiPicker.attachShadow({ mode: "open" });
				const sectionElement = shadowRoot.querySelector("section");sectionElement.setAttribute("data-theme", "light");
				});
				},1000);
				}';
            $ht_js .= '}},"#theme-switch-input1, #theme-switch-input2");'; //done
            /* size event listener */
            $ht_js .= '$(function(){if(window.innerWidth<500){$("em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21))) }else{$("em-emoji-picker").css("left", "39px")}});';
            $ht_js .= 'window.addEventListener("resize",function(){if(window.innerWidth<500){$("em-emoji-picker").css("min-width", window.innerWidth-20).css("left", (window.innerWidth-(window.innerWidth+21)))}else{$("em-emoji-picker").css("left", "39px")}});';
        }
        $ht_js .= '$(".comm-toggle-replies").each(function(){var t = $(this); var _id = t.attr("id").substr(3); if (typeof($("#"+_id+" .response_holder").html()) == "undefined"){t.detach();}});';
        /* linkify comments */
        $ht_js .= '$(".comment_h p").linkify({defaultProtocol: "https", validate: {email: function(value){return false}}, ignoreTags: ["script","style"]});';
        /* sort comments */
        $ht_js .= '$(function(){$("#entry-action-buttons").dlmenu({animationClasses:{classin:"dl-animate-in-5",classout:"dl-animate-out-5"}})});';
        /* show/hide comments */
        $ht_js .= '$(".showComments-eye").on("click", function(){$(\'#list-' . $type . '-comments, .comment_left_char\').toggle();$(\'#comm-post-form\').stop().slideToggle(\'fast\')});';

        /* comm post response */
        $comm_btn = ($cfg['channel_comments'] == 1 ? '<span class="place-right showSingle-lb-off showComments-eye mt-15" target="comments"><i class="icon-eye-blocked" rel="tooltip" title="' . $language['subnav.entry.comments'] . '"></i></span>' : null);
        $comm_all = '<span class="right-float right-padding10 lh20"><span id="total-comm-nr">0</span></span>';

        $html = '
				<div class="page_holder comm-wrapper" id="' . $type . '-comm-wrapper">
					<div class="file-views-nr">
					    <div class="d-flex mt-15">
						<div class="views_counter">' . $comm_all . $language['view.files.comm.all'] . '</div>
						<button id="entry-action-buttons" class="dl-menuwrapper">
							<span class="dl-trigger actions-trigger nbfr"><i class="icon-menu" rel="tooltip" title="' . $language['view.files.comm.sort'] . '"></i> ' . $language['view.files.comm.sort.by'] . '</span>
							<ul class="dl-menu">
								<li><a href="#top-comments" class="sort-comments"><i class="' . ((!isset($_POST['file_comm_sort']) or (isset($_POST['file_comm_sort']) and $_POST['file_comm_sort'] == 'top-comments')) ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . $language['view.files.comm.sort.tc'] . '</a></li>
								<li><a href="#new-comments" class="sort-comments"><i class="' . ((isset($_POST['file_comm_sort']) and $_POST['file_comm_sort'] == 'new-comments') ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . $language['view.files.comm.sort.nc'] . '</a></li>
								<li><a href="#new-replies" class="sort-comments"><i class="' . ((isset($_POST['file_comm_sort']) and $_POST['file_comm_sort'] == 'new-replies') ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . $language['view.files.comm.sort.nr'] . '</a></li>
								' . ((int) $_SESSION['USER_ID'] == $vuid ? '
								<li><a href="#not-approved" class="sort-comments"><i class="' . ((isset($_POST['file_comm_sort']) and $_POST['file_comm_sort'] == 'not-approved') ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . $language['view.files.comm.sort.na'] . '</a></li>
								<li><a href="#spam-reports" class="sort-comments"><i class="' . ((isset($_POST['file_comm_sort']) and $_POST['file_comm_sort'] == 'spam-reports') ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . $language['view.files.comm.sort.sr'] . '</a></li>
								' : null) . '
							</ul>
						</button>
					    </div>
					    ' . $comm_btn . '
					</div>
					<div class="clearfix"></div>
					' . ($section == $href['see_comments'] ? VGenerate::advHTML(21) : null) . '
					' . VGenerate::simpleDivWrap('', 'comm-post-response', VGenerate::noticeTpl('', $msg_arr[1], $msg_arr[0])) . '

					<form id="comm-post-form" method="post" action="" class="entry-form-class-off">
					' . ($islogged ? '
					<div id="ntm" style="display: block;">

						<div class="usr_img">
							<img width="50" height="50" alt="' . $c_usr . '" src="' . VUseraccount::getProfileImage((int) $_SESSION['USER_ID']) . '">
						</div>
						<div class="comment_h_wrap">

							<textarea name="file_comm_text" class="comm-input-action" id="textarea-content" rows="1" placeholder="' . $language['view.files.add.comm'] . '"></textarea>

						<div class="comments_actions main_comments_actions" id="main-comments-actions" style="display:none">
							<div id="comment-emotes"><i class="icon-smiley"></i></div>
							<a href="javascript:;" class="comm-cancel-action link cancel-trigger" rel="nofollow" onclick="$(\'#textarea-content\').val(\'\');$(\'#ntm .main_comments_actions,#ntm em-emoji-picker\').hide();$(\'#comment-emotes i\').removeClass(\'icon-smiley2\').addClass(\'icon-smiley\')">' . $language['frontend.global.cancel'] . '</a>
							<button value="1" type="button" class="post-comment-button" id="btn-1-post_file_comment" name="post_file_comment"><i></i> ' . $language['view.files.comm.btn'] . '</button>
						</div>
						</div>
						<span id="cp-pos" class="cp-pos">0</span>

					</div>
					' : '<div class="comments_signin"><a href="' . $cfg['main_url'] . '/' . VHref::getKey("signin") . '?next=' . VGenerate::fileHref($type[0], $f_key) . '" rel="nofollow">' . $language['frontend.global.signin'] . '</a> ' . $language['frontend.global.or'] . ' <a href="' . $cfg['main_url'] . '/' . VHref::getKey("signup") . '" rel="nofollow">' . $language['frontend.global.createaccount'] . '</a> ' . $language['view.files.comm.post'] . '</div>'
        ) . '
					' . VGenerate::simpleDivWrap('no-display', '', '<input type="hidden" name="file_comm_sort" class="comm-input-sort" id="comm-sort" value="' . (isset($_POST['file_comm_sort']) ? $class_filter->clr_str($_POST['file_comm_sort']) : 'top-comments') . '"><input type="hidden" name="comm_type" value="' . $class_filter->clr_str($type) . '" /><input type="hidden" name="comm_uid" value="' . (int) $vuid . '" />') . '
					</form>
					<div class="clearfix"></div>
					' . VGenerate::simpleDivWrap('posted-comments', 'list-' . $type . '-comments', self::listFileComments($type, $vuid)) . ($section == $href['see_comments'] ? VGenerate::advHTML(22) : null) . '
				</div>
		';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $ht_js . '});

' . ($_SESSION['USER_ID'] > 0 ? '
			var textarea = document.getElementById("textarea-content");
			textarea.addEventListener("input", () => {
				textarea.style.height = "auto";
				textarea.style.height = (textarea.scrollHeight+1) + "px";
			});
			textarea.addEventListener("focus", () => {
				$("#main-comments-actions").show();
			});
			var pickerOptions = {
				theme: $("body").hasClass("dark") ? "dark" : "light", emojiButtonSize:30, emojiSize:20, perLine:12, skinTonePosition:"search", previewPosition:"none", autoFocus:true, dynamicWidth: true, onEmojiSelect: function(emoji){
					const textarea = document.getElementById("textarea-content")
					const cursorPosition = textarea.selectionEnd
					const start = textarea.value.substring(0, textarea.selectionStart)
					const end = textarea.value.substring(textarea.selectionStart)
					const text = start + emoji.native + end
					textarea.value = text
					textarea.focus()
					textarea.selectionEnd = cursorPosition + emoji.native.length
				}
			}
			var picker = new EmojiMart.Picker(pickerOptions);
			$("#comment-emotes").append(picker);
' : null) . '
		function opencheck(k){$(".comments_activity").each(function(){var t = $(this); var _id = t.attr("id"); if (t){ if (typeof(t.find("#"+k).html()) != "undefined") {setTimeout(function () {$("#"+_id+" > .response_holder").css("display", "block"); $("#"+_id+" .comm-toggle-replies").find("i").removeClass("iconBe-chevron-down").addClass("iconBe-chevron-up"); }, 1);} } }); }
		');

        return $html;
    }

    /* listing comments */
    public function listFileComments(string $type, int $vuid, $getReplies = false)
    {
        $db           = self::$db;
        $class_filter = self::$filter;
        $cfg          = self::$cfg;
        $language     = self::$language;
        $section      = self::$section;
        $href         = self::$href;
        $file_key     = (int) self::$file_key;
        $ch_cfg       = self::$ch_cfg;
        $vuid         = $vuid == 0 ? self::$user_id : $vuid;

        if ($type == '' or $vuid == '' or $file_key == '') {
            return;
        }

        $ch_cfg['ch_comm_spam'] = 1;

        /* get blocked users */
        $c_blocked = $db->execute(sprintf("SELECT
							A.`ct_id`, A.`usr_id`, B.`usr_id` AS `blocked_usr_id`
							FROM `db_usercontacts` A, `db_accountuser` B
							WHERE
							A.`usr_id`='%s' AND A.`ct_blocked`='1' AND A.`ct_active`='1' AND A.`ct_username`=B.`usr_user`;",
            $vuid));
        if ($c_blocked->fields['ct_id']) {
            $u_blocked = array();

            while (!$c_blocked->EOF) {
                $u_blocked[] = $c_blocked->fields['blocked_usr_id'];

                $c_blocked->MoveNext();
            }
        }
        /* owner details */
        $u_info = VUserinfo::getUserInfo($vuid);
        $u_name = VNotify::displayName($u_info);
        /* is owner */
        $edit = $vuid == (int) $_SESSION['USER_ID'] ? 1 : 0;
        /* paging */
        //$cpp        = $section != $href['see_comments'] ? 15 : 50; //paging limit
        $cpp     = 15;
        $page_nr = $_GET['page'] > 1 ? intval($_GET['page']) : 1;
        //$page_nr    = isset($_GET['page']) ? intval($_GET['page'])+1 : 1;
        $s_from  = !$getReplies ? ($page_nr - 1) * $cpp : 0;
        $reply_t = !$getReplies ? "`c_replyto`=''" : sprintf("`c_replyto`='%s'", $getReplies['replyto']);
        $reply_s = !$getReplies ? "A.`c_replyto`=''" : sprintf("A.`c_replyto`='%s'", $getReplies['replyto']);
        /* sort by */
        $sort_by         = isset($_POST['file_comm_sort']) ? $class_filter->clr_str($_POST['file_comm_sort']) : (isset($_POST['comm_sort']) ? $class_filter->clr_str($_POST['comm_sort']) : 'top-comments');
        $sort_approved   = "AND A.`c_approved`='1'";
        $sort_total      = "AND `c_approved`='1'";
        $sort_spam       = null;
        $sort_spam_total = null;

        switch ($sort_by) {
            default:
            case "top-comments":
                $sort_sql = 'c_rating_value';
                break;

            case "new-comments":
                $sort_sql = 'c_id';
                break;

            case "new-replies":
                $interval      = 72;
                $sort_approved = $edit == 1 ? "AND A.`c_approved`>='0' AND A.`c_datetime` > NOW() - INTERVAL $interval HOUR" : "AND A.`c_approved`='1' AND A.`c_datetime` > NOW() - INTERVAL $interval HOUR";
                $sort_total    = $edit == 1 ? "AND `c_approved`>='0' AND `c_datetime` > NOW() - INTERVAL $interval HOUR" : "AND `c_approved`='1' AND `c_datetime` > NOW() - INTERVAL $interval HOUR";
                $sort_sql      = 'c_id';
                $reply_t       = "`c_replyto`!=''";
                $reply_s       = "A.`c_replyto`!=''";

                $getReplies = false;
                break;

            case "not-approved":
                if ($edit == 1) {
                    $sort_approved = "AND A.`c_approved`='0'";
                    $sort_total    = "AND `c_approved`='0'";
                    $sort_sql      = 'c_approved';
                    $reply_t       = "`c_replyto`=''";
                    $reply_s       = "A.`c_replyto`=''";

                    $getReplies = false;
                }
                break;

            case "spam-reports":
                if ($edit == 1) {
                    $sort_approved   = "AND A.`c_approved`>='0'";
                    $sort_total      = "AND `c_approved`>='0'";
                    $sort_spam       = "AND A.`c_spam`!=''";
                    $sort_spam_total = "AND `c_spam`!=''";
                    $sort_sql        = 'c_spam';
                    $reply_t         = "`c_replyto`=''";
                    $reply_s         = "A.`c_replyto`=''";

                    $getReplies = false;
                }
                break;
        }

        $ttotal_sql = sprintf("SELECT COUNT(*) AS `total` FROM `db_%scomments` WHERE %s `file_key`='%s' %s %s", $type, (isset($u_blocked[0]) ? sprintf("`c_usr_id` NOT IN(%s) AND", implode(",", $u_blocked)) : null), $file_key, ($edit == 1 ? $sort_spam_total : null), $sort_total);
        $total_sql  = sprintf("SELECT COUNT(*) AS `total` FROM `db_%scomments` WHERE %s %s AND `file_key`='%s' %s %s", $type, (isset($u_blocked[0]) ? sprintf("`c_usr_id` NOT IN(%s) AND", implode(",", $u_blocked)) : null), $reply_t, $file_key, ($edit == 1 ? $sort_spam_total : null), $sort_total);

        if ((($edit == 1 and ($sort_by == 'not-approved' or $sort_by == 'spam-reports' or $sort_by == 'new-replies-off')) or ($edit == 0 and $sort_by == 'new-replies-off')) and !$getReplies) {
            $total_sql = str_replace($reply_t . " AND", "", $total_sql);
        }
/*
$comm_sql    = sprintf("SELECT
A.`c_id`, A.`c_usr_id`, A.`c_key`, A.`c_body`, A.`c_datetime`,
A.`c_approved`, A.`c_rating`, A.`c_replyto`, A.`c_edited`, A.`c_pinned`,
B.`usr_user`, B.`usr_id`, B.`usr_key`, B.`usr_partner`, B.`usr_affiliate`,
B.`affiliate_badge`, B.`usr_dname`, B.`ch_title`, C.`comment_spam`, C.`comment_votes` ////////////// check the 2 C.
FROM `db_%scomments` A
JOIN `db_accountuser` B ON A.`c_usr_id`=B.`usr_id`
JOIN `db_%sfiles` C ON A.`file_key`=C.`file_key`
WHERE %s %s AND A.`file_key`='%s' %s %s
AND A.`c_active`='1'
ORDER BY
CASE WHEN A.`c_pinned`='1' THEN 0 ELSE 1 END, `%s` DESC
LIMIT %s, %s;
", $type, $type, (isset($u_blocked[0]) ? sprintf("`c_usr_id` NOT IN(%s) AND", implode(",", $u_blocked)) : null), $reply_s, $file_key, $sort_approved, $sort_spam, $sort_sql, $s_from, $cpp);
 */
        $comm_sql = sprintf("SELECT
					A.`c_id`, A.`c_usr_id`, A.`c_key`, A.`c_body`, A.`c_datetime`,
					A.`c_approved`, A.`c_rating`, A.`c_replyto`, A.`c_edited`, A.`c_pinned`,
					B.`usr_user`, B.`usr_id`, B.`usr_key`, B.`usr_partner`, B.`usr_affiliate`,
					B.`affiliate_badge`, B.`usr_dname`, B.`ch_title`
					FROM `db_%scomments` A
					JOIN `db_accountuser` B ON A.`c_usr_id`=B.`usr_id`
					WHERE %s %s AND A.`file_key`='%s' %s %s
					AND A.`c_active`='1'
					ORDER BY
					CASE WHEN A.`c_pinned`='1' THEN 0 ELSE 1 END, `%s` DESC
					LIMIT %s, %s;
		", $type, (isset($u_blocked[0]) ? sprintf("`c_usr_id` NOT IN(%s) AND", implode(",", $u_blocked)) : null), $reply_s, $file_key, $sort_approved, $sort_spam, $sort_sql, $s_from, $cpp);

        if ((($edit == 1 and ($sort_by == 'not-approved' or $sort_by == 'spam-reports' or $sort_by == 'new-replies-off')) or ($edit == 0 and $sort_by == 'new-replies-off')) and !$getReplies) {
            $comm_sql = str_replace($reply_s . " AND", "", $comm_sql);
        }
        if ($sort_by == 'top-comments') {
            $comm_sql = str_replace("`c_rating_value`", "A.`c_rating_value` DESC, A.`c_id`", $comm_sql);
        }

        $comm_db   = self::$db_cache ? $db->CacheExecute($cfg['cache_view_comments'], $comm_sql) : $db->execute($comm_sql);
        $ttotal_db = self::$db_cache ? $db->CacheExecute($cfg['cache_view_comments'], $ttotal_sql) : $db->execute($ttotal_sql);
        $total_db  = self::$db_cache ? $db->CacheExecute($cfg['cache_view_comments'], $total_sql) : $db->execute($total_sql);
        $total_nr  = (int) $total_db->fields['total'];
        $ttotal_nr = (int) $ttotal_db->fields['total'];
        $comm_rs   = $comm_db->getrows();

        $pages = ceil($total_nr / $cpp);

        if ($total_nr > 0) {
            $html = null;

            foreach ($comm_rs as $key => $val) {
                $c_key      = $val['c_key'];
                $c_body     = $val['c_body'];
                $c_usr_name = $val['usr_user'];
                $c_usr_key  = $val['usr_key'];
                $c_dusr     = $val['usr_dname'];
                $ch_usr     = $val['ch_title'];
                $c_usr      = $c_dusr != '' ? $c_dusr : ($ch_usr != '' ? $ch_usr : $c_usr_name);
                $c_replyto  = $val['c_replyto'];
                $c_edited   = $val['c_edited'];
                $c_pinned   = $val['c_pinned'];

                $val['comment_votes']      = 1;
                $cfg['file_comment_votes'] = 1;
                $val['comment_spam']       = 1;

                $c_date  = VUserinfo::timeRange($val['c_datetime']);
                $c_date  = $c_date != '' ? $c_date : $language['frontend.global.now'];
                $usr_lnk = VHref::channelURL(["username" => $c_usr_name]);

                $add = 0;
                /* comment html */
                if ($val['comment_votes'] == 1 and $cfg['file_comment_votes'] == 1) {
                    $c_rate = $val['c_rating'] != '' ? unserialize($val['c_rating']) : '';
                    if ($c_rate != '') {
                        $votes = array();
                        foreach ($c_rate as $k => $v) {
                            $add .= $v;
                            if ($k == $_SESSION['USER_ID']) {
                                $votes[$c_key] = (int) $v;
                            }
                        }
                        $t_rate = self::calculateString($add);
                    } else {
                        $t_rate = 0;
                    }
                }

                /* user thumbnail */
                $cfg['channel_comments_avatar'] = 1;
                $more_menu                      = false;
                $more_ht                        = '<ul id="" class="accordion cacc">';
                $thumb_ht                       = $cfg['channel_comments_avatar'] == 1 ? VGenerate::simpleDivWrap('comment-user', '', '<a href="' . $cfg['main_url'] . '/' . VHref::getKey("user") . '/' . VUserinfo::getUserName($val['c_usr_id']) . '" rel="nofollow"><img class="" title="" alt="" src="' . VUseraccount::getProfileImage($val['c_usr_id']) . '" width="50" height="45" /></a>') : null;
                /* pin, unpin link */
                if ($edit == 1) {
                    $more_ht .= '<li><button title="' . ($c_pinned == 1 ? $language['view.files.comm.unpin'] : $language['view.files.comm.pin']) . '" type="button" class="search-button comm-action-entry comm-' . ($c_pinned == 1 ? 'unpin' : 'pin') . '" id="p' . $c_key . '" rel="tooltip-off"><i class="icon-pushpin"></i><span>' . ($c_pinned == 1 ? $language['view.files.comm.unpin'] : $language['view.files.comm.pin']) . '</span></button></li>';
                }
                /* delete link */
                $delete_ht = ($edit == 1 or $val['c_usr_id'] == $_SESSION['USER_ID']) ? 1 : null;
                /* spam link */
                $spam_ht      = (((int) $_SESSION['USER_ID'] > 0 and $val['c_usr_id'] != (int) $_SESSION['USER_ID'] and $val['comment_spam'] == 1) ? ($delete_ht != '' ? '<span class="row"></span>' : null) . '<a href="javascript:;" rel="nofollow" class="white-bg comm-spam comm-action-entry">' . $language['upage.text.comm.spam.action'] . '</a>' : null);
                $ht_spam      = ($edit == 1 and $ch_cfg['ch_comm_spam'] == 1) ? self::spamCommentCount($type, $val['c_key']) : 0;
                $ht_spam_link = ($edit == 1 and $ch_cfg['ch_comm_spam'] == 1 and $ht_spam > 0 and $val['comment_spam'] == 1) ? '<div class="comm-spam-thumb left-float left-margin5"></div><div class="left-float left-padding3"><span class="">' . $language['view.files.comm.reports'] . '</span> <a href="javascript:;" rel="nofollow" class="' . ($ht_spam > 0 ? 'show-spamrep' : null) . '" id="sr' . $val['c_key'] . '" onclick="$(\'#spam-rep-' . $c_key . '\').toggle();">[' . $ht_spam . ']</a></div>' : null;

                /* comment voting */
                $vote_ht = null;
                if ($val['comment_votes'] == 1 and $cfg['file_comment_votes'] == 1) {
                    $vote_ht .= '<button type="button" class="comm-like-action" id="cd-' . $c_key . '"><i class="icon-thumbs-up' . ($votes[$c_key] == 1 ? ' active' : null) . '" rel="tooltip" title="' . $language['frontend.global.like'] . '"></i></button>';
                    $vote_ht .= '<button type="button" class="comm-dislike-action" id="cl-' . $c_key . '"><i class="icon-thumbs-up2' . ($votes[$c_key] == -1 ? ' active' : null) . '" rel="tooltip" title="' . $language['frontend.global.dislike'] . '"></i></button>';
                }
                /* reply */
                if ($_SESSION['USER_ID'] > 0) {
                    $vote_ht .= '<button title="' . $language['frontend.global.reply'] . '" type="button" class="search-button comm-action-entry comm-reply" id="cr-' . $c_key . '"><span>' . $language['frontend.global.reply'] . '</span></button>';
                }
                /* edit */
                if ($val['c_usr_id'] == $_SESSION['USER_ID']) {
                    $more_ht .= '<li><button title="' . $language['frontend.global.edit'] . '" type="button" class="search-button comm-action-entry comm-edit" id="e' . $c_key . '" rel="tooltip-off"><i class="icon-pencil"></i><span>' . $language['frontend.global.edit'] . '</span></button></li>';
                    $more_menu = true;
                }
                /* approve, suspend */
                if ($edit == 1) {
                    $_label    = ($val['c_approved'] == 0 ? $language['frontend.global.approve'] : $language['frontend.global.suspend.cap']);
                    $_class    = ($val['c_approved'] == 0 ? 'comm-approve' : 'comm-suspend');
                    $_class_ht = ($val['c_approved'] == 0 ? ' comm-suspended' : '');
                    $more_ht .= '<li class="' . ($val['c_approved'] == 1 ? 'no-display' : null) . '"><button title="' . $language['frontend.global.approve'] . '" type="button" class="search-button comm-action-entry comm-approve" id="cs-' . $c_key . '" rel="tooltip-off"><i class="icon-check"></i><span>' . $language['frontend.global.approve'] . '</span></button></li>';
                    $more_ht .= '<li class="' . ($val['c_approved'] == 0 ? 'no-display' : null) . '"><button title="' . $language['frontend.global.suspend.cap'] . '" type="button" class="search-button comm-action-entry comm-suspend" id="cs-' . $c_key . '" rel="tooltip-off"><i class="icon-power-off"></i><span>' . $language['frontend.global.suspend.cap'] . '</span></button></li>';
                    $more_menu = true;
                }
                /* delete */
                if ($edit == 1 or $val['c_usr_id'] == $_SESSION['USER_ID']) {
                    $more_ht .= '<li><button title="' . $language['frontend.global.delete'] . '" type="button" class="search-button comm-action-entry comm-delete" id="d' . $c_key . '" rel="tooltip-off"><i class="icon-times"></i><span>' . $language['frontend.global.delete'] . '</span></button></li>';
                    $more_menu = true;
                }
                /* block user */
                if ($edit == 1 and $val['c_usr_id'] !== $_SESSION['USER_ID']) {
                    $more_ht .= '<li class=""><button title="' . str_replace('##USER##', $c_usr, $language['view.files.comm.block']) . '" type="button" class="search-button comm-action-entry comm-block" id="cb-' . $c_key . '" rel="tooltip-off"><i class="icon-blocked"></i><span>' . str_replace('##USER##', $c_usr, $language['view.files.comm.block']) . '</span></button></li>';
                }
                /* spam */
                if ($_SESSION['USER_ID'] > 0 and $edit == 0 and $_SESSION['USER_ID'] != $vuid and $val['c_usr_id'] != $vuid and $val['c_usr_id'] != $_SESSION['USER_ID'] and $val['comment_spam'] == 1) {
                    $more_ht .= '<li><button title="' . $language['frontend.global.spam'] . '" type="button" class="search-button comm-action-entry comm-spam" id="s' . $c_key . '" rel="tooltip-off"><i class="icon-lock"></i><span>' . $language['view.files.comm.btn.spam.rep'] . ' (' . $ht_spam . ')</span></button></li>';
                    $more_menu = true;
                }
                $more_ht .= '</ul>';

                if ($ht_spam > 0) {
                }
                /* spam reports */
                $spam_rep = (($edit == 1)) ? VGenerate::simpleDivWrap('', 'wrapsr' . $c_key, self::listSpamReports($c_key, $type)) : VGenerate::simpleDivWrap('no-display', '', '');
                /* comm key form */
                $ckey_ht = '<div class="no-display comm-actions-hkey">' . $c_key . '</div>';
                /* all actions */
                $comm_actions = $_SESSION['USER_ID'] > 0 ? VGenerate::simpleDivWrap('ucls-links', '', '<div class="">' . $vote_ht . '</div>' . $ckey_ht) : null;
                $more_actions = $_SESSION['USER_ID'] > 0 ? VGenerate::simpleDivWrap('ucls-links', '', $more_ht . $ckey_ht) : null;
                $more_actions .= $_SESSION['USER_ID'] > 0 ? VGenerate::simpleDivWrap('yy-bg spam-rep-list', 'spam-rep-' . $c_key, $spam_rep, 'display: none;') : null;
                /* @username regex */
                $pattern = '/@(\w+)/';
                preg_match_all($pattern, $c_body, $matches);
                if (isset($matches[1])) {
                    $usernames = $matches[1];
                    foreach ($usernames as $username) {
                        $c_usr_key = self::$dbc->singleFieldValue('db_accountuser', 'usr_key', 'usr_user', $username);
                        if ($c_usr_key == '') {
                            $c_usr_key = self::$dbc->singleFieldValue('db_accountuser', 'usr_key', 'usr_dname', $username);
                        }
                        if ($c_usr_key == '') {
                            $c_usr_key = self::$dbc->singleFieldValue('db_accountuser', 'usr_key', 'ch_dname', $username);
                        }
                        if ($c_usr_key != '') {
                            $c_body = str_replace('@' . $username, '<a class="f-14" href="' . VHref::channelURL(["username" => $username]) . '">@' . $username . '</a>', $c_body);
                        }
                    }
                }

                $html .= '	' . ((($c_replyto > 0 and $sort_by != 'not-approved' and $sort_by != 'spam-reports' and $sort_by != 'new-replies') ? '<div class="response_holder" style="display:none">' : null)) . '
					<div id="' . $c_key . '" class="comments_activity' . (($c_replyto > 0 and $sort_by != 'not-approved' and $sort_by != 'spam-reports' and $sort_by != 'new-replies') ? ' response' : null) . $_class_ht . '" rel-resp="' . $c_replyto . '">
						<div class="usr_img">
							<a href="' . $usr_lnk . '" rel="nofollow"><img width="50" height="50" alt="' . $c_usr . '" src="' . VUseraccount::getProfileImage($val['c_usr_id']) . '"></a>
							<div class="comment-rating comment-rating' . $c_key . ($t_rate > 0 ? ' conf-green' : ' err-red') . (!isset($_SESSION['USER_ID']) ? ' no-user' : null) . '">' . ($t_rate > 0 ? '(+' . $t_rate . ')' : ($t_rate < 0 ? '(' . $t_rate . ')' : null)) . '</div>
						</div>
						<div class="comment_h" id="' . $type . '-comment' . $c_key . '">
							<div class="comm-own-holder comm-own' . $c_key . '">
								' . ($c_pinned == 1 ? '<span class="c-pinned"><i class="icon-pushpin"></i> ' . str_replace('##USER##', $u_name, $language['view.files.comm.pinned']) . '</span>' : null) . '
								<a href="' . $usr_lnk . '" class="' . ($val['c_usr_id'] == $vuid ? 'comm-owner' : null) . '" rel-usr="' . $c_usr_name . '">' . VAffiliate::affiliateBadge((($val['usr_affiliate'] == 1 or $val['usr_partner'] == 1) ? 1 : 0), $val['affiliate_badge']) . $c_usr . '</a>
								<span class="posted_on">' . $c_date . '</span>
								<span class="edited">' . ($val['c_edited'] == 1 ? $language['view.files.comm.edited'] : null) . '</span>
								<span class="comm-pending">' . ($val['c_approved'] == 0 ? $language['frontend.global.not.approved'] : null) . '</span>
							</div>
							<p class="comm-body">' . trim($c_body) . '</p>
							<div class="clearfix"></div>
							<div class="likes_holder">
								<div class="comment-wrap comment-bg' . ($val['c_usr_id'] == intval($_SESSION['USER_ID']) ? ' my-comm' : null) . '">
									<div class="comm-over-off" id="comm-actions-main' . $c_key . '">' . $comm_actions . '</div>
									<div class="no-display comm-over-off" id="comm-actions2-over' . $c_key . '">
									' . ($more_menu ? '
										<span class="no-session-icon place-right" onclick="if($(this).parent().parent().find(\'#comment-actions-dd' . $c_key . '\').is(\':visible\')){$(this).parent().parent().find(\'#comment-actions-dd' . $c_key . '\').hide();if(typeof $(this).parent().parent().find(\'#wrapsr' . $c_key . '\').html()!=\'undefined\' && $(this).parent().parent().find(\'#wrapsr' . $c_key . '\').html().length>0){$(this).parent().parent().find(\'#spam-rep-' . $c_key . '\').hide()}}else{$(this).parent().parent().find(\'.comment-actions-dd\').hide();$(this).parent().parent().find(\'#comment-actions-dd' . $c_key . '\').show();if(typeof $(this).parent().parent().find(\'#wrapsr' . $c_key . '\').html()!=\'undefined\' && $(this).parent().parent().find(\'#wrapsr' . $c_key . '\').html().length>0){$(this).parent().parent().find(\'#spam-rep-' . $c_key . '\').show()}}"><i class="mt-open"></i></span>
									' : null) . '
									</div>
									' . ($more_menu ? '
										<div class="comment-actions-dd" id="comment-actions-dd' . $c_key . '" style="display:none">
										<div class="blue">
										' . $more_actions . '
										</div>
										</div>
									' : null) . '

									' . (($c_replyto == '0' and $sort_by != 'not-approved' and $sort_by != 'spam-reports' and $sort_by != 'new-replies') ? '<div class="clearfix"></div><div class="comm-replies-show"><button id="cs-' . $c_key . '" class="search-button comm-toggle-replies" type="button" onclick="$(this).find(\'i\').toggleClass(\'iconBe-chevron-up iconBe-chevron-down\');$(this).closest(\'.comments_activity\').find(\'.response_holder\').toggle();$(\'div[rel-resp=' . $c_key . '] .comm-body\').each(function(){h=$(this).height();$(this).parent().find(\'[id^=comm-actions2-over] span\').css(\'top\',-h);$(this).parent().find(\'[id^=comment-actions-dd] .accordion.cacc\').css(\'top\',-h);});"><i class="iconBe-chevron-down" rel="tooltip" title="' . $language['view.files.replies.toggle'] . '"></i><span><span class="rnr">##</span> ' . $language['view.files.replies'] . '</span></button><button id="xx-' . $c_key . '" class="search-button comm-toggle-replies-quick no-display" type="button"><i class="iconBe-chevron-down" rel="tooltip" title="' . $language['view.files.replies.toggle'] . '"></i><span>' . $language['view.files.replies'] . '</span></button></div>' : null) . '
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
						' . (($sort_by != 'not-approved' and $sort_by != 'spam-reports' and $sort_by != 'new-replies') ? self::listFileComments($type, $vuid, array("replyto" => $c_key)) : null) . '
					</div>
					' . ((($c_replyto > 0 and $sort_by != 'not-approved' and $sort_by != 'spam-reports' and $sort_by != 'new-replies') ? '</div>' : null)) . '
					';

                echo ((isset($_POST['c_key']) and $class_filter->clr_str($_POST['c_key']) == $c_key) ? VGenerate::declareJS('opencheck("' . $c_key . '");') : null);

                if (($key + 1) == $cpp) {
                    break;
                }
            }

            /* pagination links */
            //if ($pages > 1 and !$getReplies) {
            if ($page_nr == 1 and !$getReplies) {
                $p    = 10; //number of page links to show
                $j    = ($page_nr >= $p) ? (floor($page_nr / $p) * $p) : 1;
                $j_gt = ($page_nr >= $p) ? ((floor($page_nr / $p) * $p) + ($p - 1)) : ($p - 1);

                $pag = '<center><span id="comm-spinner" style="padding: 10px; display:none"></span></center>';

                $pag .= '<center><span class="' . ($page_nr != 1 ? 'comm-page-prev pointer' : 'comm-page-start') . ' left-float no-display">' . ($page_nr != 1 ? '<a class="comm-cancel-action link cancel-trigger prev-user-comm1 paginate paginate-prev" href="javascript:;">' . $language['frontend.global.previous'] . '</a>' : '<span class="inactive">' . $language['frontend.global.previous'] . '</span>') . '</span>';

                $pag .= '<span class="' . ($page_nr != $pages ? 'comm-page-next pointer' : 'comm-page-end') . ' left-float no-display">' . ($page_nr != $pages ? '<a class="comm-cancel-action link cancel-trigger next-user-comm1 paginate paginate-next" href="javascript:;">' . $language['frontend.global.next'] . '</a>' : '<span class="inactive">' . $language['frontend.global.next'] . '<span>') . '</span></center>';

                $pag .= VGenerate::simpleDivWrap('no-display', '', '<span id="cnr">' . $page_nr . '</span>');
            }

            $html .= ($page_nr == 1 and $total_nr > 0 and $pag != '' and !$getReplies) ? VGenerate::simpleDivWrap('row wdmax left-float comm-pag', '', $pag) : null;

            $html .= (!$getReplies) ? VGenerate::declareJS('var tt = ' . $ttotal_nr . '; if(tt < 10){$("#total-comm-nr").parent().addClass("no-display");}else{$("#total-comm-nr").html("' . $ttotal_nr . '");}$("#list-' . $type . '-comments > .comments_activity").each(function(){rnr=$(this).find(".response_holder").length;if(rnr==1){$(this).find(".rnr").parent().text(rnr + " ' . $language['view.files.reply'] . '")}else{$(this).find(".rnr").text(rnr)}});' . (($page_nr >= 1 and $page_nr == $pages) ? '$(".comm-pag").detach();' : null)) : null;

            if ($sort_by == 'not-approved' or $sort_by == 'spam-reports' or $sort_by == 'new-replies') {
                $html .= VGenerate::declareJS('$("#total-comm-nr").text($("div.comments_activity").length);$("#total-comm-nr").parent().removeClass("no-display");');
            }
        }

        return $html;
    }

    /* see all comments (on separate page) */
    public function seeAllComments()
    {
        return;
    }

    /* count comment spams */
    public function spamCommentCount($type, $c_key)
    {
        $class_database = self::$dbc;

        $db_s = $class_database->singleFieldValue('db_' . $type . 'comments', 'c_spam', 'c_key', $c_key);

        return $db_count = $db_s == '' ? 0 : count(unserialize($db_s));
    }

    /* list spam reports */
    public function listSpamReports($c_key, $type)
    {
        $cfg            = self::$cfg;
        $class_database = self::$dbc;

        $i      = 0;
        $db_t   = 'db_' . $type . 'comments';
        $db_s   = $class_database->singleFieldValue($db_t, 'c_spam', 'c_key', $c_key);
        $db_arr = unserialize($db_s);

        if (is_array($db_arr)) {
            $html = '<label>Spam Reports</label>';
            foreach ($db_arr as $key => $val) {
                $i   = $i + 1;
                $usr = VUserinfo::getUserName($key);
                $html .= VGenerate::simpleDivWrap('left-float row bottom-border-dotted top-padding5 csrep-wrap', '', VGenerate::simpleDivWrap('left-float', '', $i . '.') . '<a class="left-float nodecoration font11" href="' . $cfg['main_url'] . '/' . VHref::getKey('user') . '/' . $usr . '">' . VUserinfo::truncateString($usr, 10) . '</a><span class="left-padding10 right-float">' . VUserinfo::timeRange($val) . '</span>');
            }
        }

        return $html;
    }

    /* votes from string */
    public function calculateString($mathString)
    {
        $expr = trim($mathString);
        $expr = preg_replace('/[^0-9\+\-\*\/\(\) ]/', '', $expr);
        if ($expr !== '' && $expr[0] === '-') {
            $expr = '0' . $expr;
        }
        $expr = str_replace('(-', '(0-', $expr);
        return self::evaluateExpression($expr);
    }

    private static function evaluateExpression($expr)
    {
        $nums = [];
        $ops  = [];
        $len = strlen($expr);
        $i = 0;
        $precedence = function ($op) {
            if ($op === '+' || $op === '-') return 1;
            if ($op === '*' || $op === '/') return 2;
            return 0;
        };
        $apply = function (&$nums, $op) {
            if (count($nums) < 2) return;
            $b = array_pop($nums);
            $a = array_pop($nums);
            switch ($op) {
                case '+': $nums[] = $a + $b; break;
                case '-': $nums[] = $a - $b; break;
                case '*': $nums[] = $a * $b; break;
                case '/': $nums[] = ($b == 0 ? 0 : $a / $b); break;
            }
        };
        while ($i < $len) {
            $ch = $expr[$i];
            if ($ch === ' ') { $i++; continue; }
            if (ctype_digit($ch)) {
                $val = 0;
                while ($i < $len && ctype_digit($expr[$i])) {
                    $val = $val * 10 + (ord($expr[$i]) - 48);
                    $i++;
                }
                $nums[] = $val;
                continue;
            }
            if ($ch === '(') { $ops[] = $ch; $i++; continue; }
            if ($ch === ')') {
                while (!empty($ops) && end($ops) !== '(') { $op = array_pop($ops); $apply($nums, $op); }
                if (!empty($ops) && end($ops) === '(') array_pop($ops);
                $i++; continue;
            }
            if ($ch === '+' || $ch === '-' || $ch === '*' || $ch === '/') {
                while (!empty($ops) && $precedence(end($ops)) >= $precedence($ch)) { $op = array_pop($ops); $apply($nums, $op); }
                $ops[] = $ch; $i++; continue;
            }
            $i++;
        }
        while (!empty($ops)) { $op = array_pop($ops); $apply($nums, $op); }
        return (count($nums) ? 0 + $nums[0] : 0);
    }

    /* check for consecutive comment limit */
    public function commentCheck($type)
    {
        $db           = self::$db;
        $cfg          = self::$cfg;
        $class_filter = self::$filter;

        $match    = 0;
        $file_key = self::$file_key;
        $upage_id = (int) $_SESSION['USER_ID'];
        $e_sql    = sprintf("SELECT `c_usr_id` FROM `db_%scomments` WHERE `file_key`='%s' AND `c_active`='1' ORDER BY `c_id` DESC LIMIT %s;", $type, $file_key, $cfg['fcc_limit']);
        $chk      = self::$db_cache ? $db->CacheExecute($cfg['cache_view_comments_c_usr_id'], $e_sql) : $db->execute($e_sql);

        if ($chk) {
            while (!$chk->EOF) {
                if ($chk->fields['c_usr_id'] == $upage_id) {
                    $match = $match + 1;
                }
                @$chk->MoveNext();
            }
        }

        return $match;
    }

    /* post comment on files */
    public function postComment()
    {
        $class_database = self::$dbc;
        $class_filter   = self::$filter;
        $cfg            = self::$cfg;
        $db             = self::$db;
        $language       = self::$language;
        $ch_cfg         = self::$ch_cfg;

        if ($cfg['channel_comments'] == 0) {
            return false;
        }

        $can_post    = 0;
        $type        = $class_filter->clr_str($_POST['comm_type']);
        $file_key    = self::$file_key;
        $comment     = $class_filter->clr_str($_POST['file_comm_text']);
        $comment_len = strlen($comment);
        $upage_id    = (int) $_POST['comm_uid'];
        /* country check */
        $country = maxmind_country();
        if (is_array($cfg['maxmind_country']) and in_array($country, $cfg['maxmind_country'])) {
            return VChannelComments::commLayout($type, $upage_id, array($language['notif.success.request'], ''));
        }
        /* check ip quality score */
        if ($cfg['ipqualityscore']) {
            $result = check_ip_quality_score($_SERVER[REM_ADDR]);

            if (isset($result->data) and ($result->data['recent_abuse'] or $result->data['bot_status'])) {
                return VChannelComments::commLayout($type, $upage_id, array($language['notif.success.request'], ''));
            }
        }
        /* akismet spam check */
        if ($cfg['akismet']) {
            if (!is_array($cfg['akismet_country']) or (is_array($cfg['akismet_country']) and in_array($country, $cfg['akismet_country']))) {
                $is_spam = akismet_spam_check($comment);

                if ($is_spam) {
                    return VChannelComments::commLayout($type, $upage_id, array($language['notif.success.request'], ''));
                }
            }
        }
        /* proxy check */
        if ($cfg['proxycheck']) {
            $is_proxy = proxy_check($_SERVER[REM_ADDR]);

            if ($is_proxy) {
                return VChannelComments::commLayout($type, $upage_id, array($language['notif.success.request'], ''));
            }
        }
        /* comment permissions */
        $fr_db = $db->execute(sprintf("SELECT `ct_id` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' AND `ct_friend`='1' AND `ct_active`='1' LIMIT 1;", $upage_id, $_SESSION['USER_NAME']));
        $am_fr = ($fr_db->fields['ct_id'] > 0) ? 1 : 0;
        $am_fr = ($_SESSION['USER_ID'] > 0 and $_SESSION['USER_ID'] == $upage_id) ? 1 : $am_fr;
        switch ($ch_cfg['ch_comm_perms']) {
            case "free":$c_approved = 1;
                break;
            case "appr":$c_approved = 0;
                break;
            case "fronly":
            case "custom":$c_approved = $am_fr == 1 ? 1 : 0;
                break;
        }
        /* get block status */
        $is_bl = VContacts::getBlockStatus($upage_id, $_SESSION['USER_NAME']);
        /* check if blocked, but comments allowed */
        $bl_sub = VContacts::getBlockCfg('bl_comments', $upage_id, $_SESSION['USER_NAME']);
        /* determine if user can post */
        if ($_SESSION['USER_ID'] > 0 and ($is_bl == 0 or ($is_bl == 1 and $bl_sub == 0))) {
            $can_post = 1;
        }

        /* comment checks */
        $error_message = $can_post == 0 ? $language['notif.error.blocked.request'] : ((($comment_len < $cfg['file_comment_min_length']) or ($comment_len > $cfg['file_comment_max_length'])) ? $language['notif.error.invalid.request'] : ((self::commentCheck($type) >= $cfg['fcc_limit']) ? $language['upage.text.comm.limit'] : null));
        /* add comment entry */
        if ($error_message == '') {
            $c_key    = VUserinfo::generateRandomString(10);
            $comm_arr = array("file_key" => $file_key,
                "c_usr_id"                   => (int) $_SESSION['USER_ID'],
                "c_key"                      => $c_key,
                "c_body"                     => $comment,
                "c_datetime"                 => date("Y-m-d H:i:s"),
                "c_approved"                 => $c_approved,
            );
            if ($_GET['do'] == 'comm-reply') {
                $comm_arr['c_replyto'] = $class_filter->clr_str($_POST['comm_reply']);
            }
            $db_entry       = $class_database->doInsert('db_' . $type . 'comments', $comm_arr);
            $db_id          = $db->Insert_ID();
            $notice_message = $c_approved == 1 ? $language['notif.success.request'] : $language['upage.text.comm.posted.appr'];
            //$db_nr        = sprintf("UPDATE `db_%sfiles` SET `channel_comments`=`channel_comments`+1 WHERE `file_key`='%s' LIMIT 1;", $type, $file_key);
            //$db_update    = $db->execute($db_nr);
            /* log comments */
            $log = ($cfg['activity_logging'] == 1 and $action = new VActivity($_SESSION['USER_ID'], $upage_id)) ? $action->addTo('log_channelcomment', $type . ':' . $file_key . ':' . $c_key) : null;
            /* mailing */
            if ($class_database->singleFieldValue('db_accountuser', 'usr_mail_chancomment', 'usr_id', $upage_id) == 1) {
                $mail_do = VNotify::queInit('channel_comment', array(VUserinfo::getUserEmail($upage_id)), $db_id);
            }
            if ($_GET['do'] == 'comm-reply') {
                $mail_do = VNotify::queInit('channel_comment_reply', array(VUserinfo::getUserEmail($class_database->singleFieldValue('db_' . $type . 'comments', 'c_usr_id', 'c_key', $comm_arr['c_replyto']))), $db_id);

                $cu = $db->execute(sprintf("SELECT `c_usr_id` FROM `db_%scomments` WHERE `c_key`='%s' LIMIT 1;", $type, $class_database->singleFieldValue('db_' . $type . 'comments', 'c_replyto', 'c_key', $comm_arr['c_replyto'])));
                if ($cu->fields['c_usr_id'] > 0) {
                    $ci = $db->execute(sprintf("SELECT `c_usr_id` FROM `db_%scomments` WHERE `file_key`='%s' AND `c_replyto`='' LIMIT 1;", $type, $file_key));
                    if ($ci->fields['c_usr_id'] > 0) {
                        $mail_do = VNotify::queInit('channel_comment_reply', array(VUserinfo::getUserEmail($ci->fields['c_usr_id'])), $db_id);
                    }
                }
            }

        }
        return VChannelComments::commLayout((string) $type, (int) $upage_id, array($notice_message, $error_message));
    }

    /* comment actions (when viewing files) */
    public function commentActions($act)
    {
        $class_filter   = self::$filter;
        $class_database = self::$dbc;
        $db             = self::$db;
        $cfg            = self::$cfg;
        $language       = self::$language;

        $type     = $class_filter->clr_str($_POST['comm_type']);
        $upage_id = (int) $_POST['comm_uid'];
        $c_key    = $class_filter->clr_str($_POST['c_key']);

        if ((int) $_SESSION['USER_ID'] == 0 or $upage_id == 0) {
            return;
        }

        switch ($act) {
            case "comm-pin":
            case "comm-unpin":
                $comm_uid = (int) $_POST['comm_uid'];

                if ($comm_uid == (int) $_SESSION['USER_ID']) {
                    $sql = sprintf("UPDATE `db_%scomments` SET `c_pinned`='%s' WHERE `c_key`='%s' LIMIT 1;", $type, ($act == 'comm-pin' ? '1' : '0'), $c_key);
                } else {
                    return;
                }
                break;

            case "comm-suspend":
            case "comm-approve":
                $comm_uid = (int) $_POST['comm_uid'];

                if ($comm_uid == (int) $_SESSION['USER_ID']) {
                    $sql = sprintf("UPDATE `db_%scomments` SET `c_approved`='%s' WHERE `c_key`='%s' LIMIT 1;", $type, ($act == 'comm-approve' ? '1' : '0'), $c_key);
                } else {
                    return;
                }
                break;

            case "comm-block":
                $comm_uid = (int) $_POST['comm_uid'];

                if ($comm_uid == (int) $_SESSION['USER_ID']) {
                    $db_c = $db->execute(sprintf("SELECT
									A.`c_id`, A.`c_usr_id`, B.`usr_user`
									FROM
									`db_%scomments` A, `db_accountuser` B
									WHERE
									A.`c_key`='%s' AND
									A.`c_usr_id`=B.`usr_id`
									LIMIT 1;",
                        $type, $c_key));

                    if ($c_id = $db_c->fields['c_id']) {
                        $c_usr_id = $db_c->fields['c_usr_id'];
                        $c_user   = $db_c->fields['usr_user'];

                        $ct = $db->execute(sprintf("SELECT `ct_id` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $c_user));

                        if ($ct_id = $ct->fields['ct_id']) {
                            $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_blocked`='1' WHERE `ct_id`='%s' LIMIT 1", $ct_id));
                            $db_id = $db->Affected_Rows();
                        } else {
                            $block_db = serialize(array("bl_files" => 1, "bl_channel" => 1, "bl_comments" => 1, "bl_messages" => 1, "bl_subscribe" => 1));
                            $db_arr   = array("usr_id" => (int) $_SESSION['USER_ID'], "pwd_id" => VUserinfo::generateRandomString(10), "ct_name" => "", "ct_username" => $c_user, "ct_email" => "", "ct_blocked" => 1, "ct_datetime" => date("Y-m-d H:i:s"), "ct_block_cfg" => $block_db);
                            $class_database->doInsert('db_usercontacts', $db_arr);
                            $db_id = $db->Insert_ID();
                        }

                        if ($db_id > 0) {
                            return VChannelComments::commLayout((string) $type, (int) $upage_id, array('', ''));
                        } else {
                            return;
                        }
                    } else {
                        return;
                    }
                } else {
                    return;
                }
                break;

            case "comm-delete":
                $comm_uid = (int) $_POST['comm_uid'];
                $c_uid    = (int) $_SESSION['USER_ID'];

                if ($comm_uid == $c_uid) {
                    $sql = sprintf("DELETE FROM `db_%scomments` WHERE `c_key`='%s' LIMIT 1;", $type, $c_key);
                } else {
                    $db_c = $db->execute(sprintf("SELECT `c_id` FROM `db_%scomments` WHERE `c_key`='%s' AND `c_usr_id`='%s' LIMIT 1;", $type, $c_key, $c_uid));

                    if ($c_id = $db_c->fields['c_id']) {
                        $sql = sprintf("DELETE FROM `db_%scomments` WHERE `c_key`='%s' LIMIT 1;", $type, $c_key);
                    } else {
                        return;
                    }
                }
                break;

            case "comm-edit":
                $c_uid  = intval($_SESSION['USER_ID']);
                $c_key  = intval($_POST['comm_edit']);
                $c_body = $class_filter->clr_str($_POST['file_comm_text']);
                $db_c   = $db->execute(sprintf("SELECT `c_id` FROM `db_%scomments` WHERE `c_key`='%s' AND `c_usr_id`='%s' LIMIT 1;", $type, $c_key, $c_uid));

                if ($c_id = $db_c->fields['c_id']) {
                    $sql = sprintf("UPDATE `db_%scomments` SET `c_body`='%s', `c_edited`='1', `c_edittime`='%s' WHERE `c_id`='%s' LIMIT 1", $type, $c_body, date("Y-m-d H:i:s"), $c_id);
                } else {
                    return;
                }
                break;

            case "comm-spam":
            case "comm-like":
            case "comm-dislike":
                $t_str  = null;
                $c_uid  = intval($_SESSION['USER_ID']);
                $c_arr  = array();
                $db_c   = $db->execute(sprintf("SELECT `%s` FROM `db_%scomments` WHERE `c_key`='%s' AND `c_active`='1' LIMIT 1;", ($act == 'comm-spam' ? 'c_spam' : 'c_rating'), $type, $c_key));
                $c_spam = $db_c->fields[($act == 'comm-spam' ? 'c_spam' : 'c_rating')];

                if ($c_spam != '') {
                    $c_arr = unserialize($c_spam);
                    if ($act == 'comm-spam' and $c_arr[$c_uid] == '') {
                        $c_arr[$c_uid] = $act == 'comm-spam' ? date("Y-m-d H:i:s") : ($act == 'comm-like' ? '+1' : '-1');
                    } elseif ($act !== 'comm-spam' and $c_arr[$c_uid] == '') {
                        $c_arr[$c_uid] = ($act == 'comm-like' ? '+1' : '-1');
                    } elseif ($act !== 'comm-spam' and isset($c_arr[$c_uid])) {
                        if ($c_arr[$c_uid] == '-1' and $act == 'comm-like') {
                            $c_arr[$c_uid] = '+1';
                        } elseif ($c_arr[$c_uid] == '+1' and $act == 'comm-dislike') {
                            $c_arr[$c_uid] = '-1';
                        } else {
                            unset($c_arr[$c_uid]);
                        }

                    }
                } else {
                    if ($c_arr[$c_uid] == '') {
                        $c_arr[$c_uid] = $act == 'comm-spam' ? date("Y-m-d H:i:s") : ($act == 'comm-like' ? '+1' : '-1');
                    }
                }
                if ($act == 'comm-like' or $act == 'comm-dislike') {
                    $add = 0;
                    foreach ($c_arr as $k => $v) {
                        $add .= (int) $v;
                    }
                    $t_rate = self::calculateString($add);
                    $t_str  = sprintf(", `c_rating_value`='%s'", $t_rate);
                }

                $c_db = $db->execute(sprintf("UPDATE `db_%scomments` SET `%s`='%s' %s WHERE `c_key`='%s' LIMIT 1;", $type, ($act == 'comm-spam' ? 'c_spam' : 'c_rating'), serialize($c_arr), $t_str, $c_key));
                break;
        }

        $db->execute($sql);
        if ($db->Affected_Rows() > 0) {
/*            if ($act == 'comm-delete') {
$kk    = $class_filter->clr_str($_GET[$type[0]]);
$nr_sql = sprintf("UPDATE `db_%sfiles` SET `channel_comments`=`channel_comments`-1 WHERE `file_key`='%s' LIMIT 1;", $type, $kk);
$db->execute($nr_sql);
}*/
            if ($cfg['activity_logging'] == 1) {
                if ($act == 'comm-approve') {
                    $db->execute("UPDATE `db_useractivity` SET `act_deleted`='0' WHERE `act_type` LIKE '%" . $c_key . "%';");
                } elseif ($act == 'comm-suspend' or $act == 'comm-delete') {
                    $db->execute("UPDATE `db_useractivity` SET `act_deleted`='1' WHERE `act_type` LIKE '%" . $c_key . "%';");
                }
            }

            if ($act == 'comm-pin' or $act == 'comm-unpin') {
                return VChannelComments::commLayout((string) $type, (int) $upage_id, array('', ''));
            }
        }

        if ($act == 'comm-like' or $act == 'comm-dislike') {
            if ($t_rate > 0) {
                echo VGenerate::declareJS('$("#cd-' . $c_key . ' i").addClass("active"); $("#cl-' . $c_key . ' i").removeClass("active"); $("#' . $c_key . ' .comment-rating' . $c_key . '").removeClass("err-red").addClass("conf-green").text("(+' . $t_rate . ')");');
            } elseif ($t_rate < 0) {
                echo VGenerate::declareJS('$("#cl-' . $c_key . ' i").addClass("active"); $("#cd-' . $c_key . ' i").removeClass("active"); $("#' . $c_key . ' .comment-rating' . $c_key . '").removeClass("conf-green").addClass("err-red").text("(' . $t_rate . ')");');
            } else {
                echo VGenerate::declareJS('$("#cd-' . $c_key . ' i, #cl-' . $c_key . ' i").removeClass("active"); $("#' . $c_key . ' .comment-rating' . $c_key . '").removeClass("conf-green").removeClass("err-red").text("");');
            }

            return;
        } elseif ($act == 'comm-approve') {
            echo VGenerate::declareJS('$("#' . $c_key . '.comm-suspended .comm-pending:first").text(""); $("#' . $c_key . '.comm-suspended").removeClass("comm-suspended");');

            return;
        } elseif ($act == 'comm-suspend') {
            echo VGenerate::declareJS('$("#' . $c_key . '").addClass("comm-suspended"); $("#' . $c_key . '.comm-suspended .comm-pending:first").text("' . $language['frontend.global.not.approved'] . '");');

            return;
        } elseif ($act == 'comm-delete') {
            echo VGenerate::declareJS('$("#' . $c_key . '").detach();var cnr=parseInt($("#total-comm-nr").text());$("#total-comm-nr").text(cnr-1);');

            return;
        } elseif ($act == 'comm-edit') {
            echo VGenerate::declareJS('el=$("#' . $type . '-comment' . $c_key . ' .comm-body");el.html(`' . $c_body . '`);h=el.height();el.parent().find("[id^=comm-actions2-over] span").css("top",-h);el.parent().find("[id^=comment-actions-dd] .accordion.cacc").css("top",-h);$(".comm-own' . $c_key . ' .edited").text("' . $language['view.files.comm.edited'] . '");$("#comm-edit-form' . $c_key . ' .comm-cancel-edit").click();');

            return;
        }

        return self::browseComment();
    }

    /* browsing comments */
    public function browseComment()
    {
        $class_filter = self::$filter;

        $type     = $class_filter->clr_str($_POST['comm_type']);
        $upage_id = intval($_POST['comm_uid']);
        $page     = (isset($_GET['page']) and (int) $_GET['page'] > 0);

        return $page ? VChannelComments::listFileComments($type, $upage_id) : VChannelComments::commLayout($type, $upage_id);
    }

}
