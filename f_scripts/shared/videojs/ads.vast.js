/**
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
$(document).ready(function() {

if (adTagUrl == '' || ad_client != 'vast') {
	return;
}

pb = "view-player-";
var player = videojs(pb+fk);
var options = {
    adTagUrl: adTagUrl,
    adCancelTimeout: 5000,
    playAdAlways: true,
    adsEnabled: true,
    vpaidFlashLoaderPath: current_url + 'f_scripts/shared/videojs/VPAIDFlash.swf'
};
player.vastClient(options);
player.on('vast.adStart', function () {
	count=(typeof ad_skip != "undefined" ? parseInt(ad_skip) : 0);
	if (count > 0) {
		counter=setInterval(timer, 1000);
		html = '<div class="vast-skip-button">Skip <span id="skipspan">in <span id="skiptime">'+count+'</span>...</span></div>';
		$(".video-js").append(html);
	}
	$('.vjs-ad-playing .vjs-resolution-button').css({'display':'none'});
});
player.on('vast.adEnd', function () {
	$('.vjs-resolution-button').css({'display':'block'});
	$('.vast-skip-button').detach();
});
});
function timer() { count=count-1; if (count <= 0) { clearInterval(counter); $('#skipspan').detach(); $('.vast-skip-button').addClass('enabled'); $('.vjs-ad-playing .vjs-progress-control').css({'pointer-events':'auto'}); $(".vast-skip-button.enabled").click(function() { player.trigger('vast.adEnd'); $(this).detach(); $('.vjs-resolution-button').css({'display':'block'}); }); return; } document.getElementById("skiptime").innerHTML= count; }