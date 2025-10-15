/**
 * videojs-settings-menu
 * @version 0.0.2
 * @copyright 2016 Fruitsapje <hero@streamone.nl>
 * @license MIT
 */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.videojsSettingsMenu = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){

/**
 * @file settings-menu-button.js
 */
'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; desc = parent = undefined; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _videoJs = (typeof window !== "undefined" ? window['videojs'] : typeof global !== "undefined" ? global['videojs'] : null);

var _videoJs2 = _interopRequireDefault(_videoJs);

var _settingsMenuItemJs = require('./settings-menu-item.js');

var _settingsMenuItemJs2 = _interopRequireDefault(_settingsMenuItemJs);

var MenuButton = _videoJs2['default'].getComponent('MenuButton');
var Menu = _videoJs2['default'].getComponent('Menu');
var Component = _videoJs2['default'].getComponent('Component');

/**
 * The component for controlling the settings menu
 *
 * @param {Player|Object} player
 * @param {Object=} options
 * @extends MenuButton
 * @class SettingsMenuButton
 */

var SettingsMenuButton = (function (_MenuButton) {
  _inherits(SettingsMenuButton, _MenuButton);

  function SettingsMenuButton(player, options) {
    _classCallCheck(this, SettingsMenuButton);

    _get(Object.getPrototypeOf(SettingsMenuButton.prototype), 'constructor', this).call(this, player, options);

    this.el_.setAttribute('aria-label', 'Settings Menu');

    this.on('mouseleave', _videoJs2['default'].bind(this, this.hideChildren));
  }

  /**
   * Allow sub components to stack CSS class names
   *
   * @return {String} The constructed class name
   * @method buildCSSClass
   */

  _createClass(SettingsMenuButton, [{
    key: 'buildCSSClass',
    value: function buildCSSClass() {
      // vjs-icon-cog can be removed when the settings menu is integrated in video.js
      return 'vjs-settings-menu vjs-icon-cog ' + _get(Object.getPrototypeOf(SettingsMenuButton.prototype), 'buildCSSClass', this).call(this);
    }

    /**
     * Create the settings menu
     *
     * @return {Menu} Menu object populated with items
     * @method createMenu
     */
  }, {
    key: 'createMenu',
    value: function createMenu() {
      var menu = new Menu(this.player());
      var entries = this.options_.entries;

      if (entries) {

        var openSubMenu = function openSubMenu() {

          if (_videoJs2['default'].hasClass(this.el_, 'open')) {
            _videoJs2['default'].removeClass(this.el_, 'open');
          } else {
            _videoJs2['default'].addClass(this.el_, 'open');
          }
        };

        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
          for (var _iterator = entries[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
            var entry = _step.value;

            var settingsMenuItem = new _settingsMenuItemJs2['default'](this.player(), this.options_, entry);

            menu.addChild(settingsMenuItem);

            // Hide children to avoid sub menus stacking on top of each other
            // or having multiple menus open
            settingsMenuItem.on('click', _videoJs2['default'].bind(this, this.hideChildren));

            // Wether to add or remove selected class on the settings sub menu element
            settingsMenuItem.on('click', openSubMenu);
          }
        } catch (err) {
          _didIteratorError = true;
          _iteratorError = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion && _iterator['return']) {
              _iterator['return']();
            }
          } finally {
            if (_didIteratorError) {
              throw _iteratorError;
            }
          }
        }
      }

      return menu;
    }

    /**
     * Hide all the sub menus
     */
  }, {
    key: 'hideChildren',
    value: function hideChildren() {
      var _iteratorNormalCompletion2 = true;
      var _didIteratorError2 = false;
      var _iteratorError2 = undefined;

      try {
        for (var _iterator2 = this.menu.children()[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
          var menuChild = _step2.value;

          menuChild.hideSubMenu();
        }
      } catch (err) {
        _didIteratorError2 = true;
        _iteratorError2 = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion2 && _iterator2['return']) {
            _iterator2['return']();
          }
        } finally {
          if (_didIteratorError2) {
            throw _iteratorError2;
          }
        }
      }
    }
  }]);

  return SettingsMenuButton;
})(MenuButton);

SettingsMenuButton.prototype.controlText_ = 'Settings Menu';

