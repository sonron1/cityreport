(self["webpackChunk"] = self["webpackChunk"] || []).push([["performance-optimizer"],{

/***/ "./assets/js/optimizations.js":
/*!************************************!*\
  !*** ./assets/js/optimizations.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
__webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
__webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
__webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");
__webpack_require__(/*! core-js/modules/es.symbol.to-primitive.js */ "./node_modules/core-js/modules/es.symbol.to-primitive.js");
__webpack_require__(/*! core-js/modules/es.error.cause.js */ "./node_modules/core-js/modules/es.error.cause.js");
__webpack_require__(/*! core-js/modules/es.error.to-string.js */ "./node_modules/core-js/modules/es.error.to-string.js");
__webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");
__webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");
__webpack_require__(/*! core-js/modules/es.date.to-primitive.js */ "./node_modules/core-js/modules/es.date.to-primitive.js");
__webpack_require__(/*! core-js/modules/es.map.js */ "./node_modules/core-js/modules/es.map.js");
__webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
__webpack_require__(/*! core-js/modules/es.object.define-property.js */ "./node_modules/core-js/modules/es.object.define-property.js");
__webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
__webpack_require__(/*! core-js/modules/es.promise.js */ "./node_modules/core-js/modules/es.promise.js");
__webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
__webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
__webpack_require__(/*! core-js/modules/web.timers.js */ "./node_modules/core-js/modules/web.timers.js");
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
// ✅ Optimisations globales de performance
var PerformanceOptimizer = /*#__PURE__*/function () {
  "use strict";

  function PerformanceOptimizer() {
    _classCallCheck(this, PerformanceOptimizer);
    this.debounceTimers = new Map();
    this.throttleTimers = new Map();
    this.init();
  }
  return _createClass(PerformanceOptimizer, [{
    key: "init",
    value: function init() {
      this.optimizeScrollEvents();
      this.optimizeResizeEvents();
      this.preloadCriticalResources();
      this.setupConnectionOptimizations();
    }

    // ✅ Debounce générique
  }, {
    key: "debounce",
    value: function debounce(func, wait, key) {
      var _this = this;
      return function () {
        for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
          args[_key] = arguments[_key];
        }
        if (_this.debounceTimers.has(key)) {
          clearTimeout(_this.debounceTimers.get(key));
        }
        var timer = setTimeout(function () {
          func.apply(_this, args);
          _this.debounceTimers["delete"](key);
        }, wait);
        _this.debounceTimers.set(key, timer);
      };
    }

    // ✅ Throttle générique
  }, {
    key: "throttle",
    value: function throttle(func, limit, key) {
      var _this2 = this;
      return function () {
        if (!_this2.throttleTimers.has(key)) {
          for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
            args[_key2] = arguments[_key2];
          }
          func.apply(_this2, args);
          _this2.throttleTimers.set(key, true);
          setTimeout(function () {
            _this2.throttleTimers["delete"](key);
          }, limit);
        }
      };
    }
  }, {
    key: "optimizeScrollEvents",
    value: function optimizeScrollEvents() {
      // Optimiser tous les événements scroll
      var scrollHandler = this.throttle(function () {
        // Logique de scroll
      }, 16, 'scroll'); // 60fps

      window.addEventListener('scroll', scrollHandler, {
        passive: true
      });
    }
  }, {
    key: "optimizeResizeEvents",
    value: function optimizeResizeEvents() {
      // Optimiser les événements resize
      var resizeHandler = this.debounce(function () {
        // Logique de resize
      }, 250, 'resize');
      window.addEventListener('resize', resizeHandler);
    }
  }, {
    key: "preloadCriticalResources",
    value: function preloadCriticalResources() {
      // Précharger les ressources critiques
      var criticalResources = ['/api/categories', '/api/villes'];
      criticalResources.forEach(function (url) {
        fetch(url, {
          method: 'GET',
          headers: {
            'Cache-Control': 'max-age=300'
          }
        })["catch"](function () {
          // Ignorer les erreurs de préchargement
        });
      });
    }
  }, {
    key: "setupConnectionOptimizations",
    value: function setupConnectionOptimizations() {
      // Optimisations basées sur la connexion
      if ('connection' in navigator) {
        var connection = navigator.connection;
        if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
          // Réduire la fréquence de polling pour les connexions lentes
          document.dispatchEvent(new CustomEvent('slow-connection'));
        }
      }
    }
  }]);
}(); // Initialisation globale
window.performanceOptimizer = new PerformanceOptimizer();

