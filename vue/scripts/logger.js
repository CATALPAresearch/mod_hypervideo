/* eslint-disable valid-jsdoc */
/**
 * name: Logging
 * author: 2021 Niels Seidel, niels.seidel@fernuni-hagen.de
 * license: MIT License
 * description: Logs user behavior data inclunding informations about the client system, browser, and time.
 * todo:
 */
import Communication from "./communication";

export default function (courseid, hypervideoid, options) {
    this.courseid = courseid;
    this.hypervideoid = hypervideoid;
    this.name = "log_hypervideo";
    this.ip = "";
    this.url = options.url;

    this.init = function () {
        this.options = this.mergeObjects(
            {
                outputType: 1, // -1: no logging, 0: console.log(), 1: server log,
                prefix: "",
                loggerServiceUrl: null,
                loggerServiceParams: { data: {} },
                context: "default-context",
            },
            options
        );
    };

    /**
     * Adds a message to the log by constructing a log entry
     */
    this.add = function (action, msg) {
        if (typeof msg === "string") {
            console.log("warning: uncaptured log entry: " + msg);
            return;
        }
        var time = this.getLogTime();
        var logEntry = {
            utc: time.utc,
            location: {
                //protocol: window.location.protocol,
                //port: window.location.port,
                host: window.location.host,
                pathname: window.location.href,
                url: this.url,
                //hash: window.location.hash,
                //tabId: window.name.split('=')[0] === "APLE-MOODLE" ? window.name.split('=')[1] : "unknown"
            },
            context: this.options.context,
            action: action,
            value: msg,
            userAgent: {
                cpu: navigator.oscpu,
                platform: navigator.platform,
                engine: navigator.product,
                browser: navigator.appCodeName,
                /*browserVersion: navigator.appVersion,
                        userAgent: navigator.userAgent.replace(/,/gm, ';'),
                        screenHeight: screen.height, // document.body.clientHeight
                        screenWidth: screen.width // document.body.clientWidth*/
                // retina
            },
        };
        this.output(logEntry);
    };

    /**
     * Validates the msg against illegal characters etc.
     */
    this.validate = function (msg) {
        return msg;
    };

    /**
     * Returs structured time information
     */
    this.getLogTime = function () {
        var date = new Date();
        var s = date.getSeconds();
        var mi = date.getMinutes();
        var h = date.getHours();
        var d = date.getDate();
        var m = date.getMonth() + 1;
        var y = date.getFullYear();

        return {
            utc: date.getTime(),
            date: y + "-" + (m <= 9 ? "0" + m : m) + "-" + (d <= 9 ? "0" + d : d),
            time:
                (h <= 9 ? "0" + h : h) +
                ":" +
                (mi <= 9 ? "0" + mi : mi) +
                ":" +
                (s <= 9 ? "0" + s : s) +
                ":" +
                date.getMilliseconds(),
        };
    };

    this.mergeObjects = function (obj1, obj2) {
        var obj3 = {};
        for (var attrname in obj1) {
            obj3[attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            obj3[attrname] = obj2[attrname];
        }
        return obj3;
    };

    /**
     * Interface for handling the output of the generated log entry
     */
    this.output = function (logEntry) {
        switch (this.options.outputType) {
            case 0:
                console.log("case 0: ", logEntry);
                break;
            case 1:
                this.sendLog(logEntry);
                //console.log('case 1: ',logEntry);
                break;
            default:
            // Do nothing
        }
    };

    /**
     * Makes an AJAX call to send the log data set to the server
     */
    this.sendLog = async function (entry) {
        const request = await Communication.webservice(
            'log', {
            'data': {
                'courseid': parseInt(this.courseid, 10),
                'hypervideoid': parseInt(this.hypervideoid, 10),
                'action': entry.action,
                'utc': Math.ceil(entry.utc / 1000),
                'entry': JSON.stringify(entry),
            },
        });
        if (request.success) {
            console.log("mod_hypervideo_log ok ", request.response);
        } else {
            console.log("mod_hypervideo_log fail", request);
        }
    };
}
