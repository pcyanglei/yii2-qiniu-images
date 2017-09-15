"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var QiniuFileInput = function () {
    function QiniuFileInput(config) {
        _classCallCheck(this, QiniuFileInput);

        var defaultConfig = {
            max: 3,
            accept: "image/jpeg,image/gif,image/png",
            size: 204800
        };
        this.config = Object.assign(defaultConfig, config);
        this.init();
    }

    _createClass(QiniuFileInput, [{
        key: "init",
        value: function init() {
            new Vue({
                el: this.config.el,
                data: {
                    progress: 0,
                    errMessage: '',
                    config: this.config,
                    imageList: []
                },
                mounted: function mounted() {
                    if (this.config.imageList != null) {
                        var _iteratorNormalCompletion = true;
                        var _didIteratorError = false;
                        var _iteratorError = undefined;

                        try {
                            for (var _iterator = this.config.imageList[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                                var item = _step.value;

                                this.imageList.push({
                                    name: item
                                });
                            }
                        } catch (err) {
                            _didIteratorError = true;
                            _iteratorError = err;
                        } finally {
                            try {
                                if (!_iteratorNormalCompletion && _iterator.return) {
                                    _iterator.return();
                                }
                            } finally {
                                if (_didIteratorError) {
                                    throw _iteratorError;
                                }
                            }
                        }
                    }
                },

                methods: {
                    setErrMessage: function setErrMessage() {
                        var str = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';

                        this.errMessage = str;
                    },
                    upload: function upload(event) {
                        var _this = this;

                        var totalLength = event.target.files.length + this.imageList.length;
                        if (totalLength > this.config.max) {
                            this.setErrMessage("\u56FE\u7247\u8D85\u51FA\u6700\u5927\u5141\u8BB8\u4E0A\u4F20\u4E2A\u6570:" + this.config.max + ",\u8BF7\u5220\u9664\u4E00\u4E9B\u56FE\u7247!");
                            return;
                        }
                        var bigImages = '';
                        var _iteratorNormalCompletion2 = true;
                        var _didIteratorError2 = false;
                        var _iteratorError2 = undefined;

                        try {
                            for (var _iterator2 = event.target.files[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                                var item = _step2.value;

                                if (item.size > this.config.size) {
                                    bigImages += item.name + "  ";
                                    continue;
                                }
                                var data = new FormData();
                                data.append("file", item);
                                data.append("token", this.config.token);
                                data.append("key", QiniuFileInput.getFileKey(item));
                                QiniuFileInput.requestQiniu({
                                    url: this.config.url,
                                    data: data,
                                    error: function error(r) {
                                        _this.setErrMessage(r.message);
                                    },
                                    success: function success(res) {
                                        typeof _this.config.onsuccess === "function" && _this.config.onsuccess(res);
                                        _this.imageList.push({
                                            name: _this.config.cdnUrl + "/" + res.key
                                        });
                                    },
                                    progress: function progress(p) {
                                        if (p === 100) {
                                            setTimeout(function () {
                                                _this.progress = 0;
                                            }, 500);
                                        }
                                        _this.progress = p;
                                    }
                                });
                            }
                        } catch (err) {
                            _didIteratorError2 = true;
                            _iteratorError2 = err;
                        } finally {
                            try {
                                if (!_iteratorNormalCompletion2 && _iterator2.return) {
                                    _iterator2.return();
                                }
                            } finally {
                                if (_didIteratorError2) {
                                    throw _iteratorError2;
                                }
                            }
                        }

                        if (bigImages !== '') {
                            this.setErrMessage(bigImages + "\u8D85\u51FA\u6700\u5927\u9650\u5236" + parseInt(this.config.size / 1024) + "KB,\u5DF2\u5FFD\u7565\u4E0A\u4F20");
                        }
                    },
                    deleteImg: function deleteImg(index) {
                        typeof this.config.ondelete === "function" && this.config.ondelete(this.imageList[index]);
                        this.imageList.splice(index, 1);
                    }
                }
            });
        }
    }], [{
        key: "randomChar",
        value: function randomChar() {
            var len = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 5;

            var x = "0123456789qwertyuioplkjhgfdsazxcvbnm";
            var tmp = "";
            for (var i = 0; i < len; i++) {
                tmp += x.charAt(Math.ceil(Math.random() * 100000000) % x.length);
            }
            return tmp;
        }
    }, {
        key: "requestQiniu",
        value: function requestQiniu(obj) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", obj.url, true);
            xhr.upload.addEventListener("progress", function (evt) {
                var p = Math.floor(evt.loaded / evt.total * 100);
                typeof obj.progress === "function" && obj.progress(p);
            });
            xhr.onreadystatechange = function (response) {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200 && xhr.responseText !== '') {
                        var r = JSON.parse(xhr.responseText);
                        typeof obj.success === "function" && obj.success(r);
                    }
                    if (xhr.status !== 200) {
                        typeof obj.error === "function" && obj.error({
                            message: JSON.parse(xhr.responseText).error,
                            status: xhr.status
                        });
                    }
                }
            };
            xhr.send(obj.data);
        }
    }, {
        key: "getFileKey",
        value: function getFileKey(item) {
            var len = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 10;

            var oDate = new Date();
            return oDate.getFullYear() + "/" + (oDate.getMonth() + 1) + "/" + QiniuFileInput.randomChar(len) + "." + item.name.split('.').splice(-1);
        }
    }]);

    return QiniuFileInput;
}();
//# sourceMappingURL=qiniu-input.js.map
