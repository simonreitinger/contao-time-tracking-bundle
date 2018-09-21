'use strict';

axios.defaults.headers.common['Content-Type'] = 'application/json';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

class Timer {
    constructor(timer) {
        this.id = +timer.id;
        this.description = timer.description;

        // convert the times to js date objects
        let convertedTimes = [];
        for (let time of timer.times) {
            convertedTimes.push(new Time(time));
        }
        this.times = convertedTimes;
        this.running = timer.running;
        this.submitRoute = timer.submitRoute;
    }

    current() {
        return this.times[this.times.length - 1];
    }

    pause(endTime) {
        this.running = false;
        this.times[this.times.length - 1].end = new Date(+endTime * 1000);
    }

    continue(time) {
        this.times.push(new Time(time));
        this.running = true;
    }
}

class Time {
    constructor(time) {
        this.id = time.id;
        this.start = new Date(+time.start * 1000);
        this.end = time.end ? new Date(+time.end * 1000) : null;
    }
}

const CancelToken = axios.CancelToken;
const source = CancelToken.source();
let timeout = null;

const tracking = new Vue({
    el: '#stopwatch',
    delimiters: ['[[', ']]'],
    data: {
        trackingList: [],
        description: '',
        api: {
            get(url, data, callback) {
                axios({
                    method: 'GET',
                    url: url,
                    params: data,
                    cancelToken: source.token
                })
                    .then(callback)
                    .catch(err => console.error(err));
            },
            post(url, data, callback) {
                axios({
                    method: 'POST',
                    url: url,
                    params: data,
                    cancelToken: source.token
                })
                    .then(callback)
                    .catch(err => console.error(err));
            },
            put(url, data, callback) {
                axios({
                    method: 'PUT',
                    url: url,
                    params: data,
                    cancelToken: source.token
                })
                    .then(callback)
                    .catch(err => console.error(err));
            },
            delete(url, data, callback) {
                axios({
                    method: 'DELETE',
                    url: url,
                    params: data,
                    cancelToken: source.token
                })
                    .then(callback)
                    .catch(err => console.error(err));
            }
        }
    },
    mounted() {
        this.loadData();
    },
    methods: {
        loadData() {
            this.api.get(this.$el.dataset.load, {}, res => {
                for (let timer of res.data.trackingList) {
                    this.trackingList.unshift(new Timer(timer));
                }
            });
        },
        newTracking() {
            this.api.post(this.$el.dataset.new, {description: this.description}, res => {
                for (let timer of this.trackingList) {
                    timer.pause(res.data.time);
                }
                this.trackingList.unshift(new Timer(res.data.timer));
                this.description = '';
            });

        },
        pauseTracking(timer) {
            this.api.post(this.$el.dataset.pause, {id: timer.id}, res => {
                timer.pause(res.data.end);
            });
        },
        continueTracking(timer) {
            this.api.post(this.$el.dataset.continue, {id: timer.id}, res => {
                for (let timer of this.trackingList) {
                    timer.pause(res.data.time);
                }
                timer.continue(res.data.timer);
            });
        },
        deleteTracking(timer, timerIndex) {
            this.api.delete(this.$el.dataset.deleteTracking, {id: timer.id}, res => {
                this.trackingList.splice(timerIndex, 1);
            });
        },
        deleteTime(timer, timerIndex, timeIndex) {
            this.api.delete(this.$el.dataset.deleteTime, {id: timer.times[timeIndex].id}, res => {
                timer.times.splice(timeIndex, 1);
                if (!timer.times.length) {
                    this.deleteTracking(timer, timerIndex);
                }
            });

        },
        updateDescription(timer) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                this.api.post(this.$el.dataset.description, {id: timer.id, description: timer.description}, res => {});
            }, 800);
        }
    }
});