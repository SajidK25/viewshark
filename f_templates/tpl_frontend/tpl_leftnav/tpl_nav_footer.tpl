			<div class="sidebar-nav-footer">
				{insert name="footerInit"}
				<label>&copy; {$smarty.now|date_format:"%Y"} {$website_shortname} - {lang_entry key="frontend.rights.text"}</label>
				<label>{lang_entry key="frontend.powered.text"} <a href="{$main_url}" target="_blank" rel="nofollow">{$head_title}</a></label>
				<p>
					{insert name="socialMediaLinks"}
				</p>
			</div>