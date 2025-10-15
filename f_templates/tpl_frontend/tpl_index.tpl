{if $not_found}
<div class="pointer left-float wdmax" id="cb-response">
<!-- NOTIFICATION CONTAINER -->
<article>
<h2 class="content-title"><i class="icon-warning"></i>Not found</h2>
<div class="line"></div>
<p class="">The requested page was not found!</p>
<br>
</article>
<!-- END NOTIFICATION CONTAINER -->
{else}
    <script type="text/javascript">
        var current_url  = '{$main_url}/';
        var menu_section = '{href_entry key="index"}';
    </script>
    {generate_html type="homepage" bullet_id="lt-bullet1" entry_id="lt-entry-details1" section="" bb="0"}
{/if}
