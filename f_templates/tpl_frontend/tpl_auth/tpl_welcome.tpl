				<div class="top-padding10">
				    <div class="wd350 left-float bold font14 left-padding50">
					<div>{lang_entry key="welcome.account.info"}</div>
					<div class="left-padding50 font83">
					    <ul>
						<li class="top-padding5">{lang_entry key="welcome.account.youruser"}<br /><span class="normal">{$smarty.session.USER_NAME}</span></li>
					    {if $paid_memberships eq 1}
						<li class="top-padding5">{lang_entry key="welcome.account.yourpack"}<br /><span class="normal">{$pk_name}</span></li>
					    {else}
						<li class="top-padding5">{lang_entry key="welcome.account.youremail"}<br /><span class="normal">{$usr_email}</span></li>
					    {/if}
					    </ul>
					</div>
				    </div>
				    <div class="wd350 left-float bold font14 left-padding50">
					<div>{lang_entry key="welcome.account.getstarted"} {$website_shortname}</div>
					<div class="left-padding50 font83">
					    <ul>
						<li class="top-padding5 normal">{lang_entry key="welcome.account.customize"}</li>
						<li class="top-padding5 normal">{lang_entry key="welcome.account.upload"}</li>
						<li class="top-padding5 normal">{lang_entry key="welcome.account.prefs"}</li>
					    </ul>
					</div>
				    </div>
				</div>
