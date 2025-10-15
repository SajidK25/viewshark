/* videojs-resolution-switcher.js, videojs-watermark.js, videojs-contextmenu.min.js, videojs-contextmenu-ui.min.js, videojs-settings-menu.min.js, videojs.suggestedVideoEndcap.js, videojs-youtube.min.js, videojs.thumbnails.js  */
/*! videojs-resolution-switcher - 2015-7-26
 * Copyright (c) 2016 Kasper Moskwiak
 * Modified by Pierre Kraft
 * Licensed under the Apache-2.0 license. */

(function() {
  /* jshint eqnull: true*/
  /* global require */
  'use strict';
  var videojs = null;
  if(typeof window.videojs === 'undefined' && typeof require === 'function') {
    videojs = require('video.js');
  } else {
    videojs = window.videojs;
  }

  (function(window, videojs) {


    var defaults = {},
        videoJsResolutionSwitcher,
        currentResolution = {}, // stores current resolution
        menuItemsHolder = {}; // stores menuItems

    function setSourcesSanitized(player, sources, label, customSourcePicker) {
      currentResolution = {
        label: label,
        sources: sources
      };
      if(typeof customSourcePicker === 'function'){
        return customSourcePicker(player, sources, label);
      }
      return player.src(sources.map(function(src) {
        return {src: src.src, type: src.type, res: src.res};
      }));
    }

  /*
   * Resolution menu item
   */
  var MenuItem = videojs.getComponent('MenuItem');
  var ResolutionMenuItem = videojs.extend(MenuItem, {
    constructor: function(player, options, onClickListener, label){
      this.onClickListener = onClickListener;
      this.label = label;
      // Sets this.player_, this.options_ and initializes the component
      MenuItem.call(this, player, options);
      this.src = options.src;

      this.on('click', this.onClick);
      this.on('touchstart', this.onClick);

      if (options.initialySelected) {
        this.showAsLabel();
        this.selected(true);

        this.addClass('vjs-selected');
      }
    },
    showAsLabel: function() {
      // Change menu button label to the label of this item if the menu button label is provided
      if(this.label) {
        this.label.innerHTML = this.options_.label;
      }
    },
    onClick: function(customSourcePicker){
      this.onClickListener(this);
      // Remember player state
      var currentTime = this.player_.currentTime();
      var isPaused = this.player_.paused();
      this.showAsLabel();

      // add .current class
      this.addClass('vjs-selected');

      // Hide bigPlayButton
      if(!isPaused){
        this.player_.bigPlayButton.hide();
      }
      if(typeof customSourcePicker !== 'function' &&
        typeof this.options_.customSourcePicker === 'function'){
        customSourcePicker = this.options_.customSourcePicker;
      }
      // Change player source and wait for loadeddata event, then play video
      // loadedmetadata doesn't work right now for flash.
      // Probably because of https://github.com/videojs/video-js-swf/issues/124
      // If player preload is 'none' and then loadeddata not fired. So, we need timeupdate event for seek handle (timeupdate doesn't work properly with flash)
      var handleSeekEvent = 'loadeddata';
      if(this.player_.techName_ !== 'Youtube' && this.player_.preload() === 'none' && this.player_.techName_ !== 'Flash') {
        handleSeekEvent = 'timeupdate';
      }
      setSourcesSanitized(this.player_, this.src, this.options_.label, customSourcePicker).one(handleSeekEvent, function() {
        this.player_.currentTime(currentTime);
        this.player_.handleTechSeeked_();
        if(!isPaused){
          // Start playing and hide loadingSpinner (flash issue ?)
          this.player_.play().handleTechSeeked_();
        }
        this.player_.trigger('resolutionchange');
        });
      }
    });


    /*
     * Resolution menu button
     */
     var MenuButton = videojs.getComponent('MenuButton');
     var ResolutionMenuButton = videojs.extend(MenuButton, {
       constructor: function(player, options, settings, label){
        this.sources = options.sources;
        this.label = label;
        this.label.innerHTML = options.initialySelectedLabel;
        // Sets this.player_, this.options_ and initializes the component
        MenuButton.call(this, player, options, settings);
        this.controlText('Quality');

        if(settings.dynamicLabel){
          this.el().appendChild(label);
        }else{
          var staticLabel = document.createElement('span');
					videojs.addClass(staticLabel, 'vjs-resolution-button-staticlabel');
          this.el().appendChild(staticLabel);
        }
       },
       createItems: function(){
         var menuItems = [];
         var labels = (this.sources && this.sources.label) || {};
         var onClickUnselectOthers = function(clickedItem) {
          menuItems.map(function(item) {
            item.selected(item === clickedItem);
            item.removeClass('vjs-selected');
          });
         };

         for (var key in labels) {
           if (labels.hasOwnProperty(key)) {
            menuItems.push(new ResolutionMenuItem(
              this.player_,
              {
                label: key,
                src: labels[key],
                initialySelected: key === this.options_.initialySelectedLabel,
                customSourcePicker: this.options_.customSourcePicker
              },
              onClickUnselectOthers,
              this.label));
             // Store menu item for API calls
             menuItemsHolder[key] = menuItems[menuItems.length - 1];
            }
         }
         return menuItems;
       }
     });

    /**
     * Initialize the plugin.
     * @param {object} [options] configuration for the plugin
     */
    videoJsResolutionSwitcher = function(options) {
      var settings = videojs.mergeOptions(defaults, options),
          player = this,
          label = document.createElement('span'),
          groupedSrc = {};

			videojs.addClass(label, 'vjs-resolution-button-label');
			
      /**
       * Updates player sources or returns current source URL
       * @param   {Array}  [src] array of sources [{src: '', type: '', label: '', res: ''}]
       * @returns {Object|String|Array} videojs player object if used as setter or current source URL, object, or array of sources
       */
      player.updateSrc = function(src){
        //Return current src if src is not given
        if(!src){ return player.src(); }
        // Dispose old resolution menu button before adding new sources
        if(player.controlBar.resolutionSwitcher){
          player.controlBar.resolutionSwitcher.dispose();
          delete player.controlBar.resolutionSwitcher;
        }
        //Sort sources
        src = src.sort(compareResolutions);
        groupedSrc = bucketSources(src);
        var choosen = chooseSrc(groupedSrc, src);
        var menuButton = new ResolutionMenuButton(player, { sources: groupedSrc, initialySelectedLabel: choosen.label , initialySelectedRes: choosen.res , customSourcePicker: settings.customSourcePicker}, settings, label);
				videojs.addClass(menuButton.el(), 'vjs-resolution-button');
        player.controlBar.resolutionSwitcher = player.controlBar.el_.insertBefore(menuButton.el_, player.controlBar.getChild('fullscreenToggle').el_);
        player.controlBar.resolutionSwitcher.dispose = function(){
          this.parentNode.removeChild(this);
        };
        return setSourcesSanitized(player, choosen.sources, choosen.label);
      };

      /**
       * Returns current resolution or sets one when label is specified
       * @param {String}   [label]         label name
       * @param {Function} [customSourcePicker] custom function to choose source. Takes 3 arguments: player, sources, label. Must return player object.
       * @returns {Object}   current resolution object {label: '', sources: []} if used as getter or player object if used as setter
       */
      player.currentResolution = function(label, customSourcePicker){
        if(label == null) { return currentResolution; }
        if(menuItemsHolder[label] != null){
          menuItemsHolder[label].onClick(customSourcePicker);
        }
        return player;
      };

      /**
       * Returns grouped sources by label, resolution and type
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      player.getGroupedSrc = function(){
        return groupedSrc;
      };

      /**
       * Method used for sorting list of sources
       * @param   {Object} a - source object with res property
       * @param   {Object} b - source object with res property
       * @returns {Number} result of comparation
       */
      function compareResolutions(a, b){
        if(!a.res || !b.res){ return 0; }
        return (+b.res)-(+a.res);
      }

      /**
       * Group sources by label, resolution and type
       * @param   {Array}  src Array of sources
       * @returns {Object} grouped sources: { label: { key: [] }, res: { key: [] }, type: { key: [] } }
       */
      function bucketSources(src){
        var resolutions = {
          label: {},
          res: {},
          type: {}
        };
        src.map(function(source) {
          initResolutionKey(resolutions, 'label', source);
          initResolutionKey(resolutions, 'res', source);
          initResolutionKey(resolutions, 'type', source);

          appendSourceToKey(resolutions, 'label', source);
          appendSourceToKey(resolutions, 'res', source);
          appendSourceToKey(resolutions, 'type', source);
        });
        return resolutions;
      }

      function initResolutionKey(resolutions, key, source) {
        if(resolutions[key][source[key]] == null) {
          resolutions[key][source[key]] = [];
        }
      }

      function appendSourceToKey(resolutions, key, source) {
        resolutions[key][source[key]].push(source);
      }

      /**
       * Choose src if option.default is specified
       * @param   {Object} groupedSrc {res: { key: [] }}
       * @param   {Array}  src Array of sources sorted by resolution used to find high and low res
       * @returns {Object} {res: string, sources: []}
       */
      function chooseSrc(groupedSrc, src){
        var selectedRes = settings['default']; // use array access as default is a reserved keyword
        var selectedLabel = '';
        if (selectedRes === 'high') {
          selectedRes = src[0].res;
          selectedLabel = src[0].label;
        } else if (selectedRes === 'low' || selectedRes == null || !groupedSrc.res[selectedRes]) {
          // Select low-res if default is low or not set
          selectedRes = src[src.length - 1].res;
          selectedLabel = src[src.length -1].label;
        } else if (groupedSrc.res[selectedRes]) {
          selectedLabel = groupedSrc.res[selectedRes][0].label;
        }
				
        return {res: selectedRes, label: selectedLabel, sources: groupedSrc.res[selectedRes]};
      }
			
			function initResolutionForYt(player){
				// Init resolution
				player.tech_.ytPlayer.setPlaybackQuality('default');
				
				// Capture events
				player.tech_.ytPlayer.addEventListener('onPlaybackQualityChange', function(){
					player.trigger('resolutionchange');
				});
				
				// We must wait for play event
				player.one('play', function(){
					var qualities = player.tech_.ytPlayer.getAvailableQualityLevels();
					// Map youtube qualities names
					var _yts = {
						highres: {res: 1080, label: '1080', yt: 'highres'},
						hd1080: {res: 1080, label: '1080', yt: 'hd1080'}, 
						hd720: {res: 720, label: '720', yt: 'hd720'}, 
						large: {res: 480, label: '480', yt: 'large'},
						medium: {res: 360, label: '360', yt: 'medium'}, 
						small: {res: 240, label: '240', yt: 'small'},
						tiny: {res: 144, label: '144', yt: 'tiny'},
						auto: {res: 0, label: 'auto', yt: 'default'}
					};

					var _sources = [];

					qualities.map(function(q){
						_sources.push({
							src: player.src().src,
							type: player.src().type,
							label: _yts[q].label,
							res: _yts[q].res,
							_yt: _yts[q].yt
						});
					});

					groupedSrc = bucketSources(_sources);

					// Overwrite defualt sourcePicer function
					var _customSourcePicker = function(_player, _sources, _label){
						player.tech_.ytPlayer.setPlaybackQuality(_sources[0]._yt);
						return player;
					};

					var choosen = {label: 'auto', res: 0, sources: groupedSrc.label.auto};
					var menuButton = new ResolutionMenuButton(player, { 
						sources: groupedSrc, 
						initialySelectedLabel: choosen.label, 
						initialySelectedRes: choosen.res, 
						customSourcePicker: _customSourcePicker
					}, settings, label);

					menuButton.el().classList.add('vjs-resolution-button');
					player.controlBar.resolutionSwitcher = player.controlBar.addChild(menuButton);
					$(".vjs-resolution-button").insertBefore(".vjs-fullscreen-control");
				});
			}
			
			player.ready(function(){
				if(player.options_.sources.length > 1){
					// tech: Html5 and Flash
					// Create resolution switcher for videos form <source> tag inside <video>
					player.updateSrc(player.options_.sources);
				}
				
				if(player.techName_ === 'Youtube'){
					// tech: YouTube
					initResolutionForYt(player);
				}
			});

    };

    // register the plugin
    videojs.plugin('videoJsResolutionSwitcher', videoJsResolutionSwitcher);
  })(window, videojs);
})();

