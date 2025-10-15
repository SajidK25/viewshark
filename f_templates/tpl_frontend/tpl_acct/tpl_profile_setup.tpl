    <style>#cb-response{ldelim}margin-top:-15px{rdelim}</style>
    <div id="ct-wrapper" class="">
        <form id="ct-set-form" action="" method="post">
        	<article class="no-display">
                	<h3 class="content-title"><i class="icon-profile"></i>{lang_entry key="account.entry.profile.setup"}</h3>
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

            <div class="no-display">
		<div class="sortings"><div class="no-display">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div></div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
            <div class="vs-column half">
                {generate_html bullet_id="ct-bullet1" input_type="profile_about" entry_title="account.profile.about.display" entry_id="ct-entry-details1" input_name="" input_value="" bb=1 section="fe"}
                {generate_html bullet_id="ct-bullet2" input_type="profile_details" entry_title="account.profile.personal" entry_id="ct-entry-details2" input_name="" input_value="" bb=1 section="fe"}
                {generate_html bullet_id="ct-bullet3" input_type="profile_location" entry_title="account.profile.location" entry_id="ct-entry-details3" input_name="" input_value="" bb=1 section="fe"}
            </div>
            <div class="vs-column half fit">
                {generate_html bullet_id="ct-bullet4" input_type="profile_job" entry_title="account.profile.job" entry_id="ct-entry-details4" input_name="" input_value="" bb=1 section="fe"}
                {generate_html bullet_id="ct-bullet5" input_type="profile_education" entry_title="account.profile.education" entry_id="ct-entry-details5" input_name="" input_value="" bb=1 section="fe"}
                {generate_html bullet_id="ct-bullet6" input_type="profile_interests" entry_title="account.profile.interests" entry_id="ct-entry-details6" input_name="" input_value="" bb=0 section="fe"}
            </div>
            <div class="clearfix"></div>

            <input type="hidden" name="ct_entry" id="ct_entry" value="">
        </form>
    </div>
    {include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
    <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
    <script type="text/javascript">
    	{include file="tpl_frontend/tpl_acct/tpl_profilejs.tpl"}
        $(function() {ldelim}
                SelectList.init("account_profile_bdate_m_sel");
                SelectList.init("account_profile_bdate_d_sel");
                SelectList.init("account_profile_bdate_y_sel");
                SelectList.init("account_profile_personal_gender_sel");
                SelectList.init("account_profile_personal_rel_sel");
                SelectList.init("account_profile_personal_age_sel");
                SelectList.init("account_profile_location_country_sel");
        {rdelim});
	$(document).ready(function(){ldelim}
	  $('.icheck-box input').each(function(){ldelim}
          var self = $(this);
          self.iCheck({ldelim}
                  checkboxClass: 'icheckbox_square-blue',
                  radioClass: 'iradio_square-blue',
                  increaseArea: '20%'
                  //insert: '<div class="icheck_line-icon"></div><label>' + label_text + '</label>'
          {rdelim});
        {rdelim});
		var nma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id');
		var ma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id').slice(0,19);
		$('.tpl_account #siteContent nav>ul>li>a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');if(s=='#account-menu-entry1'){ldelim}location.reload();return false;{rdelim}$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
		$('.tpl_account #siteContent .swiper-tnav .swiper-slide a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');if(s=='#account-menu-entry1'){ldelim}location.reload();return false;{rdelim}$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
	{rdelim});
    </script>
    {insert name="swiperJS" for="tnav"}