/***/ }),

/***/ "./node_modules/core-js/internals/schedulers-fix.js":
/*!**********************************************************!*\
  !*** ./node_modules/core-js/internals/schedulers-fix.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var globalThis = __webpack_require__(/*! ../internals/global-this */ "./node_modules/core-js/internals/global-this.js");
var apply = __webpack_require__(/*! ../internals/function-apply */ "./node_modules/core-js/internals/function-apply.js");
var isCallable = __webpack_require__(/*! ../internals/is-callable */ "./node_modules/core-js/internals/is-callable.js");
var ENVIRONMENT = __webpack_require__(/*! ../internals/environment */ "./node_modules/core-js/internals/environment.js");
var USER_AGENT = __webpack_require__(/*! ../internals/environment-user-agent */ "./node_modules/core-js/internals/environment-user-agent.js");
var arraySlice = __webpack_require__(/*! ../internals/array-slice */ "./node_modules/core-js/internals/array-slice.js");
var validateArgumentsLength = __webpack_require__(/*! ../internals/validate-arguments-length */ "./node_modules/core-js/internals/validate-arguments-length.js");

var Function = globalThis.Function;
// dirty IE9- and Bun 0.3.0- checks
var WRAP = /MSIE .\./.test(USER_AGENT) || ENVIRONMENT === 'BUN' && (function () {
  var version = globalThis.Bun.version.split('.');
  return version.length < 3 || version[0] === '0' && (version[1] < 3 || version[1] === '3' && version[2] === '0');
})();

// IE9- / Bun 0.3.0- setTimeout / setInterval / setImmediate additional parameters fix
// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
// https://github.com/oven-sh/bun/issues/1633
module.exports = function (scheduler, hasTimeArg) {
  var firstParamIndex = hasTimeArg ? 2 : 1;
  return WRAP ? function (handler, timeout /* , ...arguments */) {
    var boundArgs = validateArgumentsLength(arguments.length, 1) > firstParamIndex;
    var fn = isCallable(handler) ? handler : Function(handler);
    var params = boundArgs ? arraySlice(arguments, firstParamIndex) : [];
    var callback = boundArgs ? function () {
      apply(fn, this, params);
    } : fn;
    return hasTimeArg ? scheduler(callback, timeout) : scheduler(callback);
  } : scheduler;
};


/***/ }),

/***/ "./node_modules/core-js/modules/web.set-interval.js":
/*!**********************************************************!*\
  !*** ./node_modules/core-js/modules/web.set-interval.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "./node_modules/core-js/internals/global-this.js");
var schedulersFix = __webpack_require__(/*! ../internals/schedulers-fix */ "./node_modules/core-js/internals/schedulers-fix.js");

var setInterval = schedulersFix(globalThis.setInterval, true);

// Bun / IE9- setInterval additional parameters fix
// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
$({ global: true, bind: true, forced: globalThis.setInterval !== setInterval }, {
  setInterval: setInterval
});


/***/ }),

/***/ "./node_modules/core-js/modules/web.set-timeout.js":
/*!*********************************************************!*\
  !*** ./node_modules/core-js/modules/web.set-timeout.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var globalThis = __webpack_require__(/*! ../internals/global-this */ "./node_modules/core-js/internals/global-this.js");
var schedulersFix = __webpack_require__(/*! ../internals/schedulers-fix */ "./node_modules/core-js/internals/schedulers-fix.js");

var setTimeout = schedulersFix(globalThis.setTimeout, true);

