<template>
    <div>
        <div class="hypervideo">
            <div class="player-container" style="width:600px; height:auto;">
                <vue-core-video-player :src="url" @play="start" @pause="stop" @ended="stop" @abort="stop">
                </vue-core-video-player>
            </div>
            <div v-if="overlayVisible" class="overlay m-3 p-3">
                <div class="block mb-2">
                    <div class="question mb-1">
                        Wie beurteilen Sie das Video, das Sie gerade gesehen haben?
                    </div>
                    <i class="fa fa-thumbup"></i>
                    <i class="fa fa-thumbdown"></i>
                </div>
                <div class="block mb-2">
                    <div class="question mb-1">
                        Was nehmen Sie vom gerade bearbeiteten Kursmaterial für sich mit?
                        Bitte nennen Sie ein Stichwort oder beschreiben kurz, was für Sie der wichtigste Punkt ist –
                        egal, ob sich das auf den Inhalt oder Ihren persönlichen Lernfortschritt bezieht.
                    </div>
                    <textarea></textarea>
                </div>
                <div class="block mb-2">
                    <div class="question mb-1">
                        Wie geht es Ihnen im Moment?
                    </div>
                    <i class="fa fa-happy"></i>
                    <input type="radio" value="1" />
                    <input type="radio" value="2" />
                    <input type="radio" value="3" />
                    <input type="radio" value="4" />
                    <input type="radio" value="5" />
                    <i class="fa fa-sad"></i>
                </div>
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
    width: 500px;
    margin: 30 auto;
}

.question {
    font-size: 1.4em;
    font-weight: bold;
}

.block textarea {
    width: 90%;
    padding: 4px;
    font-size: 1.2em;
}
</style>
<script>
import Logger from './scripts/logger';

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
            overlayVisible: false
        }
    },
    mounted: function () {
        this.context.courseId = 2; 
        this.videoid = 'videoid' + Math.floor(Math.random() * 1000);
        this.logger = new Logger(this.context.courseId, {
                    context: "media_hypervideo",
                    outputType: 0,
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
                this.log(this.video.currentTime, { context: 'player', action: 'playback', values: currentinterval });
                lastposition = currentinterval;
            }
        },
        start: function (e) {
            this.video = e.target;
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
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        log: function(key, values) {
            var a = this.logger ? this.logger.add(key, values) : null;
        },
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