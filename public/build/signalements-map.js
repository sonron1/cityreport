"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["signalements-map"],{

/***/ "./assets/js/map-config.js":
/*!*********************************!*\
  !*** ./assets/js/map-config.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BENIN_BOUNDS: () => (/* binding */ BENIN_BOUNDS),
/* harmony export */   BENIN_CENTER_LAT: () => (/* binding */ BENIN_CENTER_LAT),
/* harmony export */   BENIN_CENTER_LNG: () => (/* binding */ BENIN_CENTER_LNG),
/* harmony export */   BENIN_DEFAULT_ZOOM: () => (/* binding */ BENIN_DEFAULT_ZOOM),
/* harmony export */   DEFAULT_ATTRIBUTION: () => (/* binding */ DEFAULT_ATTRIBUTION),
/* harmony export */   DEFAULT_TILE_LAYER: () => (/* binding */ DEFAULT_TILE_LAYER)
/* harmony export */ });
// assets/js/map-config.js

// Configuration des coordonnées pour le Bénin
var BENIN_CENTER_LAT = 9.3217;
var BENIN_CENTER_LNG = 2.3100;
var BENIN_DEFAULT_ZOOM = 7;

// Limites géographiques du Bénin (pour la restriction de navigation)
var BENIN_BOUNDS = {
  southWest: {
    lat: 5.5,
    lng: 0.5
  },
  // Sud-ouest
  northEast: {
    lat: 12.5,
    lng: 4.0
  } // Nord-est
};

// Autres configurations communes
var DEFAULT_TILE_LAYER = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
var DEFAULT_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

/***/ }),

/***/ "./assets/js/signalement-map.js":
/*!**************************************!*\
  !*** ./assets/js/signalement-map.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_parse_float_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.parse-float.js */ "./node_modules/core-js/modules/es.parse-float.js");
/* harmony import */ var core_js_modules_es_parse_float_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_parse_float_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/esnext.iterator.map.js */ "./node_modules/core-js/modules/esnext.iterator.map.js");
/* harmony import */ var core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _map_config_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./map-config.js */ "./assets/js/map-config.js");





// assets/js/signalements-map.js



// Utiliser les constantes importées
var defaultLat = _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_CENTER_LAT;
var defaultLng = _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_CENTER_LNG;
var defaultZoom = _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_DEFAULT_ZOOM;
var map, marker;

// Initialisation de la carte
document.addEventListener('DOMContentLoaded', function () {
  var mapElement = document.getElementById('map');
  if (mapElement) {
    initMap();
    setupListeners();
    initializeFromForm();
  } else {
    console.warn('Élément #map non trouvé dans le DOM');
  }
});

// Initialisation de la carte Leaflet
function initMap() {
  // Création de la carte avec les coordonnées du Bénin
  map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

  // Ajout du layer OpenStreetMap
  L.tileLayer(_map_config_js__WEBPACK_IMPORTED_MODULE_5__.DEFAULT_TILE_LAYER, {
    attribution: _map_config_js__WEBPACK_IMPORTED_MODULE_5__.DEFAULT_ATTRIBUTION
  }).addTo(map);

  // Définir des limites de navigation
  var southWest = L.latLng(_map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.southWest.lat, _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.southWest.lng);
  var northEast = L.latLng(_map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.northEast.lat, _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.northEast.lng);
  var bounds = L.latLngBounds(southWest, northEast);
  map.setMaxBounds(bounds);

  // Ajout du marqueur initial
  marker = L.marker([defaultLat, defaultLng], {
    draggable: true
  }).addTo(map);

  // Mise à jour des coordonnées quand on déplace le marqueur
  marker.on('dragend', function (event) {
    updateCoordinates(marker.getLatLng().lat, marker.getLatLng().lng);
  });

  // Ajout d'un clic sur la carte pour déplacer le marqueur
  map.on('click', function (event) {
    marker.setLatLng(event.latlng);
    updateCoordinates(event.latlng.lat, event.latlng.lng);
  });

  // Initialiser les coordonnées avec les valeurs par défaut
  updateCoordinates(defaultLat, defaultLng);
}

