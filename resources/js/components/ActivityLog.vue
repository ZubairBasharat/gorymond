<script setup>
// import { onMounted } from "vue";
// import { useAuthStore } from "../stores/auth";

// const authStore = useAuthStore();

import SvgSetColorImage from "@/img/svgicons/setcolor_normal.svg?url";
import SvgActivityLogImage from "@/img/svgicons/activityLog.svg?url";
import PhoneIcon from "@/img/svgicons/phone.svg?url";
// onMounted(async () => {
//   // await authStore.getUser();
//   // console.log(authStore.user);
// });
</script>

<template>
    <div class="col-md-6 px-4">
        <div class="card card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="pe-2">
                        <img :src="SvgActivityLogImage" class="svg-orange" alt="" />
                    </div>
                    <h4 class="d-inline">Activity Log</h4>

                    <div class="ms-auto">
                        <img :src="SvgSetColorImage" class="svg-orange cursor-pointer" alt=""
                            data-bs-toggle="dropdown" />
                        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-end text_sm">
                            <a href="#" class="dropdown-item">
                                Display Filter
                                <i class="fa fa-times float-end text-orange_main"></i></a>
                            <a href="#" class="dropdown-item">
                                <!-- <img :src="PhoneIcon" alt="" class="mr-1" /> -->
                                <span class="ms-2">Call logs</span>
                                <input type="checkbox" checked="checked" class="float-end" /></a><a href="#"
                                class="dropdown-item"><img src="/img/svgicons/textlogs_sm.svg" alt="" class="mr-1" />
                                <span class="ms-2">Text logs</span>
                                <input type="checkbox" checked="checked" class="float-end" /></a><a href="#"
                                class="dropdown-item"><img src="/img/svgicons/calendar.svg" alt="" class="mr-1" />
                                <span class="ms-2">Events</span>
                                <input type="checkbox" checked="checked" class="float-end" /></a><a href="#"
                                class="dropdown-item"><img src="/img/svgicons/clock.svg" alt="" class="mr-1" />
                                <span class="ms-2">Timer</span>
                                <input type="checkbox" checked="checked" class="float-end" /></a><a href="#"
                                class="dropdown-item"><img src="/img/svgicons/location.svg" alt="" class="mr-1" />
                                <span class="ms-2">Locations</span>
                                <input type="checkbox" checked="checked" class="float-end" /></a>
                        </div>
                    </div>
                </div>
                <p>This is some content for the activity Log component.</p>
            </div>
        </div>
    </div>
</template>

<style>
.svg-orange {
    fill: #f5a623;
    color: #f5a623;

    width: 28px;
    height: 30px;
}
</style>
