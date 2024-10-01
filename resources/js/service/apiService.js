// src/axios.js

import axios from "axios";

const apiService = axios.create();

// Set the CSRF token as a default header
apiService.defaults.headers.common["X-CSRF-TOKEN"] =
    document.head.querySelector('meta[name="csrf-token"]').content;

// apiService.interceptors.request.use(
//     (config) => {
//         console.log("Request:", config);
//         return config;
//     },
//     (error) => {
//         return Promise.reject(error);
//     }
// );

export default apiService;
