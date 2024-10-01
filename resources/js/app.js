import "bootstrap/dist/css/bootstrap.css";
import "bootstrap";
import "tippy.js/dist/tippy.css";
import "../css/tippy-goraymond.css";
import "tippy.js/animations/scale.css";
import "vue3-toastify/dist/index.css";
import { createApp, markRaw } from "vue";
import { createPinia } from "pinia";
import FloatingVue from "floating-vue";
import "floating-vue/dist/style.css";
import router from "./router";
import App from "./App.vue";
import Vue3Toastify from "vue3-toastify";
import VueTippy from "vue-tippy";

import Echo from "laravel-echo";
import Pusher from "pusher-js"; // Import Pusher

// Assign Pusher to the global window object
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: "pusher", // Still use 'pusher' for Echo compatibility
//     key: import.meta.env.VITE_REVERB_APP_KEY, // Reverb WebSocket key from .env
//     wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname, // Reverb WebSocket host
//     wsPort: import.meta.env.VITE_REVERB_PORT || 8080, // Reverb WebSocket port
//     // forceTLS: import.meta.env.VITE_REVERB_SCHEME === "https", // Force TLS if using https
//     forceTLS: false,
//     disableStats: true, // Disable stats for Echo

//     enabledTransports: ["ws", "http", "https"],
//     path: "/app/ws", // Update this line to match the new path
//     cluster: "", // Set to null or a valid cluster name
// });

const pinia = createPinia();

pinia.use(({ store }) => {
    store.router = markRaw(router);
});

const app = createApp(App);
app.use(FloatingVue);
app.use(pinia);
app.use(router);
app.use(Vue3Toastify, {
    autoClose: 4000,
});

app.use(VueTippy, {
    directive: "tippy",
    component: "tippy",
    componentSingleton: "tippy-singleton",
    theme: "tippy-goraymond",
    animation: "scale",
    delay: [200, 200],
    defaultProps: {
        placement: "bottom-start",
        allowHTML: true,
    },
});

const meta = import.meta.env;
app.mount("#app");
