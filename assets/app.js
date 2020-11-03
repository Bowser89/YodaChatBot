import Vue from 'vue'
import App from './components/js/App'
import './styles/app.css'

window.axios = require('axios');
window.$ = window.jQuery = require('jquery');

var vue = new Vue({
    el: '#app',
    components: {App}
})

