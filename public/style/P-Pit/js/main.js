(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var DynamicForm = function () {
  function DynamicForm() {
    _classCallCheck(this, DynamicForm);

    this.$button = $('.input_moreButton');
    this.clone = 0;
    this.listener();
  }

  _createClass(DynamicForm, [{
    key: 'listener',
    value: function listener() {
      var _this = this;

      this.$button.each(function (i, e) {
        var $e = $(e);
        $e.on('click', function (target) {
          _this.clone++;
          _this.cloner(target);
        });
      });
    }
  }, {
    key: 'cloner',
    value: function cloner(target) {
      var _this2 = this;

      var $target = $(target.target);
      var $container = $target.parent().parent();
      var $input = $container.find('.input_repeater');
      var $clone = $input.slice(0, 1).clone();
      $clone.children().children().each(function (i, e) {
        var $e = $(e);
        if ($e.attr('id')) {
          $e.attr('id', $e.attr('id') + _this2.clone);
          $e.val('');
        }
        if ($e.attr('label')) {
          $e.attr('label', $e.attr('label') + _this2.clone);
        }
        if ($e.attr('name')) {
          $e.attr('name', $e.attr('name') + _this2.clone);
        }
        if ($e.attr('for')) {
          $e.attr('for', $e.attr('for') + _this2.clone);
        }
      });
      $clone = $clone.append('<div class="delete"></div>');
      $input.last().after($clone);
      this.deleter();
    }
  }, {
    key: 'deleter',
    value: function deleter() {
      $('.delete').each(function (i, e) {
        var $e = $(e);
        $e.on('click', function () {
          $e.parent().remove();
        });
      });
    }
  }]);

  return DynamicForm;
}();

exports.default = DynamicForm;

},{}],2:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var File = function () {
  function File() {
    _classCallCheck(this, File);

    if ($('.state').hasClass('state4')) {
      this.initSelector();
    }
  }

  _createClass(File, [{
    key: 'initSelector',
    value: function initSelector() {
      var _this = this;

      this.$input = $('.input_fileHidden');
      this.$logo = $('.input_fileLogo');
      this.$label = $('.input_fileText');

      this.$input.each(function (i, e) {
        var $e = $(e);
        $e.on('change', function () {

          var filesName = '';

          for (var a = 0; a < $e.prop('files').length; a++) {
            if (a != 0) {
              filesName += ", ";
            }
            filesName += $e.prop('files')[a].name;
          }

          $(_this.$label[i]).text(filesName).addClass('input_fileText-valid');
          $(_this.$logo[i]).addClass('input_fileLogo-valid');
        });
      });
    }
  }]);

  return File;
}();

exports.default = File;

},{}],3:[function(require,module,exports){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Validator = function () {
  function Validator() {
    _classCallCheck(this, Validator);

    this.$input = $('input');
    this.interceptor();
  }

  _createClass(Validator, [{
    key: 'isEmpty',
    value: function isEmpty(str) {
      if (str != '') {

        return true;
      } else {

        return false;
      }
    }
  }, {
    key: 'isString',
    value: function isString(str) {
      var regexp = /[^0-9]/g;

      return regexp.test(str);
    }
  }, {
    key: 'isEmail',
    value: function isEmail(str) {
      var regexp = /\S+@\S+\.\S+/;

      return regexp.test(str);
    }
  }, {
    key: 'isNumber',
    value: function isNumber(str) {
      var regexp = /[^a-zA-Z]/;

      return regexp.test(str);
    }
  }, {
    key: 'isValid',
    value: function isValid() {
      var _this = this;

      this.error = [];

      this.$input.each(function (i, e) {
        var $e = $(e);
        var val = $e.val();
        var dataTest = $e.attr('data-test');
        var error = '';
        if (dataTest) {
          var test = dataTest.split(' ');
          for (var _i in test) {
            if (test[_i] == 'string') {
              if (!_this.isString(val)) {
                error = 'Format incorrect (caractère uniquement)';
              }
            } else if (test[_i] == 'number') {
              if (!_this.isNumber(val)) {
                error = 'Format incorrect (nombre uniquement)';
              }
            } else if (test[_i] == 'email') {
              if (!_this.isEmail(val)) {
                error = 'Format incorrect (email uniquement)';
              }
            } else if (test[_i] == 'require') {
              if (!_this.isEmpty(val)) {
                error = 'Ce champ est requis';
              }
            }
          }
        }
        if (error != '') {
          _this.error.push({
            'input': $e.attr('id'),
            'error': error
          });
        } else {
          _this.error.push({
            'input': $e.attr('id'),
            'error': false
          });
        }
      });

      this.showError(this.error);

      for (var i in this.error) {
        if (this.error[i].error) {

          return false;
        }
      }

      return true;
    }
  }, {
    key: 'showError',
    value: function showError() {
      $('.error').remove();
      $('.input_file').removeClass('errorBorder');
      for (var i in this.error) {
        $('#' + this.error[i].input).removeClass('errorBorder');
        if (this.error[i].error) {
          if ($('#' + this.error[i].input).attr('type') == 'file') {
            $('.input_file[for=' + this.error[i].input + ']').addClass('errorBorder');
          } else {
            $('#' + this.error[i].input).addClass('errorBorder');
          }
          $('#' + this.error[i].input).after('<div class="error">' + this.error[i].error + '</div>');
        }
      }
    }
  }, {
    key: 'refreshInput',
    value: function refreshInput() {
      var _this2 = this;

      this.$input.on('change', function () {
        _this2.isValid();
      });
    }
  }, {
    key: 'interceptor',
    value: function interceptor() {
      var _this3 = this;

      $('.state_button').on('click', function (e) {
        _this3.$input = $('input');
        if (!_this3.isValid()) {
          e.preventDefault();
          _this3.refreshInput();
        }
      });
    }
  }]);

  return Validator;
}();

exports.default = Validator;

},{}],4:[function(require,module,exports){
'use strict';

var _File = require('./component/File.js');

var _File2 = _interopRequireDefault(_File);

var _Validator = require('./component/Validator.js');

var _Validator2 = _interopRequireDefault(_Validator);

var _DynamicForm = require('./component/DynamicForm.js');

var _DynamicForm2 = _interopRequireDefault(_DynamicForm);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

$(function () {
  new _File2.default();
  new _Validator2.default();
  new _DynamicForm2.default();
});

},{"./component/DynamicForm.js":1,"./component/File.js":2,"./component/Validator.js":3}]},{},[4])

//# sourceMappingURL=main.js.map
