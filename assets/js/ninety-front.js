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

eval("window.onload = function () {\n  // console.log( 'geojson', geojson );\n  // console.log( 'mapOptions', mapOptions );\n  //center: { lat: 38.5816, lng: -121.4944 } // Sacramento\n  var features = geojson.features,\n      markers = [];\n  var lMapOptions = {\n    center: L.latLng([38.5816, -121.4944]),\n    zoom: 10,\n    maxZoom: 18,\n    tapTolerance: 45\n  }; //* Create Map\n\n  var map = L.map('ninety-map', lMapOptions); //* Create Marker Cluster Group\n\n  var markerClusterGroup = L.markerClusterGroup(); //* Tile Layer Options\n\n  var tilelayerOptions = {\n    attribution: 'Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a>',\n    maxZoom: 18,\n    id: 'mapbox.streets',\n    accessToken: mapOptions.apiKey\n  }; //* Create Tile Layer\n\n  var tiles = L.tileLayer(mapOptions.tileServer, tilelayerOptions);\n  features.forEach(function (feature) {\n    var g = feature.geometry.coordinates,\n        p = feature.properties; //* CircleMarker Options\n\n    var circleMarkerOptions = {\n      title: p.counts,\n      riseOnHover: true,\n      radius: 8,\n      fillColor: '#ff7800',\n      color: '#000',\n      weight: 1,\n      opacity: 1,\n      fillOpacity: 0.8\n    }; //* Marker Options\n\n    var markerOptions = {\n      title: p.counts,\n      riseOnHover: true\n    }; //* Create popup content for Marker\n\n    var popupContent = \"<h4><a href=\\\"\".concat(p.link, \"\\\" alt=\\\"Meeging title\\\">\").concat(p.title, \"</a></h4>\");\n    popupContent += \"<p>\".concat(p.address, \"</p>\");\n    popupContent += \"<p>\".concat(p.description, \"</p>\");\n    popupContent += \"<p>\".concat(p.count, \"</p>\"); //* Create Circle Marker - Believe me, if markers aren't showing up don't waste time, just swap lat/lng!!!!!\n\n    var marker = L.circleMarker(L.latLng(g[1], g[0]), circleMarkerOptions); //* Create Marker\n    // const marker = L.marker( new L.latLng( g[ 1 ], g[ 0 ] ), markerOptions );\n    //* Bind tooltip to Marker\n\n    marker.bindTooltip(p.title); //* Bind popup to Marker\n\n    marker.bindPopup(popupContent); //* Add Marker to array for later\n\n    markers.push(marker);\n  }); //* Add Tile Layer to the Map\n\n  tiles.addTo(map); //* Add Markers to Cluster Group\n\n  markerClusterGroup.addLayers(markers); //* Add Cluster Group to Map\n\n  map.addLayer(markerClusterGroup);\n};\n\n//# sourceURL=webpack:///./src/front/js/front-index.js?");

/***/ })

/******/ });