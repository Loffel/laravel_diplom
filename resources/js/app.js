/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
import { Form, HasError, AlertError } from 'vform';
import moment from 'moment';
import VueProgressBar from 'vue-progressbar';

Vue.config.ignoredElements = ['ion-icon'];


import HighchartsVue from 'highcharts-vue';
import Highcharts from 'highcharts'
import stockInit from 'highcharts/modules/stock'
import exportingInit from 'highcharts/modules/exporting'


stockInit(Highcharts)
exportingInit(Highcharts)
Highcharts.setOptions({
    lang: {
        months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
        weekdays: ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"],
        shortMonths: ["Янв", "Фев", "Мар", "Апр", "Май", "Июн", "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек"],
        shortWeekdays: ["Пн","Вт","Ср","Чт","Пт","Сб","Вс"],
        rangeSelectorZoom: "Период",
        rangeSelectorFrom: "С",
        rangeSelectorTo: "По",
        rangeSelectorZoom: "Период",
        rangeSelectorFrom: "С",
        rangeSelectorTo: "По",
        exportButtonTitle: "Экспорт",
        printButtonTitle: "Печать",
        loading: 'Загрузка...',
        downloadPNG: 'Скачать PNG',
        downloadJPEG: 'Скачать JPEG',
        downloadPDF: 'Скачать PDF',
        downloadSVG: 'Скачать SVG',
        printChart: 'Напечатать график',
        viewFullscreen: "На весь экран"
    },
});


import Swal from 'sweetalert2';
window.swal = Swal;
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
window.toast = Toast;


window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

Vue.use(VueRouter);
Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
});
Vue.use(HighchartsVue);

let routes = [
    { name: 'dashboard', path: '/dashboard', component: require('./components/Dashboard.vue').default, meta: { title: 'Панель управления' } },
    { name: 'products', path: '/products', component: require('./components/Products.vue').default, meta: { title: 'Продукты' } },
    { name: 'product', path: '/products/:id', component: require('./components/Product.vue').default, meta: { title: 'Продукт' } },
    { name: 'shops', path: '/shops', component: require('./components/Shops.vue').default, meta: { title: 'Магазины' } },
    { name: 'register', path: '/register', component: require('./components/auth/Register.vue').default, meta: { title: 'Регистрация' } },
    { name: 'login', path: '/login', component: require('./components/auth/Login.vue').default, meta: { title: 'Авторизация' } }
];

const router = new VueRouter ({
    mode: 'history',
    linkActiveClass: 'active',
    routes
});

router.beforeEach((to, from, next) => {
    document.title = to.meta.title;
    next();
});

Vue.filter('myDate', function(date){
    return moment(date).format("DD-MM-YYYY HH:mm:ss");
});

Vue.filter('priceDate', function(date){
    return moment(date).utc().format("DD-MM-YYYY");
});

Vue.filter('epochDate', function(date){
    return moment(date).format("X");
});

window.Fire = new Vue();

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/includes/Breadcrumbs.vue').default);

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */



const app = new Vue({
    el: '#app',
    router
});
