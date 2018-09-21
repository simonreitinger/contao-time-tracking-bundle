'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

var Timer = function () {
    function Timer(timer) {
        _classCallCheck(this, Timer);

        this.id = +timer.id;
        this.description = timer.description;

        // convert the times to js date objects
        var convertedTimes = [];
        var _iteratorNormalCompletion = true;
        var _didIteratorError = false;
        var _iteratorError = undefined;

        try {
            for (var _iterator = timer.times[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                var time = _step.value;

                convertedTimes.push(new Time(time));
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

        this.times = convertedTimes;
        this.running = timer.running;
        this.submitRoute = timer.submitRoute;
    }

    _createClass(Timer, [{
        key: 'current',
        value: function current() {
            return this.times[this.times.length - 1];
        }
    }, {
        key: 'pause',
        value: function pause(endTime) {
            this.running = false;
            this.times[this.times.length - 1].end = new Date(+endTime * 1000);
        }
    }, {
        key: 'continue',
        value: function _continue(time) {
            this.times.push(new Time(time));
            this.running = true;
        }
    }]);

    return Timer;
}();

var Time = function Time(time) {
    _classCallCheck(this, Time);

    this.id = time.id;
    this.start = new Date(+time.start * 1000);
    this.end = time.end ? new Date(+time.end * 1000) : null;
};

var CancelToken = axios.CancelToken;
var source = CancelToken.source();
var timeout = null;

var tracking = new Vue({
    el: '#stopwatch',
    delimiters: ['[[', ']]'],
    data: {
        trackingList: [],
        description: '',
        api: {
            get: function get(url, data, callback) {
                axios({
                    method: 'GET',
                    url: url,
                    params: data,
                    cancelToken: source.token
                }).then(callback).catch(function (err) {
                    return console.error(err);
                });
            },
            post: function post(url, data, callback) {
                axios({
                    method: 'POST',
                    url: url,
                    params: data,
                    cancelToken: source.token
                }).then(callback).catch(function (err) {
                    return console.error(err);
                });
            },
            put: function put(url, data, callback) {
                axios({
                    method: 'PUT',
                    url: url,
                    params: data,
                    cancelToken: source.token
                }).then(callback).catch(function (err) {
                    return console.error(err);
                });
            },
            delete: function _delete(url, data, callback) {
                axios({
                    method: 'DELETE',
                    url: url,
                    params: data,
                    cancelToken: source.token
                }).then(callback).catch(function (err) {
                    return console.error(err);
                });
            }
        }
    },
    mounted: function mounted() {
        this.loadData();
    },

    methods: {
        loadData: function loadData() {
            var _this = this;

            this.api.get(this.$el.dataset.load, {}, function (res) {
                var _iteratorNormalCompletion2 = true;
                var _didIteratorError2 = false;
                var _iteratorError2 = undefined;

                try {
                    for (var _iterator2 = res.data.trackingList[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
                        var timer = _step2.value;

                        _this.trackingList.unshift(new Timer(timer));
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
            });
        },
        newTracking: function newTracking() {
            var _this2 = this;

            this.api.post(this.$el.dataset.new, { description: this.description }, function (res) {
                var _iteratorNormalCompletion3 = true;
                var _didIteratorError3 = false;
                var _iteratorError3 = undefined;

                try {
                    for (var _iterator3 = _this2.trackingList[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true) {
                        var timer = _step3.value;

                        timer.pause(res.data.time);
                    }
                } catch (err) {
                    _didIteratorError3 = true;
                    _iteratorError3 = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion3 && _iterator3.return) {
                            _iterator3.return();
                        }
                    } finally {
                        if (_didIteratorError3) {
                            throw _iteratorError3;
                        }
                    }
                }

                _this2.trackingList.unshift(new Timer(res.data.timer));
                _this2.description = '';
            });
        },
        pauseTracking: function pauseTracking(timer) {
            this.api.post(this.$el.dataset.pause, { id: timer.id }, function (res) {
                timer.pause(res.data.end);
            });
        },
        continueTracking: function continueTracking(timer) {
            var _this3 = this;

            this.api.post(this.$el.dataset.continue, { id: timer.id }, function (res) {
                var _iteratorNormalCompletion4 = true;
                var _didIteratorError4 = false;
                var _iteratorError4 = undefined;

                try {
                    for (var _iterator4 = _this3.trackingList[Symbol.iterator](), _step4; !(_iteratorNormalCompletion4 = (_step4 = _iterator4.next()).done); _iteratorNormalCompletion4 = true) {
                        var _timer = _step4.value;

                        _timer.pause(res.data.time);
                    }
                } catch (err) {
                    _didIteratorError4 = true;
                    _iteratorError4 = err;
                } finally {
                    try {
                        if (!_iteratorNormalCompletion4 && _iterator4.return) {
                            _iterator4.return();
                        }
                    } finally {
                        if (_didIteratorError4) {
                            throw _iteratorError4;
                        }
                    }
                }

                timer.continue(res.data.timer);
            });
        },
        deleteTracking: function deleteTracking(timer, timerIndex) {
            var _this4 = this;

            this.api.delete(this.$el.dataset.deleteTracking, { id: timer.id }, function (res) {
                _this4.trackingList.splice(timerIndex, 1);
            });
        },
        deleteTime: function deleteTime(timer, timerIndex, timeIndex) {
            var _this5 = this;

            this.api.delete(this.$el.dataset.deleteTime, { id: timer.times[timeIndex].id }, function (res) {
                timer.times.splice(timeIndex, 1);
                if (!timer.times.length) {
                    _this5.deleteTracking(timer, timerIndex);
                }
            });
        },
        updateDescription: function updateDescription(timer) {
            var _this6 = this;

            clearTimeout(timeout);
            timeout = setTimeout(function () {
                _this6.api.post(_this6.$el.dataset.description, { id: timer.id, description: timer.description }, function (res) {});
            }, 800);
        }
    }
});