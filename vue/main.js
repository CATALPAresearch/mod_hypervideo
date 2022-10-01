import Vue from 'vue';
import VueRouter from 'vue-router';
import { store } from './store';
import App from "./app.vue";
import Communication from './scripts/communication';
import VueCoreVideoPlayer from 'vue-core-video-player'


function init(coursemoduleid, contextid, courseid, hypervideoid, fullPluginName, url, title) {    
    // We need to overwrite the variable for lazy loading.
    __webpack_public_path__ = M.cfg.wwwroot + '/mod/hypervideo/amd/build/';
    
    Communication.setPluginName(fullPluginName);
    
    Vue.use(VueRouter);    
    Vue.use(VueCoreVideoPlayer)
    
    store.commit('setPluginName', fullPluginName);
    store.commit('setCourseModuleID', coursemoduleid);
    store.commit('setHypervideoid', hypervideoid);
    store.commit('setCourseid', courseid);
    store.commit('setContextID', contextid);
    store.commit('setURL', url);
    store.commit('setTitle', title);
    store.dispatch('loadComponentStrings');

    

    // base URL is /mod/vuejsdemo/view.php/[course module id]/
    const currenturl = window.location.pathname;
    const base = currenturl.substr(0, currenturl.indexOf('.php')) + '.php/' + coursemoduleid + '/';

    const router = new VueRouter({
        mode: 'history',
       // routes,
        base
    });

    new Vue({
        el: '#app',
        store,
        render: (h) => h(App),
    });
}

export { init };
