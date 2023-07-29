 import './bootstrap';

 // import '../scss/app.scss';

 import {createApp, h} from 'vue';
 import {createInertiaApp, Link, Head} from '@inertiajs/inertia-vue3';
 import {InertiaProgress} from '@inertiajs/progress';
 import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
 // import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
 import UserAppLayout from "./Layouts/User/AppLayout.vue";
 // import AdminAppLayout from "./Layouts/Admin/AppLayout.vue";
 // import pinia from './Stores/Store';
 // import Popper from "./Components/Popper.vue";
 // Vuetify
 // import 'vuetify/styles'
 // import { createVuetify } from 'vuetify'
 // import * as components from 'vuetify/components'
 // import * as directives from 'vuetify/directives'

 // const vuetify = createVuetify({
 //     components,
 //     directives
 // })

 const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
 let rawTitle = '';

 // .component('AdminAppLayout', AdminAppLayout)

 // .use(ZiggyVue, Ziggy)
 //     .use(pinia)
 //     .use(vuetify)
 // .component('Popper', Popper)

 createInertiaApp({
     title: function (title) {
         rawTitle = title;
         return title + ' - ' + appName;
     },
     resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
     setup({el, app, props, plugin}) {
         const myApp = createApp({render: () => h(app, props)})
             .use(plugin)
             .component('UserAppLayout', UserAppLayout)
             .component('Head', Head)
             .component('Link', Link);

         myApp.config.unwrapInjectedRef = true;

         myApp.config.globalProperties.trans = function (key, replace = {}, emptyControl = false) {
             if (typeof key != 'string') {
                 return "";
             }
             let translation = JSON.parse(this.$page.props.languageMessages) !== null ? JSON.parse(this.$page.props.languageMessages)[this.$page.props.currentLanguage] : {};
             translation = translation[key] ?? key;
             Object.keys(replace).forEach(function (key) {
                 translation = translation.replace(':' + key, replace[key])
             });
             if (emptyControl) {
                 if (translation === key) {
                     return '';
                 }
             }
             return translation;
         };

         myApp.config.globalProperties.can = function (route) {
             return this.$page.props.permissions[route];
         };

         myApp.config.globalProperties.getTitle = function () {
             return rawTitle;
         };

         // myApp.config.globalProperties.breadcrumbsObj = breadcrumbsObj;
         // myApp.config.globalProperties.downloadCsv = downloadCsv;

         myApp.mount(el);
         return myApp;
     },
 });
