<script setup>
import { ref, onMounted, provide } from "vue";
import { useAuthStore } from "../stores/auth";
import MapAndAutoComplete from "./MapAndAutoComplete.vue";

import ActivityLog from "./ActivityLog.vue";
import CountdownTimers from "./CountdownTimers.vue";
import Events from "./Events.vue";

import SvgLocationImage from "@/img/svgicons/locationblue.svg?url";

import Header from "./layouts/Header.vue";

const authStore = useAuthStore();

const locationStatus = ref('Player Location');
provide('locationStatus', locationStatus);


onMounted(async () => {
    await authStore.getUser();
});
</script>

<template>
    <div>
        <div>
            <Header />
            <div class="container">
                <div class="row justify-content-center mx-0 p-4">
                    <!-- Activity Log -->
                    <ActivityLog />

                    <!-- Map -->
                    <div class="col-md-6 px-4">
                        <div class="card card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex pb-4">
                                    <div class="pe-2">
                                        <img :src="SvgLocationImage" class="svg-orange" alt="" />
                                    </div>
                                    <h4 class="d-inline">{{ locationStatus }}</h4>
                                </div>
                                <MapAndAutoComplete />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mx-0 p-4">
                    <!-- Count Down Timers -->
                    <CountdownTimers />

                    <!-- Events -->
                    <Events />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.svg-logo {
    width: 73px;
    height: 22px;
}

.svg-orange {
    fill: #f5a623;
    color: #f5a623;

    width: 28px;
    height: 30px;
}

.svg-small {
    width: 20px;
    height: 15px;
}
</style>
