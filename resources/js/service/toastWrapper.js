// wrapper for vue3 toastify
import { toast as originalToast } from "vue3-toastify";
const meta = import.meta.env;

const debug = meta.VITE_APP_DEBUG ? meta.VITE_APP_DEBUG === "true" : false;

const toast = {
    success(message, options) {
        if (debug === true) {
            originalToast.success(message, options);
        }
    },
    error(message, options) {
        if (debug === true) {
            originalToast.error(message, options);
        }
    },
    info(message, options) {
        if (debug === true) {
            originalToast.info(message, options);
        }
    },
    warn(message, options) {
        if (debug === true) {
            originalToast.warn(message, options);
        }
    },
};

export { toast };
