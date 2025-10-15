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

var Ads = function() {

if (adTagUrl == '' || ad_client != 'ima') {
	return;
}

  pb = "view-player-";
  this.player = videojs(pb+fk);
  this.adTagInputvalue = adTagUrl;
/*  this.sampleAdTag = document.getElementById('sampleAdTag');
  this.sampleAdTag.addEventListener(
      'click',
      this.bind(this, this.onSampleAdTagClick_),
      false);*/

  // Remove controls from the player on iPad to stop native controls from stealing
  // our click
  var contentPlayer =  document.getElementById(pb+fk+'_html5_api');
  if ((navigator.userAgent.match(/iPad/i) ||
          navigator.userAgent.match(/Android/i)) &&
      contentPlayer.hasAttribute('controls')) {
    contentPlayer.removeAttribute('controls');
  }

  // Start ads when the video player is clicked, but only the first time it's
  // clicked.
  var startEvent = 'click';
  if (navigator.userAgent.match(/iPhone/i) ||
      navigator.userAgent.match(/iPad/i) ||
      navigator.userAgent.match(/Android/i)) {
    startEvent = 'touchend';
  }
  this.player.one(startEvent, this.bind(this, this.init));

  this.options = {
    id: pb+fk
  };

  this.events = [
    google.ima.AdEvent.Type.ALL_ADS_COMPLETED,
    google.ima.AdEvent.Type.CLICK,
    google.ima.AdEvent.Type.COMPLETE,
    google.ima.AdEvent.Type.FIRST_QUARTILE,
    google.ima.AdEvent.Type.LOADED,
    google.ima.AdEvent.Type.MIDPOINT,
    google.ima.AdEvent.Type.PAUSED,
    google.ima.AdEvent.Type.STARTED,
    google.ima.AdEvent.Type.THIRD_QUARTILE
  ];

//  this.console = document.getElementById('ima-sample-console');

  this.player.ima(
      this.options,
      this.bind(this, this.adsManagerLoadedCallback));

};

Ads.prototype.SAMPLE_AD_TAG = adTagUrl;

Ads.prototype.init = function() {
  if (this.adTagInputvalue == '') {
    this.log('Error: please fill in an ad tag');
  } else {
    this.player.ima.initializeAdDisplayContainer();
//    this.player.ima.setContent(null, this.adTagInputvalue, true);
    this.player.ima.setContentWithAdTag(null, this.adTagInputvalue, true);
    this.player.ima.requestAds();
    this.player.play();
  }
};

Ads.prototype.onSampleAdTagClick_ = function() {
  this.adTagInputvalue = this.SAMPLE_AD_TAG;
};

Ads.prototype.adsManagerLoadedCallback = function() {
  for (var index = 0; index < this.events.length; index++) {
    this.player.ima.addEventListener(
        this.events[index],
        this.bind(this, this.onAdEvent));
  }
  this.player.ima.startFromReadyCallback();
};

Ads.prototype.onAdEvent = function(event) {
  this.log('Ad event: ' + event.type);
};

Ads.prototype.log = function(message) {
//  this.console.innerHTML = this.console.innerHTML + '<br/>' + message;
}

Ads.prototype.bind = function(thisObj, fn) {
  return function() {
    fn.apply(thisObj, arguments);
  };
};
