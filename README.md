# mod_hypervideo
Video player with advanced logging functionality and short survey.

**Features**
* provides basic HTLM5 video player
* logs playback events
* logs watched segments

**Roadmap**
* triggers short surveys depending on video watching progress


# Installation
- clone the repository into the folder /mod: `git clone git@gitlab.pi6.fernuni-hagen.de:aple/entwicklung/mod_hypervideo.git`
- rename the folder to 'hypervideo': `mv mod_hypervideo hypervideo`
- follow the install instruction in the Moodle administration area
- Include an URL of a video file in a text so that the plugin will display the hypervideo player with the video

# Maintanance

* Set tag for integration into the production system: `git tag -d ws2223 && git push --delete origin ws2223 && git tag ws2223 && git push origin ws2223`

# Credits
* https://github.com/core-player/vue-core-video-player

