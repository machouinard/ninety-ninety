!function(e){var r={};function t(n){if(r[n])return r[n].exports;var o=r[n]={i:n,l:!1,exports:{}};return e[n].call(o.exports,o,o.exports,t),o.l=!0,o.exports}t.m=e,t.c=r,t.d=function(e,r,n){t.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:n})},t.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t.t=function(e,r){if(1&r&&(e=t(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(t.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var o in e)t.d(n,o,function(r){return e[r]}.bind(null,o));return n},t.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(r,"a",r),r},t.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},t.p="",t(t.s=2)}({2:function(e,r){jQuery(document).ready((function(e){e(".ninety-color-field").wpColorPicker(),e(".ninety-datepicker").datepicker(),e(".pdf-clear").click((function(e){e.target.previousSibling.value=""}));var r=document.querySelectorAll("input.ninety-danger");Object.keys(r).forEach((function(e){var t=r[e].closest("tr");t.style.backgroundColor="rgba(204, 0, 0,.25)";var n=t.querySelector("th");n.style.backgroundColor="rgb(204, 0, 0)",n.style.color="rgb( 255, 255, 255 )",n.style.padding="5px"}));var t=document.querySelectorAll("input.ninety-config");Object.keys(t).forEach((function(e){var r=t[e].closest("tr");r.style.backgroundColor="rgba(73,183,78,.25)";var n=r.querySelector("th");n.style.backgroundColor="rgb(73,183,78)",n.style.color="rgb( 255, 255, 255 )",n.style.padding="5px"}))}))}});