// Bun / IE9- setTimeout additional parameters fix
// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
$({ global: true, bind: true, forced: globalThis.setTimeout !== setTimeout }, {
  setTimeout: setTimeout
});


/***/ }),

/***/ "./node_modules/core-js/modules/web.timers.js":
/*!****************************************************!*\
  !*** ./node_modules/core-js/modules/web.timers.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

// TODO: Remove this module from `core-js@4` since it's split to modules listed below
__webpack_require__(/*! ../modules/web.set-interval */ "./node_modules/core-js/modules/web.set-interval.js");
__webpack_require__(/*! ../modules/web.set-timeout */ "./node_modules/core-js/modules/web.set-timeout.js");


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_internals_an-instance_js-node_modules_core-js_internals_array-it-055933","vendors-node_modules_core-js_internals_dom-iterables_js-node_modules_core-js_internals_dom-to-aac983","vendors-node_modules_core-js_modules_es_date_to-primitive_js-node_modules_core-js_modules_es_-c7b0e4"], () => (__webpack_exec__("./assets/js/optimizations.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFPLENBQUMsdUZBQTZCO0FBQ3JDLG1CQUFPLENBQUMscUZBQTRCIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL29wdGltaXphdGlvbnMuanMiLCJ3ZWJwYWNrOi8vLy4vbm9kZV9tb2R1bGVzL2NvcmUtanMvaW50ZXJuYWxzL3NjaGVkdWxlcnMtZml4LmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jb3JlLWpzL21vZHVsZXMvd2ViLnNldC1pbnRlcnZhbC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9tb2R1bGVzL3dlYi5zZXQtdGltZW91dC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9tb2R1bGVzL3dlYi50aW1lcnMuanMiXSwic291cmNlc0NvbnRlbnQiOlsiLy8g4pyFIE9wdGltaXNhdGlvbnMgZ2xvYmFsZXMgZGUgcGVyZm9ybWFuY2VcclxuY2xhc3MgUGVyZm9ybWFuY2VPcHRpbWl6ZXIge1xyXG4gICAgY29uc3RydWN0b3IoKSB7XHJcbiAgICAgICAgdGhpcy5kZWJvdW5jZVRpbWVycyA9IG5ldyBNYXAoKTtcclxuICAgICAgICB0aGlzLnRocm90dGxlVGltZXJzID0gbmV3IE1hcCgpO1xyXG5cclxuICAgICAgICB0aGlzLmluaXQoKTtcclxuICAgIH1cclxuXHJcbiAgICBpbml0KCkge1xyXG4gICAgICAgIHRoaXMub3B0aW1pemVTY3JvbGxFdmVudHMoKTtcclxuICAgICAgICB0aGlzLm9wdGltaXplUmVzaXplRXZlbnRzKCk7XHJcbiAgICAgICAgdGhpcy5wcmVsb2FkQ3JpdGljYWxSZXNvdXJjZXMoKTtcclxuICAgICAgICB0aGlzLnNldHVwQ29ubmVjdGlvbk9wdGltaXphdGlvbnMoKTtcclxuICAgIH1cclxuXHJcbiAgICAvLyDinIUgRGVib3VuY2UgZ8OpbsOpcmlxdWVcclxuICAgIGRlYm91bmNlKGZ1bmMsIHdhaXQsIGtleSkge1xyXG4gICAgICAgIHJldHVybiAoLi4uYXJncykgPT4ge1xyXG4gICAgICAgICAgICBpZiAodGhpcy5kZWJvdW5jZVRpbWVycy5oYXMoa2V5KSkge1xyXG4gICAgICAgICAgICAgICAgY2xlYXJUaW1lb3V0KHRoaXMuZGVib3VuY2VUaW1lcnMuZ2V0KGtleSkpO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICBjb25zdCB0aW1lciA9IHNldFRpbWVvdXQoKCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgZnVuYy5hcHBseSh0aGlzLCBhcmdzKTtcclxuICAgICAgICAgICAgICAgIHRoaXMuZGVib3VuY2VUaW1lcnMuZGVsZXRlKGtleSk7XHJcbiAgICAgICAgICAgIH0sIHdhaXQpO1xyXG5cclxuICAgICAgICAgICAgdGhpcy5kZWJvdW5jZVRpbWVycy5zZXQoa2V5LCB0aW1lcik7XHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICAvLyDinIUgVGhyb3R0bGUgZ8OpbsOpcmlxdWVcclxuICAgIHRocm90dGxlKGZ1bmMsIGxpbWl0LCBrZXkpIHtcclxuICAgICAgICByZXR1cm4gKC4uLmFyZ3MpID0+IHtcclxuICAgICAgICAgICAgaWYgKCF0aGlzLnRocm90dGxlVGltZXJzLmhhcyhrZXkpKSB7XHJcbiAgICAgICAgICAgICAgICBmdW5jLmFwcGx5KHRoaXMsIGFyZ3MpO1xyXG4gICAgICAgICAgICAgICAgdGhpcy50aHJvdHRsZVRpbWVycy5zZXQoa2V5LCB0cnVlKTtcclxuXHJcbiAgICAgICAgICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLnRocm90dGxlVGltZXJzLmRlbGV0ZShrZXkpO1xyXG4gICAgICAgICAgICAgICAgfSwgbGltaXQpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuICAgIH1cclxuXHJcbiAgICBvcHRpbWl6ZVNjcm9sbEV2ZW50cygpIHtcclxuICAgICAgICAvLyBPcHRpbWlzZXIgdG91cyBsZXMgw6l2w6luZW1lbnRzIHNjcm9sbFxyXG4gICAgICAgIGNvbnN0IHNjcm9sbEhhbmRsZXIgPSB0aGlzLnRocm90dGxlKCgpID0+IHtcclxuICAgICAgICAgICAgLy8gTG9naXF1ZSBkZSBzY3JvbGxcclxuICAgICAgICB9LCAxNiwgJ3Njcm9sbCcpOyAvLyA2MGZwc1xyXG5cclxuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcignc2Nyb2xsJywgc2Nyb2xsSGFuZGxlciwgeyBwYXNzaXZlOiB0cnVlIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIG9wdGltaXplUmVzaXplRXZlbnRzKCkge1xyXG4gICAgICAgIC8vIE9wdGltaXNlciBsZXMgw6l2w6luZW1lbnRzIHJlc2l6ZVxyXG4gICAgICAgIGNvbnN0IHJlc2l6ZUhhbmRsZXIgPSB0aGlzLmRlYm91bmNlKCgpID0+IHtcclxuICAgICAgICAgICAgLy8gTG9naXF1ZSBkZSByZXNpemVcclxuICAgICAgICB9LCAyNTAsICdyZXNpemUnKTtcclxuXHJcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoJ3Jlc2l6ZScsIHJlc2l6ZUhhbmRsZXIpO1xyXG4gICAgfVxyXG5cclxuICAgIHByZWxvYWRDcml0aWNhbFJlc291cmNlcygpIHtcclxuICAgICAgICAvLyBQcsOpY2hhcmdlciBsZXMgcmVzc291cmNlcyBjcml0aXF1ZXNcclxuICAgICAgICBjb25zdCBjcml0aWNhbFJlc291cmNlcyA9IFtcclxuICAgICAgICAgICAgJy9hcGkvY2F0ZWdvcmllcycsXHJcbiAgICAgICAgICAgICcvYXBpL3ZpbGxlcydcclxuICAgICAgICBdO1xyXG5cclxuICAgICAgICBjcml0aWNhbFJlc291cmNlcy5mb3JFYWNoKHVybCA9PiB7XHJcbiAgICAgICAgICAgIGZldGNoKHVybCwge1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiAnR0VUJyxcclxuICAgICAgICAgICAgICAgIGhlYWRlcnM6IHsgJ0NhY2hlLUNvbnRyb2wnOiAnbWF4LWFnZT0zMDAnIH1cclxuICAgICAgICAgICAgfSkuY2F0Y2goKCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgLy8gSWdub3JlciBsZXMgZXJyZXVycyBkZSBwcsOpY2hhcmdlbWVudFxyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICBzZXR1cENvbm5lY3Rpb25PcHRpbWl6YXRpb25zKCkge1xyXG4gICAgICAgIC8vIE9wdGltaXNhdGlvbnMgYmFzw6llcyBzdXIgbGEgY29ubmV4aW9uXHJcbiAgICAgICAgaWYgKCdjb25uZWN0aW9uJyBpbiBuYXZpZ2F0b3IpIHtcclxuICAgICAgICAgICAgY29uc3QgY29ubmVjdGlvbiA9IG5hdmlnYXRvci5jb25uZWN0aW9uO1xyXG5cclxuICAgICAgICAgICAgaWYgKGNvbm5lY3Rpb24uZWZmZWN0aXZlVHlwZSA9PT0gJ3Nsb3ctMmcnIHx8IGNvbm5lY3Rpb24uZWZmZWN0aXZlVHlwZSA9PT0gJzJnJykge1xyXG4gICAgICAgICAgICAgICAgLy8gUsOpZHVpcmUgbGEgZnLDqXF1ZW5jZSBkZSBwb2xsaW5nIHBvdXIgbGVzIGNvbm5leGlvbnMgbGVudGVzXHJcbiAgICAgICAgICAgICAgICBkb2N1bWVudC5kaXNwYXRjaEV2ZW50KG5ldyBDdXN0b21FdmVudCgnc2xvdy1jb25uZWN0aW9uJykpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcblxyXG4vLyBJbml0aWFsaXNhdGlvbiBnbG9iYWxlXHJcbndpbmRvdy5wZXJmb3JtYW5jZU9wdGltaXplciA9IG5ldyBQZXJmb3JtYW5jZU9wdGltaXplcigpOyIsIid1c2Ugc3RyaWN0JztcbnZhciBnbG9iYWxUaGlzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2dsb2JhbC10aGlzJyk7XG52YXIgYXBwbHkgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZnVuY3Rpb24tYXBwbHknKTtcbnZhciBpc0NhbGxhYmxlID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2lzLWNhbGxhYmxlJyk7XG52YXIgRU5WSVJPTk1FTlQgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZW52aXJvbm1lbnQnKTtcbnZhciBVU0VSX0FHRU5UID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2Vudmlyb25tZW50LXVzZXItYWdlbnQnKTtcbnZhciBhcnJheVNsaWNlID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2FycmF5LXNsaWNlJyk7XG52YXIgdmFsaWRhdGVBcmd1bWVudHNMZW5ndGggPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvdmFsaWRhdGUtYXJndW1lbnRzLWxlbmd0aCcpO1xuXG52YXIgRnVuY3Rpb24gPSBnbG9iYWxUaGlzLkZ1bmN0aW9uO1xuLy8gZGlydHkgSUU5LSBhbmQgQnVuIDAuMy4wLSBjaGVja3NcbnZhciBXUkFQID0gL01TSUUgLlxcLi8udGVzdChVU0VSX0FHRU5UKSB8fCBFTlZJUk9OTUVOVCA9PT0gJ0JVTicgJiYgKGZ1bmN0aW9uICgpIHtcbiAgdmFyIHZlcnNpb24gPSBnbG9iYWxUaGlzLkJ1bi52ZXJzaW9uLnNwbGl0KCcuJyk7XG4gIHJldHVybiB2ZXJzaW9uLmxlbmd0aCA8IDMgfHwgdmVyc2lvblswXSA9PT0gJzAnICYmICh2ZXJzaW9uWzFdIDwgMyB8fCB2ZXJzaW9uWzFdID09PSAnMycgJiYgdmVyc2lvblsyXSA9PT0gJzAnKTtcbn0pKCk7XG5cbi8vIElFOS0gLyBCdW4gMC4zLjAtIHNldFRpbWVvdXQgLyBzZXRJbnRlcnZhbCAvIHNldEltbWVkaWF0ZSBhZGRpdGlvbmFsIHBhcmFtZXRlcnMgZml4XG4vLyBodHRwczovL2h0bWwuc3BlYy53aGF0d2cub3JnL211bHRpcGFnZS90aW1lcnMtYW5kLXVzZXItcHJvbXB0cy5odG1sI3RpbWVyc1xuLy8gaHR0cHM6Ly9naXRodWIuY29tL292ZW4tc2gvYnVuL2lzc3Vlcy8xNjMzXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIChzY2hlZHVsZXIsIGhhc1RpbWVBcmcpIHtcbiAgdmFyIGZpcnN0UGFyYW1JbmRleCA9IGhhc1RpbWVBcmcgPyAyIDogMTtcbiAgcmV0dXJuIFdSQVAgPyBmdW5jdGlvbiAoaGFuZGxlciwgdGltZW91dCAvKiAsIC4uLmFyZ3VtZW50cyAqLykge1xuICAgIHZhciBib3VuZEFyZ3MgPSB2YWxpZGF0ZUFyZ3VtZW50c0xlbmd0aChhcmd1bWVudHMubGVuZ3RoLCAxKSA+IGZpcnN0UGFyYW1JbmRleDtcbiAgICB2YXIgZm4gPSBpc0NhbGxhYmxlKGhhbmRsZXIpID8gaGFuZGxlciA6IEZ1bmN0aW9uKGhhbmRsZXIpO1xuICAgIHZhciBwYXJhbXMgPSBib3VuZEFyZ3MgPyBhcnJheVNsaWNlKGFyZ3VtZW50cywgZmlyc3RQYXJhbUluZGV4KSA6IFtdO1xuICAgIHZhciBjYWxsYmFjayA9IGJvdW5kQXJncyA/IGZ1bmN0aW9uICgpIHtcbiAgICAgIGFwcGx5KGZuLCB0aGlzLCBwYXJhbXMpO1xuICAgIH0gOiBmbjtcbiAgICByZXR1cm4gaGFzVGltZUFyZyA/IHNjaGVkdWxlcihjYWxsYmFjaywgdGltZW91dCkgOiBzY2hlZHVsZXIoY2FsbGJhY2spO1xuICB9IDogc2NoZWR1bGVyO1xufTtcbiIsIid1c2Ugc3RyaWN0JztcbnZhciAkID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2V4cG9ydCcpO1xudmFyIGdsb2JhbFRoaXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZ2xvYmFsLXRoaXMnKTtcbnZhciBzY2hlZHVsZXJzRml4ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3NjaGVkdWxlcnMtZml4Jyk7XG5cbnZhciBzZXRJbnRlcnZhbCA9IHNjaGVkdWxlcnNGaXgoZ2xvYmFsVGhpcy5zZXRJbnRlcnZhbCwgdHJ1ZSk7XG5cbi8vIEJ1biAvIElFOS0gc2V0SW50ZXJ2YWwgYWRkaXRpb25hbCBwYXJhbWV0ZXJzIGZpeFxuLy8gaHR0cHM6Ly9odG1sLnNwZWMud2hhdHdnLm9yZy9tdWx0aXBhZ2UvdGltZXJzLWFuZC11c2VyLXByb21wdHMuaHRtbCNkb20tc2V0aW50ZXJ2YWxcbiQoeyBnbG9iYWw6IHRydWUsIGJpbmQ6IHRydWUsIGZvcmNlZDogZ2xvYmFsVGhpcy5zZXRJbnRlcnZhbCAhPT0gc2V0SW50ZXJ2YWwgfSwge1xuICBzZXRJbnRlcnZhbDogc2V0SW50ZXJ2YWxcbn0pO1xuIiwiJ3VzZSBzdHJpY3QnO1xudmFyICQgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZXhwb3J0Jyk7XG52YXIgZ2xvYmFsVGhpcyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9nbG9iYWwtdGhpcycpO1xudmFyIHNjaGVkdWxlcnNGaXggPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvc2NoZWR1bGVycy1maXgnKTtcblxudmFyIHNldFRpbWVvdXQgPSBzY2hlZHVsZXJzRml4KGdsb2JhbFRoaXMuc2V0VGltZW91dCwgdHJ1ZSk7XG5cbi8vIEJ1biAvIElFOS0gc2V0VGltZW91dCBhZGRpdGlvbmFsIHBhcmFtZXRlcnMgZml4XG4vLyBodHRwczovL2h0bWwuc3BlYy53aGF0d2cub3JnL211bHRpcGFnZS90aW1lcnMtYW5kLXVzZXItcHJvbXB0cy5odG1sI2RvbS1zZXR0aW1lb3V0XG4kKHsgZ2xvYmFsOiB0cnVlLCBiaW5kOiB0cnVlLCBmb3JjZWQ6IGdsb2JhbFRoaXMuc2V0VGltZW91dCAhPT0gc2V0VGltZW91dCB9LCB7XG4gIHNldFRpbWVvdXQ6IHNldFRpbWVvdXRcbn0pO1xuIiwiJ3VzZSBzdHJpY3QnO1xuLy8gVE9ETzogUmVtb3ZlIHRoaXMgbW9kdWxlIGZyb20gYGNvcmUtanNANGAgc2luY2UgaXQncyBzcGxpdCB0byBtb2R1bGVzIGxpc3RlZCBiZWxvd1xucmVxdWlyZSgnLi4vbW9kdWxlcy93ZWIuc2V0LWludGVydmFsJyk7XG5yZXF1aXJlKCcuLi9tb2R1bGVzL3dlYi5zZXQtdGltZW91dCcpO1xuIl0sIm5hbWVzIjpbIlBlcmZvcm1hbmNlT3B0aW1pemVyIiwiX2NsYXNzQ2FsbENoZWNrIiwiZGVib3VuY2VUaW1lcnMiLCJNYXAiLCJ0aHJvdHRsZVRpbWVycyIsImluaXQiLCJfY3JlYXRlQ2xhc3MiLCJrZXkiLCJ2YWx1ZSIsIm9wdGltaXplU2Nyb2xsRXZlbnRzIiwib3B0aW1pemVSZXNpemVFdmVudHMiLCJwcmVsb2FkQ3JpdGljYWxSZXNvdXJjZXMiLCJzZXR1cENvbm5lY3Rpb25PcHRpbWl6YXRpb25zIiwiZGVib3VuY2UiLCJmdW5jIiwid2FpdCIsIl90aGlzIiwiX2xlbiIsImFyZ3VtZW50cyIsImxlbmd0aCIsImFyZ3MiLCJBcnJheSIsIl9rZXkiLCJoYXMiLCJjbGVhclRpbWVvdXQiLCJnZXQiLCJ0aW1lciIsInNldFRpbWVvdXQiLCJhcHBseSIsInNldCIsInRocm90dGxlIiwibGltaXQiLCJfdGhpczIiLCJfbGVuMiIsIl9rZXkyIiwic2Nyb2xsSGFuZGxlciIsIndpbmRvdyIsImFkZEV2ZW50TGlzdGVuZXIiLCJwYXNzaXZlIiwicmVzaXplSGFuZGxlciIsImNyaXRpY2FsUmVzb3VyY2VzIiwiZm9yRWFjaCIsInVybCIsImZldGNoIiwibWV0aG9kIiwiaGVhZGVycyIsIm5hdmlnYXRvciIsImNvbm5lY3Rpb24iLCJlZmZlY3RpdmVUeXBlIiwiZG9jdW1lbnQiLCJkaXNwYXRjaEV2ZW50IiwiQ3VzdG9tRXZlbnQiLCJwZXJmb3JtYW5jZU9wdGltaXplciJdLCJzb3VyY2VSb290IjoiIn0=