/**
 * videojs-watermark
 * @version 1.0.1
 * @copyright 2016 Brooks Lyrette <brooks@dotsub.com>
 * @license Apache-2.0
 */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.videojsWatermark = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

var _videoJs = (typeof window !== "undefined" ? window['videojs'] : typeof global !== "undefined" ? global['videojs'] : null);

var _videoJs2 = _interopRequireDefault(_videoJs);

// Default options for the plugin.
var defaults = {
  position: 'top-right',
  fadeTime: 3000,
  url: undefined,
  image: undefined
};

/**
 * Sets up the div, img and optional a tags for the plugin.
 *
 * @function setupWatermark
 * @param    {Player} player
 * @param    {Object} [options={}]
 */
var setupWatermark = function setupWatermark(player, options) {
  // Add a div and img tag
  var videoEl = player.el();
  var div = document.createElement('div');
  var img = document.createElement('img');

  div.classList.add('vjs-watermark-content');
  div.classList.add('vjs-watermark-' + options.position);
  img.src = options.image;

  // if a url is provided make the image link to that URL.
  if (options.url) {
    var a = document.createElement('a');

    a.href = options.url;
    // if the user clicks the link pause and open a new window
    a.onclick = function (e) {
      e.preventDefault();
      player.pause();
      window.open(options.url);
    };
    a.appendChild(img);
    div.appendChild(a);
  } else {
    div.appendChild(img);
  }
  videoEl.appendChild(div);
};

/**
 * Fades the watermark image.
 *
 * @function fadeWatermark
 * @param    {Object} [options={
 *                  fadeTime:
 *                  'The number of milliseconds before the inital watermark fade out'}]
 */
var fadeWatermark = function fadeWatermark(options) {
  setTimeout(function () {
    return document.getElementsByClassName('vjs-watermark-content')[0].classList.add('vjs-watermark-fade');
  }, options.fadeTime);
};

/**
 * Function to invoke when the player is ready.
 *
 * This is a great place for your plugin to initialize itself. When this
 * function is called, the player will have its DOM and child components
 * in place.
 *
 * @function onPlayerReady
 * @param    {Player} player
 * @param    {Object} [options={}]
 */
