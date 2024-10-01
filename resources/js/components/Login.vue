<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import Nav from "./Nav.vue";

import SvgLogoImage from "@/img/svgicons/logo.svg?url";
import SvgUserImage from "@/img/svgicons/user.svg?url";
import SvgPasswordImage from "@/img/svgicons/password.svg?url";
import SvgEyeImage from "@/img/svgicons/eye.svg?url"; // Add eye-open icon

const meta = import.meta.env;

const authStore = useAuthStore();

const form = ref({
    email: "",
    password: "",
});

let passwordVisible = ref(false);

function togglePasswordVisibility() {
    passwordVisible.value = !passwordVisible.value;
}
</script>

<template>
    <div class="container">
        <div class="w-100 mt-6">
            <div class="d-flex justify-content-center">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-center">
                        <div
                            class="position-relative d-flex flex-column align-items-center form_bg"
                        >
                            <figure>
                                <img :src="SvgLogoImage" alt="" />
                            </figure>

                            <form
                                @submit.prevent="authStore.handleLogin(form)"
                                class="mt-7 p-4"
                            >
                                <h3 class="text_header1 text-center">Log In</h3>

                                <!-- USER NAME -->
                                <div class="position-relative mt-4">
                                    <div class="input-group col-md-4">
                                        <div
                                            class="position-absolute fixed-top fixed-bottom px-2 py-2 d-flex"
                                            :style="{ width: 10 + 'px' }"
                                        >
                                            <img
                                                :src="SvgUserImage"
                                                class="input-icon"
                                                alt=""
                                            />
                                        </div>

                                        <div class="form-row">
                                            <input
                                                v-model="form.email"
                                                class="form-control py-2 px-4 pl-4 border"
                                                style="text-indent: 20px"
                                                type="email"
                                                placeholder="User Name (e-mail address)"
                                                id="email"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="position-relative mt-4">
                                    <div class="input-group col-md-4">
                                        <div
                                            class="position-absolute fixed-top fixed-bottom px-2 py-2 d-flex"
                                            :style="{ width: 10 + 'px' }"
                                        >
                                            <img
                                                :src="SvgPasswordImage"
                                                class="input-icon"
                                                alt=""
                                            />
                                        </div>

                                        <div class="form-row">
                                            <input
                                                v-model="form.password"
                                                class="form-control py-2 px-4 pl-4 mr-2 border fixed-left"
                                                style="text-indent: 20px"
                                                :type="
                                                    passwordVisible
                                                        ? 'text'
                                                        : 'password'
                                                "
                                                placeholder="Password"
                                                id="password"
                                            />
                                        </div>
                                        <div
                                            id="toggle-password"
                                            class="py-2 px-2 fixed-right align-items-center svg-orange"
                                            style="width: 30px"
                                            @click="togglePasswordVisibility"
                                        >
                                            <img
                                                :src="SvgEyeImage"
                                                class="svg-orange"
                                                alt=""
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- LINKS -->
                                <div class="form-row mt-1">
                                    <router-link
                                        to="/forgot-password"
                                        class="mb-2 inline-block text-base text-[#adadad] hover:text-primary hover:underline w-100 custom-link"
                                    >
                                        Forgot Password?
                                    </router-link>

                                    <router-link
                                        to="/register"
                                        class="mb-2 inline-block text-base text-[#adadad] hover:text-primary hover:underline w-100 custom-link"
                                    >
                                        Create an Account
                                    </router-link>
                                </div>

                                <div class="mt-5 d-flex justify-content-center">
                                    <button
                                        type="submit"
                                        :disabled="authStore.actionLoader"
                                        class="btnstd btnstd__orange"
                                    >
                                        Log In
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.form-row {
    display: flex;
    flex-wrap: wrap;
}

svg {
    fill: #f5a623;
}

img .svg-orange {
    fill: #f5a623;
    color: #f5a623;
}
</style>
