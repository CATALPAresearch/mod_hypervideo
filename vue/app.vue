<template>
    <div>
        <div class="hypervideo">
            <div class="player-container" style="width:100%; max-width:800px; margin-top:30px; height:auto;">
                <vue-core-video-player :src="url" 
                    @play="start" 
                    @pause="stop"
                    @loadeddata="canplay" 
                    @timeupdate="timeupdate"
                    @seeked="seeked" 
                    @seeking="seeking" 
                    @ended="ended" 
                    @abort="stop">
                </vue-core-video-player>
            </div>
            <div v-if="overlayVisible" class="overlay m-3 p-3">
                <div class="block mb-3">
                    <div class="question mb-1">
                        Wie beurteilen Sie das Video, das Sie gerade gesehen haben?
                        <i @click="q1='up'" :style="q1=='up' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-up ml-3 buttons"></i>
                        <i @click="q1='middle'" :style="q1=='middle' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-up thumbs-horizontal ml-3 buttons"></i>    
                        <i @click="q1='down'" :style="q1=='down' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-down ml-3 buttons"></i>
                    </div>
                </div>
                <div class="block mb-3">
                    <div class="question mb-1">
                        Was nehmen Sie vom gerade bearbeiteten Kursmaterial für sich mit?
                        Bitte nennen Sie ein Stichwort oder beschreiben kurz, was für Sie der wichtigste Punkt ist –
                        egal, ob sich das auf den Inhalt oder Ihren persönlichen Lernfortschritt bezieht.
                    </div>
                    <textarea v-model="q2" placeholder=""></textarea>
                </div>
                <div class="block mb-2">
                    <div class="question mb-1">
                        Wie geht es Ihnen im Moment?
                    </div>
                    <div class="form-group">
                        <div class="form-check form-check-inline"><i class="fa fa-smile"></i></div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="1"
                                class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="2"
                                class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="3"
                                class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="4"
                                class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="5"
                                class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <i class="fa fa-frown mt-1"></i>
                        </div>
                    </div>
                </div>
                <button class="btn btn-submit btn-secondary" @click="submitOverlay()">absenden und weiter</button>
                <button class="btn btn-link right" @click="closeOverlay()">jetzt nicht</button>
            </div>
        </div>
    </div>
</template>
<style>
.hypervideo {
    position: relative;
}

.player-container {
    z-index: 1;
}

.overlay {
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 30000;
    background-color: #e9f5f9;
    opacity: 0.8;
    width: 100%;
    max-width: 700px;
    margin: 50 auto;
}

.question {
    font-size: 1.4em;
    font-weight: normal;
}

.block textarea {
    width: 90%;
    padding: 4px;
    min-height: 50px;
    font-size: 1.3em;
    background-color: #fff;
    opacity: 1;
}

.overlay .fa {
    font-size: 1.4em;
}

.overlay .fa-frown,
.overlay .fa-smile {
    font-size: 2.1em;
}

.overlay .thumbs-horizontal{
    transform: rotate(270deg);
}

.overlay .buttons:hover {
    color: blue;
}

.overlay .radio-ho:hover{
    background-color: blue;
}

.overlay .fa-thumbs-up, .overlay .fa-thumbs-down {
    cursor: pointer;
}

.overlay .fa-thumbs-up:hover, .overlay .fa-thumbs-down:hover {
    color: blue;
}


</style>
<script>
import Logger from './scripts/logger';
import ajax from 'core/ajax';

