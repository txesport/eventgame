// public/js/bootstrap.js

// Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel Echo + Pusher
window.Pusher = require('pusher-js');
import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'VOTRE_PUSHER_APP_KEY',
    cluster: 'VOTRE_PUSHER_APP_CLUSTER',
    wsHost: '127.0.0.1',
    wsPort: 6001,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
});
