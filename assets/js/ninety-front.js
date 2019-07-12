/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/front/js/front-index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/front/js/front-index.js":
/*!*************************************!*\
  !*** ./src/front/js/front-index.js ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("console.log('geojson', geojson);\nconsole.log('mapOptions', mapOptions);\nvar geojsonMarkerOptions = {\n  radius: 8,\n  fillColor: '#ff7800',\n  color: '#000',\n  weight: 1,\n  opacity: 1,\n  fillOpacity: 0.8\n}; //center: { lat: 38.5816, lng: -121.4944 } // Sacramento\n\nvar map = L.map('ninety-map').setView([38.5816, -121.4944], 10);\nL.tileLayer(mapOptions.tileServer, {\n  attribution: 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a>',\n  maxZoom: 18,\n  id: 'mapbox.streets',\n  accessToken: mapOptions.apiKey\n}).addTo(map);\n\nfunction onEachFeature(feature, layer) {\n  var popupContent = '<h3><a href=\"' + feature.properties.link + '\" >' + feature.properties.title + '</a></h3>';\n  popupContent += '<p>' + feature.properties.address + '</p>';\n  popupContent += '<p>' + feature.properties.count + '</p>';\n  popupContent += '<p>' + feature.properties.description + '</p>';\n  layer.bindPopup(popupContent);\n}\n\nL.geoJSON(geojson.features, {\n  onEachFeature: onEachFeature,\n  pointToLayer: function pointToLayer(feature, latlng) {\n    return L.circleMarker(latlng, geojsonMarkerOptions);\n  }\n}).addTo(map);\n\n//# sourceURL=webpack:///./src/front/js/front-index.js?");

/***/ })

/******/ });