// Ajouter cette fonction après initMap()
function updateCoordinates(lat, lng) {
  // Mettre à jour les champs du formulaire
  document.getElementById('signalement_type_form_latitude').value = lat;
  document.getElementById('signalement_type_form_longitude').value = lng;

  // Mettre à jour les champs visibles (facultatif)
  var manualLatInput = document.getElementById('manual-latitude');
  var manualLngInput = document.getElementById('manual-longitude');
  if (manualLatInput && manualLngInput) {
    manualLatInput.value = lat;
    manualLngInput.value = lng;
  }

  // Mettre à jour la position du marqueur si nécessaire
  if (marker) {
    marker.setLatLng([lat, lng]);
  }

  // Centrer la carte sur les nouvelles coordonnées
  if (map) {
    map.panTo([lat, lng]);
  }
}
// Ajouter cette fonction également
function setupListeners() {
  // Géolocalisation de l'utilisateur
  var geolocateBtn = document.getElementById('geolocate-btn');
  if (geolocateBtn) {
    geolocateBtn.addEventListener('click', function () {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
          var lat = position.coords.latitude;
          var lng = position.coords.longitude;

          // Vérifier si les coordonnées sont dans les limites du Bénin
          if (lat >= _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.southWest.lat && lat <= _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.northEast.lat && lng >= _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.southWest.lng && lng <= _map_config_js__WEBPACK_IMPORTED_MODULE_5__.BENIN_BOUNDS.northEast.lng) {
            updateCoordinates(lat, lng);
          } else {
            alert('Votre position actuelle est en dehors des limites de la carte.');
          }
        }, function (error) {
          console.error('Erreur de géolocalisation:', error);
          alert('Impossible d\'obtenir votre position actuelle.');
        });
      } else {
        alert('La géolocalisation n\'est pas prise en charge par votre navigateur.');
      }
    });
  }

  // Appliquer les coordonnées manuelles
  var applyCoordsBtn = document.getElementById('apply-coords-btn');
  if (applyCoordsBtn) {
    applyCoordsBtn.addEventListener('click', function () {
      var manualLatInput = document.getElementById('manual-latitude');
      var manualLngInput = document.getElementById('manual-longitude');
      if (manualLatInput && manualLngInput) {
        var lat = parseFloat(manualLatInput.value);
        var lng = parseFloat(manualLngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
          updateCoordinates(lat, lng);
        } else {
          alert('Veuillez saisir des coordonnées valides.');
        }
      }
    });
  }
}
function initializeFromForm() {
  // Récupérer les valeurs initiales depuis le formulaire s'il y en a
  var latField = document.getElementById('signalement_type_form_latitude');
  var lngField = document.getElementById('signalement_type_form_longitude');
  if (latField && lngField && latField.value && lngField.value) {
    var lat = parseFloat(latField.value);
    var lng = parseFloat(lngField.value);
    if (!isNaN(lat) && !isNaN(lng)) {
      updateCoordinates(lat, lng);
    }
  }
}
// Reste du code existant...

/***/ }),

/***/ "./node_modules/core-js/internals/number-parse-float.js":
/*!**************************************************************!*\
  !*** ./node_modules/core-js/internals/number-parse-float.js ***!
  \**************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {


var globalThis = __webpack_require__(/*! ../internals/global-this */ "./node_modules/core-js/internals/global-this.js");
var fails = __webpack_require__(/*! ../internals/fails */ "./node_modules/core-js/internals/fails.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var toString = __webpack_require__(/*! ../internals/to-string */ "./node_modules/core-js/internals/to-string.js");
var trim = (__webpack_require__(/*! ../internals/string-trim */ "./node_modules/core-js/internals/string-trim.js").trim);
var whitespaces = __webpack_require__(/*! ../internals/whitespaces */ "./node_modules/core-js/internals/whitespaces.js");

var charAt = uncurryThis(''.charAt);
var $parseFloat = globalThis.parseFloat;
var Symbol = globalThis.Symbol;
var ITERATOR = Symbol && Symbol.iterator;
var FORCED = 1 / $parseFloat(whitespaces + '-0') !== -Infinity
  // MS Edge 18- broken with boxed symbols
  || (ITERATOR && !fails(function () { $parseFloat(Object(ITERATOR)); }));

// `parseFloat` method
// https://tc39.es/ecma262/#sec-parsefloat-string
module.exports = FORCED ? function parseFloat(string) {
  var trimmedString = trim(toString(string));
  var result = $parseFloat(trimmedString);
  return result === 0 && charAt(trimmedString, 0) === '-' ? -0 : result;
} : $parseFloat;


/***/ }),

/***/ "./node_modules/core-js/internals/string-trim.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/internals/string-trim.js ***!
  \*******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {


var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var requireObjectCoercible = __webpack_require__(/*! ../internals/require-object-coercible */ "./node_modules/core-js/internals/require-object-coercible.js");
var toString = __webpack_require__(/*! ../internals/to-string */ "./node_modules/core-js/internals/to-string.js");
var whitespaces = __webpack_require__(/*! ../internals/whitespaces */ "./node_modules/core-js/internals/whitespaces.js");

var replace = uncurryThis(''.replace);
var ltrim = RegExp('^[' + whitespaces + ']+');
var rtrim = RegExp('(^|[^' + whitespaces + '])[' + whitespaces + ']+$');

// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
var createMethod = function (TYPE) {
  return function ($this) {
    var string = toString(requireObjectCoercible($this));
    if (TYPE & 1) string = replace(string, ltrim, '');
    if (TYPE & 2) string = replace(string, rtrim, '$1');
    return string;
  };
};

module.exports = {
  // `String.prototype.{ trimLeft, trimStart }` methods
  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
  start: createMethod(1),
  // `String.prototype.{ trimRight, trimEnd }` methods
  // https://tc39.es/ecma262/#sec-string.prototype.trimend
  end: createMethod(2),
  // `String.prototype.trim` method
  // https://tc39.es/ecma262/#sec-string.prototype.trim
  trim: createMethod(3)
};


/***/ }),

/***/ "./node_modules/core-js/internals/to-string.js":
/*!*****************************************************!*\
  !*** ./node_modules/core-js/internals/to-string.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {


var classof = __webpack_require__(/*! ../internals/classof */ "./node_modules/core-js/internals/classof.js");

