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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/backyard/sigarang/goods/goods/import.js":
/*!**************************************************************!*\
  !*** ./resources/js/backyard/sigarang/goods/goods/import.js ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var _data = window["_goodsImportData"];
document.addEventListener('DOMContentLoaded', function (event) {
  methods.initFileUpload();
});
var methods = {
  initFileUpload: function initFileUpload() {
    var fileAdd = function fileAdd(e, data) {
      data.loadingTmp = parseInt(Math.random() * 1000000);
      var progressBar = "<div class=\"progress progress-mini progress-tiny progress-".concat(data.loadingTmp, "\"><div class=\"progress-bar progress-bar-success\" style=\"\"></div></div>");
      data.loadingId = alertify.warning("".concat(progressBar, " Mengupload ").concat(data.files[0].name, " <br />Harap tunggu hingga proses selesai"), 0);
      data.submit();
    };

    var fileProgress = function fileProgress(e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $('.progress.progress-' + data.loadingTmp + ' .progress-bar').css('width', progress + '%');
    };

    var fileDone = function fileDone(e, data) {
      for (var i in data.result) {
        var v = data.result[i];

        if (v.error) {
          (function () {
            var alert = alertify.error("".concat(v.file, "</br>").concat(v.error), 0);
            $(alert).on('click', function () {
              return alert.dismiss();
            });
          })();
        } else {
          (function () {
            var alert = alertify.success("".concat(v.file, "</br>").concat(v.message), 0);
            $(alert).on('click', function () {
              return alert.dismiss();
            });
          })();
        }

        data.loadingId.dismiss();
        document.getElementById('file-upload').value = null;
      }
    };

    $('#file-upload').fileupload({
      url: _data.routeGoodsUpload,
      dataType: 'json',
      add: fileAdd,
      done: fileDone,
      progress: fileProgress,
      fail: function fail(e, data) {
        alertify.error('Proses Upload Gagal');
      }
    });
  }
};

/***/ }),

/***/ 6:
/*!********************************************************************!*\
  !*** multi ./resources/js/backyard/sigarang/goods/goods/import.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /var/www/html/laravel6/resources/js/backyard/sigarang/goods/goods/import.js */"./resources/js/backyard/sigarang/goods/goods/import.js");


/***/ })

/******/ });