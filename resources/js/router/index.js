import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";

import Home from "../components/Home.vue";
import Login from "../components/Login.vue";
import Register from "../components/Register.vue";
import ForgotPassword from "../components/ForgotPassword.vue";
import ResetPassword from "../components/ResetPassword.vue";
import Dashboard from "../components/Dashboard.vue";
import MainProfileScreen from "../components/profile/MainProfileScreen.vue";
import TermScreen from "../components/Terms.vue";
import PlayerConfig from "../components/playerConfig/playerConfig.vue";
import PlayerCreate from "../components/CreatePlayer.vue";
import { SITE_TOKEN_VAR, getStorageValue } from "../constants/variables";
const routes = [
    { path: "/", name: "Home", component: Home },
    {
        path: "/login",
        name: "Login",
        component: Login,
    },
    {
        path: "/register",
        name: "Register",
        component: Register,
    },
    {
        path: "/forgot-password",
        name: "ForgotPassword",
        component: ForgotPassword,
    },
    {
        path: "/password-reset/:token",
        name: "ResetPassword",
        component: ResetPassword,
    },
    { path: "/dashboard", component: Dashboard },
    { path: "/profile", component: MainProfileScreen },
    { path: "/terms", component: TermScreen },
    { path: "/players/configuration/:id", component: PlayerConfig },
    { path: "/player/create", component: PlayerCreate },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    const loggedIn = await getStorageValue(SITE_TOKEN_VAR);

    const publicPages = [
        "/login",
        "/register",
        "/forgot-password",
        /\/password-reset\/[^\/]+$/, // Allow paths matching "/password-reset/" followed by any characters except slashes
    ];

    const authRequired = publicPages.some((page) =>
        page instanceof RegExp ? page.test(to.path) : to.path === page
    );

    // console.log("authRequired: ", authRequired);
    // console.log("router.beforeEach: loggedIn: ", loggedIn);
    if (!authRequired && loggedIn) {
        return next();
    } else if (!authRequired && !loggedIn) {
        return next("/login");
    } else if (authRequired && !loggedIn) {
        return next();
    } else if (authRequired && loggedIn) {
        return next("/dashboard");
    }
});

export default router;
