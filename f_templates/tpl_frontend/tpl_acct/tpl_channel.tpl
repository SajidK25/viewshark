	<script type="text/javascript">
            var current_url  = '{$main_url}/';
            var menu_section = '{href_entry key="channel"}';
            var fe_mask      = 'on';
        </script>
        <div class="wdmax" id="ct-wrapper">
            {generate_html type="channel_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        </div>

{insert name="swiperJS" for="tnav"}
{if $video_module}{insert name="swiperJS" for="video"}{/if}
{if $short_module}{insert name="swiperJS" for="short"}{/if}
{if $live_module}{insert name="swiperJS" for="live"}{/if}
{if $image_module}{insert name="swiperJS" for="image"}{/if}
{if $audio_module}{insert name="swiperJS" for="audio"}{/if}
{if $document_module}{insert name="swiperJS" for="doc"}{/if}
{if $blog_module}{insert name="swiperJS" for="blog"}{/if}
