(self["webpackChunk"] = self["webpackChunk"] || []).push([["notifications"],{

/***/ "./assets/js/notifications.js":
/*!************************************!*\
  !*** ./assets/js/notifications.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return r; }; var t, r = {}, e = Object.prototype, n = e.hasOwnProperty, o = "function" == typeof Symbol ? Symbol : {}, i = o.iterator || "@@iterator", a = o.asyncIterator || "@@asyncIterator", u = o.toStringTag || "@@toStringTag"; function c(t, r, e, n) { return Object.defineProperty(t, r, { value: e, enumerable: !n, configurable: !n, writable: !n }); } try { c({}, ""); } catch (t) { c = function c(t, r, e) { return t[r] = e; }; } function h(r, e, n, o) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype); return c(a, "_invoke", function (r, e, n) { var o = 1; return function (i, a) { if (3 === o) throw Error("Generator is already running"); if (4 === o) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var u = n.delegate; if (u) { var c = d(u, n); if (c) { if (c === f) continue; return c; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (1 === o) throw o = 4, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = 3; var h = s(r, e, n); if ("normal" === h.type) { if (o = n.done ? 4 : 2, h.arg === f) continue; return { value: h.arg, done: n.done }; } "throw" === h.type && (o = 4, n.method = "throw", n.arg = h.arg); } }; }(r, n, new Context(o || [])), !0), a; } function s(t, r, e) { try { return { type: "normal", arg: t.call(r, e) }; } catch (t) { return { type: "throw", arg: t }; } } r.wrap = h; var f = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var l = {}; c(l, i, function () { return this; }); var p = Object.getPrototypeOf, y = p && p(p(x([]))); y && y !== e && n.call(y, i) && (l = y); var v = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(l); function g(t) { ["next", "throw", "return"].forEach(function (r) { c(t, r, function (t) { return this._invoke(r, t); }); }); } function AsyncIterator(t, r) { function e(o, i, a, u) { var c = s(t[o], t, i); if ("throw" !== c.type) { var h = c.arg, f = h.value; return f && "object" == _typeof(f) && n.call(f, "__await") ? r.resolve(f.__await).then(function (t) { e("next", t, a, u); }, function (t) { e("throw", t, a, u); }) : r.resolve(f).then(function (t) { h.value = t, a(h); }, function (t) { return e("throw", t, a, u); }); } u(c.arg); } var o; c(this, "_invoke", function (t, n) { function i() { return new r(function (r, o) { e(t, n, r, o); }); } return o = o ? o.then(i, i) : i(); }, !0); } function d(r, e) { var n = e.method, o = r.i[n]; if (o === t) return e.delegate = null, "throw" === n && r.i["return"] && (e.method = "return", e.arg = t, d(r, e), "throw" === e.method) || "return" !== n && (e.method = "throw", e.arg = new TypeError("The iterator does not provide a '" + n + "' method")), f; var i = s(o, r.i, e.arg); if ("throw" === i.type) return e.method = "throw", e.arg = i.arg, e.delegate = null, f; var a = i.arg; return a ? a.done ? (e[r.r] = a.value, e.next = r.n, "return" !== e.method && (e.method = "next", e.arg = t), e.delegate = null, f) : a : (e.method = "throw", e.arg = new TypeError("iterator result is not an object"), e.delegate = null, f); } function w(t) { this.tryEntries.push(t); } function m(r) { var e = r[4] || {}; e.type = "normal", e.arg = t, r[4] = e; } function Context(t) { this.tryEntries = [[-1]], t.forEach(w, this), this.reset(!0); } function x(r) { if (null != r) { var e = r[i]; if (e) return e.call(r); if ("function" == typeof r.next) return r; if (!isNaN(r.length)) { var o = -1, a = function e() { for (; ++o < r.length;) if (n.call(r, o)) return e.value = r[o], e.done = !1, e; return e.value = t, e.done = !0, e; }; return a.next = a; } } throw new TypeError(_typeof(r) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, c(v, "constructor", GeneratorFunctionPrototype), c(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = c(GeneratorFunctionPrototype, u, "GeneratorFunction"), r.isGeneratorFunction = function (t) { var r = "function" == typeof t && t.constructor; return !!r && (r === GeneratorFunction || "GeneratorFunction" === (r.displayName || r.name)); }, r.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, c(t, u, "GeneratorFunction")), t.prototype = Object.create(v), t; }, r.awrap = function (t) { return { __await: t }; }, g(AsyncIterator.prototype), c(AsyncIterator.prototype, a, function () { return this; }), r.AsyncIterator = AsyncIterator, r.async = function (t, e, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(h(t, e, n, o), i); return r.isGeneratorFunction(e) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, g(v), c(v, u, "Generator"), c(v, i, function () { return this; }), c(v, "toString", function () { return "[object Generator]"; }), r.keys = function (t) { var r = Object(t), e = []; for (var n in r) e.unshift(n); return function t() { for (; e.length;) if ((n = e.pop()) in r) return t.value = n, t.done = !1, t; return t.done = !0, t; }; }, r.values = x, Context.prototype = { constructor: Context, reset: function reset(r) { if (this.prev = this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(m), !r) for (var e in this) "t" === e.charAt(0) && n.call(this, e) && !isNaN(+e.slice(1)) && (this[e] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0][4]; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(r) { if (this.done) throw r; var e = this; function n(t) { a.type = "throw", a.arg = r, e.next = t; } for (var o = e.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i[4], u = this.prev, c = i[1], h = i[2]; if (-1 === i[0]) return n("end"), !1; if (!c && !h) throw Error("try statement without catch or finally"); if (null != i[0] && i[0] <= u) { if (u < c) return this.method = "next", this.arg = t, n(c), !0; if (u < h) return n(h), !1; } } }, abrupt: function abrupt(t, r) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var n = this.tryEntries[e]; if (n[0] > -1 && n[0] <= this.prev && this.prev < n[2]) { var o = n; break; } } o && ("break" === t || "continue" === t) && o[0] <= r && r <= o[2] && (o = null); var i = o ? o[4] : {}; return i.type = t, i.arg = r, o ? (this.method = "next", this.next = o[2], f) : this.complete(i); }, complete: function complete(t, r) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && r && (this.next = r), f; }, finish: function finish(t) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var e = this.tryEntries[r]; if (e[2] === t) return this.complete(e[4], e[3]), m(e), f; } }, "catch": function _catch(t) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var e = this.tryEntries[r]; if (e[0] === t) { var n = e[4]; if ("throw" === n.type) { var o = n.arg; m(e); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(r, e, n) { return this.delegate = { i: x(r), r: e, n: n }, "next" === this.method && (this.arg = t), f; } }, r; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
__webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
__webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
__webpack_require__(/*! core-js/modules/es.symbol.iterator.js */ "./node_modules/core-js/modules/es.symbol.iterator.js");
__webpack_require__(/*! core-js/modules/es.symbol.to-primitive.js */ "./node_modules/core-js/modules/es.symbol.to-primitive.js");
__webpack_require__(/*! core-js/modules/es.error.cause.js */ "./node_modules/core-js/modules/es.error.cause.js");
__webpack_require__(/*! core-js/modules/es.error.to-string.js */ "./node_modules/core-js/modules/es.error.to-string.js");
__webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
__webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");
__webpack_require__(/*! core-js/modules/es.array.iterator.js */ "./node_modules/core-js/modules/es.array.iterator.js");
__webpack_require__(/*! core-js/modules/es.array.push.js */ "./node_modules/core-js/modules/es.array.push.js");
__webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
__webpack_require__(/*! core-js/modules/es.array.unshift.js */ "./node_modules/core-js/modules/es.array.unshift.js");
__webpack_require__(/*! core-js/modules/es.date.now.js */ "./node_modules/core-js/modules/es.date.now.js");
__webpack_require__(/*! core-js/modules/es.date.to-primitive.js */ "./node_modules/core-js/modules/es.date.to-primitive.js");
__webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
__webpack_require__(/*! core-js/modules/es.map.js */ "./node_modules/core-js/modules/es.map.js");
__webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
__webpack_require__(/*! core-js/modules/es.object.create.js */ "./node_modules/core-js/modules/es.object.create.js");
__webpack_require__(/*! core-js/modules/es.object.define-property.js */ "./node_modules/core-js/modules/es.object.define-property.js");
__webpack_require__(/*! core-js/modules/es.object.get-prototype-of.js */ "./node_modules/core-js/modules/es.object.get-prototype-of.js");
__webpack_require__(/*! core-js/modules/es.object.proto.js */ "./node_modules/core-js/modules/es.object.proto.js");
__webpack_require__(/*! core-js/modules/es.object.set-prototype-of.js */ "./node_modules/core-js/modules/es.object.set-prototype-of.js");
__webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
__webpack_require__(/*! core-js/modules/es.parse-int.js */ "./node_modules/core-js/modules/es.parse-int.js");
__webpack_require__(/*! core-js/modules/es.promise.js */ "./node_modules/core-js/modules/es.promise.js");
__webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
__webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "./node_modules/core-js/modules/esnext.iterator.for-each.js");
__webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
__webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
__webpack_require__(/*! core-js/modules/web.timers.js */ "./node_modules/core-js/modules/web.timers.js");
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
// ✅ Version optimisée de NotificationManager
var NotificationManager = /*#__PURE__*/function () {
  "use strict";

  function NotificationManager() {
    _classCallCheck(this, NotificationManager);
    this.pollInterval = 60000; // Réduit à 1 minute
    this.isPolling = false;
    this.cache = new Map();
    this.debounceTimer = null;
    this.pollTimer = null;

    // Éléments DOM
    this.notificationDropdown = document.querySelector('#notificationDropdown');
    this.notificationBadge = document.querySelector('#notificationBadge');
    this.notificationsList = document.querySelector('#notificationsList');
    this.init();
  }
  return _createClass(NotificationManager, [{
    key: "init",
    value: function init() {
      // Chargement différé
      this.setupIntersectionObserver();

      // Polling intelligent (seulement si la page est visible)
      this.setupVisibilityHandler();

      // Événements
      this.setupEventListeners();

      // Chargement initial
      this.loadNotifications();
    }

    // ✅ NOUVEAU : Gestion de la visibilité de la page
  }, {
    key: "setupVisibilityHandler",
    value: function setupVisibilityHandler() {
      var _this = this;
      document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
          _this.stopPolling();
        } else {
          _this.startPolling();
          _this.loadNotifications(); // Refresh immédiat au retour
        }
      });
    }

    // ✅ NOUVEAU : Intersection Observer pour le chargement paresseux
  }, {
    key: "setupIntersectionObserver",
    value: function setupIntersectionObserver() {
      var _this2 = this;
      if (!this.notificationDropdown) return;
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            _this2.startPolling();
          } else {
            _this2.stopPolling();
          }
        });
      }, {
        threshold: 0.1 // Déclenche quand 10% visible
      });
      observer.observe(this.notificationDropdown);
    }
  }, {
    key: "startPolling",
    value: function startPolling() {
      var _this3 = this;
      if (this.isPolling) return;
      this.isPolling = true;
      this.pollTimer = setInterval(function () {
        _this3.loadNotifications();
      }, this.pollInterval);
    }
  }, {
    key: "stopPolling",
    value: function stopPolling() {
      this.isPolling = false;
      if (this.pollTimer) {
        clearInterval(this.pollTimer);
        this.pollTimer = null;
      }
    }

    // ✅ OPTIMISÉ : Requête avec cache et debounce
  }, {
    key: "loadNotifications",
    value: function () {
      var _loadNotifications = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee2() {
        var _this4 = this;
        return _regeneratorRuntime().wrap(function _callee2$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              // Debounce pour éviter les requêtes trop fréquentes
              if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
              }
              this.debounceTimer = setTimeout(/*#__PURE__*/_asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
                var cached, response, data, _cached;
                return _regeneratorRuntime().wrap(function _callee$(_context) {
                  while (1) switch (_context.prev = _context.next) {
                    case 0:
                      _context.prev = 0;
                      // Vérifier le cache local d'abord
                      cached = _this4.cache.get('notifications');
                      if (!(cached && Date.now() - cached.timestamp < 30000)) {
                        _context.next = 5;
                        break;
                      }
                      // Cache 30s
                      _this4.updateUI(cached.data);
                      return _context.abrupt("return");
                    case 5:
                      _context.next = 7;
                      return fetch('/notifications/api/liste', {
                        method: 'GET',
                        headers: {
                          'Cache-Control': 'max-age=30',
                          'X-Requested-With': 'XMLHttpRequest'
                        }
                      });
                    case 7:
                      response = _context.sent;
                      if (response.ok) {
                        _context.next = 10;
                        break;
                      }
                      throw new Error("HTTP ".concat(response.status));
                    case 10:
                      _context.next = 12;
                      return response.json();
                    case 12:
                      data = _context.sent;
                      // Mettre en cache
                      _this4.cache.set('notifications', {
                        data: data,
                        timestamp: Date.now()
                      });
                      _this4.updateUI(data);
                      _context.next = 22;
                      break;
                    case 17:
                      _context.prev = 17;
                      _context.t0 = _context["catch"](0);
                      console.error('Erreur notifications:', _context.t0);
                      // Fallback sur les données en cache si disponibles
                      _cached = _this4.cache.get('notifications');
                      if (_cached) {
                        _this4.updateUI(_cached.data);
                      }
                    case 22:
                    case "end":
                      return _context.stop();
                  }
                }, _callee, null, [[0, 17]]);
              })), 100); // Debounce de 100ms
            case 2:
            case "end":
              return _context2.stop();
          }
        }, _callee2, this);
      }));
      function loadNotifications() {
        return _loadNotifications.apply(this, arguments);
      }
      return loadNotifications;
    }() // ✅ OPTIMISÉ : Batch DOM updates
  }, {
    key: "updateUI",
    value: function updateUI(data) {
      var _this5 = this;
      // Utiliser requestAnimationFrame pour optimiser les updates DOM
      requestAnimationFrame(function () {
        _this5.updateBadge(data.nombreNonLues);
        _this5.updateNotificationsList(data.notifications);
      });
    }
  }, {
    key: "updateBadge",
    value: function updateBadge(count) {
      if (!this.notificationBadge) return;

      // Éviter les updates inutiles
      var currentCount = parseInt(this.notificationBadge.textContent) || 0;
      if (currentCount === count) return;
      this.notificationBadge.textContent = count;
      this.notificationBadge.style.display = count > 0 ? 'inline' : 'none';
    }

    // ✅ OPTIMISÉ : DocumentFragment pour les performances DOM
  }, {
    key: "updateNotificationsList",
    value: function updateNotificationsList(notifications) {
      var _this6 = this;
      if (!this.notificationsList) return;

      // Créer un fragment pour minimiser les reflows
      var fragment = document.createDocumentFragment();
      if (notifications.length === 0) {
        var emptyDiv = document.createElement('div');
        emptyDiv.className = 'dropdown-item text-center text-muted';
        emptyDiv.innerHTML = '<small>Aucune notification</small>';
        fragment.appendChild(emptyDiv);
      } else {
        notifications.forEach(function (notification) {
          var item = _this6.createNotificationItem(notification);
          fragment.appendChild(item);
        });
      }

      // Une seule manipulation DOM
      this.notificationsList.innerHTML = '';
      this.notificationsList.appendChild(fragment);
    }
  }, {
    key: "createNotificationItem",
    value: function createNotificationItem(notification) {
      var div = document.createElement('div');
      div.className = "dropdown-item notification-item ".concat(notification.isLue ? 'lue' : 'non-lue');
      div.innerHTML = "\n            <div class=\"d-flex align-items-start\">\n                <span class=\"me-2\">".concat(notification.typeIcon, "</span>\n                <div class=\"flex-grow-1\">\n                    <p class=\"mb-1 small\">").concat(notification.message, "</p>\n                    <small class=\"text-muted\">").concat(notification.dateRelative, "</small>\n                </div>\n                <div class=\"notification-actions\">\n                    ").concat(!notification.isLue ? "\n                        <button class=\"btn btn-sm btn-outline-primary mark-read-btn\" \n                                data-id=\"".concat(notification.id, "\" title=\"Marquer comme lu\">\n                            \u2713\n                        </button>\n                    ") : '', "\n                </div>\n            </div>\n        ");

      // Redirection vers le signalement
      if (notification.signalementId) {
        div.addEventListener('click', function (e) {
          if (!e.target.classList.contains('mark-read-btn')) {
            window.location.href = "/signalements/".concat(notification.signalementId);
          }
        });
      }
      return div;
    }
  }, {
    key: "setupEventListeners",
    value: function setupEventListeners() {
      var _this7 = this;
      // ✅ OPTIMISÉ : Event delegation pour les performances
      document.addEventListener('click', /*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee3(e) {
          var notificationId;
          return _regeneratorRuntime().wrap(function _callee3$(_context3) {
            while (1) switch (_context3.prev = _context3.next) {
              case 0:
                if (!e.target.classList.contains('mark-read-btn')) {
                  _context3.next = 8;
                  break;
                }
                e.preventDefault();
                e.stopPropagation();
                notificationId = e.target.dataset.id;
                _context3.next = 6;
                return _this7.markAsRead(notificationId);
              case 6:
                // Invalider le cache pour forcer le rechargement
                _this7.cache["delete"]('notifications');
                _this7.loadNotifications();
              case 8:
              case "end":
                return _context3.stop();
            }
          }, _callee3);
        }));
        return function (_x) {
          return _ref2.apply(this, arguments);
        };
      }());

      // Marquer toutes comme lues
      var markAllBtn = document.querySelector('#markAllRead');
      if (markAllBtn) {
        markAllBtn.addEventListener('click', /*#__PURE__*/_asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee4() {
          return _regeneratorRuntime().wrap(function _callee4$(_context4) {
            while (1) switch (_context4.prev = _context4.next) {
              case 0:
                _context4.next = 2;
                return _this7.markAllAsRead();
              case 2:
                _this7.cache["delete"]('notifications');
                _this7.loadNotifications();
              case 4:
              case "end":
                return _context4.stop();
            }
          }, _callee4);
        })));
      }
    }
  }, {
    key: "markAsRead",
    value: function () {
      var _markAsRead = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee5(notificationId) {
        return _regeneratorRuntime().wrap(function _callee5$(_context5) {
          while (1) switch (_context5.prev = _context5.next) {
            case 0:
              _context5.prev = 0;
              _context5.next = 3;
              return fetch("/notifications/".concat(notificationId, "/lire"), {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
            case 3:
              _context5.next = 8;
              break;
            case 5:
              _context5.prev = 5;
              _context5.t0 = _context5["catch"](0);
              console.error('Erreur marquer comme lu:', _context5.t0);
            case 8:
            case "end":
              return _context5.stop();
          }
        }, _callee5, null, [[0, 5]]);
      }));
      function markAsRead(_x2) {
        return _markAsRead.apply(this, arguments);
      }
      return markAsRead;
    }()
  }, {
    key: "markAllAsRead",
    value: function () {
      var _markAllAsRead = _asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee6() {
        return _regeneratorRuntime().wrap(function _callee6$(_context6) {
          while (1) switch (_context6.prev = _context6.next) {
            case 0:
              _context6.prev = 0;
              _context6.next = 3;
              return fetch('/notifications/marquer-toutes-lues', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                }
              });
            case 3:
              _context6.next = 8;
              break;
            case 5:
              _context6.prev = 5;
              _context6.t0 = _context6["catch"](0);
              console.error('Erreur marquer toutes comme lues:', _context6.t0);
            case 8:
            case "end":
              return _context6.stop();
          }
        }, _callee6, null, [[0, 5]]);
      }));
      function markAllAsRead() {
        return _markAllAsRead.apply(this, arguments);
      }
      return markAllAsRead;
    }() // ✅ NOUVEAU : Méthode de nettoyage
  }, {
    key: "destroy",
    value: function destroy() {
      this.stopPolling();
      if (this.debounceTimer) {
        clearTimeout(this.debounceTimer);
      }
      this.cache.clear();
    }
  }]);
}(); // ✅ OPTIMISÉ : Initialisation avec vérification de l'état de chargement
function initNotificationManager() {
  if (document.querySelector('#notificationDropdown')) {
    new NotificationManager();
  }
}

