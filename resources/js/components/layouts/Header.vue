<script setup>
import avatar from "@/img/avatar.png?url";
import SvgLogoImage from "@/img/svgicons/header.svg?url";
import editIcon from "@/img/svgicons/edit.svg?url";
import SvgSetImage from "@/img/svgicons/set.svg?url";
import SvgNotificationImage from "@/img/svgicons/notification.svg?url";
import SvgUserImage from "@/img/svgicons/user.svg?url";
import SvgConfigurationImage from "@/img/svgicons/user_sm.svg?url";
import SvgPasswordImage from "@/img/svgicons/password.svg?url";
import SvgLogoutImage from "@/img/svgicons/logout.svg?url";
import { onMounted } from "vue";
import { usePlayerStore } from "../../stores/player";
import { useAuthStore } from "../../stores/auth";
const playerStore = usePlayerStore();
const authStore = useAuthStore();
onMounted(() => {
    authStore.getUser();
    playerStore.getPlayers();
});
</script>
<template>
    <div class="main-header">
        <div class="container">
            <div class="row mx-0 p-4">
                <div class="col-md-4 px-4">
                    <router-link
                        :to="
                            playerStore.player?.id
                                ? `/players/configuration/${playerStore.player?.id}`
                                : '/player/create'
                        "
                        class="d-flex align-items-center text-decoration-none"
                        style="color: #212529"
                    >
                        <img
                            :src="
                                authStore.userData?.avatar
                                    ? authStore.userData?.avatar
                                    : avatar
                            "
                            alt="username"
                            width="45px"
                            height="45px"
                            class="rounded-circle me-2"
                        />
                        <h5
                            class="mb-0 pf-name me-2"
                            v-if="playerStore.player?.id"
                        >
                            {{ playerStore?.player?.first_name }}
                            {{ playerStore?.player?.last_name }}
                        </h5>
                        <span
                            type="button"
                            v-if="
                                playerStore.player ||
                                playerStore.pageLoader == false
                            "
                        >
                            <img
                                :src="editIcon"
                                height="18px"
                                alt="Edit icon"
                                v-if="playerStore.player?.id"
                            />
                            <small
                                class="text-decoration-underline text-primary"
                                v-else
                                >Add new player</small
                            >
                        </span>
                    </router-link>
                </div>
                <div class="col-md-4 text-center align-self-center">
                    <router-link to="/dashboard">
                        <img :src="SvgLogoImage" class="svg-logo" alt="" />
                    </router-link>
                </div>
                <div class="col-md-4 align-self-center">
                    <div class="d-flex justify-content-end">
                        <div>
                            <router-link to="/profile">
                                <img :src="SvgUserImage" class="" alt="" />
                            </router-link>
                        </div>
                        <div class="px-4">
                            <img :src="SvgNotificationImage" class="" alt="" />
                        </div>
                        <div type="button" data-bs-toggle="dropdown">
                            <img :src="SvgSetImage" class="" alt="" />
                        </div>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-item">Settings</div>

                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center"
                                    :href="
                                        playerStore.player?.id
                                            ? `/players/configuration/${playerStore.player?.id}`
                                            : '/player/create'
                                    "
                                >
                                    <div class="pe-2 d-inline-flex">
                                        <img
                                            :src="SvgConfigurationImage"
                                            class="svg-small pr-4"
                                            alt=""
                                        />
                                    </div>
                                    Player Config
                                </a>
                            </li>
                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center"
                                    href="#"
                                >
                                    <div class="pe-2 d-inline-flex">
                                        <img
                                            :src="SvgPasswordImage"
                                            class="svg-small"
                                            alt=""
                                        />
                                    </div>
                                    Security
                                </a>
                            </li>
                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center"
                                    href="#"
                                >
                                    <div class="pe-2 d-inline-flex">
                                        <img
                                            :src="SvgPasswordImage"
                                            class="svg-small"
                                            alt=""
                                        />
                                    </div>
                                    Warranty
                                </a>
                            </li>
                            <li>
                                <a
                                    class="dropdown-item d-flex align-items-center"
                                    href="#"
                                    @click.prevent="authStore.handleLogout"
                                >
                                    <div class="pe-2 d-inline-flex">
                                        <img
                                            :src="SvgLogoutImage"
                                            class="svg-small"
                                            alt=""
                                        />
                                    </div>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
