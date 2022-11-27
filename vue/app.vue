<template>
    <div>
        <div class="hypervideo">
            <div class="player-container" style="width:100%; max-width:800px; margin-top:30px; height:auto;">
                <vue-core-video-player :src="url" @play="start" @pause="stop" @loadeddata="canplay"
                    @timeupdate="timeupdate" @seeked="seeked" @seeking="seeking" @ended="ended" @abort="stop">
                </vue-core-video-player>
            </div>
            <div v-if="overlayVisible" class="overlay m-3 p-3">
                <div class="block mb-3">
                    <div class="question mb-1">
                        Wie beurteilen Sie das Video, das Sie gerade gesehen haben?
                        <i @click="q1 = 'up'" :style="q1 == 'up' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-up ml-3 buttons"></i>
                        <i @click="q1 = 'middle'" :style="q1 == 'middle' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-up thumbs-horizontal ml-3 buttons"></i>
                        <i @click="q1 = 'down'" :style="q1 == 'down' ? 'color:blue;' : 'color:#333;'"
                            class="fa fa-thumbs-down ml-3 buttons"></i>
                    </div>
                </div>
                <div class="block mb-3">
                    <div class="question mb-1">
                        Was nehmen Sie vom gerade bearbeiteten Kursmaterial für sich mit?
                        Bitte nennen Sie ein Stichwort oder beschreiben kurz, was für Sie
                        der wichtigste Punkt ist – egal, ob sich das auf den Inhalt oder
                        Ihren persönlichen Lernfortschritt bezieht.
                    </div>
                    <textarea v-model="q2" placeholder=""></textarea>
                </div>
                <div class="block mb-2">
                    <div class="question mb-1">
                        Wie geht es Ihnen im Moment?
                    </div>
                    <div class="form-group">
                        <div class="form-check form-check-inline">
                            <i class="fa fa-smile"></i>
                        </div>
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
                <button class="btn btn-submit btn-secondary" @click="submitSurvey()">
                    absenden und weiter
                </button>
                <button class="btn btn-link right" @click="closeOverlay()">
                    jetzt nicht
                </button>
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

.overlay .thumbs-horizontal {
    transform: rotate(270deg);
}

.overlay .buttons:hover {
    color: blue;
}

.overlay .radio-ho:hover {
    background-color: blue;
}

.overlay .fa-thumbs-up,
.overlay .fa-thumbs-down {
    cursor: pointer;
}

.overlay .fa-thumbs-up:hover,
.overlay .fa-thumbs-down:hover {
    color: blue;
}
</style>
<script>
import Logger from "./scripts/logger";
import Communication from "./scripts/communication";
import ajax from "core/ajax";

export default {
    data: function () {
        return {
            video: null,
            seek_start: 0,
            videoid: 0,
            videoprogress: 0,
            interval: 2,
            lastposition: -1,
            timer: null,
            logger: null,
            overlayVisible: false,
            surveyCompleted: 0,
            surveyDismissed: false,
            q1: null,
            q2: null,
            q3: null,
        };
    },
    mounted: function () {
        this.videoid = "videoid" + Math.floor(Math.random() * 1000);
        this.logger = new Logger(
            this.$store.state.courseid,
            this.$store.state.hypervideoid,
            {
                context: "media_hypervideo",
                outputType: 1,
                url: this.$store.state.url,
            }
        );
        this.logger.init();
        this.getVideoProgress();
    },
    watch: {
        videoprogress: function (old_progress, current_progress) {
            console.log(this.videoprogress, current_progress)
            if (current_progress / this.duration > 0.8 && this.surveyCompleted == 0) {
                this.overlayVisible = true;
            }else{
                this.overlayVisible = false;
            }
        }
    },
    methods: {
        getVideoProgress: async function () {
            const response = await Communication.webservice(
                'videoprogress', {
                'data': {
                    'course': parseInt(this.$store.getters.getCourseid, 10),
                    'hypervideo': parseInt(this.$store.getters.getHypervideoid, 10)
                }
            });
            if (response.success) {
                this.videoprogress = parseInt(JSON.parse(response.data).videoprogress * this.interval, 10);
                this.surveycompleted = JSON.parse(response.data).survey ? JSON.parse(response.data).survey.id : 0;
                console.log(this.videoprogress, this.surveycompleted);
            }
        },
        canplay: function (e) {
            let _this = this;
            this.video = e.target;
            this.video.setAttribute("id", this.videoid);
            //console.log("can play");
            this.video.addEventListener("seeking", function (e) {
                //_this.seek_start = _this.video.currentTime || 0;
            });
        },
        timeupdate: function (e) {
            this.seek_start = this.video.currentTime;
        },
        loop: function (e) {
            var lastposition = -1;
            var curr = this.video.currentTime || 0;
            var currentinterval = curr > 0 ? Math.round(curr / this.interval) : 0;
            if (currentinterval != lastposition) {
                this.log("playback", {
                    context: "player",
                    action: "playback",
                    values: currentinterval,
                    currenttime: this.video.currentTime,
                    duration: this.video.duration,
                });
                this.videoprogress += this.interval;
                lastposition = currentinterval;
            }
        },
        start: function (e) {
            this.video = e.target;
            this.log("play", {
                context: "player",
                action: "play",
                values: "",
                currenttime: this.video.currentTime,
                duration: this.video.duration,
            });
            this.video.setAttribute("id", this.id);
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
            this.log("pause", {
                context: "player",
                action: "pause",
                values: "",
                currenttime: this.video.currentTime,
                duration: this.video.duration,
            });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        seeking: function (e) {
            console.log("seeking", this.video.currentTime);
        },
        seeked: function (e) {
            console.log(e);
            console.log(
                "::: seeked started at ",
                this.seek_start,
                this.video.currentTime,
                this.video.currentTime - this.seek_start
            );
            this.log("seeked", {
                context: "player",
                action: "seeked",
                values: "",
                currenttime: this.video.currentTime,
                duration: this.video.duration,
            });
        },
        ended: function () {
            this.log("ended", {
                context: "player",
                action: "ended",
                values: "",
                currenttime: this.video.currentTime,
                duration: this.video.duration,
            });
            this.timer = clearInterval(this.timer);
            this.loop();
        },
        log: function (key, values) {
            var a = this.logger ? this.logger.add(key, values) : null;
        },
        openOverlay: function () {
            this.overlayVisible = true;
        },
        closeOverlay: function () {
            this.overlayVisible = false;
            this.surveyDismissed = true;
        },
        submitSurvey: async function () {
            var date = new Date();
            const request = await Communication.webservice(
                'survey',
                {
                    'data': {
                        courseid: this.$store.state.courseid,
                        hypervideoid: this.$store.state.hypervideoid,
                        url: this.url,
                        utc: Math.ceil(date.getTime() / 1000),
                        q1: this.q1,
                        q2: this.q2,
                        q3: this.q3,
                    },
                }
            );
            if (request.success) {
                console.log("mod_hypervideo_survey ok ", request.data);
                this.closeOverlay();
            } else {
                console.error("mod_hypervideo_survey fail");
            }
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
        },
    },
};
</script>