export default {
    data: function () {
        return {
            video: null,
            seek_start: 0,
            videoid: 0,
            interval: 2,
            lastposition: -1,
            timer: null,
            logger: null,
            overlayVisible: false,
            dismissed: false,
            q1: null,
            q2: null,
            q3: null
        }
    },
    mounted: function () {
        this.videoid = 'videoid' + Math.floor(Math.random() * 1000);
        this.logger = new Logger(this.$store.state.courseid, this.$store.state.hypervideoid, {
            context: "media_hypervideo",
            outputType: 1,
            url: this.$store.state.url
        });
        console.log(1);
        this.logger.init();
        console.log(2);
    },
    methods: {
        canplay: function(e){
            console.log(3);
            let _this = this;
            this.video = e.target;
            this.video.setAttribute('id', this.videoid);
            console.log('can play')
            this.video.addEventListener('seeking', function(e){
                //_this.seek_start = _this.video.currentTime || 0;
            });
            console.log(4);
        },
        timeupdate: function(e){
            this.seek_start = this.video.currentTime;
        },
        loop: function (e) {
            var lastposition = -1;
            var curr = this.video.currentTime || 0;
            var currentinterval = curr > 0 ? Math.round(curr / this.interval) : 0;
            if (currentinterval != lastposition) {
                this.log('playback', { context: 'player', action: 'playback', values: currentinterval, currenttime: this.video.currentTime, duration: this.video.duration });
                lastposition = currentinterval;
            }
        },
        start: function (e) {
            this.video = e.target;
            this.log('play', { context: 'player', action: 'play', values: '', currenttime: this.video.currentTime, duration: this.video.duration });
            this.video.setAttribute('id', this.id)
            if (this.timer) {
                this.timer = clearInterval(this.timer);
            }
            this.timer = setInterval(this.loop, this.interval * 1000);
            setTimeout(this.loop, 100);
        },
        restart: function () {
            if (this.timer) {
                this.timer = clearInterval(this.timer);
            }
            //lasttime = -1;
            this.timer = setInterval(this.loop, this.interval * 1000);
            setTimeout(this.loop, 100);
        },
        stop: function () {
            this.log('pause', { context: 'player', action: 'pause', values: '', currenttime: this.video.currentTime, duration: this.video.duration });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        seeking: function (e) {
            console.log('seeking', this.video.currentTime);
        },
        seeked: function (e) {
            console.log(e);
            console.log('::: seeked started at ', this.seek_start, this.video.currentTime, this.video.currentTime-this.seek_start);
            this.log('seeked', { context: 'player', action: 'seeked', values: '', currenttime: this.video.currentTime, duration: this.video.duration });
        },
        ended: function () {
            this.log('ended', { context: 'player', action: 'ended', values: '', currenttime: this.video.currentTime, duration: this.video.duration });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        log: function (key, values) {
            var a = this.logger ? this.logger.add(key, values) : null;
        },
        watchSurvey: function(){
            /**
             * load total watched segments
             * if ()
             * if ( Math.round( watched_segments / Math.floor(this.video.duration/this.interval) ) )
             * if (Math.random()>0.66){ this.openOverlay; }
             * 
             * SELECT count(action)
FROM `mdl_hypervideo_log`
WHERE value<>0 AND action='playback'
             */
        },
        openOverlay: function () {
            this.overlayVisible = true;
        },
        closeOverlay: function () {
            this.overlayVisible = false;
            this.dismissed = true;
        },
        submitOverlay: function () {
            let _this = this;
            var date = new Date();
            ajax.call([{
                methodname: 'mod_hypervideo_survey',
                args: {
                    data: {
                        courseid: _this.$store.state.courseid,
                        url: _this.url,
                        utc: Math.ceil(date.getTime() / 1000),
                        q1: _this.q1,
                        q2: _this.q2,
                        q3: _this.q3
                    }
                },
                done: function (msg) {
                    console.log('survey log ok ', msg);
                    _this.closeOverlay();
                },
                fail: function (e) {
                    console.error('fail', e);
                }
            }]);
        }
    },
    computed: {
        url: function () {
            return this.$store.state.url;
        },
        title: function () {
            return this.$store.state.title;
        },
        alertType: function () {
            return this.$store.getters.getAlertType;
        },
        showAlert: function () {
            return this.$store.getters.getAlertState;
        },
        alertMessage: function () {
            return this.$store.getters.getAlertMessage;
        }
    }
}
</script>