window.Vue = require('vue')
window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

// Vue Pages
Vue.component('dashboard-page', require('./src/pages/Dashboard.vue').default)

// Components
Vue.component('dropdown', require('./src/components/Form/Dropdown.vue').default)
Vue.component('dropdown-link', require('./src/components/Form/DropdownLink.vue').default)
Vue.component('alert', require('./src/components/Alert.vue').default)
Vue.component('my-button', require('./src/components/Buttons/Button.vue').default)
Vue.component('my-label', require('./src/components/Form/Label.vue').default)
Vue.component('my-input', require('./src/components/Form/Input.vue').default)

new Vue({
  el: '#app'
})