var onPlayerReady = function onPlayerReady(player, options) {
  player.addClass('vjs-watermark');

  // if there is no image set just exit
  if (!options.image) {
    return;
  }
  setupWatermark(player, options);

  // Setup watermark autofade
  if (options.fadeTime === null) {
    return;
  }

  player.on('play', function () {
    return fadeWatermark(options);
  });
};

/**
 * A video.js plugin.
 *
 * In the plugin function, the value of `this` is a video.js `Player`
 * instance. You cannot rely on the player being in a "ready" state here,
 * depending on how the plugin is invoked. This may or may not be important
 * to you; if not, remove the wait for "ready"!
 *
 * @function watermark
 * @param    {Object} [options={}]
 *           An object of options left to the plugin author to define.
 */
var watermark = function watermark(options) {
  var _this = this;

  this.ready(function () {
    onPlayerReady(_this, _videoJs2['default'].mergeOptions(defaults, options));
  });
};

// Register the plugin with video.js.
_videoJs2['default'].plugin('watermark', watermark);

// Include the version number.
watermark.VERSION = '1.0.1';

exports['default'] = watermark;
module.exports = exports['default'];
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1])(1)
});
/**
 * videojs-contextmenu
 * @version 1.2.2
 * @copyright 2017 Brightcove, Inc.
 * @license Apache-2.0
 */
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.videojsContextmenu=e()}}(function(){return function e(t,n,o){function i(r,c){if(!n[r]){if(!t[r]){var s="function"==typeof require&&require;if(!c&&s)return s(r,!0);if(u)return u(r,!0);var f=new Error("Cannot find module '"+r+"'");throw f.code="MODULE_NOT_FOUND",f}var d=n[r]={exports:{}};t[r][0].call(d.exports,function(e){var n=t[r][1][e];return i(n?n:e)},d,d.exports,e,t,n,o)}return n[r].exports}for(var u="function"==typeof require&&require,r=0;r<o.length;r++)i(o[r]);return i}({1:[function(e,t,n){(function(e){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}function i(e,t){if(e.contextmenu.options.disabled)return e;var n={target:e,type:p};return["clientX","clientY","pageX","pageY","screenX","screenY"].forEach(function(e){n[e]=t[e]}),e.trigger(n)}function u(e){var t=this.contextmenu.current;if(t){var n=this.contextmenu.options.wait;"touchend"===e.type&&Number(new Date)-t.time>=n&&i(this,e),this.contextmenu.current=null}}function r(e){var t=this.contextmenu.current;if(t){var n=e.touches[0],o=this.contextmenu.options.sensitivity;(n.screenX-t.screenX>o||n.screenY-t.screenY>o)&&(this.contextmenu.current=null)}}function c(e){if(!this.contextmenu.current){var t=e.touches[0];this.contextmenu.current={screenX:t.screenX,screenY:t.screenY,time:Number(new Date)}}}function s(e){this.contextmenu.options.cancel&&!this.contextmenu.options.disabled&&e.preventDefault(),i(this,e),this.off(["touchcancel","touchend"],u),this.off("touchmove",r),this.off("touchstart",c)}function f(e){var t=this;this.contextmenu.options=a.default.mergeOptions(h,e),this.contextmenu.VERSION="1.2.2",this.on("contextmenu",s),this.on(["touchcancel","touchend"],u),this.on("touchmove",r),this.on("touchstart",c),this.ready(function(){return t.addClass(p)})}Object.defineProperty(n,"__esModule",{value:!0});var d="undefined"!=typeof window?window.videojs:"undefined"!=typeof e?e.videojs:null,a=o(d),l=a.default.registerPlugin||a.default.plugin,h={cancel:!0,sensitivity:10,wait:500,disabled:!1},p="vjs-contextmenu";l("contextmenu",f),f.VERSION="1.2.2",n.default=f,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}]},{},[1])(1)});
/**
 * videojs-contextmenu-ui
 * @version 3.0.5
 * @copyright 2017 Brightcove, Inc.
 * @license Apache-2.0
 */
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.videojsContextmenuUi=e()}}(function(){return function e(t,n,o){function i(r,f){if(!n[r]){if(!t[r]){var l="function"==typeof require&&require;if(!f&&l)return l(r,!0);if(u)return u(r,!0);var d=new Error("Cannot find module '"+r+"'");throw d.code="MODULE_NOT_FOUND",d}var a=n[r]={exports:{}};t[r][0].call(a.exports,function(e){var n=t[r][1][e];return i(n?n:e)},a,a.exports,e,t,n,o)}return n[r].exports}for(var u="function"==typeof require&&require,r=0;r<o.length;r++)i(o[r]);return i}({1:[function(e,t,n){(function(o){"use strict";function i(e){return e&&e.__esModule?e:{default:e}}function u(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function r(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(n,"__esModule",{value:!0});var f=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),l=function(e,t,n){for(var o=!0;o;){var i=e,u=t,r=n;o=!1,null===i&&(i=Function.prototype);var f=Object.getOwnPropertyDescriptor(i,u);if(void 0!==f){if("value"in f)return f.value;var l=f.get;if(void 0===l)return;return l.call(r)}var d=Object.getPrototypeOf(i);if(null===d)return;e=d,t=u,n=r,o=!0,f=d=void 0}},d=e("global/window"),a=i(d),c="undefined"!=typeof window?window.videojs:"undefined"!=typeof o?o.videojs:null,s=i(c),p=s.default.getComponent("MenuItem"),y=function(e){function t(){u(this,t),l(Object.getPrototypeOf(t.prototype),"constructor",this).apply(this,arguments)}return r(t,e),f(t,[{key:"handleClick",value:function(e){var n=this;l(Object.getPrototypeOf(t.prototype),"handleClick",this).call(this),this.options_.listener(),a.default.setTimeout(function(){n.player().contextmenuUI.menu.dispose()},1)}}]),t}(p);n.default=y,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"global/window":6}],2:[function(e,t,n){(function(o){"use strict";function i(e){return e&&e.__esModule?e:{default:e}}function u(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function r(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(n,"__esModule",{value:!0});var f=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),l=function(e,t,n){for(var o=!0;o;){var i=e,u=t,r=n;o=!1,null===i&&(i=Function.prototype);var f=Object.getOwnPropertyDescriptor(i,u);if(void 0!==f){if("value"in f)return f.value;var l=f.get;if(void 0===l)return;return l.call(r)}var d=Object.getPrototypeOf(i);if(null===d)return;e=d,t=u,n=r,o=!0,f=d=void 0}},d=e("global/window"),a=i(d),c="undefined"!=typeof window?window.videojs:"undefined"!=typeof o?o.videojs:null,s=i(c),p=e("./context-menu-item"),y=i(p),h=s.default.getComponent("Menu"),w=s.default.dom||s.default,m=function(e){function t(e,n){var o=this;u(this,t),l(Object.getPrototypeOf(t.prototype),"constructor",this).call(this,e,n),this.dispose=s.default.bind(this,this.dispose),n.content.forEach(function(t){var n=function(){};"function"==typeof t.listener?n=t.listener:"string"==typeof t.href&&(n=function(){return a.default.open(t.href)}),o.addItem(new y.default(e,{label:t.label,listener:s.default.bind(e,n)}))})}return r(t,e),f(t,[{key:"createEl",value:function(){var e=l(Object.getPrototypeOf(t.prototype),"createEl",this).call(this);return w.addClass(e,"vjs-contextmenu-ui-menu"),e.style.left=this.options_.position.left+"px",e.style.top=this.options_.position.top+"px",e}}]),t}(h);n.default=m,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./context-menu-item":1,"global/window":6}],3:[function(e,t,n){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}function i(e){var t=void 0;if(e.getBoundingClientRect&&e.parentNode&&(t=e.getBoundingClientRect()),!t)return{left:0,top:0};var n=f.default.documentElement,o=f.default.body,i=n.clientLeft||o.clientLeft||0,u=d.default.pageXOffset||o.scrollLeft,r=t.left+u-i,l=n.clientTop||o.clientTop||0,a=d.default.pageYOffset||o.scrollTop,c=t.top+a-l;return{left:Math.round(r),top:Math.round(c)}}function u(e,t){var n={},o=i(e),u=e.offsetWidth,r=e.offsetHeight,f=o.top,l=o.left,d=t.pageY,a=t.pageX;return t.changedTouches&&(a=t.changedTouches[0].pageX,d=t.changedTouches[0].pageY),n.y=Math.max(0,Math.min(1,(f-d+r)/r)),n.x=Math.max(0,Math.min(1,(a-l)/u)),n}Object.defineProperty(n,"__esModule",{value:!0}),n.findElPosition=i,n.getPointerPosition=u;var r=e("global/document"),f=o(r),l=e("global/window"),d=o(l)},{"global/document":5,"global/window":6}],4:[function(e,t,n){},{}],5:[function(e,t,n){(function(n){var o="undefined"!=typeof n?n:"undefined"!=typeof window?window:{},i=e("min-document");if("undefined"!=typeof document)t.exports=document;else{var u=o["__GLOBAL_DOCUMENT_CACHE@4"];u||(u=o["__GLOBAL_DOCUMENT_CACHE@4"]=i),t.exports=u}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"min-document":4}],6:[function(e,t,n){(function(e){"undefined"!=typeof window?t.exports=window:"undefined"!=typeof e?t.exports=e:"undefined"!=typeof self?t.exports=self:t.exports={}}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],7:[function(e,t,n){(function(o){"use strict";function i(e){return e&&e.__esModule?e:{default:e}}function u(e){return e.hasOwnProperty("contextmenuUI")&&e.contextmenuUI.hasOwnProperty("menu")&&e.contextmenuUI.menu.el()}function r(e,t){return{left:Math.round(t.width*e.x),top:Math.round(t.height-t.height*e.y)}}function f(e){var t=this;if(u(this))return void this.contextmenuUI.menu.dispose();this.contextmenu.options.cancel=!1;var n=(0,h.getPointerPosition)(this.el(),e),o=this.el().getBoundingClientRect(),i=r(n,o);e.preventDefault();var f=this.contextmenuUI.menu=new y.default(this,{content:this.contextmenuUI.content,position:i});this.contextmenuUI.closeMenu=function(){s.default.warn("player.contextmenuUI.closeMenu() is deprecated, please use player.contextmenuUI.menu.dispose() instead!"),f.dispose()},f.on("dispose",function(){t.contextmenu.options.cancel=!0,s.default.off(a.default,["click","tap"],f.dispose),t.removeChild(f),delete t.contextmenuUI.menu}),this.addChild(f),s.default.on(a.default,["click","tap"],f.dispose)}function l(e){var t=this;if(!Array.isArray(e.content))throw new Error('"content" required');u(this)&&(this.contextmenuUI.menu.dispose(),this.off("vjs-contextmenu",this.contextmenuUI.onVjsContextMenu),delete this.contextmenuUI),this.contextmenu();var n=this.contextmenuUI=function(){l.apply(this,arguments)};n.onVjsContextMenu=s.default.bind(this,f),n.content=e.content,n.VERSION="3.0.5",this.on("vjs-contextmenu",n.onVjsContextMenu),this.ready(function(){return t.addClass("vjs-contextmenu-ui")})}Object.defineProperty(n,"__esModule",{value:!0});var d=e("global/document"),a=i(d),c="undefined"!=typeof window?window.videojs:"undefined"!=typeof o?o.videojs:null,s=i(c),p=e("./context-menu"),y=i(p),h=e("./util"),w=s.default.registerPlugin||s.default.plugin;w("contextmenuUI",l),l.VERSION="3.0.5",n.default=l,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./context-menu":2,"./util":3,"global/document":5}]},{},[7])(7)});
/**
 * videojs-settings-menu
 * @version 0.0.2
 * @copyright 2016 Fruitsapje <hero@streamone.nl>
 * @license MIT
 */
!function(e){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=e();else if("function"==typeof define&&define.amd)define([],e);else{var t;t="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,t.videojsSettingsMenu=e()}}(function(){return function e(t,n,o){function i(u,l){if(!n[u]){if(!t[u]){var s="function"==typeof require&&require;if(!l&&s)return s(u,!0);if(r)return r(u,!0);var a=new Error("Cannot find module '"+u+"'");throw a.code="MODULE_NOT_FOUND",a}var f=n[u]={exports:{}};t[u][0].call(f.exports,function(e){var n=t[u][1][e];return i(n?n:e)},f,f.exports,e,t,n,o)}return n[u].exports}for(var r="function"==typeof require&&require,u=0;u<o.length;u++)i(o[u]);return i}({1:[function(e,t,n){(function(o){"use strict";function i(e){return e&&e.__esModule?e:{default:e}}function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function u(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(n,"__esModule",{value:!0});var l=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),s=function(e,t,n){for(var o=!0;o;){var i=e,r=t,u=n;o=!1,null===i&&(i=Function.prototype);var l=Object.getOwnPropertyDescriptor(i,r);if(void 0!==l){if("value"in l)return l.value;var s=l.get;if(void 0===s)return;return s.call(u)}var a=Object.getPrototypeOf(i);if(null===a)return;e=a,t=r,n=u,o=!0,l=a=void 0}},a="undefined"!=typeof window?window.videojs:"undefined"!=typeof o?o.videojs:null,f=i(a),d=e("./settings-menu-item.js"),c=i(d),p=f.default.getComponent("MenuButton"),h=f.default.getComponent("Menu"),v=f.default.getComponent("Component"),y=function(e){function t(e,n){r(this,t),s(Object.getPrototypeOf(t.prototype),"constructor",this).call(this,e,n),this.el_.setAttribute("aria-label","Settings Menu"),this.on("mouseleave",f.default.bind(this,this.hideChildren))}return u(t,e),l(t,[{key:"buildCSSClass",value:function(){return"vjs-settings-menu vjs-icon-cog "+s(Object.getPrototypeOf(t.prototype),"buildCSSClass",this).call(this)}},{key:"createMenu",value:function(){var e=new h(this.player()),t=this.options_.entries;if(t){var n=function(){f.default.hasClass(this.el_,"open")?f.default.removeClass(this.el_,"open"):f.default.addClass(this.el_,"open")},o=!0,i=!1,r=void 0;try{for(var u,l=t[Symbol.iterator]();!(o=(u=l.next()).done);o=!0){var s=u.value,a=new c.default(this.player(),this.options_,s);e.addChild(a),a.on("click",f.default.bind(this,this.hideChildren)),a.on("click",n)}}catch(e){i=!0,r=e}finally{try{!o&&l.return&&l.return()}finally{if(i)throw r}}}return e}},{key:"hideChildren",value:function(){var e=!0,t=!1,n=void 0;try{for(var o,i=this.menu.children()[Symbol.iterator]();!(e=(o=i.next()).done);e=!0){var r=o.value;r.hideSubMenu()}}catch(e){t=!0,n=e}finally{try{!e&&i.return&&i.return()}finally{if(t)throw n}}}}]),t}(p);y.prototype.controlText_="Settings Menu",v.registerComponent("SettingsMenuButton",y),n.default=y,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{"./settings-menu-item.js":2}],2:[function(e,t,n){(function(e){"use strict";function o(e){return e&&e.__esModule?e:{default:e}}function i(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function r(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}Object.defineProperty(n,"__esModule",{value:!0});var u=function(){function e(e,t){for(var n=0;n<t.length;n++){var o=t[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(e,o.key,o)}}return function(t,n,o){return n&&e(t.prototype,n),o&&e(t,o),t}}(),l=function(e,t,n){for(var o=!0;o;){var i=e,r=t,u=n;o=!1,null===i&&(i=Function.prototype);var l=Object.getOwnPropertyDescriptor(i,r);if(void 0!==l){if("value"in l)return l.value;var s=l.get;if(void 0===s)return;return s.call(u)}var a=Object.getPrototypeOf(i);if(null===a)return;e=a,t=r,n=u,o=!0,l=a=void 0}},s="undefined"!=typeof window?window.videojs:"undefined"!=typeof e?e.videojs:null,a=o(s),f=a.default.getComponent("MenuItem"),d=a.default.getComponent("PlaybackRateMenuButton"),c=a.default.getComponent("Component"),p=function(e){return e.charAt(0).toUpperCase()+e.slice(1)},h=function(e){function t(e,n,o){i(this,t),l(Object.getPrototypeOf(t.prototype),"constructor",this).call(this,e,n);var r=p(o),u=a.default.getComponent(r);if(!u)throw new Error("Component "+r+" does not exist");this.subMenu=new u(this.player(),n);var s=a.default.bind(this,this.update),f=function(){setTimeout(s,0)},d=!0,h=!1,v=void 0;try{for(var y,b=this.subMenu.menu.children()[Symbol.iterator]();!(d=(y=b.next()).done);d=!0){var m=y.value;m instanceof c&&m.on("click",f)}}catch(e){h=!0,v=e}finally{try{!d&&b.return&&b.return()}finally{if(h)throw v}}this.update()}return r(t,e),u(t,[{key:"createEl",value:function(){var e=a.default.createEl("li",{className:"vjs-menu-item"});return this.settingsSubMenuTitleEl_=a.default.createEl("div",{className:"vjs-settings-sub-menu-title"}),e.appendChild(this.settingsSubMenuTitleEl_),this.settingsSubMenuValueEl_=a.default.createEl("div",{className:"vjs-settings-sub-menu-value"}),e.appendChild(this.settingsSubMenuValueEl_),this.settingsSubMenuEl_=a.default.createEl("div",{className:"vjs-settings-sub-menu vjs-hidden"}),e.appendChild(this.settingsSubMenuEl_),e}},{key:"handleClick",value:function(){a.default.removeClass(this.el_,"open"),l(Object.getPrototypeOf(t.prototype),"handleClick",this).call(this),a.default.hasClass(this.settingsSubMenuEl_,"vjs-hidden")?a.default.removeClass(this.settingsSubMenuEl_,"vjs-hidden"):a.default.addClass(this.settingsSubMenuEl_,"vjs-hidden")}},{key:"update",value:function(){if(this.settingsSubMenuTitleEl_.innerHTML=this.subMenu.controlText_+":",this.settingsSubMenuEl_.appendChild(this.subMenu.menu.el_),this.subMenu instanceof d)this.settingsSubMenuValueEl_.innerHTML=this.subMenu.labelEl_.innerHTML;else{var e=!0,t=!1,n=void 0;try{for(var o,i=this.subMenu.menu.children_[Symbol.iterator]();!(e=(o=i.next()).done);e=!0){var r=o.value;r instanceof c&&(r.options_.selected||r.hasClass("vjs-selected"))&&(this.settingsSubMenuValueEl_.innerHTML=r.options_.label)}}catch(e){t=!0,n=e}finally{try{!e&&i.return&&i.return()}finally{if(t)throw n}}}}},{key:"hideSubMenu",value:function(){a.default.hasClass(this.el_,"open")&&(a.default.addClass(this.settingsSubMenuEl_,"vjs-hidden"),a.default.removeClass(this.el_,"open"))}}]),t}(f);h.prototype.contentElType="button",a.default.registerComponent("SettingsMenuItem",h),n.default=h,t.exports=n.default}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}],3:[function(e,t,n){"use strict";e("./components/settings-menu-button.js"),e("./components/settings-menu-item.js")},{"./components/settings-menu-button.js":1,"./components/settings-menu-item.js":2}]},{},[3])(3)});
/**
 * Video.js Suggested Video Endcap
 * Created by Justin McCraw for New York Media LLC
 * License information: https://github.com/jmccraw/videojs-suggestedVideoEndcap/blob/master/LICENSE
 * Plugin details: https://github.com/jmccraw/videojs-suggestedVideoEndcap
 */

(function(videojs) {
  'use strict';

  videojs.plugin('suggestedVideoEndcap', function(opts) {
    opts = opts || {
        header: 'You may also like…',
        suggestions: [
          {
            title: '',
            url: '',
            image: '',
            alt: '',
            target: '_self'
          }
        ]
      };
    var player = this;
    var _sve;

    /**
     * Generate the DOM elements for the suggested video endcap content
     * @type {function}
     */
    function constructSuggestedVideoEndcapContent() {
      var sugs = opts.suggestions;
      var _frag = document.createDocumentFragment();
      var _aside = document.createElement('aside');
      var _div = document.createElement('div');
      var _header = document.createElement('h5');
      // can only hold eight suggestions at a time
      var i = sugs.length - 1 > 7 ? 7 : sugs.length - 1;
      var _a;
      var _img;

      _aside.className = 'vjs-suggested-video-endcap';
      _div.className = 'vjs-suggested-video-endcap-container';

      _header.innerHTML = opts.header;
      _header.className = 'vjs-suggested-video-endcap-header';

      _aside.appendChild(_header);

      // construct the individual suggested content pieces
      for (; i >= 0; --i) {
        _a = document.createElement('a');
        _a.className = 'vjs-suggested-video-endcap-link';
        _a.href = sugs[i].url;
        _a.target = sugs[i].target || '_self';
        _a.title = sugs[i].title;

        _img = document.createElement('img');
        _img.className = 'vjs-suggested-video-endcap-img';
        _img.src = sugs[i].image;
        _img.alt = sugs[i].alt || sugs[i].title;
        _a.appendChild(_img);

        _a.innerHTML += sugs[i].title;

        _div.appendChild(_a);
      }

      _aside.appendChild(_div);
      _sve = _aside;
      _frag.appendChild(_aside);
      player.el().appendChild(_frag);
    }

    // attach VideoJS event handlers
    player.on('ended', function() {
      _sve.classList.add('is-active');
    }).on('play', function() {
      _sve.classList.remove('is-active');
    });

    player.ready(function() {
      constructSuggestedVideoEndcapContent();
    });


  });
}(window.videojs));
(function(root,factory){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=factory(require("video.js"))}else if(typeof define==="function"&&define.amd){define(["videojs"],function(videojs){return root.Youtube=factory(videojs)})}else{root.Youtube=factory(root.videojs)}})(this,function(videojs){"use strict";var _isOnMobile=videojs.browser.IS_IOS||videojs.browser.IS_ANDROID;var Tech=videojs.getTech("Tech");var Youtube=videojs.extend(Tech,{constructor:function(options,ready){Tech.call(this,options,ready);this.setPoster(options.poster);this.setSrc(this.options_.source,true);setTimeout(function(){this.el_.parentNode.className+=" vjs-youtube";if(_isOnMobile){this.el_.parentNode.className+=" vjs-youtube-mobile"}if(Youtube.isApiReady){this.initYTPlayer()}else{Youtube.apiReadyQueue.push(this)}}.bind(this))},dispose:function(){if(this.ytPlayer){this.ytPlayer.stopVideo();this.ytPlayer.destroy()}else{var index=Youtube.apiReadyQueue.indexOf(this);if(index!==-1){Youtube.apiReadyQueue.splice(index,1)}}this.ytPlayer=null;this.el_.parentNode.className=this.el_.parentNode.className.replace(" vjs-youtube","").replace(" vjs-youtube-mobile","");this.el_.parentNode.removeChild(this.el_);Tech.prototype.dispose.call(this)},createEl:function(){var div=document.createElement("div");div.setAttribute("id",this.options_.techId);div.setAttribute("style","width:100%;height:100%;top:0;left:0;position:absolute");div.setAttribute("class","vjs-tech");var divWrapper=document.createElement("div");divWrapper.appendChild(div);if(!_isOnMobile&&!this.options_.ytControls){var divBlocker=document.createElement("div");divBlocker.setAttribute("class","vjs-iframe-blocker");divBlocker.setAttribute("style","position:absolute;top:0;left:0;width:100%;height:100%");divBlocker.onclick=function(){this.pause()}.bind(this);divWrapper.appendChild(divBlocker)}return divWrapper},initYTPlayer:function(){var playerVars={controls:0,modestbranding:1,rel:0,showinfo:0,loop:this.options_.loop?1:0};if(typeof this.options_.autohide!=="undefined"){playerVars.autohide=this.options_.autohide}if(typeof this.options_["cc_load_policy"]!=="undefined"){playerVars["cc_load_policy"]=this.options_["cc_load_policy"]}if(typeof this.options_.ytControls!=="undefined"){playerVars.controls=this.options_.ytControls}if(typeof this.options_.disablekb!=="undefined"){playerVars.disablekb=this.options_.disablekb}if(typeof this.options_.end!=="undefined"){playerVars.end=this.options_.end}if(typeof this.options_.color!=="undefined"){playerVars.color=this.options_.color}if(!playerVars.controls){playerVars.fs=0}else if(typeof this.options_.fs!=="undefined"){playerVars.fs=this.options_.fs}if(typeof this.options_.end!=="undefined"){playerVars.end=this.options_.end}if(typeof this.options_.hl!=="undefined"){playerVars.hl=this.options_.hl}else if(typeof this.options_.language!=="undefined"){playerVars.hl=this.options_.language.substr(0,2)}if(typeof this.options_["iv_load_policy"]!=="undefined"){playerVars["iv_load_policy"]=this.options_["iv_load_policy"]}if(typeof this.options_.list!=="undefined"){playerVars.list=this.options_.list}else if(this.url&&typeof this.url.listId!=="undefined"){playerVars.list=this.url.listId}if(typeof this.options_.listType!=="undefined"){playerVars.listType=this.options_.listType}if(typeof this.options_.modestbranding!=="undefined"){playerVars.modestbranding=this.options_.modestbranding}if(typeof this.options_.playlist!=="undefined"){playerVars.playlist=this.options_.playlist}if(typeof this.options_.playsinline!=="undefined"){playerVars.playsinline=this.options_.playsinline}if(typeof this.options_.rel!=="undefined"){playerVars.rel=this.options_.rel}if(typeof this.options_.showinfo!=="undefined"){playerVars.showinfo=this.options_.showinfo}if(typeof this.options_.start!=="undefined"){playerVars.start=this.options_.start}if(typeof this.options_.theme!=="undefined"){playerVars.theme=this.options_.theme}if(typeof this.options_.customVars!=="undefined"){var customVars=this.options_.customVars;Object.keys(customVars).forEach(function(key){playerVars[key]=customVars[key]})}this.activeVideoId=this.url?this.url.videoId:null;this.activeList=playerVars.list;this.ytPlayer=new YT.Player(this.options_.techId,{videoId:this.activeVideoId,playerVars:playerVars,events:{onReady:this.onPlayerReady.bind(this),onPlaybackQualityChange:this.onPlayerPlaybackQualityChange.bind(this),onPlaybackRateChange:this.onPlayerPlaybackRateChange.bind(this),onStateChange:this.onPlayerStateChange.bind(this),onError:this.onPlayerError.bind(this)}})},onPlayerReady:function(){if(this.options_.muted){this.ytPlayer.mute()}var playbackRates=this.ytPlayer.getAvailablePlaybackRates();if(playbackRates.length>1){this.featuresPlaybackRate=true}this.playerReady_=true;this.triggerReady();if(this.playOnReady){this.play()}else if(this.cueOnReady){this.cueVideoById_(this.url.videoId);this.activeVideoId=this.url.videoId}},onPlayerPlaybackQualityChange:function(){},onPlayerPlaybackRateChange:function(){this.trigger("ratechange")},onPlayerStateChange:function(e){var state=e.data;if(state===this.lastState||this.errorNumber){return}this.lastState=state;switch(state){case-1:this.trigger("loadstart");this.trigger("loadedmetadata");this.trigger("durationchange");this.trigger("ratechange");break;case YT.PlayerState.ENDED:this.trigger("ended");break;case YT.PlayerState.PLAYING:this.trigger("timeupdate");this.trigger("durationchange");this.trigger("playing");this.trigger("play");if(this.isSeeking){this.onSeeked()}break;case YT.PlayerState.PAUSED:this.trigger("canplay");if(this.isSeeking){this.onSeeked()}else{this.trigger("pause")}break;case YT.PlayerState.BUFFERING:this.player_.trigger("timeupdate");this.player_.trigger("waiting");break}},onPlayerError:function(e){this.errorNumber=e.data;this.trigger("pause");this.trigger("error")},error:function(){var code=1e3+this.errorNumber;switch(this.errorNumber){case 5:return{code:code,message:"Error while trying to play the video"};case 2:case 100:return{code:code,message:"Unable to find the video"};case 101:case 150:return{code:code,message:"Playback on other Websites has been disabled by the video owner."}}return{code:code,message:"YouTube unknown error ("+this.errorNumber+")"}},loadVideoById_:function(id){var options={videoId:id};if(this.options_.start){options.startSeconds=this.options_.start}if(this.options_.end){options.endEnd=this.options_.end}this.ytPlayer.loadVideoById(options)},cueVideoById_:function(id){var options={videoId:id};if(this.options_.start){options.startSeconds=this.options_.start}if(this.options_.end){options.endEnd=this.options_.end}this.ytPlayer.cueVideoById(options)},src:function(src){if(src){this.setSrc({src:src})}return this.source},poster:function(){if(_isOnMobile){return null}return this.poster_},setPoster:function(poster){this.poster_=poster},setSrc:function(source){if(!source||!source.src){return}delete this.errorNumber;this.source=source;this.url=Youtube.parseUrl(source.src);if(!this.options_.poster){if(this.url.videoId){this.poster_="https://img.youtube.com/vi/"+this.url.videoId+"/0.jpg";this.trigger("posterchange");this.checkHighResPoster()}}if(this.options_.autoplay&&!_isOnMobile){if(this.isReady_){this.play()}else{this.playOnReady=true}}else if(this.activeVideoId!==this.url.videoId){if(this.isReady_){this.cueVideoById_(this.url.videoId);this.activeVideoId=this.url.videoId}else{this.cueOnReady=true}}},autoplay:function(){return this.options_.autoplay},setAutoplay:function(val){this.options_.autoplay=val},loop:function(){return this.options_.loop},setLoop:function(val){this.options_.loop=val},play:function(){if(!this.url||!this.url.videoId){return}this.wasPausedBeforeSeek=false;if(this.isReady_){if(this.url.listId){if(this.activeList===this.url.listId){this.ytPlayer.playVideo()}else{this.ytPlayer.loadPlaylist(this.url.listId);this.activeList=this.url.listId}}if(this.activeVideoId===this.url.videoId){this.ytPlayer.playVideo()}else{this.loadVideoById_(this.url.videoId);this.activeVideoId=this.url.videoId}}else{this.trigger("waiting");this.playOnReady=true}},pause:function(){if(this.ytPlayer){this.ytPlayer.pauseVideo()}},paused:function(){return this.ytPlayer?this.lastState!==YT.PlayerState.PLAYING&&this.lastState!==YT.PlayerState.BUFFERING:true},currentTime:function(){return this.ytPlayer?this.ytPlayer.getCurrentTime():0},setCurrentTime:function(seconds){if(this.lastState===YT.PlayerState.PAUSED){this.timeBeforeSeek=this.currentTime()}if(!this.isSeeking){this.wasPausedBeforeSeek=this.paused()}this.ytPlayer.seekTo(seconds,true);this.trigger("timeupdate");this.trigger("seeking");this.isSeeking=true;if(this.lastState===YT.PlayerState.PAUSED&&this.timeBeforeSeek!==seconds){clearInterval(this.checkSeekedInPauseInterval);this.checkSeekedInPauseInterval=setInterval(function(){if(this.lastState!==YT.PlayerState.PAUSED||!this.isSeeking){clearInterval(this.checkSeekedInPauseInterval)}else if(this.currentTime()!==this.timeBeforeSeek){this.trigger("timeupdate");this.onSeeked()}}.bind(this),250)}},seeking:function(){return this.isSeeking},seekable:function(){if(!this.ytPlayer){return videojs.createTimeRange()}return videojs.createTimeRange(0,this.ytPlayer.getDuration())},onSeeked:function(){clearInterval(this.checkSeekedInPauseInterval);this.isSeeking=false;if(this.wasPausedBeforeSeek){this.pause()}this.trigger("seeked")},playbackRate:function(){return this.ytPlayer?this.ytPlayer.getPlaybackRate():1},setPlaybackRate:function(suggestedRate){if(!this.ytPlayer){return}this.ytPlayer.setPlaybackRate(suggestedRate)},duration:function(){return this.ytPlayer?this.ytPlayer.getDuration():0},currentSrc:function(){return this.source&&this.source.src},ended:function(){return this.ytPlayer?this.lastState===YT.PlayerState.ENDED:false},volume:function(){return this.ytPlayer?this.ytPlayer.getVolume()/100:1},setVolume:function(percentAsDecimal){if(!this.ytPlayer){return}this.ytPlayer.setVolume(percentAsDecimal*100);this.setTimeout(function(){this.trigger("volumechange")},50)},muted:function(){return this.ytPlayer?this.ytPlayer.isMuted():false},setMuted:function(mute){if(!this.ytPlayer){return}else{this.muted(true)}if(mute){this.ytPlayer.mute()}else{this.ytPlayer.unMute()}this.setTimeout(function(){this.trigger("volumechange")},50)},buffered:function(){if(!this.ytPlayer||!this.ytPlayer.getVideoLoadedFraction){return videojs.createTimeRange()}var bufferedEnd=this.ytPlayer.getVideoLoadedFraction()*this.ytPlayer.getDuration();return videojs.createTimeRange(0,bufferedEnd)},preload:function(){},load:function(){},reset:function(){},supportsFullScreen:function(){return true},checkHighResPoster:function(){var uri="https://img.youtube.com/vi/"+this.url.videoId+"/maxresdefault.jpg";try{var image=new Image;image.onload=function(){if("naturalHeight"in image){if(image.naturalHeight<=90||image.naturalWidth<=120){return}}else if(image.height<=90||image.width<=120){return}this.poster_=uri;this.trigger("posterchange")}.bind(this);image.onerror=function(){};image.src=uri}catch(e){}}});Youtube.isSupported=function(){return true};Youtube.canPlaySource=function(e){return Youtube.canPlayType(e.type)};Youtube.canPlayType=function(e){return e==="video/youtube"};Youtube.parseUrl=function(url){var result={videoId:null};var regex=/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;var match=url.match(regex);if(match&&match[2].length===11){result.videoId=match[2]}var regPlaylist=/[?&]list=([^#\&\?]+)/;match=url.match(regPlaylist);if(match&&match[1]){result.listId=match[1]}return result};function apiLoaded(){YT.ready(function(){Youtube.isApiReady=true;for(var i=0;i<Youtube.apiReadyQueue.length;++i){Youtube.apiReadyQueue[i].initYTPlayer()}})}function loadScript(src,callback){var loaded=false;var tag=document.createElement("script");var firstScriptTag=document.getElementsByTagName("script")[0];firstScriptTag.parentNode.insertBefore(tag,firstScriptTag);tag.onload=function(){if(!loaded){loaded=true;callback()}};tag.onreadystatechange=function(){if(!loaded&&(this.readyState==="complete"||this.readyState==="loaded")){loaded=true;callback()}};tag.src=src}function injectCss(){var css=".vjs-youtube .vjs-iframe-blocker { display: none; }"+".vjs-youtube.vjs-user-inactive .vjs-iframe-blocker { display: block; }"+".vjs-youtube .vjs-poster { background-size: cover; }"+".vjs-youtube-mobile .vjs-big-play-button { display: none; }";var head=document.head||document.getElementsByTagName("head")[0];var style=document.createElement("style");style.type="text/css";if(style.styleSheet){style.styleSheet.cssText=css}else{style.appendChild(document.createTextNode(css))}head.appendChild(style)}Youtube.apiReadyQueue=[];loadScript("https://www.youtube.com/iframe_api",apiLoaded);injectCss();if(typeof videojs.registerTech!=="undefined"){videojs.registerTech("Youtube",Youtube)}else{videojs.registerComponent("Youtube",Youtube)}});
(function() {
  var defaults = {
      width:0, height:0, basePath : ""
    },
    extend = function() {
      var args, target, i, object, property;
      args = Array.prototype.slice.call(arguments);
      target = args.shift() || {};
      for (i in args) {
        object = args[i];
        for (property in object) {
          if (object.hasOwnProperty(property)) {
            if (typeof object[property] === 'object') {
              target[property] = extend(target[property], object[property]);
            } else {
              target[property] = object[property];
            }
          }
        }
      }
      return target;
    },
    getComputedStyle = function(el, pseudo) {
      return function(prop) {
        if (window.getComputedStyle) {
          return window.getComputedStyle(el, pseudo)[prop];
        } else {
          return el.currentStyle[prop];
        }
      };
    },
    offsetParent = function(el) {
      if (el.nodeName !== 'HTML' && getComputedStyle(el)('position') === 'static') {
        return offsetParent(el.offsetParent);
      }
      return el;
    },
    getScrollOffset = function() {
      if (window.pageXOffset) {
        return {
          x: window.pageXOffset,
          y: window.pageYOffset
        };
      }
      return {
        x: document.documentElement.scrollLeft,
        y: document.documentElement.scrollTop
      };
    },
    parseImageLink = function(imglocation) {
      var lsrc, clip, hashindex, hashstring;
      hashindex = imglocation.indexOf('#');
      if (hashindex === -1) {
        return {src:imglocation,w:0,h:0,x:0,y:0};
      } 
      lsrc = imglocation.substring(0,hashindex);
      hashstring = imglocation.substring(hashindex+1);
      if (hashstring.substring(0,5) !== 'xywh=') {
        return {src:defaults.basePath + lsrc,w:0,h:0,x:0,y:0};
      } 
      var data = hashstring.substring(5).split(',');
      return {src:defaults.basePath + lsrc,w:parseInt(data[2]),h:parseInt(data[3]),x:parseInt(data[0]),y:parseInt(data[1])};
    };

  /**
   * register the thubmnails plugin
   */
  videojs.plugin('thumbnails', function(options) {
    var div, settings, img, player, progressControl, duration, moveListener, moveCancel, thumbTrack;
    defaults.basePath = options.basePath || defaults.basePath;
    settings = extend({}, defaults, options);
    player = this;
    //detect which track we use. For now we just use the first metadata track
    var numtracks = player.textTracks().length;
    if (numtracks === 0) {
      return;
    }
    i = 0;
    while (i<numtracks) {
      if (player.textTracks()[i].kind==='metadata') {
        thumbTrack = player.textTracks()[i];
        //Chrome needs this
        thumbTrack.mode = 'hidden';
        break;
      }
      i++;
    }
    (function() {
      var progressControl, addFakeActive, removeFakeActive;
      // Android doesn't support :active and :hover on non-anchor and non-button elements
      // so, we need to fake the :active selector for thumbnails to show up.
      if (navigator.userAgent.toLowerCase().indexOf("android") !== -1) {
        progressControl = player.controlBar.progressControl;

        addFakeActive = function() {
          progressControl.addClass('fake-active');
        };
        removeFakeActive = function() {
          progressControl.removeClass('fake-active');
        };

        progressControl.on('touchstart', addFakeActive);
        progressControl.on('touchend', removeFakeActive);
        progressControl.on('touchcancel', removeFakeActive);
      }
    })();

    // create the thumbnail
    div = document.createElement('div');
    div.className = 'vjs-thumbnail-holder';
    img = document.createElement('img');
    div.appendChild(img);
    img.className = 'vjs-thumbnail';
    dur = document.createElement('div');
    dur.className = 'vjs-thumb-duration';
    div.appendChild(dur);

    // keep track of the duration to calculate correct thumbnail to display
    duration = player.duration();
    
    // when the container is MP4
    player.on('durationchange', function(event) {
      duration = player.duration();
    });

    // when the container is HLS
    player.on('loadedmetadata', function(event) {
      duration = player.duration();
    });

    // add the thumbnail to the player
    progressControl = player.controlBar.progressControl;
    progressControl.el().appendChild(div);
    
	var eleForTime;
    for( var e in progressControl.el().childNodes ) {
      var childNode = progressControl.el().childNodes[e];
      if( childNode.className.indexOf('vjs-progress-holder') >= 0 ) {
        eleForTime = childNode;
        break;
      }
    }    

    moveListener = function(event) {
      var mouseTime, time, active, left, setting, pageX, right, width, halfWidth, pageXOffset, clientRect;
      active = 0;
      pageXOffset = getScrollOffset().x;
      clientRect = offsetParent(progressControl.el()).getBoundingClientRect();
      right = (clientRect.width || clientRect.right) + pageXOffset;

      pageX = event.pageX;
      if (event.changedTouches) {
        pageX = event.changedTouches[0].pageX;
      }

      // find the page offset of the mouse
      left = pageX || (event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft);
      // subtract the page offset of the positioned offset parent
	  left -= eleForTime.getBoundingClientRect().left + pageXOffset;

      // apply updated styles to the thumbnail if necessary
      // mouseTime is the position of the mouse along the progress control bar
      // `left` applies to the mouse position relative to the player so we need
      // to remove the progress control's left offset to know the mouse position
      // relative to the progress control
	  mouseTime = left / eleForTime.getBoundingClientRect().width * duration;
	  	  	
	  dur.innerHTML = $('.vjs-mouse-display').attr('data-current-time');

      //Now check which of the cues applies
      var cnum = thumbTrack&&thumbTrack.cues.length;
      i = 0;
      while (i<cnum) {
        var ccue = thumbTrack.cues[i];
        if (ccue.startTime <= mouseTime && ccue.endTime >= mouseTime) {
          setting = parseImageLink(ccue.text);
          break;
        }
        i++;
      }
      //None found, so show nothing
      if (typeof setting === 'undefined') {
        return;
      } 

      //Changed image?
      if (setting.src && img.src != setting.src) {
        img.src = setting.src;
      }

      //Fall back to plugin defaults in case no height/width is specified
      if (setting.w === 0) {
        setting.w = settings.width;
      }
      if (setting.h === 0) {
        setting.h = settings.height;
      }

      //Set the container width/height if it changed
      if (div.style.width != setting.w || div.style.height != setting.h) {
        div.style.width = setting.w + 'px';
        div.style.height = setting.h + 'px';
      }
      //Set the image cropping
      img.style.left = -(setting.x) + 'px';
      img.style.top = -(setting.y) + 'px';
      img.style.clip = 'rect('+setting.y+'px,'+(setting.w+setting.x)+'px,'+(setting.y+setting.h)+'px,'+setting.x+'px)';
      
      width = setting.w;
      halfWidth = width / 2;

      // make sure that the thumbnail doesn't fall off the right side of the left side of the player
      if ( (left + halfWidth) > right ) {
        left = right - width;
      } else if (left < halfWidth) {
        left = 0;
      } else {
        left = left - halfWidth;
      }

      div.style.left = left + 'px';
    };

    // update the thumbnail while hovering
    progressControl.on('mousemove', moveListener);
    progressControl.on('touchmove', moveListener);

    moveCancel = function(event) {
      div.style.left = '-1000px';
    };

    // move the placeholder out of the way when not hovering
    progressControl.on('mouseout', moveCancel);
    progressControl.on('touchcancel', moveCancel);
    progressControl.on('touchend', moveCancel);
    player.on('userinactive', moveCancel);
  });  
})();
