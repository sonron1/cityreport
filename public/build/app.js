"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.for-each.js */ "./node_modules/core-js/modules/es.array.for-each.js");
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.promise.js */ "./node_modules/core-js/modules/es.promise.js");
/* harmony import */ var core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_promise_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/esnext.iterator.constructor.js */ "./node_modules/core-js/modules/esnext.iterator.constructor.js");
/* harmony import */ var core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_constructor_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/esnext.iterator.for-each.js */ "./node_modules/core-js/modules/esnext.iterator.for-each.js");
/* harmony import */ var core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_for_each_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/esnext.iterator.map.js */ "./node_modules/core-js/modules/esnext.iterator.map.js");
/* harmony import */ var core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_esnext_iterator_map_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_web_timers_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/web.timers.js */ "./node_modules/core-js/modules/web.timers.js");
/* harmony import */ var core_js_modules_web_timers_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_timers_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _styles_app_scss__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./styles/app.scss */ "./assets/styles/app.scss");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.esm.js");
/* harmony import */ var _js_leaflet_setup__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./js/leaflet-setup */ "./assets/js/leaflet-setup.js");












// Import de Bootstrap


// Import Leaflet


// Fonction pour mettre à jour les arrondissements
function updateArrondissements() {
  var villeSelect = document.getElementById('ville-select'); // Assurez-vous que l'ID correspond
  var arrondissementSelect = document.getElementById('arrondissement-select'); // Assurez-vous que l'ID correspond

  if (!villeSelect || !arrondissementSelect) return;
  var villeId = villeSelect.value;

  // Désactiver le select d'arrondissement pendant le chargement
  arrondissementSelect.disabled = true;
  if (villeId) {
    // Faire une requête AJAX pour obtenir les arrondissements de la ville sélectionnée
    fetch("/api/arrondissements-by-ville/".concat(villeId)).then(function (response) {
      return response.json();
    }).then(function (data) {
      // Vider le select d'arrondissement
      arrondissementSelect.innerHTML = '<option value="">Sélectionnez un arrondissement (optionnel)</option>';

      // Ajouter les nouveaux arrondissements
      data.forEach(function (arrondissement) {
        var option = document.createElement('option');
        option.value = arrondissement.id;
        option.textContent = arrondissement.nom;
        arrondissementSelect.appendChild(option);
      });

      // Réactiver le select d'arrondissement
      arrondissementSelect.disabled = false;
    });
  } else {
    // Si aucune ville n'est sélectionnée, vider le select d'arrondissement
    arrondissementSelect.innerHTML = '<option value="">Sélectionnez d\'abord une ville</option>';
    arrondissementSelect.disabled = true;
  }
}

// Ajouter un écouteur d'événement au select de ville
document.addEventListener('DOMContentLoaded', function () {
  var villeSelect = document.getElementById('ville-select');
  if (villeSelect) {
    villeSelect.addEventListener('change', updateArrondissements);
  }
});

// Activation des fonctionnalités Bootstrap
document.addEventListener('DOMContentLoaded', function () {
  // Activation des tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Activation des popovers
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Gestion des alertes avec fermeture automatique
  var alerts = document.querySelectorAll('.alert-auto-close');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      var closeButton = alert.querySelector('.btn-close');
      if (closeButton) {
        closeButton.click();
      }
    }, 5000);
  });

  //Arrondissement

  var villeSelect = document.querySelector('[name$="[ville]"]');
  var arrondissementSelect = document.querySelector('[name$="[arrondissement]"]');
  if (villeSelect && arrondissementSelect) {
    // Fonction pour mettre à jour les arrondissements en fonction de la ville sélectionnée
    var _updateArrondissements = function _updateArrondissements() {
      var villeId = villeSelect.value;

      // Désactiver le select d'arrondissement pendant le chargement
      arrondissementSelect.disabled = true;
      if (villeId) {
        // Faire une requête AJAX pour obtenir les arrondissements de la ville sélectionnée
        fetch("/api/arrondissements-by-ville/".concat(villeId)).then(function (response) {
          return response.json();
        }).then(function (data) {
          // Vider le select d'arrondissement
          arrondissementSelect.innerHTML = '<option value="">Sélectionnez un arrondissement (optionnel)</option>';

          // Ajouter les nouveaux arrondissements
          data.forEach(function (arrondissement) {
            var option = document.createElement('option');
            option.value = arrondissement.id;
            option.textContent = arrondissement.nom;
            arrondissementSelect.appendChild(option);
          });

          // Réactiver le select d'arrondissement
          arrondissementSelect.disabled = false;
        });
      } else {
        // Si aucune ville n'est sélectionnée, vider le select d'arrondissement
        arrondissementSelect.innerHTML = '<option value="">Sélectionnez d\'abord une ville</option>';
        arrondissementSelect.disabled = true;
      }
    };

    // Mettre à jour les arrondissements lors du chargement de la page
    _updateArrondissements();

    // Mettre à jour les arrondissements lorsque la ville change
    villeSelect.addEventListener('change', _updateArrondissements);
  }
});

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

