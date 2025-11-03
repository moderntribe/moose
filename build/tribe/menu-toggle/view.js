import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "./wp-content/themes/core/assets/js/config/options.js":
/*!************************************************************!*\
  !*** ./wp-content/themes/core/assets/js/config/options.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   HEADER_BREAKPOINT: () => (/* binding */ HEADER_BREAKPOINT),
/* harmony export */   MOBILE_BREAKPOINT: () => (/* binding */ MOBILE_BREAKPOINT)
/* harmony export */ });
// breakpoint settings

const MOBILE_BREAKPOINT = 768;
const HEADER_BREAKPOINT = 960;

/***/ }),

/***/ "./wp-content/themes/core/assets/js/config/state.js":
/*!**********************************************************!*\
  !*** ./wp-content/themes/core/assets/js/config/state.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  desktop_initialized: false,
  is_desktop: false,
  is_mobile: false,
  mobile_initialized: false,
  v_height: 0,
  v_width: 0,
  isMobileMenuShown: false
});

/***/ }),

/***/ "./wp-content/themes/core/assets/js/utils/events.js":
/*!**********************************************************!*\
  !*** ./wp-content/themes/core/assets/js/utils/events.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ready: () => (/* binding */ ready),
/* harmony export */   triggerCustomEvent: () => (/* binding */ triggerCustomEvent)
/* harmony export */ });
/**
 * @module
 * @description Some event functions for use in other modules
 */

const ready = fn => {
  if (document.readyState !== 'loading') {
    fn();
  } else if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', fn);
  } else {
    document.attachEvent('onreadystatechange', () => {
      if (document.readyState !== 'loading') {
        fn();
      }
    });
  }
};

/**
 * @function triggerCustomEvent
 * @description Trigger a custom event
 * @param {string} type   The event type
 * @param {Node}   el     The element on which to emit the event
 * @param {*}      detail Any details to pass along with the event
 */
const triggerCustomEvent = (type, el = document, detail = {}) => {
  // Event type is required
  if (!type) {
    return;
  }

  // Create new event
  // eslint-disable-next-line no-undef
  const event = new CustomEvent(type, {
    bubbles: true,
    cancelable: true,
    detail
  });

  // Dispatch event
  return el.dispatchEvent(event);
};


/***/ }),

/***/ "./wp-content/themes/core/assets/js/utils/tools.js":
/*!*********************************************************!*\
  !*** ./wp-content/themes/core/assets/js/utils/tools.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bodyLock: () => (/* binding */ bodyLock),
/* harmony export */   debounce: () => (/* binding */ debounce)
/* harmony export */ });
/**
 * @module
 * @description Vanilla JS cross browser utilities
 */

/**
 * @function debounce
 * @description Run a callback after a specified wait duration.
 * @param {Function} callback
 * @param {number}   wait
 */

const debounce = (callback, wait = 200) => {
  let timeoutId = null;
  return (...args) => {
    window.clearTimeout(timeoutId);
    timeoutId = window.setTimeout(() => {
      callback.apply(null, args);
    }, wait);
  };
};

/**
 * @function bodyLock
 * @description Lock or unlock page scrolling.
 * @param {boolean} lock
 */
const bodyLock = (lock = false) => {
  if (lock) {
    document.body.style.overflow = 'hidden';
    return;
  }
  document.body.style.overflow = 'visible';
};

/***/ }),

/***/ "@wordpress/interactivity":
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
/***/ ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/define property getters */
/******/ (() => {
/******/ 	// define getter functions for harmony exports
/******/ 	__webpack_require__.d = (exports, definition) => {
/******/ 		for(var key in definition) {
/******/ 			if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 				Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 			}
/******/ 		}
/******/ 	};
/******/ })();
/******/ 
/******/ /* webpack/runtime/hasOwnProperty shorthand */
/******/ (() => {
/******/ 	__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ })();
/******/ 
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************************************************!*\
  !*** ./wp-content/themes/core/blocks/tribe/menu-toggle/view.js ***!
  \*****************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");
/* harmony import */ var utils_events_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! utils/events.js */ "./wp-content/themes/core/assets/js/utils/events.js");
/* harmony import */ var config_state_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! config/state.js */ "./wp-content/themes/core/assets/js/config/state.js");
/* harmony import */ var config_options_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! config/options.js */ "./wp-content/themes/core/assets/js/config/options.js");
/* harmony import */ var utils_tools_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! utils/tools.js */ "./wp-content/themes/core/assets/js/utils/tools.js");





const el = {
  body: null,
  toggle: null
};
const classes = {
  mobileMenuShown: 'mobile-menu-shown'
};

/**
 * @function openMobileMenu
 *
 * @description opens the mobile menu and sets the global isMobileMenuShown to true
 */
const openMobileMenu = () => {
  el.body.classList.add(classes.mobileMenuShown);
  config_state_js__WEBPACK_IMPORTED_MODULE_2__["default"].isMobileMenuShown = true;
  (0,utils_tools_js__WEBPACK_IMPORTED_MODULE_4__.bodyLock)(true);
  (0,utils_events_js__WEBPACK_IMPORTED_MODULE_1__.triggerCustomEvent)('modern_tribe/mobile_menu_open');
};

/**
 * @function closeMobileMenu
 *
 * @description closes the mobile menu and sets the global isMobileMenuShown to true
 */
const closeMobileMenu = () => {
  el.body.classList.remove(classes.mobileMenuShown);
  config_state_js__WEBPACK_IMPORTED_MODULE_2__["default"].isMobileMenuShown = false;
  (0,utils_tools_js__WEBPACK_IMPORTED_MODULE_4__.bodyLock)(false);
};

/**
 * @function maybeRemoveActiveMobileMenuState
 *
 * @description checks if active mobile menu state can/should be removed before removing it
 */
const maybeRemoveActiveMobileMenuState = () => {
  if (el.body.classList.contains(classes.mobileMenuShown) && config_state_js__WEBPACK_IMPORTED_MODULE_2__["default"].isMobileMenuShown && window.innerWidth > config_options_js__WEBPACK_IMPORTED_MODULE_3__.HEADER_BREAKPOINT) {
    closeMobileMenu();
  }
};

/**
 * @function handleMobileMenuToggleClick
 *
 * @description handle click of mobile menu toggle button
 */
const handleMobileMenuToggleClick = () => {
  if (el.body.classList.contains(classes.mobileMenuShown) && config_state_js__WEBPACK_IMPORTED_MODULE_2__["default"].isMobileMenuShown) {
    closeMobileMenu();
    return;
  }
  openMobileMenu();
};

/**
 * @function bindEvents
 *
 * @description bind events to cached elements
 */
const bindEvents = () => {
  el.toggle.addEventListener('click', handleMobileMenuToggleClick);

  // handle resize
  document.addEventListener('modern_tribe/resize_executed', maybeRemoveActiveMobileMenuState);
  document.addEventListener('modern_tribe/close_on_escape', closeMobileMenu);
};

/**
 * @function cacheElements
 *
 * @description save elements for later use
 */
const cacheElements = () => {
  el.body = document.body;
  el.toggle = document.querySelector('[data-js="menu-toggle"]');
};

/**
 * @function init
 *
 * @description kick of this modules functionality
 */
const init = () => {
  cacheElements();
  bindEvents();
};

// ready( init );

const {
  state
} = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)('menuToggle', {
  state: {
    open: false
  },
  actions: {
    toggleMenu() {
      if (state.open) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    }
  }
});
})();


//# sourceMappingURL=view.js.map