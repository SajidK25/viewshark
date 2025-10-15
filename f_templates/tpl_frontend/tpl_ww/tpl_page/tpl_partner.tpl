<style>{literal}#siteContent{font-size:14px}{/literal}</style>
<div class="lb-margins">
  <article>
    <h3 class="content-title"><i class="icon-coin"></i>{$website_shortname} Partner Program</h3>
    <div class="line"></div>
  </article>
  <h1>Join the <u>{$website_shortname} Partner Program</u> today and start earning revenue from every subscription to your channel!</h1>
  <p style="margin-bottom:0">We are currently sharing <b>{$sub_shared_revenue}%</b> from every <b>paid subscription</b> to your channel.</p><br>
  <p style="">This means, when someone subscribes to you for $5, then you have earned 60% from that, namely $3.</p>
  <article>
    <h3 class="content-title"><i class="icon-signup"></i>How to Apply</h3>
    <div class="line"></div>
  </article>
{if $partner_requirements_type eq "1"}{assign var=ac value="video views"}
{elseif $partner_requirements_type eq "2"}{assign var=ac value="channel views"}
{elseif $partner_requirements_type eq "3"}{assign var=ac value="subscriber(s)"}
{elseif $partner_requirements_type eq "4"}{assign var=ac value="follower(s)"}
{/if}
  <div>
        <ul>
            <li><i class="iconBe-chevron-right"></i> Website members can request to become partners, if their user account matches the following condition: <b>must have at least {$partner_requirements_min} {$ac}</b> in order to be allowed to apply for partner.</li>
            <li><i class="iconBe-chevron-right"></i> If the partner program conditions are met, members can request to become partners by going to <a href="{$main_url}/{href_entry key="subscribers"}" target="_blank">Subscribers Panel</a> and clicking the <b>"Apply for Partner"</b> button.</li>
            <li><i class="iconBe-chevron-right"></i> Partner requests will be reviewed and will get approved or denied by the website administrator.</li>
            <li><i class="iconBe-chevron-right"></i> Email notifications will be delivered to both parties during the entire process.</li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-cog"></i>How it Works</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> Any user of the site may have paid subscribers, however <b>only partnered users may benefit from the revenue sharing program</b>.</li>
    	    <li><i class="iconBe-chevron-right"></i> Partnered members have access to additional tools and features for managing their subscriber earnings.</li>
    	    <li><i class="iconBe-chevron-right"></i> The following additional features will be unlocked for partner members, within the <a href="{$main_url}/{href_entry key="subscribers"}" target="_blank">Subscribers Panel</a> section:
    	    <ul style="margin-left:25px">
    	    <li><i class="icon-bars"></i> <b>Revenue Reports</b> - charts and graphs for earned revenue and subscribers (weekly, monthly, yearly).</li>
    	    <li><i class="icon-paypal"></i> <b>Manage Payouts</b> - manage and keep track of your payments.</li>
    	    </ul>
    	    </li>
    	    <li><i class="iconBe-chevron-right"></i> Partner members can also have a <i class="icon-checkmark-circle"></i> "verified badge" assigned next to their username. </li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-paypal"></i>Getting Paid</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> Partner members are <b>required to have a minimum of {$sub_threshold} subscribers</b> in order to get paid.</li>
    	    <li><i class="iconBe-chevron-right"></i> Payouts for partner members are calculated and generated automatically between the <b>13-15th of every month</b>.</li>
    	    <li><i class="iconBe-chevron-right"></i> The website administrator will then issue PayPal payments to the PayPal addresses of the partner members.</li>
    	    <li><i class="iconBe-chevron-right"></i> Partner members will be notified by email after every payment that has been issued.</li>
	</ul>
	<div class="clearfix"></div>
	<br>
	<article>
	    <h3 class="content-title"><i class="icon-blocked"></i>Cancelling the Partner Membership</h3>
	    <div class="line"></div>
	</article>
        <ul>
    	    <li><i class="iconBe-chevron-right"></i> A partner member may request the termination of the partner membership at any time, by accessing the <a href="{$main_url}/{href_entry key="subscribers"}" target="_blank">Subscribers Panel</a> and clicking the <b>"Request Partner Cancellation"</b> button.</li>
    	    <li><i class="iconBe-chevron-right"></i> The website administrator will review the request and take action accordingly.</li>
    	    <li><i class="iconBe-chevron-right"></i> Email notifications will be delivered to both parties during the entire process.</li>
	</ul>
	<div class="clearfix"></div>
  </div>
</div>