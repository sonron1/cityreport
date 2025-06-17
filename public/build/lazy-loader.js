(self["webpackChunk"] = self["webpackChunk"] || []).push([["lazy-loader"],{

/***/ "./assets/js/lazyLoader.js":
/*!*********************************!*\
  !*** ./assets/js/lazyLoader.js ***!
  \*********************************/
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
__webpack_require__(/*! core-js/modules/es.weak-set.js */ "./node_modules/core-js/modules/es.weak-set.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "./node_modules/core-js/modules/esnext.iterator.for-each.js");
__webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
__webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
// ✅ Utilitaire de chargement paresseux général
var LazyLoader = /*#__PURE__*/function () {
  "use strict";

  function LazyLoader() {
    _classCallCheck(this, LazyLoader);
    this.observers = new Map();
    this.loadedElements = new WeakSet();
  }

  // ✅ Observer pour les images
  return _createClass(LazyLoader, [{
    key: "observeImages",
    value: function observeImages() {
      var _this = this;
      var imageObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting && !_this.loadedElements.has(entry.target)) {
            _this.loadImage(entry.target);
            imageObserver.unobserve(entry.target);
          }
        });
      }, {
        rootMargin: '50px' // Charger 50px avant d'être visible
      });
      document.querySelectorAll('img[data-src]').forEach(function (img) {
        imageObserver.observe(img);
      });
      this.observers.set('images', imageObserver);
    }

    // ✅ Observer pour les cartes/sections lourdes
  }, {
    key: "observeHeavyContent",
    value: function observeHeavyContent() {
      var _this2 = this;
      var contentObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            _this2.loadHeavyContent(entry.target);
            contentObserver.unobserve(entry.target);
          }
        });
      });
      document.querySelectorAll('[data-lazy-load]').forEach(function (element) {
        contentObserver.observe(element);
      });
      this.observers.set('content', contentObserver);
    }
  }, {
    key: "loadImage",
    value: function loadImage(img) {
      var src = img.dataset.src;
      if (src) {
        img.src = src;
        img.classList.add('loaded');
        this.loadedElements.add(img);
      }
    }
  }, {
    key: "loadHeavyContent",
    value: function loadHeavyContent(element) {
      var loadType = element.dataset.lazyLoad;
      switch (loadType) {
        case 'map':
          this.loadMap(element);
          break;
        case 'chart':
          this.loadChart(element);
          break;
        case 'table':
          this.loadTable(element);
          break;
      }
    }
  }, {
    key: "loadMap",
    value: function loadMap(element) {
      // Charger dynamiquement Leaflet si nécessaire
      __webpack_require__.e(/*! import() */ "vendors-node_modules_leaflet_dist_leaflet-src_js").then(__webpack_require__.t.bind(__webpack_require__, /*! leaflet */ "./node_modules/leaflet/dist/leaflet-src.js", 23)).then(function (L) {
        // Initialiser la carte
        console.log('Carte chargée paresseusement');
      });
    }
  }, {
    key: "loadChart",
    value: function loadChart(element) {
      // Charger Chart.js si nécessaire
      console.log('Graphique chargé paresseusement');
    }
  }, {
    key: "loadTable",
    value: function loadTable(element) {
      // Charger des données de tableau lourdes
      console.log('Tableau chargé paresseusement');
    }
  }, {
    key: "destroy",
    value: function destroy() {
      this.observers.forEach(function (observer) {
        return observer.disconnect();
      });
      this.observers.clear();
    }
  }]);
}(); // Initialisation
document.addEventListener('DOMContentLoaded', function () {
  var lazyLoader = new LazyLoader();
  lazyLoader.observeImages();
  lazyLoader.observeHeavyContent();
});

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_internals_an-instance_js-node_modules_core-js_internals_array-it-055933","vendors-node_modules_core-js_internals_dom-iterables_js-node_modules_core-js_internals_dom-to-aac983","vendors-node_modules_core-js_modules_es_date_to-primitive_js-node_modules_core-js_modules_es_-c7b0e4","vendors-node_modules_core-js_modules_es_weak-set_js-node_modules_core-js_modules_esnext_itera-8ae9a0"], () => (__webpack_exec__("./assets/js/lazyLoader.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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LENBQUMsa0JBQWtCLEVBQUUsWUFBTTtFQUNoRCxJQUFNQyxVQUFVLEdBQUcsSUFBSWxELFVBQVUsQ0FBQyxDQUFDO0VBQ25Da0QsVUFBVSxDQUFDekMsYUFBYSxDQUFDLENBQUM7RUFDMUJ5QyxVQUFVLENBQUN2QixtQkFBbUIsQ0FBQyxDQUFDO0FBQ3BDLENBQUMsQ0FBQyIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9qcy9sYXp5TG9hZGVyLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8vIOKchSBVdGlsaXRhaXJlIGRlIGNoYXJnZW1lbnQgcGFyZXNzZXV4IGfDqW7DqXJhbFxyXG5jbGFzcyBMYXp5TG9hZGVyIHtcclxuICAgIGNvbnN0cnVjdG9yKCkge1xyXG4gICAgICAgIHRoaXMub2JzZXJ2ZXJzID0gbmV3IE1hcCgpO1xyXG4gICAgICAgIHRoaXMubG9hZGVkRWxlbWVudHMgPSBuZXcgV2Vha1NldCgpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vIOKchSBPYnNlcnZlciBwb3VyIGxlcyBpbWFnZXNcclxuICAgIG9ic2VydmVJbWFnZXMoKSB7XHJcbiAgICAgICAgY29uc3QgaW1hZ2VPYnNlcnZlciA9IG5ldyBJbnRlcnNlY3Rpb25PYnNlcnZlcigoZW50cmllcykgPT4ge1xyXG4gICAgICAgICAgICBlbnRyaWVzLmZvckVhY2goZW50cnkgPT4ge1xyXG4gICAgICAgICAgICAgICAgaWYgKGVudHJ5LmlzSW50ZXJzZWN0aW5nICYmICF0aGlzLmxvYWRlZEVsZW1lbnRzLmhhcyhlbnRyeS50YXJnZXQpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy5sb2FkSW1hZ2UoZW50cnkudGFyZ2V0KTtcclxuICAgICAgICAgICAgICAgICAgICBpbWFnZU9ic2VydmVyLnVub2JzZXJ2ZShlbnRyeS50YXJnZXQpO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9LCB7XHJcbiAgICAgICAgICAgIHJvb3RNYXJnaW46ICc1MHB4JyAvLyBDaGFyZ2VyIDUwcHggYXZhbnQgZCfDqnRyZSB2aXNpYmxlXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ2ltZ1tkYXRhLXNyY10nKS5mb3JFYWNoKGltZyA9PiB7XHJcbiAgICAgICAgICAgIGltYWdlT2JzZXJ2ZXIub2JzZXJ2ZShpbWcpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICB0aGlzLm9ic2VydmVycy5zZXQoJ2ltYWdlcycsIGltYWdlT2JzZXJ2ZXIpO1xyXG4gICAgfVxyXG5cclxuICAgIC8vIOKchSBPYnNlcnZlciBwb3VyIGxlcyBjYXJ0ZXMvc2VjdGlvbnMgbG91cmRlc1xyXG4gICAgb2JzZXJ2ZUhlYXZ5Q29udGVudCgpIHtcclxuICAgICAgICBjb25zdCBjb250ZW50T2JzZXJ2ZXIgPSBuZXcgSW50ZXJzZWN0aW9uT2JzZXJ2ZXIoKGVudHJpZXMpID0+IHtcclxuICAgICAgICAgICAgZW50cmllcy5mb3JFYWNoKGVudHJ5ID0+IHtcclxuICAgICAgICAgICAgICAgIGlmIChlbnRyeS5pc0ludGVyc2VjdGluZykge1xyXG4gICAgICAgICAgICAgICAgICAgIHRoaXMubG9hZEhlYXZ5Q29udGVudChlbnRyeS50YXJnZXQpO1xyXG4gICAgICAgICAgICAgICAgICAgIGNvbnRlbnRPYnNlcnZlci51bm9ic2VydmUoZW50cnkudGFyZ2V0KTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJ1tkYXRhLWxhenktbG9hZF0nKS5mb3JFYWNoKGVsZW1lbnQgPT4ge1xyXG4gICAgICAgICAgICBjb250ZW50T2JzZXJ2ZXIub2JzZXJ2ZShlbGVtZW50KTtcclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgdGhpcy5vYnNlcnZlcnMuc2V0KCdjb250ZW50JywgY29udGVudE9ic2VydmVyKTtcclxuICAgIH1cclxuXHJcbiAgICBsb2FkSW1hZ2UoaW1nKSB7XHJcbiAgICAgICAgY29uc3Qgc3JjID0gaW1nLmRhdGFzZXQuc3JjO1xyXG4gICAgICAgIGlmIChzcmMpIHtcclxuICAgICAgICAgICAgaW1nLnNyYyA9IHNyYztcclxuICAgICAgICAgICAgaW1nLmNsYXNzTGlzdC5hZGQoJ2xvYWRlZCcpO1xyXG4gICAgICAgICAgICB0aGlzLmxvYWRlZEVsZW1lbnRzLmFkZChpbWcpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICBsb2FkSGVhdnlDb250ZW50KGVsZW1lbnQpIHtcclxuICAgICAgICBjb25zdCBsb2FkVHlwZSA9IGVsZW1lbnQuZGF0YXNldC5sYXp5TG9hZDtcclxuXHJcbiAgICAgICAgc3dpdGNoIChsb2FkVHlwZSkge1xyXG4gICAgICAgICAgICBjYXNlICdtYXAnOlxyXG4gICAgICAgICAgICAgICAgdGhpcy5sb2FkTWFwKGVsZW1lbnQpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ2NoYXJ0JzpcclxuICAgICAgICAgICAgICAgIHRoaXMubG9hZENoYXJ0KGVsZW1lbnQpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgIGNhc2UgJ3RhYmxlJzpcclxuICAgICAgICAgICAgICAgIHRoaXMubG9hZFRhYmxlKGVsZW1lbnQpO1xyXG4gICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIGxvYWRNYXAoZWxlbWVudCkge1xyXG4gICAgICAgIC8vIENoYXJnZXIgZHluYW1pcXVlbWVudCBMZWFmbGV0IHNpIG7DqWNlc3NhaXJlXHJcbiAgICAgICAgaW1wb3J0KCdsZWFmbGV0JykudGhlbihMID0+IHtcclxuICAgICAgICAgICAgLy8gSW5pdGlhbGlzZXIgbGEgY2FydGVcclxuICAgICAgICAgICAgY29uc29sZS5sb2coJ0NhcnRlIGNoYXJnw6llIHBhcmVzc2V1c2VtZW50Jyk7XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgbG9hZENoYXJ0KGVsZW1lbnQpIHtcclxuICAgICAgICAvLyBDaGFyZ2VyIENoYXJ0LmpzIHNpIG7DqWNlc3NhaXJlXHJcbiAgICAgICAgY29uc29sZS5sb2coJ0dyYXBoaXF1ZSBjaGFyZ8OpIHBhcmVzc2V1c2VtZW50Jyk7XHJcbiAgICB9XHJcblxyXG4gICAgbG9hZFRhYmxlKGVsZW1lbnQpIHtcclxuICAgICAgICAvLyBDaGFyZ2VyIGRlcyBkb25uw6llcyBkZSB0YWJsZWF1IGxvdXJkZXNcclxuICAgICAgICBjb25zb2xlLmxvZygnVGFibGVhdSBjaGFyZ8OpIHBhcmVzc2V1c2VtZW50Jyk7XHJcbiAgICB9XHJcblxyXG4gICAgZGVzdHJveSgpIHtcclxuICAgICAgICB0aGlzLm9ic2VydmVycy5mb3JFYWNoKG9ic2VydmVyID0+IG9ic2VydmVyLmRpc2Nvbm5lY3QoKSk7XHJcbiAgICAgICAgdGhpcy5vYnNlcnZlcnMuY2xlYXIoKTtcclxuICAgIH1cclxufVxyXG5cclxuLy8gSW5pdGlhbGlzYXRpb25cclxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsICgpID0+IHtcclxuICAgIGNvbnN0IGxhenlMb2FkZXIgPSBuZXcgTGF6eUxvYWRlcigpO1xyXG4gICAgbGF6eUxvYWRlci5vYnNlcnZlSW1hZ2VzKCk7XHJcbiAgICBsYXp5TG9hZGVyLm9ic2VydmVIZWF2eUNvbnRlbnQoKTtcclxufSk7Il0sIm5hbWVzIjpbIkxhenlMb2FkZXIiLCJfY2xhc3NDYWxsQ2hlY2siLCJvYnNlcnZlcnMiLCJNYXAiLCJsb2FkZWRFbGVtZW50cyIsIldlYWtTZXQiLCJfY3JlYXRlQ2xhc3MiLCJrZXkiLCJ2YWx1ZSIsIm9ic2VydmVJbWFnZXMiLCJfdGhpcyIsImltYWdlT2JzZXJ2ZXIiLCJJbnRlcnNlY3Rpb25PYnNlcnZlciIsImVudHJpZXMiLCJmb3JFYWNoIiwiZW50cnkiLCJpc0ludGVyc2VjdGluZyIsImhhcyIsInRhcmdldCIsImxvYWRJbWFnZSIsInVub2JzZXJ2ZSIsInJvb3RNYXJnaW4iLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJpbWciLCJvYnNlcnZlIiwic2V0Iiwib2JzZXJ2ZUhlYXZ5Q29udGVudCIsIl90aGlzMiIsImNvbnRlbnRPYnNlcnZlciIsImxvYWRIZWF2eUNvbnRlbnQiLCJlbGVtZW50Iiwic3JjIiwiZGF0YXNldCIsImNsYXNzTGlzdCIsImFkZCIsImxvYWRUeXBlIiwibGF6eUxvYWQiLCJsb2FkTWFwIiwibG9hZENoYXJ0IiwibG9hZFRhYmxlIiwidGhlbiIsIkwiLCJjb25zb2xlIiwibG9nIiwiZGVzdHJveSIsIm9ic2VydmVyIiwiZGlzY29ubmVjdCIsImNsZWFyIiwiYWRkRXZlbnRMaXN0ZW5lciIsImxhenlMb2FkZXIiXSwic291cmNlUm9vdCI6IiJ9