var $String = String;

module.exports = function (argument) {
  if (classof(argument) === 'Symbol') throw new TypeError('Cannot convert a Symbol value to a string');
  return $String(argument);
};


/***/ }),

/***/ "./node_modules/core-js/internals/whitespaces.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/internals/whitespaces.js ***!
  \*******************************************************/
/***/ ((module) => {


// a string of all valid unicode whitespaces
module.exports = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';


/***/ }),

/***/ "./node_modules/core-js/modules/es.parse-float.js":
/*!********************************************************!*\
  !*** ./node_modules/core-js/modules/es.parse-float.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {


var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var $parseFloat = __webpack_require__(/*! ../internals/number-parse-float */ "./node_modules/core-js/internals/number-parse-float.js");

// `parseFloat` method
// https://tc39.es/ecma262/#sec-parsefloat-string
$({ global: true, forced: parseFloat !== $parseFloat }, {
  parseFloat: $parseFloat
});


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_internals_an-instance_js-node_modules_core-js_internals_array-it-055933","vendors-node_modules_core-js_modules_es_array_map_js-node_modules_core-js_modules_esnext_iter-01c182"], () => (__webpack_exec__("./assets/js/signalement-map.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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cyI6WyJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL21hcC1jb25maWcuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL3NpZ25hbGVtZW50LW1hcC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9pbnRlcm5hbHMvbnVtYmVyLXBhcnNlLWZsb2F0LmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jb3JlLWpzL2ludGVybmFscy9zdHJpbmctdHJpbS5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9pbnRlcm5hbHMvdG8tc3RyaW5nLmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jb3JlLWpzL2ludGVybmFscy93aGl0ZXNwYWNlcy5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9tb2R1bGVzL2VzLnBhcnNlLWZsb2F0LmpzIl0sInNvdXJjZXNDb250ZW50IjpbIi8vIGFzc2V0cy9qcy9tYXAtY29uZmlnLmpzXHJcblxyXG4vLyBDb25maWd1cmF0aW9uIGRlcyBjb29yZG9ubsOpZXMgcG91ciBsZSBCw6luaW5cclxuZXhwb3J0IGNvbnN0IEJFTklOX0NFTlRFUl9MQVQgPSA5LjMyMTc7XHJcbmV4cG9ydCBjb25zdCBCRU5JTl9DRU5URVJfTE5HID0gMi4zMTAwO1xyXG5leHBvcnQgY29uc3QgQkVOSU5fREVGQVVMVF9aT09NID0gNztcclxuXHJcbi8vIExpbWl0ZXMgZ8Opb2dyYXBoaXF1ZXMgZHUgQsOpbmluIChwb3VyIGxhIHJlc3RyaWN0aW9uIGRlIG5hdmlnYXRpb24pXHJcbmV4cG9ydCBjb25zdCBCRU5JTl9CT1VORFMgPSB7XHJcbiAgc291dGhXZXN0OiB7IGxhdDogNS41LCBsbmc6IDAuNSB9LCAgLy8gU3VkLW91ZXN0XHJcbiAgbm9ydGhFYXN0OiB7IGxhdDogMTIuNSwgbG5nOiA0LjAgfSAgLy8gTm9yZC1lc3RcclxufTtcclxuXHJcbi8vIEF1dHJlcyBjb25maWd1cmF0aW9ucyBjb21tdW5lc1xyXG5leHBvcnQgY29uc3QgREVGQVVMVF9USUxFX0xBWUVSID0gJ2h0dHBzOi8ve3N9LnRpbGUub3BlbnN0cmVldG1hcC5vcmcve3p9L3t4fS97eX0ucG5nJztcclxuZXhwb3J0IGNvbnN0IERFRkFVTFRfQVRUUklCVVRJT04gPSAnJmNvcHk7IDxhIGhyZWY9XCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnMnOyIsIi8vIGFzc2V0cy9qcy9zaWduYWxlbWVudHMtbWFwLmpzXHJcblxyXG5pbXBvcnQgeyBcclxuICAgIEJFTklOX0NFTlRFUl9MQVQsIFxyXG4gICAgQkVOSU5fQ0VOVEVSX0xORywgXHJcbiAgICBCRU5JTl9ERUZBVUxUX1pPT00sXHJcbiAgICBCRU5JTl9CT1VORFMsXHJcbiAgICBERUZBVUxUX1RJTEVfTEFZRVIsXHJcbiAgICBERUZBVUxUX0FUVFJJQlVUSU9OXHJcbn0gZnJvbSAnLi9tYXAtY29uZmlnLmpzJztcclxuXHJcbi8vIFV0aWxpc2VyIGxlcyBjb25zdGFudGVzIGltcG9ydMOpZXNcclxubGV0IGRlZmF1bHRMYXQgPSBCRU5JTl9DRU5URVJfTEFUO1xyXG5sZXQgZGVmYXVsdExuZyA9IEJFTklOX0NFTlRFUl9MTkc7XHJcbmxldCBkZWZhdWx0Wm9vbSA9IEJFTklOX0RFRkFVTFRfWk9PTTtcclxubGV0IG1hcCwgbWFya2VyO1xyXG5cclxuLy8gSW5pdGlhbGlzYXRpb24gZGUgbGEgY2FydGVcclxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIGZ1bmN0aW9uKCkge1xyXG4gICAgY29uc3QgbWFwRWxlbWVudCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYXAnKTtcclxuICAgIFxyXG4gICAgaWYgKG1hcEVsZW1lbnQpIHtcclxuICAgICAgICBpbml0TWFwKCk7XHJcbiAgICAgICAgc2V0dXBMaXN0ZW5lcnMoKTtcclxuICAgICAgICBpbml0aWFsaXplRnJvbUZvcm0oKTtcclxuICAgIH0gZWxzZSB7XHJcbiAgICAgICAgY29uc29sZS53YXJuKCfDiWzDqW1lbnQgI21hcCBub24gdHJvdXbDqSBkYW5zIGxlIERPTScpO1xyXG4gICAgfVxyXG59KTtcclxuXHJcbi8vIEluaXRpYWxpc2F0aW9uIGRlIGxhIGNhcnRlIExlYWZsZXRcclxuZnVuY3Rpb24gaW5pdE1hcCgpIHtcclxuICAgIC8vIENyw6lhdGlvbiBkZSBsYSBjYXJ0ZSBhdmVjIGxlcyBjb29yZG9ubsOpZXMgZHUgQsOpbmluXHJcbiAgICBtYXAgPSBMLm1hcCgnbWFwJykuc2V0VmlldyhbZGVmYXVsdExhdCwgZGVmYXVsdExuZ10sIGRlZmF1bHRab29tKTtcclxuICAgIFxyXG4gICAgLy8gQWpvdXQgZHUgbGF5ZXIgT3BlblN0cmVldE1hcFxyXG4gICAgTC50aWxlTGF5ZXIoREVGQVVMVF9USUxFX0xBWUVSLCB7XHJcbiAgICAgICAgYXR0cmlidXRpb246IERFRkFVTFRfQVRUUklCVVRJT05cclxuICAgIH0pLmFkZFRvKG1hcCk7XHJcbiAgICBcclxuICAgIC8vIETDqWZpbmlyIGRlcyBsaW1pdGVzIGRlIG5hdmlnYXRpb25cclxuICAgIGNvbnN0IHNvdXRoV2VzdCA9IEwubGF0TG5nKEJFTklOX0JPVU5EUy5zb3V0aFdlc3QubGF0LCBCRU5JTl9CT1VORFMuc291dGhXZXN0LmxuZyk7XHJcbiAgICBjb25zdCBub3J0aEVhc3QgPSBMLmxhdExuZyhCRU5JTl9CT1VORFMubm9ydGhFYXN0LmxhdCwgQkVOSU5fQk9VTkRTLm5vcnRoRWFzdC5sbmcpO1xyXG4gICAgY29uc3QgYm91bmRzID0gTC5sYXRMbmdCb3VuZHMoc291dGhXZXN0LCBub3J0aEVhc3QpO1xyXG4gICAgXHJcbiAgICBtYXAuc2V0TWF4Qm91bmRzKGJvdW5kcyk7XHJcbiAgICBcclxuICAgIC8vIEFqb3V0IGR1IG1hcnF1ZXVyIGluaXRpYWxcclxuICAgIG1hcmtlciA9IEwubWFya2VyKFtkZWZhdWx0TGF0LCBkZWZhdWx0TG5nXSwge1xyXG4gICAgICAgIGRyYWdnYWJsZTogdHJ1ZVxyXG4gICAgfSkuYWRkVG8obWFwKTtcclxuICAgIFxyXG4gICAgLy8gTWlzZSDDoCBqb3VyIGRlcyBjb29yZG9ubsOpZXMgcXVhbmQgb24gZMOpcGxhY2UgbGUgbWFycXVldXJcclxuICAgIG1hcmtlci5vbignZHJhZ2VuZCcsIGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICAgICAgdXBkYXRlQ29vcmRpbmF0ZXMobWFya2VyLmdldExhdExuZygpLmxhdCwgbWFya2VyLmdldExhdExuZygpLmxuZyk7XHJcbiAgICB9KTtcclxuICAgIFxyXG4gICAgLy8gQWpvdXQgZCd1biBjbGljIHN1ciBsYSBjYXJ0ZSBwb3VyIGTDqXBsYWNlciBsZSBtYXJxdWV1clxyXG4gICAgbWFwLm9uKCdjbGljaycsIGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICAgICAgbWFya2VyLnNldExhdExuZyhldmVudC5sYXRsbmcpO1xyXG4gICAgICAgIHVwZGF0ZUNvb3JkaW5hdGVzKGV2ZW50LmxhdGxuZy5sYXQsIGV2ZW50LmxhdGxuZy5sbmcpO1xyXG4gICAgfSk7XHJcbiAgICBcclxuICAgIC8vIEluaXRpYWxpc2VyIGxlcyBjb29yZG9ubsOpZXMgYXZlYyBsZXMgdmFsZXVycyBwYXIgZMOpZmF1dFxyXG4gICAgdXBkYXRlQ29vcmRpbmF0ZXMoZGVmYXVsdExhdCwgZGVmYXVsdExuZyk7XHJcbn1cclxuXHJcbi8vIEFqb3V0ZXIgY2V0dGUgZm9uY3Rpb24gYXByw6hzIGluaXRNYXAoKVxyXG5mdW5jdGlvbiB1cGRhdGVDb29yZGluYXRlcyhsYXQsIGxuZykge1xyXG4gICAgLy8gTWV0dHJlIMOgIGpvdXIgbGVzIGNoYW1wcyBkdSBmb3JtdWxhaXJlXHJcbiAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2lnbmFsZW1lbnRfdHlwZV9mb3JtX2xhdGl0dWRlJykudmFsdWUgPSBsYXQ7XHJcbiAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2lnbmFsZW1lbnRfdHlwZV9mb3JtX2xvbmdpdHVkZScpLnZhbHVlID0gbG5nO1xyXG4gICAgXHJcbiAgICAvLyBNZXR0cmUgw6Agam91ciBsZXMgY2hhbXBzIHZpc2libGVzIChmYWN1bHRhdGlmKVxyXG4gICAgY29uc3QgbWFudWFsTGF0SW5wdXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFudWFsLWxhdGl0dWRlJyk7XHJcbiAgICBjb25zdCBtYW51YWxMbmdJbnB1dCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdtYW51YWwtbG9uZ2l0dWRlJyk7XHJcbiAgICBcclxuICAgIGlmIChtYW51YWxMYXRJbnB1dCAmJiBtYW51YWxMbmdJbnB1dCkge1xyXG4gICAgICAgIG1hbnVhbExhdElucHV0LnZhbHVlID0gbGF0O1xyXG4gICAgICAgIG1hbnVhbExuZ0lucHV0LnZhbHVlID0gbG5nO1xyXG4gICAgfVxyXG4gICAgXHJcbiAgICAvLyBNZXR0cmUgw6Agam91ciBsYSBwb3NpdGlvbiBkdSBtYXJxdWV1ciBzaSBuw6ljZXNzYWlyZVxyXG4gICAgaWYgKG1hcmtlcikge1xyXG4gICAgICAgIG1hcmtlci5zZXRMYXRMbmcoW2xhdCwgbG5nXSk7XHJcbiAgICB9XHJcbiAgICBcclxuICAgIC8vIENlbnRyZXIgbGEgY2FydGUgc3VyIGxlcyBub3V2ZWxsZXMgY29vcmRvbm7DqWVzXHJcbiAgICBpZiAobWFwKSB7XHJcbiAgICAgICAgbWFwLnBhblRvKFtsYXQsIGxuZ10pO1xyXG4gICAgfVxyXG59XHJcbi8vIEFqb3V0ZXIgY2V0dGUgZm9uY3Rpb24gw6lnYWxlbWVudFxyXG5mdW5jdGlvbiBzZXR1cExpc3RlbmVycygpIHtcclxuICAgIC8vIEfDqW9sb2NhbGlzYXRpb24gZGUgbCd1dGlsaXNhdGV1clxyXG4gICAgY29uc3QgZ2VvbG9jYXRlQnRuID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2dlb2xvY2F0ZS1idG4nKTtcclxuICAgIGlmIChnZW9sb2NhdGVCdG4pIHtcclxuICAgICAgICBnZW9sb2NhdGVCdG4uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgaWYgKG5hdmlnYXRvci5nZW9sb2NhdGlvbikge1xyXG4gICAgICAgICAgICAgICAgbmF2aWdhdG9yLmdlb2xvY2F0aW9uLmdldEN1cnJlbnRQb3NpdGlvbihcclxuICAgICAgICAgICAgICAgICAgICBmdW5jdGlvbihwb3NpdGlvbikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjb25zdCBsYXQgPSBwb3NpdGlvbi5jb29yZHMubGF0aXR1ZGU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IGxuZyA9IHBvc2l0aW9uLmNvb3Jkcy5sb25naXR1ZGU7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBWw6lyaWZpZXIgc2kgbGVzIGNvb3Jkb25uw6llcyBzb250IGRhbnMgbGVzIGxpbWl0ZXMgZHUgQsOpbmluXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChsYXQgPj0gQkVOSU5fQk9VTkRTLnNvdXRoV2VzdC5sYXQgJiYgbGF0IDw9IEJFTklOX0JPVU5EUy5ub3J0aEVhc3QubGF0ICYmXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBsbmcgPj0gQkVOSU5fQk9VTkRTLnNvdXRoV2VzdC5sbmcgJiYgbG5nIDw9IEJFTklOX0JPVU5EUy5ub3J0aEVhc3QubG5nKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cGRhdGVDb29yZGluYXRlcyhsYXQsIGxuZyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBhbGVydCgnVm90cmUgcG9zaXRpb24gYWN0dWVsbGUgZXN0IGVuIGRlaG9ycyBkZXMgbGltaXRlcyBkZSBsYSBjYXJ0ZS4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICAgICAgZnVuY3Rpb24oZXJyb3IpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY29uc29sZS5lcnJvcignRXJyZXVyIGRlIGfDqW9sb2NhbGlzYXRpb246JywgZXJyb3IpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBhbGVydCgnSW1wb3NzaWJsZSBkXFwnb2J0ZW5pciB2b3RyZSBwb3NpdGlvbiBhY3R1ZWxsZS4nKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICApO1xyXG4gICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgYWxlcnQoJ0xhIGfDqW9sb2NhbGlzYXRpb24gblxcJ2VzdCBwYXMgcHJpc2UgZW4gY2hhcmdlIHBhciB2b3RyZSBuYXZpZ2F0ZXVyLicpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbiAgICBcclxuICAgIC8vIEFwcGxpcXVlciBsZXMgY29vcmRvbm7DqWVzIG1hbnVlbGxlc1xyXG4gICAgY29uc3QgYXBwbHlDb29yZHNCdG4gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnYXBwbHktY29vcmRzLWJ0bicpO1xyXG4gICAgaWYgKGFwcGx5Q29vcmRzQnRuKSB7XHJcbiAgICAgICAgYXBwbHlDb29yZHNCdG4uYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgY29uc3QgbWFudWFsTGF0SW5wdXQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFudWFsLWxhdGl0dWRlJyk7XHJcbiAgICAgICAgICAgIGNvbnN0IG1hbnVhbExuZ0lucHV0ID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ21hbnVhbC1sb25naXR1ZGUnKTtcclxuICAgICAgICAgICAgXHJcbiAgICAgICAgICAgIGlmIChtYW51YWxMYXRJbnB1dCAmJiBtYW51YWxMbmdJbnB1dCkge1xyXG4gICAgICAgICAgICAgICAgY29uc3QgbGF0ID0gcGFyc2VGbG9hdChtYW51YWxMYXRJbnB1dC52YWx1ZSk7XHJcbiAgICAgICAgICAgICAgICBjb25zdCBsbmcgPSBwYXJzZUZsb2F0KG1hbnVhbExuZ0lucHV0LnZhbHVlKTtcclxuICAgICAgICAgICAgICAgIFxyXG4gICAgICAgICAgICAgICAgaWYgKCFpc05hTihsYXQpICYmICFpc05hTihsbmcpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgdXBkYXRlQ29vcmRpbmF0ZXMobGF0LCBsbmcpO1xyXG4gICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICBhbGVydCgnVmV1aWxsZXogc2Fpc2lyIGRlcyBjb29yZG9ubsOpZXMgdmFsaWRlcy4nKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG59XHJcbmZ1bmN0aW9uIGluaXRpYWxpemVGcm9tRm9ybSgpIHtcclxuICAgIC8vIFLDqWN1cMOpcmVyIGxlcyB2YWxldXJzIGluaXRpYWxlcyBkZXB1aXMgbGUgZm9ybXVsYWlyZSBzJ2lsIHkgZW4gYVxyXG4gICAgY29uc3QgbGF0RmllbGQgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnc2lnbmFsZW1lbnRfdHlwZV9mb3JtX2xhdGl0dWRlJyk7XHJcbiAgICBjb25zdCBsbmdGaWVsZCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdzaWduYWxlbWVudF90eXBlX2Zvcm1fbG9uZ2l0dWRlJyk7XHJcbiAgICBcclxuICAgIGlmIChsYXRGaWVsZCAmJiBsbmdGaWVsZCAmJiBsYXRGaWVsZC52YWx1ZSAmJiBsbmdGaWVsZC52YWx1ZSkge1xyXG4gICAgICAgIGNvbnN0IGxhdCA9IHBhcnNlRmxvYXQobGF0RmllbGQudmFsdWUpO1xyXG4gICAgICAgIGNvbnN0IGxuZyA9IHBhcnNlRmxvYXQobG5nRmllbGQudmFsdWUpO1xyXG4gICAgICAgIFxyXG4gICAgICAgIGlmICghaXNOYU4obGF0KSAmJiAhaXNOYU4obG5nKSkge1xyXG4gICAgICAgICAgICB1cGRhdGVDb29yZGluYXRlcyhsYXQsIGxuZyk7XHJcbiAgICAgICAgfVxyXG4gICAgfVxyXG59XHJcbi8vIFJlc3RlIGR1IGNvZGUgZXhpc3RhbnQuLi4iLCIndXNlIHN0cmljdCc7XG52YXIgZ2xvYmFsVGhpcyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9nbG9iYWwtdGhpcycpO1xudmFyIGZhaWxzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2ZhaWxzJyk7XG52YXIgdW5jdXJyeVRoaXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvZnVuY3Rpb24tdW5jdXJyeS10aGlzJyk7XG52YXIgdG9TdHJpbmcgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvdG8tc3RyaW5nJyk7XG52YXIgdHJpbSA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9zdHJpbmctdHJpbScpLnRyaW07XG52YXIgd2hpdGVzcGFjZXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvd2hpdGVzcGFjZXMnKTtcblxudmFyIGNoYXJBdCA9IHVuY3VycnlUaGlzKCcnLmNoYXJBdCk7XG52YXIgJHBhcnNlRmxvYXQgPSBnbG9iYWxUaGlzLnBhcnNlRmxvYXQ7XG52YXIgU3ltYm9sID0gZ2xvYmFsVGhpcy5TeW1ib2w7XG52YXIgSVRFUkFUT1IgPSBTeW1ib2wgJiYgU3ltYm9sLml0ZXJhdG9yO1xudmFyIEZPUkNFRCA9IDEgLyAkcGFyc2VGbG9hdCh3aGl0ZXNwYWNlcyArICctMCcpICE9PSAtSW5maW5pdHlcbiAgLy8gTVMgRWRnZSAxOC0gYnJva2VuIHdpdGggYm94ZWQgc3ltYm9sc1xuICB8fCAoSVRFUkFUT1IgJiYgIWZhaWxzKGZ1bmN0aW9uICgpIHsgJHBhcnNlRmxvYXQoT2JqZWN0KElURVJBVE9SKSk7IH0pKTtcblxuLy8gYHBhcnNlRmxvYXRgIG1ldGhvZFxuLy8gaHR0cHM6Ly90YzM5LmVzL2VjbWEyNjIvI3NlYy1wYXJzZWZsb2F0LXN0cmluZ1xubW9kdWxlLmV4cG9ydHMgPSBGT1JDRUQgPyBmdW5jdGlvbiBwYXJzZUZsb2F0KHN0cmluZykge1xuICB2YXIgdHJpbW1lZFN0cmluZyA9IHRyaW0odG9TdHJpbmcoc3RyaW5nKSk7XG4gIHZhciByZXN1bHQgPSAkcGFyc2VGbG9hdCh0cmltbWVkU3RyaW5nKTtcbiAgcmV0dXJuIHJlc3VsdCA9PT0gMCAmJiBjaGFyQXQodHJpbW1lZFN0cmluZywgMCkgPT09ICctJyA/IC0wIDogcmVzdWx0O1xufSA6ICRwYXJzZUZsb2F0O1xuIiwiJ3VzZSBzdHJpY3QnO1xudmFyIHVuY3VycnlUaGlzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2Z1bmN0aW9uLXVuY3VycnktdGhpcycpO1xudmFyIHJlcXVpcmVPYmplY3RDb2VyY2libGUgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvcmVxdWlyZS1vYmplY3QtY29lcmNpYmxlJyk7XG52YXIgdG9TdHJpbmcgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvdG8tc3RyaW5nJyk7XG52YXIgd2hpdGVzcGFjZXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvd2hpdGVzcGFjZXMnKTtcblxudmFyIHJlcGxhY2UgPSB1bmN1cnJ5VGhpcygnJy5yZXBsYWNlKTtcbnZhciBsdHJpbSA9IFJlZ0V4cCgnXlsnICsgd2hpdGVzcGFjZXMgKyAnXSsnKTtcbnZhciBydHJpbSA9IFJlZ0V4cCgnKF58W14nICsgd2hpdGVzcGFjZXMgKyAnXSlbJyArIHdoaXRlc3BhY2VzICsgJ10rJCcpO1xuXG4vLyBgU3RyaW5nLnByb3RvdHlwZS57IHRyaW0sIHRyaW1TdGFydCwgdHJpbUVuZCwgdHJpbUxlZnQsIHRyaW1SaWdodCB9YCBtZXRob2RzIGltcGxlbWVudGF0aW9uXG52YXIgY3JlYXRlTWV0aG9kID0gZnVuY3Rpb24gKFRZUEUpIHtcbiAgcmV0dXJuIGZ1bmN0aW9uICgkdGhpcykge1xuICAgIHZhciBzdHJpbmcgPSB0b1N0cmluZyhyZXF1aXJlT2JqZWN0Q29lcmNpYmxlKCR0aGlzKSk7XG4gICAgaWYgKFRZUEUgJiAxKSBzdHJpbmcgPSByZXBsYWNlKHN0cmluZywgbHRyaW0sICcnKTtcbiAgICBpZiAoVFlQRSAmIDIpIHN0cmluZyA9IHJlcGxhY2Uoc3RyaW5nLCBydHJpbSwgJyQxJyk7XG4gICAgcmV0dXJuIHN0cmluZztcbiAgfTtcbn07XG5cbm1vZHVsZS5leHBvcnRzID0ge1xuICAvLyBgU3RyaW5nLnByb3RvdHlwZS57IHRyaW1MZWZ0LCB0cmltU3RhcnQgfWAgbWV0aG9kc1xuICAvLyBodHRwczovL3RjMzkuZXMvZWNtYTI2Mi8jc2VjLXN0cmluZy5wcm90b3R5cGUudHJpbXN0YXJ0XG4gIHN0YXJ0OiBjcmVhdGVNZXRob2QoMSksXG4gIC8vIGBTdHJpbmcucHJvdG90eXBlLnsgdHJpbVJpZ2h0LCB0cmltRW5kIH1gIG1ldGhvZHNcbiAgLy8gaHR0cHM6Ly90YzM5LmVzL2VjbWEyNjIvI3NlYy1zdHJpbmcucHJvdG90eXBlLnRyaW1lbmRcbiAgZW5kOiBjcmVhdGVNZXRob2QoMiksXG4gIC8vIGBTdHJpbmcucHJvdG90eXBlLnRyaW1gIG1ldGhvZFxuICAvLyBodHRwczovL3RjMzkuZXMvZWNtYTI2Mi8jc2VjLXN0cmluZy5wcm90b3R5cGUudHJpbVxuICB0cmltOiBjcmVhdGVNZXRob2QoMylcbn07XG4iLCIndXNlIHN0cmljdCc7XG52YXIgY2xhc3NvZiA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9jbGFzc29mJyk7XG5cbnZhciAkU3RyaW5nID0gU3RyaW5nO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIChhcmd1bWVudCkge1xuICBpZiAoY2xhc3NvZihhcmd1bWVudCkgPT09ICdTeW1ib2wnKSB0aHJvdyBuZXcgVHlwZUVycm9yKCdDYW5ub3QgY29udmVydCBhIFN5bWJvbCB2YWx1ZSB0byBhIHN0cmluZycpO1xuICByZXR1cm4gJFN0cmluZyhhcmd1bWVudCk7XG59O1xuIiwiJ3VzZSBzdHJpY3QnO1xuLy8gYSBzdHJpbmcgb2YgYWxsIHZhbGlkIHVuaWNvZGUgd2hpdGVzcGFjZXNcbm1vZHVsZS5leHBvcnRzID0gJ1xcdTAwMDlcXHUwMDBBXFx1MDAwQlxcdTAwMENcXHUwMDBEXFx1MDAyMFxcdTAwQTBcXHUxNjgwXFx1MjAwMFxcdTIwMDFcXHUyMDAyJyArXG4gICdcXHUyMDAzXFx1MjAwNFxcdTIwMDVcXHUyMDA2XFx1MjAwN1xcdTIwMDhcXHUyMDA5XFx1MjAwQVxcdTIwMkZcXHUyMDVGXFx1MzAwMFxcdTIwMjhcXHUyMDI5XFx1RkVGRic7XG4iLCIndXNlIHN0cmljdCc7XG52YXIgJCA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9leHBvcnQnKTtcbnZhciAkcGFyc2VGbG9hdCA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9udW1iZXItcGFyc2UtZmxvYXQnKTtcblxuLy8gYHBhcnNlRmxvYXRgIG1ldGhvZFxuLy8gaHR0cHM6Ly90YzM5LmVzL2VjbWEyNjIvI3NlYy1wYXJzZWZsb2F0LXN0cmluZ1xuJCh7IGdsb2JhbDogdHJ1ZSwgZm9yY2VkOiBwYXJzZUZsb2F0ICE9PSAkcGFyc2VGbG9hdCB9LCB7XG4gIHBhcnNlRmxvYXQ6ICRwYXJzZUZsb2F0XG59KTtcbiJdLCJuYW1lcyI6WyJCRU5JTl9DRU5URVJfTEFUIiwiQkVOSU5fQ0VOVEVSX0xORyIsIkJFTklOX0RFRkFVTFRfWk9PTSIsIkJFTklOX0JPVU5EUyIsInNvdXRoV2VzdCIsImxhdCIsImxuZyIsIm5vcnRoRWFzdCIsIkRFRkFVTFRfVElMRV9MQVlFUiIsIkRFRkFVTFRfQVRUUklCVVRJT04iLCJkZWZhdWx0TGF0IiwiZGVmYXVsdExuZyIsImRlZmF1bHRab29tIiwibWFwIiwibWFya2VyIiwiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIiwibWFwRWxlbWVudCIsImdldEVsZW1lbnRCeUlkIiwiaW5pdE1hcCIsInNldHVwTGlzdGVuZXJzIiwiaW5pdGlhbGl6ZUZyb21Gb3JtIiwiY29uc29sZSIsIndhcm4iLCJMIiwic2V0VmlldyIsInRpbGVMYXllciIsImF0dHJpYnV0aW9uIiwiYWRkVG8iLCJsYXRMbmciLCJib3VuZHMiLCJsYXRMbmdCb3VuZHMiLCJzZXRNYXhCb3VuZHMiLCJkcmFnZ2FibGUiLCJvbiIsImV2ZW50IiwidXBkYXRlQ29vcmRpbmF0ZXMiLCJnZXRMYXRMbmciLCJzZXRMYXRMbmciLCJsYXRsbmciLCJ2YWx1ZSIsIm1hbnVhbExhdElucHV0IiwibWFudWFsTG5nSW5wdXQiLCJwYW5UbyIsImdlb2xvY2F0ZUJ0biIsIm5hdmlnYXRvciIsImdlb2xvY2F0aW9uIiwiZ2V0Q3VycmVudFBvc2l0aW9uIiwicG9zaXRpb24iLCJjb29yZHMiLCJsYXRpdHVkZSIsImxvbmdpdHVkZSIsImFsZXJ0IiwiZXJyb3IiLCJhcHBseUNvb3Jkc0J0biIsInBhcnNlRmxvYXQiLCJpc05hTiIsImxhdEZpZWxkIiwibG5nRmllbGQiXSwic291cmNlUm9vdCI6IiJ9