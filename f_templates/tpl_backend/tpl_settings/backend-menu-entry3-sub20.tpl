	<div class="wdmax" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="wdmax top-bottom-padding left-float s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="conversion_mp4_360p" entry_title="backend.menu.entry6.sub1.conv.mp4.360p" entry_id="ct-entry-details1" input_name="" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="conversion_mp4_480p" entry_title="backend.menu.entry6.sub1.conv.mp4.480p" entry_id="ct-entry-details2" input_name="" input_value="" bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet3" input_type="conversion_mp4_720p" entry_title="backend.menu.entry6.sub1.conv.mp4.720p" entry_id="ct-entry-details3" input_name="" input_value="" bb=1}
            {generate_html bullet_id="ct-bullet4" input_type="conversion_mp4_1080p" entry_title="backend.menu.entry6.sub1.conv.mp4.1080p" entry_id="ct-entry-details4" input_name="" input_value="" bb=0}
            </div>
            <div class="clearfix"></div>
	    <div class="wdmax top-bottom-padding left-float s-wrap">
		<div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	    <input type="hidden" name="ct_entry" id="ct_entry" value="" />
	</form>
	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">{include file="f_scripts/be/js/jquery.nouislider.init.js"}</script>
	<script type="text/javascript">
	$(document).ready(function () {ldelim}
	$(function() {ldelim}
                SelectList.init("conversion_mp4_360p_bitrate_mt");
                SelectList.init("conversion_mp4_480p_bitrate_mt");
                SelectList.init("conversion_mp4_720p_bitrate_mt");
                SelectList.init("conversion_mp4_1080p_bitrate_mt");
                SelectList.init("conversion_mp4_360p_encoding");
                SelectList.init("conversion_mp4_480p_encoding");
                SelectList.init("conversion_mp4_720p_encoding");
                SelectList.init("conversion_mp4_1080p_encoding");
        {rdelim});
        $('#slider-conversion_mp4_360p_bitrate_video').noUiSlider({ start: [ $("input[name='conversion_mp4_360p_bitrate_video']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 20000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_360p_bitrate_video").Link('lower').to($("input[name='conversion_mp4_360p_bitrate_video']"));
        $('#slider-conversion_mp4_480p_bitrate_video').noUiSlider({ start: [ $("input[name='conversion_mp4_480p_bitrate_video']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 20000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_480p_bitrate_video").Link('lower').to($("input[name='conversion_mp4_480p_bitrate_video']"));
        $('#slider-conversion_mp4_720p_bitrate_video').noUiSlider({ start: [ $("input[name='conversion_mp4_720p_bitrate_video']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 20000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_720p_bitrate_video").Link('lower').to($("input[name='conversion_mp4_720p_bitrate_video']"));
        $('#slider-conversion_mp4_1080p_bitrate_video').noUiSlider({ start: [ $("input[name='conversion_mp4_1080p_bitrate_video']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 20000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_1080p_bitrate_video").Link('lower').to($("input[name='conversion_mp4_1080p_bitrate_video']"));

        $('#slider-conversion_mp4_360p_resize_w').noUiSlider({ start: [ $("input[name='conversion_mp4_360p_resize_w']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_360p_resize_w").Link('lower').to($("input[name='conversion_mp4_360p_resize_w']"));
        $('#slider-conversion_mp4_360p_resize_h').noUiSlider({ start: [ $("input[name='conversion_mp4_360p_resize_h']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_360p_resize_h").Link('lower').to($("input[name='conversion_mp4_360p_resize_h']"));
        $('#slider-conversion_mp4_480p_resize_w').noUiSlider({ start: [ $("input[name='conversion_mp4_480p_resize_w']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_480p_resize_w").Link('lower').to($("input[name='conversion_mp4_480p_resize_w']"));
        $('#slider-conversion_mp4_480p_resize_h').noUiSlider({ start: [ $("input[name='conversion_mp4_480p_resize_h']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_480p_resize_h").Link('lower').to($("input[name='conversion_mp4_480p_resize_h']"));
        $('#slider-conversion_mp4_720p_resize_w').noUiSlider({ start: [ $("input[name='conversion_mp4_720p_resize_w']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_720p_resize_w").Link('lower').to($("input[name='conversion_mp4_720p_resize_w']"));
        $('#slider-conversion_mp4_720p_resize_h').noUiSlider({ start: [ $("input[name='conversion_mp4_720p_resize_h']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_720p_resize_h").Link('lower').to($("input[name='conversion_mp4_720p_resize_h']"));
        $('#slider-conversion_mp4_1080p_resize_w').noUiSlider({ start: [ $("input[name='conversion_mp4_1080p_resize_w']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_1080p_resize_w").Link('lower').to($("input[name='conversion_mp4_1080p_resize_w']"));
        $('#slider-conversion_mp4_1080p_resize_h').noUiSlider({ start: [ $("input[name='conversion_mp4_1080p_resize_h']").val() ], step: 1, range: { 'min': [ 100 ], 'max': [ 2000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_1080p_resize_h").Link('lower').to($("input[name='conversion_mp4_1080p_resize_h']"));

        $('#slider-conversion_mp4_360p_bitrate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_360p_bitrate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 320 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_360p_bitrate_audio").Link('lower').to($("input[name='conversion_mp4_360p_bitrate_audio']"));
        $('#slider-conversion_mp4_480p_bitrate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_480p_bitrate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 320 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_480p_bitrate_audio").Link('lower').to($("input[name='conversion_mp4_480p_bitrate_audio']"));
        $('#slider-conversion_mp4_720p_bitrate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_720p_bitrate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 320 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_720p_bitrate_audio").Link('lower').to($("input[name='conversion_mp4_720p_bitrate_audio']"));
        $('#slider-conversion_mp4_1080p_bitrate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_1080p_bitrate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 320 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_1080p_bitrate_audio").Link('lower').to($("input[name='conversion_mp4_1080p_bitrate_audio']"));

        $('#slider-conversion_mp4_360p_srate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_360p_srate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 48000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_360p_srate_audio").Link('lower').to($("input[name='conversion_mp4_360p_srate_audio']"));
        $('#slider-conversion_mp4_480p_srate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_480p_srate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 48000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_480p_srate_audio").Link('lower').to($("input[name='conversion_mp4_480p_srate_audio']"));
        $('#slider-conversion_mp4_720p_srate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_720p_srate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 48000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_720p_srate_audio").Link('lower').to($("input[name='conversion_mp4_720p_srate_audio']"));
        $('#slider-conversion_mp4_1080p_srate_audio').noUiSlider({ start: [ $("input[name='conversion_mp4_1080p_srate_audio']").val() ], step: 1, range: { 'min': [ 1 ], 'max': [ 48000 ] }, format:wNumb({ decimals: 0 }) });
	$("#slider-conversion_mp4_1080p_srate_audio").Link('lower').to($("input[name='conversion_mp4_1080p_srate_audio']"));
	{rdelim});
        </script>