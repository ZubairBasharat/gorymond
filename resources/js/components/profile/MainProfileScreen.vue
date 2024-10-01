<script setup>
import { ref } from "vue";
import Header from "../layouts/Header.vue";
import avatar from "@/img/avatar.png?url";
import Profile from "./Profile.vue";
import Warranty from "./Warranty.vue";
import PrivacyPolicy from "./PrivacyPolicy.vue";
import editOrange from "@/img/svgicons/editorange.svg?url";
import { useAuthStore } from "../../stores/auth";
const authStore = useAuthStore();
const tabsList = [
    {
        title: "Profile",
        component: Profile,
    },
    {
        title: "Warranty",
        component: Warranty,
    },
    {
        title: "Privacy Policy",
        component: PrivacyPolicy,
    },
];
const activeTab = ref(tabsList[0]);
</script>
<template>
    <div>
        <Header />
        <div class="pt-5">
            <div class="profile-card mx-auto mt-5">
                <div class="text-center">
                    <img
                        :src="
                            authStore.userData?.avatar
                                ? authStore.userData?.avatar
                                : avatar
                        "
                        width="136px"
                        height="136px"
                        class="rounded-circle profile-avatar"
                        alt="avatar"
                    />
                    <label v-if="activeTab.title == 'Profile'">
                        <img
                            :src="editOrange"
                            alt=""
                            class="circle-icon border"
                        />
                        <input type="file" class="d-none" />
                    </label>
                </div>
                <div class="profile-card-title mt-4">
                    <h3 class="text-center">My Profile</h3>
                </div>
                <div class="profile-card-body">
                    <ul
                        class="list-unstyled tabs-links px-4 mb-5 d-flex align-items-center justify-content-between"
                    >
                        <li
                            v-for="list in tabsList"
                            :class="
                                list.title == activeTab.title
                                    ? 'text-orange'
                                    : ''
                            "
                        >
                            <span @click="activeTab = list">{{
                                list.title
                            }}</span>
                        </li>
                        <li>
                            <router-link
                                class="text-initial text-decoration-none"
                                to="/terms"
                            >
                                <span> Terms of Use </span>
                            </router-link>
                        </li>
                    </ul>
                    <activeTab.component />
                </div>
            </div>
        </div>
    </div>
</template>
