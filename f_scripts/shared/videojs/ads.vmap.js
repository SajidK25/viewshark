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

if (adTagUrl == '' || ad_client != 'vmap') {
	return;
}

    var options = {
      debug: true,
      plugins: {
        ottAdScheduler: {
          debug: true,
          requestUrl: 'http://pulse-demo.videoplaza.tv/proxy/distributor/v2?tt=p&rt=vmap_1.0&t=standard-linears',
        }
      }
    };

pb = "view-player-";
var player = videojs(pb+fk);
player.ads(options);

//player.ottAdScheduler({requestUrl: 'http://pulse-demo.videoplaza.tv/proxy/distributor/v2?tt=p&rt=vmap_1.0&t=standard-linears', debug: true});

/*
player.ottAdScheduler({
          debug: true,
          requestUrl: adTagUrl
});
*/

});