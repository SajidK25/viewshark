<style>{literal}#siteContent{font-size:14px}{/literal}</style>
<div class="lb-margins">
  <article>
    <h3 class="content-title"><i class="icon-coin"></i>{$website_shortname} Affiliate Program</h3>
    <div class="line"></div>
  </article>
  <h1>Join the <u>{$website_shortname} Affiliate Program</u> today and start making money from the number of views on your uploaded content!</h1>
  <p style="margin-bottom:0">We are currently paying <b>{($affiliate_payout_share*$affiliate_payout_figure)/100} {$affiliate_payout_currency}</b> for every <b>{$affiliate_payout_units} unique views</b> per uploaded file.</p><br>
  <p style="margin-bottom:0">This means, when one of your videos gets {$affiliate_payout_units} unique views, then you have earned {($affiliate_payout_share*$affiliate_payout_figure)/100} {$affiliate_payout_currency}.</p>
  <p>Or, if your video gets {$affiliate_payout_units*10} unique views, then you have earned {(($affiliate_payout_share*$affiliate_payout_figure)/100)*10} {$affiliate_payout_currency}. And so on.</p>
  <article>
    <h3 class="content-title"><i class="icon-signup"></i>How to Apply</h3>
    <div class="line"></div>
  </article>
{if $affiliate_requirements_type eq "1"}{assign var=ac value="video views"}
{elseif $affiliate_requirements_type eq "2"}{assign var=ac value="channel views"}
{elseif $affiliate_requirements_type eq "3"}{assign var=ac value="subscriber(s)"}
{elseif $affiliate_requirements_type eq "4"}{assign var=ac value="follower(s)"}
{/if}
  <div>
        <ul>
            <li><i class="iconBe-chevron-right"></i> Website members can request to become affiliates, if their user account matches the following condition: <b>must have at least {$affiliate_requirements_min} {$ac}</b> in order to be allowed to apply for affiliate.</li>
            <li><i class="iconBe-chevron-right"></i> If the affiliate program conditions are met, members can request to become affiliates by going to <a href="{$main_url}/{href_entry key="account"}" target="_blank">My Account</a> and clicking the <b>"Apply for Affiliate"</b> button.</li>
            <li><i class="iconBe-chevron-right"></i> Affiliate requests will be reviewed and will get approved or denied by the website administrator.</li>
            <li><i class="iconBe-chevron-right"></i> Email notifications will be delivered to both parties during the entire process.</li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-cog"></i>How it Works</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> Affiliate members have access to additional tools and features for managing and listing the views received on their uploaded content.</li>
    	    <li><i class="iconBe-chevron-right"></i> The following additional features will be unlocked for affiliate members, within the <a href="{$main_url}/{href_entry key="affiliate"}" target="_blank">Affiliate Panel</a> section:
    	    <ul style="margin-left:25px">
    	    <li><i class="icon-pie"></i> <b>Views Statistics</b> - charts and graphs with views by number, views by device, views by page title.</li>
    	    <li><i class="icon-globe"></i> <b>Geo Statistics</b> - geo maps depicting the locations from which the views originated.</li>
    	    <li><i class="icon-bars"></i> <b>Compare Views</b> - charts comparing the views by week, month or year.</li>
    	    <li><i class="icon-paypal"></i> <b>Manage Payouts</b> - manage and keep track of your payments.</li>
    	    </ul>
    	    </li>
    	    <li><i class="iconBe-chevron-right"></i> Affiliate members can also have a <i class="icon-checkmark-circle"></i> "verified badge" assigned next to their username. </li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-paypal"></i>Getting Paid</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> Payouts for affiliate members are calculated and generated automatically between the <b>13-15th of every month</b>.</li>
    	    <li><i class="iconBe-chevron-right"></i> The website administrator will then issue PayPal payments to the PayPal addresses of the affiliate members.</li>
    	    <li><i class="iconBe-chevron-right"></i> Affiliate members will be notified by email after every payment that has been issued.</li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-blocked"></i>Cancelling the Affiliate Membership</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> An affiliate member may request the termination of the affiliate membership at any time, by accessing the <a href="{$main_url}/{href_entry key="affiliate"}" target="_blank">Affiliate Panel</a> and clicking the <b>"Request Affiliate Cancellation"</b> button.</li>
    	    <li><i class="iconBe-chevron-right"></i> The website administrator will review the request and take action accordingly.</li>
    	    <li><i class="iconBe-chevron-right"></i> Email notifications will be delivered to both parties during the entire process.</li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-globe"></i>Google Maps API Key</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> Affiliate members are required to set up their own Google Maps API Key in order to be able to see the <i class="icon-globe"></i> <b>Geo Statistics</b>.</li>
    	    <li><i class="iconBe-chevron-right"></i> An API key can be obtained by performing the following steps:</li><br>
    	    <li>
    	    <ul>
    	    <li>1. Go to <a href="https://console.developers.google.com/" target="_blank">https://console.developers.google.com/</a> and create a new project.</li>
    	    <li>2. Go to <b>Library</b>, search for <b>analytics</b>, click on <b>Analytics API</b>, then click the <b>Enable</b> button.</li>
    	    <li>3. Go to <b>Library</b>, search for <b>maps geocoding</b>, click on both <b>Google Maps Geocoding API</b> and <b>Google Maps Javascript API</b>, then click the <b>Enable</b> button for both.</li>
    	    <li>4. Go to <b>Library</b>, search for <b>maps geolocation</b>, click on <b>Google Maps Geolocation API</b>, then click the <b>Enable</b> button.</li>
    	    <li>5. Go to <b>Credentials</b> and click <b>Create credentials > API Key</b> and then enter the API key at the <a href="{$main_url}/{href_entry key="affiliate"}" target="_blank">Affiliate Panel</a>.</li>
    	    </ul>
    	    </li>
	</ul>
	<div class="clearfix"></div>
  </div>
</div>