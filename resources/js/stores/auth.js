import { defineStore } from "pinia";
import axios from "axios";
import {
    RemoveValueFromStorage,
    SITE_TOKEN_VAR,
    SetValueToStorage,
} from "../constants/variables";

import { toast } from "../service/toastWrapper";

const meta = import.meta.env;

export const useAuthStore = defineStore("auth", {
    state: () => ({
        authUser: null,
        userData: null,
        actionLoader: false,
    }),

    getters: {
        user: (state) => state.authUser,
    },

    actions: {
        async getToken() {
            await axios.get(meta.VITE_csrfPath);
        },
        async getUser() {
            try {
                await this.getToken();
                const data = await axios.get("/api/user");
                this.userData = data.data;
                this.authUser = data.data;
                SetValueToStorage(SITE_TOKEN_VAR, data.data);
            } catch (error) {
                if (error.request.status == 401) {
                    RemoveValueFromStorage(SITE_TOKEN_VAR);
                    this.router.push("/login");
                }
            }
        },
        async updateUser(payload) {
            try {
                const data = await axios.post("/api/me", payload);
                this.userData = data.data;
                this.authUser = data.data;
                toast.success("User has been updated successfully");
                return Promise.resolve();
            } catch (error) {
                const { response } = error;
                toast.error(response.data.message || "SOmething went wrong");
            } finally {
                return Promise.resolve();
            }
        },
        async handleLogin(data) {
            this.actionLoader = true;
            this.authErrors = [];
            await this.getToken();
            try {
                const loginData = await axios.post("/login", {
                    email: data.email,
                    password: data.password,
                });
                this.authUser = loginData.data;
                SetValueToStorage(SITE_TOKEN_VAR, loginData.data);
                window.location.href = "/dashboard";
            } catch (error) {
                const { response } = error;
                toast.error(response.data.message || "Something went wrong");
                // if (error.response.status === 422) {
                //   this.authErrors = error.response.data.errors;
                // }
            } finally {
                this.actionLoader = false;
            }
        },

        async handleLogout() {
            await axios.post("/logout");
            this.authUser = null;
            this.userData = null;
            RemoveValueFromStorage(SITE_TOKEN_VAR);
            this.router.push("/login");
        },

        async handleRegister(data) {
            this.authErrors = [];
            await this.getToken();

            try {
                await axios.post("/register", {
                    name: data.name,

                    first_name: data.first_name,
                    last_name: data.last_name,
                    email: data.email,
                    password: data.password,
                    password_confirmation: data.password_confirmation,
                });
                this.router.push("/login");
            } catch (error) {
                if (error.response.status === 422) {
                    this.authErrors = error.response.data.errors;
                }
            }
        },

        async handleForgotPassword(data) {
            this.authErrors = [];
            this.getToken();

            try {
                const response = await axios.post("/forgot-password", {
                    email: data.email,
                });
                this.authStatus = response.data.status;
            } catch (error) {
                console.log(error);
                if (error.response.status === 422) {
                    this.authErrors = error.response.data.errors;
                }
            }
        },
        async handleResetPassword(resetData) {
            this.authErrors = [];
            try {
                const response = await axios.post("/reset-password", resetData);
                this.authStatus = response.data.status;
            } catch (error) {
                if (error.response.status === 422) {
                    this.authErrors = error.response.data.errors;
                }
            }
        },

        async handleCancel() {
            this.router.go(-1);
        },
    },
});
