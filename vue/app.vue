<template>
    <div>
        <div class="hypervideo">
            <div class="player-container" style="width:100%; max-width:800px; height:auto;">
                <vue-core-video-player :src="url" @play="start" @pause="stop" @ended="ended" @abort="stop" @seeked="seeked">
                </vue-core-video-player>
            </div>
            <div v-if="overlayVisible" class="overlay m-3 p-3">
                {{q1}}, {{q2}}, {{q3}}

                <div class="block mb-3">
                    <div class="question mb-1">
                        Wie beurteilen Sie das Video, das Sie gerade gesehen haben?
                        <i @click="q1='up'" :style="q1=='up' ? 'color:blue;' : 'color:#333;'" class="fa fa-thumbs-up ml-3 buttons"></i>
                        <i @click="q1='down'" :style="q1=='down' ? 'color:blue;' : 'color:#333;'" class="fa fa-thumbs-down ml-3 buttons" ></i>
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
                            <input type="radio" v-model="q3" name="moodRadio" value="1" class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="2" class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="3" class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="4" class="form-check-input mr-2 ml-2 radio-ho" />
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" v-model="q3" name="moodRadio" value="5" class="form-check-input mr-2 ml-2 radio-ho" />
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
    background-color: antiquewhite;
    opacity: 0.8;
    width: 100%;
    max-width:700px;
    margin: 50 auto;
}

.question {
    font-size: 1.4em;
    font-weight: bold;
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
    font-size:1.4em;
}

.overlay .fa-frown, .overlay .fa-smile {
    font-size:2.1em;
}

.overlay .buttons:hover {
    color: blue;
}

.overlay .radio-ho:hover{
    background-color:blue;
}

</style>
<script>
import Logger from './scripts/logger';
import ajax from 'core/ajax';

export default {
    data: function () {
        return {
            context: {},
            video: null,
            videoid: 0,
            interval: 2,
            lastposition: -1,
            timer: null,
            logger: null,
            overlayVisible: true,
            q1:null,
            q2:null,
            q3:null
        }
    },
    mounted: function () {
        this.context.courseId = 2; 
        this.videoid = 'videoid' + Math.floor(Math.random() * 1000);
        this.logger = new Logger(this.context.courseId, {
                    context: "media_hypervideo",
                    outputType: 1,
                    url: this.$store.state.url
                });
        this.logger.init();

    },
    computed: {
        
    },
    methods: {
        /*
        this.video.addEventListener('timeupdate', function (e) {
            //_this.start();
        });
         player.oncanplay(start);
                player.onSeek(restart);
         player.onPause(stop);
             player.onBuffer(stop);
         player.onIdle(stop);
         player.onComplete(stop);
             player.onError(stop);
       */
        loop: function (e) {
            var interval = 2, lastposition = -1;
            var curr = this.video.currentTime || 0;
            var currentinterval = curr > 0 ? Math.round(curr / interval) : 0;
            //console.log(currentinterval)
            if (currentinterval != lastposition) {
                this.log('playback', { context: 'player', action: 'playback', values: currentinterval, currenttime: this.video.currentTime });
                lastposition = currentinterval;
            }
        },
        start: function (e) {
            this.video = e.target;
            this.log('play', { context: 'player', action: 'play', values:'' ,currenttime: this.video.currentTime });
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
            this.log('pause', { context: 'player', action: 'pause', values:'' ,currenttime: this.video.currentTime });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        seeked: function(e){
            console.log('eeeee', e)
            this.log('seeked', { context: 'player', action: 'seeked', values:'' ,currenttime: this.video.currentTime });
        },
        ended: function(){
            this.log('ended', { context: 'player', action: 'ended', values:'' ,currenttime: this.video.currentTime });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        log: function(key, values) {
            var a = this.logger ? this.logger.add(key, values) : null;
        },
        closeOverlay: function(){
            this.overlayVisible = false;
        },
        submitOverlay: function(){
            let _this = this;
            var date = new Date();
            ajax.call([{
                methodname: 'mod_hypervideo_survey',
                args: {
                    data: {
                        courseid: _this.courseId,
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