Component.registerComponent('SettingsMenuButton', SettingsMenuButton);
exports['default'] = SettingsMenuButton;
module.exports = exports['default'];
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{"./settings-menu-item.js":2}],2:[function(require,module,exports){
(function (global){
/**
 * @file settings-menu-item.js
 */

'use strict';

Object.defineProperty(exports, '__esModule', {
  value: true
});

var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

var _get = function get(_x, _x2, _x3) { var _again = true; _function: while (_again) { var object = _x, property = _x2, receiver = _x3; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x = parent; _x2 = property; _x3 = receiver; _again = true; desc = parent = undefined; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

var _videoJs = (typeof window !== "undefined" ? window['videojs'] : typeof global !== "undefined" ? global['videojs'] : null);

var _videoJs2 = _interopRequireDefault(_videoJs);

var MenuItem = _videoJs2['default'].getComponent('MenuItem');
var playbackRateMenuButton = _videoJs2['default'].getComponent('PlaybackRateMenuButton');
var component = _videoJs2['default'].getComponent('Component');

var toTitleCase = function toTitleCase(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
};

/**
 * The specific menu item type for selecting a setting
 *
 * @param {Player|Object} player
 * @param {Object=} options
 * @param {String=} entry
 * @extends MenuItem
 * @class SettingsMenuItem
 */

var SettingsMenuItem = (function (_MenuItem) {
  _inherits(SettingsMenuItem, _MenuItem);

  function SettingsMenuItem(player, options, entry) {
    _classCallCheck(this, SettingsMenuItem);

    _get(Object.getPrototypeOf(SettingsMenuItem.prototype), 'constructor', this).call(this, player, options);

    var subMenuName = toTitleCase(entry);

    var SubMenuComponent = _videoJs2['default'].getComponent(subMenuName);

    if (!SubMenuComponent) {
      throw new Error('Component ' + subMenuName + ' does not exist');
    }

    this.subMenu = new SubMenuComponent(this.player(), options);

    var update = _videoJs2['default'].bind(this, this.update);
    // To update the sub menu value on click, setTimeout is needed because
    // updating the value is not instant
    var updateAfterTimeout = function updateAfterTimeout() {
      setTimeout(update, 0);
    };

    var _iteratorNormalCompletion = true;
    var _didIteratorError = false;
    var _iteratorError = undefined;

    try {
      for (var _iterator = this.subMenu.menu.children()[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
        var item = _step.value;

        if (!(item instanceof component)) {
          continue;
        }
        item.on('click', updateAfterTimeout);
      }
    } catch (err) {
      _didIteratorError = true;
      _iteratorError = err;
    } finally {
      try {
        if (!_iteratorNormalCompletion && _iterator['return']) {
          _iterator['return']();
        }
      } finally {
        if (_didIteratorError) {
          throw _iteratorError;
        }
      }
    }

    this.update();
  }

  /**
   * Create the component's DOM element
   *
   * @return {Element}
   * @method createEl
   */

  _createClass(SettingsMenuItem, [{
    key: 'createEl',
    value: function createEl() {
      // Hide this component by default
      var el = _videoJs2['default'].createEl('li', {
        className: 'vjs-menu-item'
      });

      this.settingsSubMenuTitleEl_ = _videoJs2['default'].createEl('div', {
        className: 'vjs-settings-sub-menu-title'
      });

      el.appendChild(this.settingsSubMenuTitleEl_);

      this.settingsSubMenuValueEl_ = _videoJs2['default'].createEl('div', {
        className: 'vjs-settings-sub-menu-value'
      });

      el.appendChild(this.settingsSubMenuValueEl_);

      this.settingsSubMenuEl_ = _videoJs2['default'].createEl('div', {
        className: 'vjs-settings-sub-menu vjs-hidden'
      });

      el.appendChild(this.settingsSubMenuEl_);

      return el;
    }

    /**
     * Handle click on menu item
     *
     * @method handleClick
     */
  }, {
    key: 'handleClick',
    value: function handleClick() {
      // Remove open class to ensure only the open submenu gets this class
      _videoJs2['default'].removeClass(this.el_, 'open');

      _get(Object.getPrototypeOf(SettingsMenuItem.prototype), 'handleClick', this).call(this);

      // Wether to add or remove vjs-hidden class on the settingsSubMenuEl element
      if (_videoJs2['default'].hasClass(this.settingsSubMenuEl_, 'vjs-hidden')) {
        _videoJs2['default'].removeClass(this.settingsSubMenuEl_, 'vjs-hidden');
      } else {
        _videoJs2['default'].addClass(this.settingsSubMenuEl_, 'vjs-hidden');
      }
    }

    /**
     * Update the sub menus
     *
     * @method update
     */
  }, {
    key: 'update',
    value: function update() {
      this.settingsSubMenuTitleEl_.innerHTML = this.subMenu.controlText_ + ':';
      this.settingsSubMenuEl_.appendChild(this.subMenu.menu.el_);

      // Playback rate menu button doesn't get a vjs-selected class
      // or sets options_['selected'] on the selected playback rate.
      // Thus we get the submenu value based on the labelEl of playbackRateMenuButton
      if (this.subMenu instanceof playbackRateMenuButton) {
        this.settingsSubMenuValueEl_.innerHTML = this.subMenu.labelEl_.innerHTML;
      } else {
        // Loop trough the submenu items to find the selected child
        var _iteratorNormalCompletion2 = true;
        var _didIteratorError2 = false;
        var _iteratorError2 = undefined;

        try {
          for (var _iterator2 = this.subMenu.menu.children_[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
            var subMenuItem = _step2.value;

            if (!(subMenuItem instanceof component)) {
              continue;
            }
            // Set submenu value based on what item is selected
            if (subMenuItem.options_.selected || subMenuItem.hasClass('vjs-selected')) {
              this.settingsSubMenuValueEl_.innerHTML = subMenuItem.options_.label;
            }
          }
        } catch (err) {
          _didIteratorError2 = true;
          _iteratorError2 = err;
        } finally {
          try {
            if (!_iteratorNormalCompletion2 && _iterator2['return']) {
              _iterator2['return']();
            }
          } finally {
            if (_didIteratorError2) {
              throw _iteratorError2;
            }
          }
        }
      }
    }

    /**
     * Hide the sub menu
     */
  }, {
    key: 'hideSubMenu',
    value: function hideSubMenu() {
      if (_videoJs2['default'].hasClass(this.el_, 'open')) {
        _videoJs2['default'].addClass(this.settingsSubMenuEl_, 'vjs-hidden');
        _videoJs2['default'].removeClass(this.el_, 'open');
      }
    }
  }]);

  return SettingsMenuItem;
})(MenuItem);

SettingsMenuItem.prototype.contentElType = 'button';

_videoJs2['default'].registerComponent('SettingsMenuItem', SettingsMenuItem);
exports['default'] = SettingsMenuItem;
module.exports = exports['default'];
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}],3:[function(require,module,exports){
'use strict';

require('./components/settings-menu-button.js');

require('./components/settings-menu-item.js');
},{"./components/settings-menu-button.js":1,"./components/settings-menu-item.js":2}]},{},[3])(3)
});