// Initialisation optimisée
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initNotificationManager);
} else {
  initNotificationManager();
}

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_internals_an-instance_js-node_modules_core-js_internals_array-it-055933","vendors-node_modules_core-js_internals_dom-iterables_js-node_modules_core-js_internals_dom-to-aac983","vendors-node_modules_core-js_modules_es_date_to-primitive_js-node_modules_core-js_modules_es_-c7b0e4","vendors-node_modules_core-js_modules_es_array_concat_js-node_modules_core-js_modules_es_array-35fca0"], () => (__webpack_exec__("./assets/js/notifications.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QkEsQ0FBQSxFQUFHO0VBQy9CLElBQUlySCxRQUFRLENBQUNDLGFBQWEsQ0FBQyx1QkFBdUIsQ0FBQyxFQUFFO0lBQ2pELElBQUlULG1CQUFtQixDQUFDLENBQUM7RUFDN0I7QUFDSjs7QUFFQTtBQUNBLElBQUlRLFFBQVEsQ0FBQ3NILFVBQVUsS0FBSyxTQUFTLEVBQUU7RUFDbkN0SCxRQUFRLENBQUNVLGdCQUFnQixDQUFDLGtCQUFrQixFQUFFMkcsdUJBQXVCLENBQUM7QUFDMUUsQ0FBQyxNQUFNO0VBQ0hBLHVCQUF1QixDQUFDLENBQUM7QUFDN0IiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvbm90aWZpY2F0aW9ucy5qcyJdLCJzb3VyY2VzQ29udGVudCI6WyJcclxuLy8g4pyFIFZlcnNpb24gb3B0aW1pc8OpZSBkZSBOb3RpZmljYXRpb25NYW5hZ2VyXHJcbmNsYXNzIE5vdGlmaWNhdGlvbk1hbmFnZXIge1xyXG4gICAgY29uc3RydWN0b3IoKSB7XHJcbiAgICAgICAgdGhpcy5wb2xsSW50ZXJ2YWwgPSA2MDAwMDsgLy8gUsOpZHVpdCDDoCAxIG1pbnV0ZVxyXG4gICAgICAgIHRoaXMuaXNQb2xsaW5nID0gZmFsc2U7XHJcbiAgICAgICAgdGhpcy5jYWNoZSA9IG5ldyBNYXAoKTtcclxuICAgICAgICB0aGlzLmRlYm91bmNlVGltZXIgPSBudWxsO1xyXG4gICAgICAgIHRoaXMucG9sbFRpbWVyID0gbnVsbDtcclxuXHJcbiAgICAgICAgLy8gw4lsw6ltZW50cyBET01cclxuICAgICAgICB0aGlzLm5vdGlmaWNhdGlvbkRyb3Bkb3duID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignI25vdGlmaWNhdGlvbkRyb3Bkb3duJyk7XHJcbiAgICAgICAgdGhpcy5ub3RpZmljYXRpb25CYWRnZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoJyNub3RpZmljYXRpb25CYWRnZScpO1xyXG4gICAgICAgIHRoaXMubm90aWZpY2F0aW9uc0xpc3QgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjbm90aWZpY2F0aW9uc0xpc3QnKTtcclxuXHJcbiAgICAgICAgdGhpcy5pbml0KCk7XHJcbiAgICB9XHJcblxyXG4gICAgaW5pdCgpIHtcclxuICAgICAgICAvLyBDaGFyZ2VtZW50IGRpZmbDqXLDqVxyXG4gICAgICAgIHRoaXMuc2V0dXBJbnRlcnNlY3Rpb25PYnNlcnZlcigpO1xyXG5cclxuICAgICAgICAvLyBQb2xsaW5nIGludGVsbGlnZW50IChzZXVsZW1lbnQgc2kgbGEgcGFnZSBlc3QgdmlzaWJsZSlcclxuICAgICAgICB0aGlzLnNldHVwVmlzaWJpbGl0eUhhbmRsZXIoKTtcclxuXHJcbiAgICAgICAgLy8gw4l2w6luZW1lbnRzXHJcbiAgICAgICAgdGhpcy5zZXR1cEV2ZW50TGlzdGVuZXJzKCk7XHJcblxyXG4gICAgICAgIC8vIENoYXJnZW1lbnQgaW5pdGlhbFxyXG4gICAgICAgIHRoaXMubG9hZE5vdGlmaWNhdGlvbnMoKTtcclxuICAgIH1cclxuXHJcbiAgICAvLyDinIUgTk9VVkVBVSA6IEdlc3Rpb24gZGUgbGEgdmlzaWJpbGl0w6kgZGUgbGEgcGFnZVxyXG4gICAgc2V0dXBWaXNpYmlsaXR5SGFuZGxlcigpIHtcclxuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCd2aXNpYmlsaXR5Y2hhbmdlJywgKCkgPT4ge1xyXG4gICAgICAgICAgICBpZiAoZG9jdW1lbnQuaGlkZGVuKSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLnN0b3BQb2xsaW5nKCk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICB0aGlzLnN0YXJ0UG9sbGluZygpO1xyXG4gICAgICAgICAgICAgICAgdGhpcy5sb2FkTm90aWZpY2F0aW9ucygpOyAvLyBSZWZyZXNoIGltbcOpZGlhdCBhdSByZXRvdXJcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIC8vIOKchSBOT1VWRUFVIDogSW50ZXJzZWN0aW9uIE9ic2VydmVyIHBvdXIgbGUgY2hhcmdlbWVudCBwYXJlc3NldXhcclxuICAgIHNldHVwSW50ZXJzZWN0aW9uT2JzZXJ2ZXIoKSB7XHJcbiAgICAgICAgaWYgKCF0aGlzLm5vdGlmaWNhdGlvbkRyb3Bkb3duKSByZXR1cm47XHJcblxyXG4gICAgICAgIGNvbnN0IG9ic2VydmVyID0gbmV3IEludGVyc2VjdGlvbk9ic2VydmVyKChlbnRyaWVzKSA9PiB7XHJcbiAgICAgICAgICAgIGVudHJpZXMuZm9yRWFjaChlbnRyeSA9PiB7XHJcbiAgICAgICAgICAgICAgICBpZiAoZW50cnkuaXNJbnRlcnNlY3RpbmcpIHtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLnN0YXJ0UG9sbGluZygpO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICB0aGlzLnN0b3BQb2xsaW5nKCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0sIHtcclxuICAgICAgICAgICAgdGhyZXNob2xkOiAwLjEgLy8gRMOpY2xlbmNoZSBxdWFuZCAxMCUgdmlzaWJsZVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICBvYnNlcnZlci5vYnNlcnZlKHRoaXMubm90aWZpY2F0aW9uRHJvcGRvd24pO1xyXG4gICAgfVxyXG5cclxuICAgIHN0YXJ0UG9sbGluZygpIHtcclxuICAgICAgICBpZiAodGhpcy5pc1BvbGxpbmcpIHJldHVybjtcclxuXHJcbiAgICAgICAgdGhpcy5pc1BvbGxpbmcgPSB0cnVlO1xyXG4gICAgICAgIHRoaXMucG9sbFRpbWVyID0gc2V0SW50ZXJ2YWwoKCkgPT4ge1xyXG4gICAgICAgICAgICB0aGlzLmxvYWROb3RpZmljYXRpb25zKCk7XHJcbiAgICAgICAgfSwgdGhpcy5wb2xsSW50ZXJ2YWwpO1xyXG4gICAgfVxyXG5cclxuICAgIHN0b3BQb2xsaW5nKCkge1xyXG4gICAgICAgIHRoaXMuaXNQb2xsaW5nID0gZmFsc2U7XHJcbiAgICAgICAgaWYgKHRoaXMucG9sbFRpbWVyKSB7XHJcbiAgICAgICAgICAgIGNsZWFySW50ZXJ2YWwodGhpcy5wb2xsVGltZXIpO1xyXG4gICAgICAgICAgICB0aGlzLnBvbGxUaW1lciA9IG51bGw7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG5cclxuICAgIC8vIOKchSBPUFRJTUlTw4kgOiBSZXF1w6p0ZSBhdmVjIGNhY2hlIGV0IGRlYm91bmNlXHJcbiAgICBhc3luYyBsb2FkTm90aWZpY2F0aW9ucygpIHtcclxuICAgICAgICAvLyBEZWJvdW5jZSBwb3VyIMOpdml0ZXIgbGVzIHJlcXXDqnRlcyB0cm9wIGZyw6lxdWVudGVzXHJcbiAgICAgICAgaWYgKHRoaXMuZGVib3VuY2VUaW1lcikge1xyXG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGhpcy5kZWJvdW5jZVRpbWVyKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHRoaXMuZGVib3VuY2VUaW1lciA9IHNldFRpbWVvdXQoYXN5bmMgKCkgPT4ge1xyXG4gICAgICAgICAgICB0cnkge1xyXG4gICAgICAgICAgICAgICAgLy8gVsOpcmlmaWVyIGxlIGNhY2hlIGxvY2FsIGQnYWJvcmRcclxuICAgICAgICAgICAgICAgIGNvbnN0IGNhY2hlZCA9IHRoaXMuY2FjaGUuZ2V0KCdub3RpZmljYXRpb25zJyk7XHJcbiAgICAgICAgICAgICAgICBpZiAoY2FjaGVkICYmIERhdGUubm93KCkgLSBjYWNoZWQudGltZXN0YW1wIDwgMzAwMDApIHsgLy8gQ2FjaGUgMzBzXHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cGRhdGVVSShjYWNoZWQuZGF0YSk7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIGNvbnN0IHJlc3BvbnNlID0gYXdhaXQgZmV0Y2goJy9ub3RpZmljYXRpb25zL2FwaS9saXN0ZScsIHtcclxuICAgICAgICAgICAgICAgICAgICBtZXRob2Q6ICdHRVQnLFxyXG4gICAgICAgICAgICAgICAgICAgIGhlYWRlcnM6IHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJ0NhY2hlLUNvbnRyb2wnOiAnbWF4LWFnZT0zMCcsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICdYLVJlcXVlc3RlZC1XaXRoJzogJ1hNTEh0dHBSZXF1ZXN0J1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgICAgIGlmICghcmVzcG9uc2Uub2spIHtcclxuICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IoYEhUVFAgJHtyZXNwb25zZS5zdGF0dXN9YCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgY29uc3QgZGF0YSA9IGF3YWl0IHJlc3BvbnNlLmpzb24oKTtcclxuXHJcbiAgICAgICAgICAgICAgICAvLyBNZXR0cmUgZW4gY2FjaGVcclxuICAgICAgICAgICAgICAgIHRoaXMuY2FjaGUuc2V0KCdub3RpZmljYXRpb25zJywge1xyXG4gICAgICAgICAgICAgICAgICAgIGRhdGE6IGRhdGEsXHJcbiAgICAgICAgICAgICAgICAgICAgdGltZXN0YW1wOiBEYXRlLm5vdygpXHJcbiAgICAgICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgICAgICB0aGlzLnVwZGF0ZVVJKGRhdGEpO1xyXG5cclxuICAgICAgICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ0VycmV1ciBub3RpZmljYXRpb25zOicsIGVycm9yKTtcclxuICAgICAgICAgICAgICAgIC8vIEZhbGxiYWNrIHN1ciBsZXMgZG9ubsOpZXMgZW4gY2FjaGUgc2kgZGlzcG9uaWJsZXNcclxuICAgICAgICAgICAgICAgIGNvbnN0IGNhY2hlZCA9IHRoaXMuY2FjaGUuZ2V0KCdub3RpZmljYXRpb25zJyk7XHJcbiAgICAgICAgICAgICAgICBpZiAoY2FjaGVkKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdGhpcy51cGRhdGVVSShjYWNoZWQuZGF0YSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9LCAxMDApOyAvLyBEZWJvdW5jZSBkZSAxMDBtc1xyXG4gICAgfVxyXG5cclxuICAgIC8vIOKchSBPUFRJTUlTw4kgOiBCYXRjaCBET00gdXBkYXRlc1xyXG4gICAgdXBkYXRlVUkoZGF0YSkge1xyXG4gICAgICAgIC8vIFV0aWxpc2VyIHJlcXVlc3RBbmltYXRpb25GcmFtZSBwb3VyIG9wdGltaXNlciBsZXMgdXBkYXRlcyBET01cclxuICAgICAgICByZXF1ZXN0QW5pbWF0aW9uRnJhbWUoKCkgPT4ge1xyXG4gICAgICAgICAgICB0aGlzLnVwZGF0ZUJhZGdlKGRhdGEubm9tYnJlTm9uTHVlcyk7XHJcbiAgICAgICAgICAgIHRoaXMudXBkYXRlTm90aWZpY2F0aW9uc0xpc3QoZGF0YS5ub3RpZmljYXRpb25zKTtcclxuICAgICAgICB9KTtcclxuICAgIH1cclxuXHJcbiAgICB1cGRhdGVCYWRnZShjb3VudCkge1xyXG4gICAgICAgIGlmICghdGhpcy5ub3RpZmljYXRpb25CYWRnZSkgcmV0dXJuO1xyXG5cclxuICAgICAgICAvLyDDiXZpdGVyIGxlcyB1cGRhdGVzIGludXRpbGVzXHJcbiAgICAgICAgY29uc3QgY3VycmVudENvdW50ID0gcGFyc2VJbnQodGhpcy5ub3RpZmljYXRpb25CYWRnZS50ZXh0Q29udGVudCkgfHwgMDtcclxuICAgICAgICBpZiAoY3VycmVudENvdW50ID09PSBjb3VudCkgcmV0dXJuO1xyXG5cclxuICAgICAgICB0aGlzLm5vdGlmaWNhdGlvbkJhZGdlLnRleHRDb250ZW50ID0gY291bnQ7XHJcbiAgICAgICAgdGhpcy5ub3RpZmljYXRpb25CYWRnZS5zdHlsZS5kaXNwbGF5ID0gY291bnQgPiAwID8gJ2lubGluZScgOiAnbm9uZSc7XHJcbiAgICB9XHJcblxyXG4gICAgLy8g4pyFIE9QVElNSVPDiSA6IERvY3VtZW50RnJhZ21lbnQgcG91ciBsZXMgcGVyZm9ybWFuY2VzIERPTVxyXG4gICAgdXBkYXRlTm90aWZpY2F0aW9uc0xpc3Qobm90aWZpY2F0aW9ucykge1xyXG4gICAgICAgIGlmICghdGhpcy5ub3RpZmljYXRpb25zTGlzdCkgcmV0dXJuO1xyXG5cclxuICAgICAgICAvLyBDcsOpZXIgdW4gZnJhZ21lbnQgcG91ciBtaW5pbWlzZXIgbGVzIHJlZmxvd3NcclxuICAgICAgICBjb25zdCBmcmFnbWVudCA9IGRvY3VtZW50LmNyZWF0ZURvY3VtZW50RnJhZ21lbnQoKTtcclxuXHJcbiAgICAgICAgaWYgKG5vdGlmaWNhdGlvbnMubGVuZ3RoID09PSAwKSB7XHJcbiAgICAgICAgICAgIGNvbnN0IGVtcHR5RGl2ID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnZGl2Jyk7XHJcbiAgICAgICAgICAgIGVtcHR5RGl2LmNsYXNzTmFtZSA9ICdkcm9wZG93bi1pdGVtIHRleHQtY2VudGVyIHRleHQtbXV0ZWQnO1xyXG4gICAgICAgICAgICBlbXB0eURpdi5pbm5lckhUTUwgPSAnPHNtYWxsPkF1Y3VuZSBub3RpZmljYXRpb248L3NtYWxsPic7XHJcbiAgICAgICAgICAgIGZyYWdtZW50LmFwcGVuZENoaWxkKGVtcHR5RGl2KTtcclxuICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICBub3RpZmljYXRpb25zLmZvckVhY2gobm90aWZpY2F0aW9uID0+IHtcclxuICAgICAgICAgICAgICAgIGNvbnN0IGl0ZW0gPSB0aGlzLmNyZWF0ZU5vdGlmaWNhdGlvbkl0ZW0obm90aWZpY2F0aW9uKTtcclxuICAgICAgICAgICAgICAgIGZyYWdtZW50LmFwcGVuZENoaWxkKGl0ZW0pO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIFVuZSBzZXVsZSBtYW5pcHVsYXRpb24gRE9NXHJcbiAgICAgICAgdGhpcy5ub3RpZmljYXRpb25zTGlzdC5pbm5lckhUTUwgPSAnJztcclxuICAgICAgICB0aGlzLm5vdGlmaWNhdGlvbnNMaXN0LmFwcGVuZENoaWxkKGZyYWdtZW50KTtcclxuICAgIH1cclxuXHJcbiAgICBjcmVhdGVOb3RpZmljYXRpb25JdGVtKG5vdGlmaWNhdGlvbikge1xyXG4gICAgICAgIGNvbnN0IGRpdiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2RpdicpO1xyXG4gICAgICAgIGRpdi5jbGFzc05hbWUgPSBgZHJvcGRvd24taXRlbSBub3RpZmljYXRpb24taXRlbSAke25vdGlmaWNhdGlvbi5pc0x1ZSA/ICdsdWUnIDogJ25vbi1sdWUnfWA7XHJcbiAgICAgICAgZGl2LmlubmVySFRNTCA9IGBcclxuICAgICAgICAgICAgPGRpdiBjbGFzcz1cImQtZmxleCBhbGlnbi1pdGVtcy1zdGFydFwiPlxyXG4gICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJtZS0yXCI+JHtub3RpZmljYXRpb24udHlwZUljb259PC9zcGFuPlxyXG4gICAgICAgICAgICAgICAgPGRpdiBjbGFzcz1cImZsZXgtZ3Jvdy0xXCI+XHJcbiAgICAgICAgICAgICAgICAgICAgPHAgY2xhc3M9XCJtYi0xIHNtYWxsXCI+JHtub3RpZmljYXRpb24ubWVzc2FnZX08L3A+XHJcbiAgICAgICAgICAgICAgICAgICAgPHNtYWxsIGNsYXNzPVwidGV4dC1tdXRlZFwiPiR7bm90aWZpY2F0aW9uLmRhdGVSZWxhdGl2ZX08L3NtYWxsPlxyXG4gICAgICAgICAgICAgICAgPC9kaXY+XHJcbiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPVwibm90aWZpY2F0aW9uLWFjdGlvbnNcIj5cclxuICAgICAgICAgICAgICAgICAgICAkeyFub3RpZmljYXRpb24uaXNMdWUgPyBgXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJidG4gYnRuLXNtIGJ0bi1vdXRsaW5lLXByaW1hcnkgbWFyay1yZWFkLWJ0blwiIFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGEtaWQ9XCIke25vdGlmaWNhdGlvbi5pZH1cIiB0aXRsZT1cIk1hcnF1ZXIgY29tbWUgbHVcIj5cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIOKck1xyXG4gICAgICAgICAgICAgICAgICAgICAgICA8L2J1dHRvbj5cclxuICAgICAgICAgICAgICAgICAgICBgIDogJyd9XHJcbiAgICAgICAgICAgICAgICA8L2Rpdj5cclxuICAgICAgICAgICAgPC9kaXY+XHJcbiAgICAgICAgYDtcclxuXHJcbiAgICAgICAgLy8gUmVkaXJlY3Rpb24gdmVycyBsZSBzaWduYWxlbWVudFxyXG4gICAgICAgIGlmIChub3RpZmljYXRpb24uc2lnbmFsZW1lbnRJZCkge1xyXG4gICAgICAgICAgICBkaXYuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCAoZSkgPT4ge1xyXG4gICAgICAgICAgICAgICAgaWYgKCFlLnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ21hcmstcmVhZC1idG4nKSkge1xyXG4gICAgICAgICAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gYC9zaWduYWxlbWVudHMvJHtub3RpZmljYXRpb24uc2lnbmFsZW1lbnRJZH1gO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIHJldHVybiBkaXY7XHJcbiAgICB9XHJcblxyXG4gICAgc2V0dXBFdmVudExpc3RlbmVycygpIHtcclxuICAgICAgICAvLyDinIUgT1BUSU1JU8OJIDogRXZlbnQgZGVsZWdhdGlvbiBwb3VyIGxlcyBwZXJmb3JtYW5jZXNcclxuICAgICAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGFzeW5jIChlKSA9PiB7XHJcbiAgICAgICAgICAgIGlmIChlLnRhcmdldC5jbGFzc0xpc3QuY29udGFpbnMoJ21hcmstcmVhZC1idG4nKSkge1xyXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICAgICAgICAgZS5zdG9wUHJvcGFnYXRpb24oKTtcclxuXHJcbiAgICAgICAgICAgICAgICBjb25zdCBub3RpZmljYXRpb25JZCA9IGUudGFyZ2V0LmRhdGFzZXQuaWQ7XHJcbiAgICAgICAgICAgICAgICBhd2FpdCB0aGlzLm1hcmtBc1JlYWQobm90aWZpY2F0aW9uSWQpO1xyXG5cclxuICAgICAgICAgICAgICAgIC8vIEludmFsaWRlciBsZSBjYWNoZSBwb3VyIGZvcmNlciBsZSByZWNoYXJnZW1lbnRcclxuICAgICAgICAgICAgICAgIHRoaXMuY2FjaGUuZGVsZXRlKCdub3RpZmljYXRpb25zJyk7XHJcbiAgICAgICAgICAgICAgICB0aGlzLmxvYWROb3RpZmljYXRpb25zKCk7XHJcbiAgICAgICAgICAgIH1cclxuICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgLy8gTWFycXVlciB0b3V0ZXMgY29tbWUgbHVlc1xyXG4gICAgICAgIGNvbnN0IG1hcmtBbGxCdG4gPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjbWFya0FsbFJlYWQnKTtcclxuICAgICAgICBpZiAobWFya0FsbEJ0bikge1xyXG4gICAgICAgICAgICBtYXJrQWxsQnRuLmFkZEV2ZW50TGlzdGVuZXIoJ2NsaWNrJywgYXN5bmMgKCkgPT4ge1xyXG4gICAgICAgICAgICAgICAgYXdhaXQgdGhpcy5tYXJrQWxsQXNSZWFkKCk7XHJcbiAgICAgICAgICAgICAgICB0aGlzLmNhY2hlLmRlbGV0ZSgnbm90aWZpY2F0aW9ucycpO1xyXG4gICAgICAgICAgICAgICAgdGhpcy5sb2FkTm90aWZpY2F0aW9ucygpO1xyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9XHJcbiAgICB9XHJcblxyXG4gICAgYXN5bmMgbWFya0FzUmVhZChub3RpZmljYXRpb25JZCkge1xyXG4gICAgICAgIHRyeSB7XHJcbiAgICAgICAgICAgIGF3YWl0IGZldGNoKGAvbm90aWZpY2F0aW9ucy8ke25vdGlmaWNhdGlvbklkfS9saXJlYCwge1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXHJcbiAgICAgICAgICAgICAgICBoZWFkZXJzOiB7XHJcbiAgICAgICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJyxcclxuICAgICAgICAgICAgICAgICAgICAnWC1SZXF1ZXN0ZWQtV2l0aCc6ICdYTUxIdHRwUmVxdWVzdCdcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcclxuICAgICAgICAgICAgY29uc29sZS5lcnJvcignRXJyZXVyIG1hcnF1ZXIgY29tbWUgbHU6JywgZXJyb3IpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICBhc3luYyBtYXJrQWxsQXNSZWFkKCkge1xyXG4gICAgICAgIHRyeSB7XHJcbiAgICAgICAgICAgIGF3YWl0IGZldGNoKCcvbm90aWZpY2F0aW9ucy9tYXJxdWVyLXRvdXRlcy1sdWVzJywge1xyXG4gICAgICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsXHJcbiAgICAgICAgICAgICAgICBoZWFkZXJzOiB7XHJcbiAgICAgICAgICAgICAgICAgICAgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJyxcclxuICAgICAgICAgICAgICAgICAgICAnWC1SZXF1ZXN0ZWQtV2l0aCc6ICdYTUxIdHRwUmVxdWVzdCdcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSBjYXRjaCAoZXJyb3IpIHtcclxuICAgICAgICAgICAgY29uc29sZS5lcnJvcignRXJyZXVyIG1hcnF1ZXIgdG91dGVzIGNvbW1lIGx1ZXM6JywgZXJyb3IpO1xyXG4gICAgICAgIH1cclxuICAgIH1cclxuXHJcbiAgICAvLyDinIUgTk9VVkVBVSA6IE3DqXRob2RlIGRlIG5ldHRveWFnZVxyXG4gICAgZGVzdHJveSgpIHtcclxuICAgICAgICB0aGlzLnN0b3BQb2xsaW5nKCk7XHJcbiAgICAgICAgaWYgKHRoaXMuZGVib3VuY2VUaW1lcikge1xyXG4gICAgICAgICAgICBjbGVhclRpbWVvdXQodGhpcy5kZWJvdW5jZVRpbWVyKTtcclxuICAgICAgICB9XHJcbiAgICAgICAgdGhpcy5jYWNoZS5jbGVhcigpO1xyXG4gICAgfVxyXG59XHJcblxyXG4vLyDinIUgT1BUSU1JU8OJIDogSW5pdGlhbGlzYXRpb24gYXZlYyB2w6lyaWZpY2F0aW9uIGRlIGwnw6l0YXQgZGUgY2hhcmdlbWVudFxyXG5mdW5jdGlvbiBpbml0Tm90aWZpY2F0aW9uTWFuYWdlcigpIHtcclxuICAgIGlmIChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjbm90aWZpY2F0aW9uRHJvcGRvd24nKSkge1xyXG4gICAgICAgIG5ldyBOb3RpZmljYXRpb25NYW5hZ2VyKCk7XHJcbiAgICB9XHJcbn1cclxuXHJcbi8vIEluaXRpYWxpc2F0aW9uIG9wdGltaXPDqWVcclxuaWYgKGRvY3VtZW50LnJlYWR5U3RhdGUgPT09ICdsb2FkaW5nJykge1xyXG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIGluaXROb3RpZmljYXRpb25NYW5hZ2VyKTtcclxufSBlbHNlIHtcclxuICAgIGluaXROb3RpZmljYXRpb25NYW5hZ2VyKCk7XHJcbn0iXSwibmFtZXMiOlsiX3JlZ2VuZXJhdG9yUnVudGltZSIsInIiLCJ0IiwiZSIsIk9iamVjdCIsInByb3RvdHlwZSIsIm4iLCJoYXNPd25Qcm9wZXJ0eSIsIm8iLCJTeW1ib2wiLCJpIiwiaXRlcmF0b3IiLCJhIiwiYXN5bmNJdGVyYXRvciIsInUiLCJ0b1N0cmluZ1RhZyIsImMiLCJkZWZpbmVQcm9wZXJ0eSIsInZhbHVlIiwiZW51bWVyYWJsZSIsImNvbmZpZ3VyYWJsZSIsIndyaXRhYmxlIiwiaCIsIkdlbmVyYXRvciIsImNyZWF0ZSIsIkVycm9yIiwiZG9uZSIsIm1ldGhvZCIsImFyZyIsImRlbGVnYXRlIiwiZCIsImYiLCJzZW50IiwiX3NlbnQiLCJkaXNwYXRjaEV4Y2VwdGlvbiIsImFicnVwdCIsInMiLCJ0eXBlIiwiQ29udGV4dCIsImNhbGwiLCJ3cmFwIiwiR2VuZXJhdG9yRnVuY3Rpb24iLCJHZW5lcmF0b3JGdW5jdGlvblByb3RvdHlwZSIsImwiLCJwIiwiZ2V0UHJvdG90eXBlT2YiLCJ5IiwieCIsInYiLCJnIiwiZm9yRWFjaCIsIl9pbnZva2UiLCJBc3luY0l0ZXJhdG9yIiwiX3R5cGVvZiIsInJlc29sdmUiLCJfX2F3YWl0IiwidGhlbiIsIlR5cGVFcnJvciIsIm5leHQiLCJ3IiwidHJ5RW50cmllcyIsInB1c2giLCJtIiwicmVzZXQiLCJpc05hTiIsImxlbmd0aCIsImRpc3BsYXlOYW1lIiwiaXNHZW5lcmF0b3JGdW5jdGlvbiIsImNvbnN0cnVjdG9yIiwibmFtZSIsIm1hcmsiLCJzZXRQcm90b3R5cGVPZiIsIl9fcHJvdG9fXyIsImF3cmFwIiwiYXN5bmMiLCJQcm9taXNlIiwia2V5cyIsInVuc2hpZnQiLCJwb3AiLCJ2YWx1ZXMiLCJwcmV2IiwiY2hhckF0Iiwic2xpY2UiLCJzdG9wIiwicnZhbCIsImNvbXBsZXRlIiwiZmluaXNoIiwiX2NhdGNoIiwiZGVsZWdhdGVZaWVsZCIsImFzeW5jR2VuZXJhdG9yU3RlcCIsIl9hc3luY1RvR2VuZXJhdG9yIiwiYXJndW1lbnRzIiwiYXBwbHkiLCJfbmV4dCIsIl90aHJvdyIsInJlcXVpcmUiLCJfY2xhc3NDYWxsQ2hlY2siLCJfZGVmaW5lUHJvcGVydGllcyIsIl90b1Byb3BlcnR5S2V5Iiwia2V5IiwiX2NyZWF0ZUNsYXNzIiwiX3RvUHJpbWl0aXZlIiwidG9QcmltaXRpdmUiLCJTdHJpbmciLCJOdW1iZXIiLCJOb3RpZmljYXRpb25NYW5hZ2VyIiwicG9sbEludGVydmFsIiwiaXNQb2xsaW5nIiwiY2FjaGUiLCJNYXAiLCJkZWJvdW5jZVRpbWVyIiwicG9sbFRpbWVyIiwibm90aWZpY2F0aW9uRHJvcGRvd24iLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3IiLCJub3RpZmljYXRpb25CYWRnZSIsIm5vdGlmaWNhdGlvbnNMaXN0IiwiaW5pdCIsInNldHVwSW50ZXJzZWN0aW9uT2JzZXJ2ZXIiLCJzZXR1cFZpc2liaWxpdHlIYW5kbGVyIiwic2V0dXBFdmVudExpc3RlbmVycyIsImxvYWROb3RpZmljYXRpb25zIiwiX3RoaXMiLCJhZGRFdmVudExpc3RlbmVyIiwiaGlkZGVuIiwic3RvcFBvbGxpbmciLCJzdGFydFBvbGxpbmciLCJfdGhpczIiLCJvYnNlcnZlciIsIkludGVyc2VjdGlvbk9ic2VydmVyIiwiZW50cmllcyIsImVudHJ5IiwiaXNJbnRlcnNlY3RpbmciLCJ0aHJlc2hvbGQiLCJvYnNlcnZlIiwiX3RoaXMzIiwic2V0SW50ZXJ2YWwiLCJjbGVhckludGVydmFsIiwiX2xvYWROb3RpZmljYXRpb25zIiwiX2NhbGxlZTIiLCJfdGhpczQiLCJfY2FsbGVlMiQiLCJfY29udGV4dDIiLCJjbGVhclRpbWVvdXQiLCJzZXRUaW1lb3V0IiwiX2NhbGxlZSIsImNhY2hlZCIsInJlc3BvbnNlIiwiZGF0YSIsIl9jYWNoZWQiLCJfY2FsbGVlJCIsIl9jb250ZXh0IiwiZ2V0IiwiRGF0ZSIsIm5vdyIsInRpbWVzdGFtcCIsInVwZGF0ZVVJIiwiZmV0Y2giLCJoZWFkZXJzIiwib2siLCJjb25jYXQiLCJzdGF0dXMiLCJqc29uIiwic2V0IiwidDAiLCJjb25zb2xlIiwiZXJyb3IiLCJfdGhpczUiLCJyZXF1ZXN0QW5pbWF0aW9uRnJhbWUiLCJ1cGRhdGVCYWRnZSIsIm5vbWJyZU5vbkx1ZXMiLCJ1cGRhdGVOb3RpZmljYXRpb25zTGlzdCIsIm5vdGlmaWNhdGlvbnMiLCJjb3VudCIsImN1cnJlbnRDb3VudCIsInBhcnNlSW50IiwidGV4dENvbnRlbnQiLCJzdHlsZSIsImRpc3BsYXkiLCJfdGhpczYiLCJmcmFnbWVudCIsImNyZWF0ZURvY3VtZW50RnJhZ21lbnQiLCJlbXB0eURpdiIsImNyZWF0ZUVsZW1lbnQiLCJjbGFzc05hbWUiLCJpbm5lckhUTUwiLCJhcHBlbmRDaGlsZCIsIm5vdGlmaWNhdGlvbiIsIml0ZW0iLCJjcmVhdGVOb3RpZmljYXRpb25JdGVtIiwiZGl2IiwiaXNMdWUiLCJ0eXBlSWNvbiIsIm1lc3NhZ2UiLCJkYXRlUmVsYXRpdmUiLCJpZCIsInNpZ25hbGVtZW50SWQiLCJ0YXJnZXQiLCJjbGFzc0xpc3QiLCJjb250YWlucyIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsIl90aGlzNyIsIl9yZWYyIiwiX2NhbGxlZTMiLCJub3RpZmljYXRpb25JZCIsIl9jYWxsZWUzJCIsIl9jb250ZXh0MyIsInByZXZlbnREZWZhdWx0Iiwic3RvcFByb3BhZ2F0aW9uIiwiZGF0YXNldCIsIm1hcmtBc1JlYWQiLCJfeCIsIm1hcmtBbGxCdG4iLCJfY2FsbGVlNCIsIl9jYWxsZWU0JCIsIl9jb250ZXh0NCIsIm1hcmtBbGxBc1JlYWQiLCJfbWFya0FzUmVhZCIsIl9jYWxsZWU1IiwiX2NhbGxlZTUkIiwiX2NvbnRleHQ1IiwiX3gyIiwiX21hcmtBbGxBc1JlYWQiLCJfY2FsbGVlNiIsIl9jYWxsZWU2JCIsIl9jb250ZXh0NiIsImRlc3Ryb3kiLCJjbGVhciIsImluaXROb3RpZmljYXRpb25NYW5hZ2VyIiwicmVhZHlTdGF0ZSJdLCJzb3VyY2VSb290IjoiIn0=