    <link rel="stylesheet" type="text/css" href="{$styles_url}/view.min.css">
    <div class="left-float wdmax">
	<div id="overview-userinfo">
	    <div class="statsBox">
		<article class="no-display">
                	<h3 class="content-title"><i class="icon-user"></i>{lang_entry key="account.entry.account.overview"}</h3>
                	<div class="line"></div>
        	</article>
        	<div class="swiper-ph swiper-ph-tnav"></div>
        	<div class="swiper-top swiper-top-tnav" style="display:{$ssd}">
        		<div class="swiper-button-prev swiper-button-prev-tnav"{$sso}></div>
        		<div class="swiper-button-next swiper-button-next-tnav"{$sso}></div>
        		<div class="swiper swiper-tnav">
        			<div class="swiper-wrapper">
            <div class="swiper-slide{if $smarty.get.s eq "account-menu-entry1" or $smarty.get.s eq ""} swiper-slide-current{/if}" rel-m="{href_entry key="account"}"><a href="" rel-s="#account-menu-entry1" rel="nofollow"><span><i class="icon-user"></i> {lang_entry key="account.entry.overview"}</span></a></div>
            <div class="swiper-slide{if $smarty.get.s eq "account-menu-entry2"} swiper-slide-current{/if}" rel-m="{href_entry key="account"}"><a href="" rel-s="#account-menu-entry2" rel="nofollow"><span><i class="icon-profile"></i> {lang_entry key="account.entry.profile.setup"}</span></a></div>
            <div class="swiper-slide{if $smarty.get.s eq "account-menu-entry4"} swiper-slide-current{/if}" rel-m="{href_entry key="account"}"><a href="" rel-s="#account-menu-entry4" rel="nofollow"><span><i class="icon-envelope"></i> {lang_entry key="account.entry.mail.opts"}</span></a></div>
            {if $activity_logging eq 1}
            <div class="swiper-slide{if $smarty.get.s eq "account-menu-entry5"} swiper-slide-current{/if}" rel-m="{href_entry key="account"}"><a href="" rel-s="#account-menu-entry5" rel="nofollow"><span><i class="icon-share"></i> {lang_entry key="account.entry.act.share"}</span></a></div>
            {/if}
            <div class="swiper-slide{if $smarty.get.s eq "account-menu-entry6"} swiper-slide-current{/if}" rel-m="{href_entry key="account"}"><a href="" rel-s="#account-menu-entry6" rel="nofollow"><span><i class="icon-key"></i> {lang_entry key="account.entry.act.manage"}</span></a></div>
        			</div>
        		</div>
        	</div>

		<div class="vs-column fourths mt-15">
		    <div class="user-thumb-xlarge">
			<div><center><a href="{$main_url}/@{$smarty.session.USER_NAME}"><img id="own-profile-image" title="{$smarty.session.USER_NAME}" alt="{$smarty.session.USER_NAME}" src="{insert name="getProfileImage" assign="profileImage" for="{$smarty.session.USER_ID}"}{$profileImage}"></a></center></div>
		    </div>
		    <div class="imageChange">
		    <form method="post" action="" class="entry-form-class overview-form">
		    	<center>
		    		<button class="save-entry-button button-grey search-button form-button new-image" type="button" onclick="$('.thumb-popup').trigger('click');"><span>{lang_entry key="account.image.change"}</span></button>
		    		<a href="javascript:;" rel="popuprel" class="thumb-popup hidden">{lang_entry key="frontend.global.change"}</a>
		    	</center>
		    </form>
		    </div>
		    <div class="popupbox" id="popuprel"></div>
		    <div id="fade"></div>
		</div>
		
		<div class="vs-column three_fourths fit mt-15">
			{insert name="getUserStats" type="sub"}
		</div>
		{insert name="getUserStats" type="stats"}
	    </div>
			<div class="clearfix"></div>
	</div>
    </div>
    <script type="text/javascript">
	$(document).ready(function () {ldelim}
		var nma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id');
		var ma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id').slice(0,19);
		$('.tpl_account #siteContent nav>ul>li>a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
		$('.tpl_account #siteContent .swiper-tnav .swiper-slide a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
	{rdelim});
    </script>
    {insert name="swiperJS" for="tnav"}
