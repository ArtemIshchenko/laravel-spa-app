require('./bootstrap');

import Vue from 'vue'
import App from './components/App.vue'
import VueRouter from 'vue-router'
import Vuelidate from 'vuelidate'
import Home from './components/Home.vue'
import Desks from './components/desks/Desks.vue'
import ShowDesk from './components/desks/ShowDesk.vue'

Vue.config.productionTip = false
Vue.use(VueRouter)
Vue.use(Vuelidate)

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/desks',
            name: 'desks',
            component: Desks
        },
        {
            path: '/desks/:deskId',
            name: 'showDesk',
            component: ShowDesk,
            props: true
        },
    ]
})

const app = new Vue({
    render: h => h(App),
    router
}).$mount('#app')