console.log('App.js loaded successfully');

/***/ }),

/***/ "./assets/js/leaflet-setup.js":
/*!************************************!*\
  !*** ./assets/js/leaflet-setup.js ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var leaflet__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! leaflet */ "./node_modules/leaflet/dist/leaflet-src.js");
/* harmony import */ var leaflet__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(leaflet__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var leaflet_dist_leaflet_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! leaflet/dist/leaflet.css */ "./node_modules/leaflet/dist/leaflet.css");
// Import Leaflet depuis les node_modules



// Rendre Leaflet disponible globalement
window.L = (leaflet__WEBPACK_IMPORTED_MODULE_0___default());

// Exportation par défaut
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((leaflet__WEBPACK_IMPORTED_MODULE_0___default()));

/***/ }),

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_internals_an-instance_js-node_modules_core-js_internals_array-it-055933","vendors-node_modules_core-js_internals_dom-iterables_js-node_modules_core-js_internals_dom-to-aac983","vendors-node_modules_core-js_modules_es_array_map_js-node_modules_core-js_modules_esnext_iter-01c182","vendors-node_modules_leaflet_dist_leaflet-src_js","vendors-node_modules_bootstrap_dist_js_bootstrap_esm_js-node_modules_leaflet_dist_leaflet_css-e3453e","assets_styles_app_scss"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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TSxDQUFDRyxXQUFXLEdBQUdKLGNBQWMsQ0FBQ0ssR0FBRztZQUN2Q2pCLG9CQUFvQixDQUFDa0IsV0FBVyxDQUFDTCxNQUFNLENBQUM7VUFDNUMsQ0FBQyxDQUFDOztVQUVGO1VBQ0FiLG9CQUFvQixDQUFDRyxRQUFRLEdBQUcsS0FBSztRQUN6QyxDQUFDLENBQUM7TUFDVixDQUFDLE1BQU07UUFDSDtRQUNBSCxvQkFBb0IsQ0FBQ1UsU0FBUyxHQUFHLDJEQUEyRDtRQUM1RlYsb0JBQW9CLENBQUNHLFFBQVEsR0FBRyxJQUFJO01BQ3hDO0lBQ0osQ0FBQzs7SUFFRDtJQUNBUCxzQkFBcUIsQ0FBQyxDQUFDOztJQUV2QjtJQUNBQyxXQUFXLENBQUNzQixnQkFBZ0IsQ0FBQyxRQUFRLEVBQUV2QixzQkFBcUIsQ0FBQztFQUNqRTtBQUVKLENBQUMsQ0FBQzs7QUFFRjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUF5QyxPQUFPLENBQUNDLEdBQUcsQ0FBQyw0QkFBNEIsQ0FBQzs7Ozs7Ozs7Ozs7Ozs7Ozs7QUN2SXpDO0FBQ3dCO0FBQ1U7O0FBRWxDO0FBQ0FFLE1BQU0sQ0FBQ0QsQ0FBQyxHQUFHQSxnREFBQzs7QUFFWjtBQUNBLGlFQUFlQSxnREFBQzs7Ozs7Ozs7Ozs7QUNSaEIiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvYXBwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qcy9sZWFmbGV0LXNldHVwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zdHlsZXMvYXBwLnNjc3M/M2U4YSJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJy4vc3R5bGVzL2FwcC5zY3NzJztcblxuLy8gSW1wb3J0IGRlIEJvb3RzdHJhcFxuaW1wb3J0ICdib290c3RyYXAnO1xuXG4vLyBJbXBvcnQgTGVhZmxldFxuaW1wb3J0ICcuL2pzL2xlYWZsZXQtc2V0dXAnO1xuXG4vLyBGb25jdGlvbiBwb3VyIG1ldHRyZSDDoCBqb3VyIGxlcyBhcnJvbmRpc3NlbWVudHNcbmZ1bmN0aW9uIHVwZGF0ZUFycm9uZGlzc2VtZW50cygpIHtcbiAgICBjb25zdCB2aWxsZVNlbGVjdCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCd2aWxsZS1zZWxlY3QnKTsgLy8gQXNzdXJlei12b3VzIHF1ZSBsJ0lEIGNvcnJlc3BvbmRcbiAgICBjb25zdCBhcnJvbmRpc3NlbWVudFNlbGVjdCA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdhcnJvbmRpc3NlbWVudC1zZWxlY3QnKTsgLy8gQXNzdXJlei12b3VzIHF1ZSBsJ0lEIGNvcnJlc3BvbmRcbiAgICBcbiAgICBpZiAoIXZpbGxlU2VsZWN0IHx8ICFhcnJvbmRpc3NlbWVudFNlbGVjdCkgcmV0dXJuO1xuICAgIFxuICAgIGNvbnN0IHZpbGxlSWQgPSB2aWxsZVNlbGVjdC52YWx1ZTtcbiAgICBcbiAgICAvLyBEw6lzYWN0aXZlciBsZSBzZWxlY3QgZCdhcnJvbmRpc3NlbWVudCBwZW5kYW50IGxlIGNoYXJnZW1lbnRcbiAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5kaXNhYmxlZCA9IHRydWU7XG4gICAgXG4gICAgaWYgKHZpbGxlSWQpIHtcbiAgICAgICAgLy8gRmFpcmUgdW5lIHJlcXXDqnRlIEFKQVggcG91ciBvYnRlbmlyIGxlcyBhcnJvbmRpc3NlbWVudHMgZGUgbGEgdmlsbGUgc8OpbGVjdGlvbm7DqWVcbiAgICAgICAgZmV0Y2goYC9hcGkvYXJyb25kaXNzZW1lbnRzLWJ5LXZpbGxlLyR7dmlsbGVJZH1gKVxuICAgICAgICAgICAgLnRoZW4ocmVzcG9uc2UgPT4gcmVzcG9uc2UuanNvbigpKVxuICAgICAgICAgICAgLnRoZW4oZGF0YSA9PiB7XG4gICAgICAgICAgICAgICAgLy8gVmlkZXIgbGUgc2VsZWN0IGQnYXJyb25kaXNzZW1lbnRcbiAgICAgICAgICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5pbm5lckhUTUwgPSAnPG9wdGlvbiB2YWx1ZT1cIlwiPlPDqWxlY3Rpb25uZXogdW4gYXJyb25kaXNzZW1lbnQgKG9wdGlvbm5lbCk8L29wdGlvbj4nO1xuICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgIC8vIEFqb3V0ZXIgbGVzIG5vdXZlYXV4IGFycm9uZGlzc2VtZW50c1xuICAgICAgICAgICAgICAgIGRhdGEuZm9yRWFjaChhcnJvbmRpc3NlbWVudCA9PiB7XG4gICAgICAgICAgICAgICAgICAgIGNvbnN0IG9wdGlvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ29wdGlvbicpO1xuICAgICAgICAgICAgICAgICAgICBvcHRpb24udmFsdWUgPSBhcnJvbmRpc3NlbWVudC5pZDtcbiAgICAgICAgICAgICAgICAgICAgb3B0aW9uLnRleHRDb250ZW50ID0gYXJyb25kaXNzZW1lbnQubm9tO1xuICAgICAgICAgICAgICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5hcHBlbmRDaGlsZChvcHRpb24pO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIFxuICAgICAgICAgICAgICAgIC8vIFLDqWFjdGl2ZXIgbGUgc2VsZWN0IGQnYXJyb25kaXNzZW1lbnRcbiAgICAgICAgICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5kaXNhYmxlZCA9IGZhbHNlO1xuICAgICAgICAgICAgfSk7XG4gICAgfSBlbHNlIHtcbiAgICAgICAgLy8gU2kgYXVjdW5lIHZpbGxlIG4nZXN0IHPDqWxlY3Rpb25uw6llLCB2aWRlciBsZSBzZWxlY3QgZCdhcnJvbmRpc3NlbWVudFxuICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5pbm5lckhUTUwgPSAnPG9wdGlvbiB2YWx1ZT1cIlwiPlPDqWxlY3Rpb25uZXogZFxcJ2Fib3JkIHVuZSB2aWxsZTwvb3B0aW9uPic7XG4gICAgICAgIGFycm9uZGlzc2VtZW50U2VsZWN0LmRpc2FibGVkID0gdHJ1ZTtcbiAgICB9XG59XG5cbi8vIEFqb3V0ZXIgdW4gw6ljb3V0ZXVyIGQnw6l2w6luZW1lbnQgYXUgc2VsZWN0IGRlIHZpbGxlXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgZnVuY3Rpb24oKSB7XG4gICAgY29uc3QgdmlsbGVTZWxlY3QgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgndmlsbGUtc2VsZWN0Jyk7XG4gICAgaWYgKHZpbGxlU2VsZWN0KSB7XG4gICAgICAgIHZpbGxlU2VsZWN0LmFkZEV2ZW50TGlzdGVuZXIoJ2NoYW5nZScsIHVwZGF0ZUFycm9uZGlzc2VtZW50cyk7XG4gICAgfVxufSk7XG5cbi8vIEFjdGl2YXRpb24gZGVzIGZvbmN0aW9ubmFsaXTDqXMgQm9vdHN0cmFwXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgKCkgPT4ge1xuICAgIC8vIEFjdGl2YXRpb24gZGVzIHRvb2x0aXBzXG4gICAgY29uc3QgdG9vbHRpcFRyaWdnZXJMaXN0ID0gW10uc2xpY2UuY2FsbChkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCdbZGF0YS1icy10b2dnbGU9XCJ0b29sdGlwXCJdJykpO1xuICAgIHRvb2x0aXBUcmlnZ2VyTGlzdC5tYXAoZnVuY3Rpb24gKHRvb2x0aXBUcmlnZ2VyRWwpIHtcbiAgICAgICAgcmV0dXJuIG5ldyBib290c3RyYXAuVG9vbHRpcCh0b29sdGlwVHJpZ2dlckVsKTtcbiAgICB9KTtcbiAgICBcbiAgICAvLyBBY3RpdmF0aW9uIGRlcyBwb3BvdmVyc1xuICAgIGNvbnN0IHBvcG92ZXJUcmlnZ2VyTGlzdCA9IFtdLnNsaWNlLmNhbGwoZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnW2RhdGEtYnMtdG9nZ2xlPVwicG9wb3ZlclwiXScpKTtcbiAgICBwb3BvdmVyVHJpZ2dlckxpc3QubWFwKGZ1bmN0aW9uIChwb3BvdmVyVHJpZ2dlckVsKSB7XG4gICAgICAgIHJldHVybiBuZXcgYm9vdHN0cmFwLlBvcG92ZXIocG9wb3ZlclRyaWdnZXJFbCk7XG4gICAgfSk7XG4gICAgXG4gICAgLy8gR2VzdGlvbiBkZXMgYWxlcnRlcyBhdmVjIGZlcm1ldHVyZSBhdXRvbWF0aXF1ZVxuICAgIGNvbnN0IGFsZXJ0cyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5hbGVydC1hdXRvLWNsb3NlJyk7XG4gICAgYWxlcnRzLmZvckVhY2goYWxlcnQgPT4ge1xuICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgIGNvbnN0IGNsb3NlQnV0dG9uID0gYWxlcnQucXVlcnlTZWxlY3RvcignLmJ0bi1jbG9zZScpO1xuICAgICAgICAgICAgaWYgKGNsb3NlQnV0dG9uKSB7XG4gICAgICAgICAgICAgICAgY2xvc2VCdXR0b24uY2xpY2soKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSwgNTAwMCk7XG4gICAgfSk7XG5cblxuICAgIC8vQXJyb25kaXNzZW1lbnRcblxuICAgIGNvbnN0IHZpbGxlU2VsZWN0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignW25hbWUkPVwiW3ZpbGxlXVwiXScpO1xuICAgIGNvbnN0IGFycm9uZGlzc2VtZW50U2VsZWN0ID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvcignW25hbWUkPVwiW2Fycm9uZGlzc2VtZW50XVwiXScpO1xuXG4gICAgaWYgKHZpbGxlU2VsZWN0ICYmIGFycm9uZGlzc2VtZW50U2VsZWN0KSB7XG4gICAgICAgIC8vIEZvbmN0aW9uIHBvdXIgbWV0dHJlIMOgIGpvdXIgbGVzIGFycm9uZGlzc2VtZW50cyBlbiBmb25jdGlvbiBkZSBsYSB2aWxsZSBzw6lsZWN0aW9ubsOpZVxuICAgICAgICBjb25zdCB1cGRhdGVBcnJvbmRpc3NlbWVudHMgPSBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIGNvbnN0IHZpbGxlSWQgPSB2aWxsZVNlbGVjdC52YWx1ZTtcblxuICAgICAgICAgICAgLy8gRMOpc2FjdGl2ZXIgbGUgc2VsZWN0IGQnYXJyb25kaXNzZW1lbnQgcGVuZGFudCBsZSBjaGFyZ2VtZW50XG4gICAgICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5kaXNhYmxlZCA9IHRydWU7XG5cbiAgICAgICAgICAgIGlmICh2aWxsZUlkKSB7XG4gICAgICAgICAgICAgICAgLy8gRmFpcmUgdW5lIHJlcXXDqnRlIEFKQVggcG91ciBvYnRlbmlyIGxlcyBhcnJvbmRpc3NlbWVudHMgZGUgbGEgdmlsbGUgc8OpbGVjdGlvbm7DqWVcbiAgICAgICAgICAgICAgICBmZXRjaChgL2FwaS9hcnJvbmRpc3NlbWVudHMtYnktdmlsbGUvJHt2aWxsZUlkfWApXG4gICAgICAgICAgICAgICAgICAgIC50aGVuKHJlc3BvbnNlID0+IHJlc3BvbnNlLmpzb24oKSlcbiAgICAgICAgICAgICAgICAgICAgLnRoZW4oZGF0YSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBWaWRlciBsZSBzZWxlY3QgZCdhcnJvbmRpc3NlbWVudFxuICAgICAgICAgICAgICAgICAgICAgICAgYXJyb25kaXNzZW1lbnRTZWxlY3QuaW5uZXJIVE1MID0gJzxvcHRpb24gdmFsdWU9XCJcIj5Tw6lsZWN0aW9ubmV6IHVuIGFycm9uZGlzc2VtZW50IChvcHRpb25uZWwpPC9vcHRpb24+JztcblxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gQWpvdXRlciBsZXMgbm91dmVhdXggYXJyb25kaXNzZW1lbnRzXG4gICAgICAgICAgICAgICAgICAgICAgICBkYXRhLmZvckVhY2goYXJyb25kaXNzZW1lbnQgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnN0IG9wdGlvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ29wdGlvbicpO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbi52YWx1ZSA9IGFycm9uZGlzc2VtZW50LmlkO1xuICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbi50ZXh0Q29udGVudCA9IGFycm9uZGlzc2VtZW50Lm5vbTtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBhcnJvbmRpc3NlbWVudFNlbGVjdC5hcHBlbmRDaGlsZChvcHRpb24pO1xuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG5cbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIFLDqWFjdGl2ZXIgbGUgc2VsZWN0IGQnYXJyb25kaXNzZW1lbnRcbiAgICAgICAgICAgICAgICAgICAgICAgIGFycm9uZGlzc2VtZW50U2VsZWN0LmRpc2FibGVkID0gZmFsc2U7XG4gICAgICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICAvLyBTaSBhdWN1bmUgdmlsbGUgbidlc3Qgc8OpbGVjdGlvbm7DqWUsIHZpZGVyIGxlIHNlbGVjdCBkJ2Fycm9uZGlzc2VtZW50XG4gICAgICAgICAgICAgICAgYXJyb25kaXNzZW1lbnRTZWxlY3QuaW5uZXJIVE1MID0gJzxvcHRpb24gdmFsdWU9XCJcIj5Tw6lsZWN0aW9ubmV6IGRcXCdhYm9yZCB1bmUgdmlsbGU8L29wdGlvbj4nO1xuICAgICAgICAgICAgICAgIGFycm9uZGlzc2VtZW50U2VsZWN0LmRpc2FibGVkID0gdHJ1ZTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfTtcblxuICAgICAgICAvLyBNZXR0cmUgw6Agam91ciBsZXMgYXJyb25kaXNzZW1lbnRzIGxvcnMgZHUgY2hhcmdlbWVudCBkZSBsYSBwYWdlXG4gICAgICAgIHVwZGF0ZUFycm9uZGlzc2VtZW50cygpO1xuXG4gICAgICAgIC8vIE1ldHRyZSDDoCBqb3VyIGxlcyBhcnJvbmRpc3NlbWVudHMgbG9yc3F1ZSBsYSB2aWxsZSBjaGFuZ2VcbiAgICAgICAgdmlsbGVTZWxlY3QuYWRkRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgdXBkYXRlQXJyb25kaXNzZW1lbnRzKTtcbiAgICB9XG5cbn0pO1xuXG4vKlxuICogV2VsY29tZSB0byB5b3VyIGFwcCdzIG1haW4gSmF2YVNjcmlwdCBmaWxlIVxuICpcbiAqIFdlIHJlY29tbWVuZCBpbmNsdWRpbmcgdGhlIGJ1aWx0IHZlcnNpb24gb2YgdGhpcyBKYXZhU2NyaXB0IGZpbGVcbiAqIChhbmQgaXRzIENTUyBmaWxlKSBpbiB5b3VyIGJhc2UgbGF5b3V0IChiYXNlLmh0bWwudHdpZykuXG4gKi9cblxuY29uc29sZS5sb2coJ0FwcC5qcyBsb2FkZWQgc3VjY2Vzc2Z1bGx5Jyk7IiwiLy8gSW1wb3J0IExlYWZsZXQgZGVwdWlzIGxlcyBub2RlX21vZHVsZXNcclxuaW1wb3J0IEwgZnJvbSAnbGVhZmxldCc7XHJcbmltcG9ydCAnbGVhZmxldC9kaXN0L2xlYWZsZXQuY3NzJztcclxuXHJcbi8vIFJlbmRyZSBMZWFmbGV0IGRpc3BvbmlibGUgZ2xvYmFsZW1lbnRcclxud2luZG93LkwgPSBMO1xyXG5cclxuLy8gRXhwb3J0YXRpb24gcGFyIGTDqWZhdXRcclxuZXhwb3J0IGRlZmF1bHQgTDsiLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiXSwibmFtZXMiOlsidXBkYXRlQXJyb25kaXNzZW1lbnRzIiwidmlsbGVTZWxlY3QiLCJkb2N1bWVudCIsImdldEVsZW1lbnRCeUlkIiwiYXJyb25kaXNzZW1lbnRTZWxlY3QiLCJ2aWxsZUlkIiwidmFsdWUiLCJkaXNhYmxlZCIsImZldGNoIiwiY29uY2F0IiwidGhlbiIsInJlc3BvbnNlIiwianNvbiIsImRhdGEiLCJpbm5lckhUTUwiLCJmb3JFYWNoIiwiYXJyb25kaXNzZW1lbnQiLCJvcHRpb24iLCJjcmVhdGVFbGVtZW50IiwiaWQiLCJ0ZXh0Q29udGVudCIsIm5vbSIsImFwcGVuZENoaWxkIiwiYWRkRXZlbnRMaXN0ZW5lciIsInRvb2x0aXBUcmlnZ2VyTGlzdCIsInNsaWNlIiwiY2FsbCIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJtYXAiLCJ0b29sdGlwVHJpZ2dlckVsIiwiYm9vdHN0cmFwIiwiVG9vbHRpcCIsInBvcG92ZXJUcmlnZ2VyTGlzdCIsInBvcG92ZXJUcmlnZ2VyRWwiLCJQb3BvdmVyIiwiYWxlcnRzIiwiYWxlcnQiLCJzZXRUaW1lb3V0IiwiY2xvc2VCdXR0b24iLCJxdWVyeVNlbGVjdG9yIiwiY2xpY2siLCJjb25zb2xlIiwibG9nIiwiTCIsIndpbmRvdyJdLCJzb3VyY2VSb290IjoiIn0=