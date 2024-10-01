<script setup>
import { ref, watchEffect } from "vue";
import { useTimerStore } from "../stores/timer";
import SvgCountDownTimerImage from "@/img/svgicons/countdowntimers.svg?url";
import SvgAddNewImage from "@/img/svgicons/addnewtimer_normal.svg?url";
import CalendarIcon from "@/img/svgicons/calendar.svg?url";
import CancelIcon from "@/img/svgicons/cancel_normal.svg?url";
import saveIcon from "@/img/svgicons/save_normal.svg?url";

// import editIcon from "@/img/svgicons/edit.svg?url";
import SvgEditImage from "@/img/svgicons/edit.svg";
import TrashIcon from "@/img/svgicons/trashorange.svg?url";
import Reminder from "./Reminder.vue";
import ToneButton from "./ToneButton.vue";
import { usePlayerStore } from "../stores/player";
import { ConfirmDeleteModule } from "../constants/helpers";

const timerStore = useTimerStore();
const playerStore = usePlayerStore();
const toggleCountDown = ref(false);
const timerFetchCount = ref(0);
const formData = ref({
    name: "",
    duration: "",
    reminders: [],
});

watchEffect(() => {
    if (playerStore.player && timerFetchCount.value == 0) {
        timerStore.getTimers(playerStore.player.id);
        timerFetchCount.value = 1;
    }
    if (toggleCountDown.value === false) {
        formData.value = {
            name: "",
            duration: "",
            reminders: [],
        };
    }
});
const options = [
    { value: 1, text: "1 MIN" },
    { value: 2, text: "2 MIN" },
    { value: 3, text: "3 MIN" },
    { value: 4, text: "4 MIN" },
    { value: 5, text: "5 MIN" },
    { value: 10, text: "10 MIN" },
    { value: 15, text: "15 MIN" },
    { value: 20, text: "20 MIN" },
    { value: 30, text: "30 MIN" },
    { value: 45, text: "45 MIN" },
    { value: 60, text: "1 HR" },
    { value: 120, text: "2 HR" },
    { value: 240, text: "4 HR" },
];

const onSubmitForm = async () => {
    await timerStore
        .postTimers(formData.value, playerStore.player.id)
        .then(() => {
            toggleCountDown.value = false;
        });
};
const onConfirm = (item) => {
    timerStore.deleteTimer(playerStore.player.id, item.id);
};
const deleteItem = (item) => {
    ConfirmDeleteModule(onConfirm, item.title, item);
};
const showEdit = (list) => {
    Object.keys(formData.value).forEach((key) => {
        formData.value[key] = list[key];
    });
    formData.value["id"] = list.id;
    toggleCountDown.value = true;
};
</script>

<template>
    <div class="col-md-6 px-4">
        <div class="card card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="pe-2">
                        <img
                            :src="SvgCountDownTimerImage"
                            class="svg-orange"
                            alt=""
                        />
                    </div>
                    <h4 class="d-inline">Count Down Timers</h4>

                    <div class="ms-auto d-flex" v-if="!toggleCountDown">
                        <ToneButton toneType="Timer" />
                        <button
                            type="button"
                            class="btn_sm d-flex align-items-center justify-content-center"
                            @click="toggleCountDown = !toggleCountDown"
                        >
                            <img
                                :src="SvgAddNewImage"
                                class="svg-orange"
                                alt=""
                            />
                        </button>
                    </div>
                </div>

                <div class="mt-3" v-if="toggleCountDown">
                    <form @submit.prevent="onSubmitForm">
                        <input
                            type="text"
                            class="w-100 form-control"
                            placeholder="Timer name"
                            required="required"
                            minlength="3"
                            v-model="formData.name"
                        />
                        <div class="d-flex mt-3 align-items-center">
                            <span class="me-3">Set timer duration</span>
                            <select
                                class="form-select inline-form-select"
                                required="required"
                                v-model="formData.duration"
                                @change="
                                    (e) =>
                                        (formData.reminders =
                                            formData.reminders.filter(
                                                (x) => x < e.target.value
                                            ))
                                "
                            >
                                <option value="" selected disabled hidden>
                                    Select duration
                                </option>
                                <option
                                    v-for="list in options"
                                    :value="list.value"
                                >
                                    {{ list.text }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <b class="f-12"><small>Reminders:</small></b>
                        </div>
                        <Reminder
                            @update:reminders="
                                (val) => {
                                    formData.reminders = val;
                                }
                            "
                            :remindersData="formData.reminders"
                            :duration="formData.duration"
                        />
                        <div class="btn-group justify-content-end w-100">
                            <button
                                type="submit"
                                class="btn_sm d-flex align-items-center justify-content-center mt-3 me-3"
                                :disabled="timerStore.actionLoader"
                            >
                                <img :src="saveIcon" alt="" />
                            </button>
                            <button
                                type="button"
                                class="btn_sm ml-2 d-flex align-items-center justify-content-center mt-3"
                                @click="toggleCountDown = !toggleCountDown"
                                :disabled="timerStore.actionLoader"
                            >
                                <img :src="CancelIcon" alt="" />
                            </button>
                        </div>
                    </form>
                </div>
                <div
                    class="record-item w-100 mt-3"
                    v-if="!toggleCountDown && !timerStore.pageLoader"
                >
                    <div
                        v-for="list in timerStore.timers"
                        class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                    >
                        <div class="flex-1">
                            <img
                                :src="CalendarIcon"
                                alt="Calendar"
                                class="me-1"
                            />
                            <span class="align-middle"
                                >{{ list.name }}:
                                {{
                                    list.duration >= 60
                                        ? `${list.duration / 60} ${
                                              list.duration / 60 > 1
                                                  ? "hours"
                                                  : "hour"
                                          }`
                                        : `${list.duration}&nbsp;${
                                              list.duration > 1 ? "Mins" : "Min"
                                          }`
                                }}</span
                            >
                        </div>
                        <SvgEditImage
                            class="svg-orange-edit me-2"
                            @click="showEdit(list)"
                            v-if="playerStore.player?.id"
                        />

                        <!-- <button class="align-self-start p-0 me-2 bg-transparent border-0" style="line-height: 1.2"
                            @click="() => showEdit(list)">
                            <img class="svg-orange-edit" :src="editIcon" alt="Edit icon" height="15px"
                                v-if="playerStore.player?.id" />
                        </button>
 -->
                        <button
                            class="align-self-start p-0 bg-transparent border-0"
                            style="line-height: 1"
                            @click="() => deleteItem(list)"
                            :disabled="timerStore.actionLoader"
                        >
                            <img :src="TrashIcon" alt="Trash icon" />
                        </button>
                    </div>
                </div>
                <h6
                    class="text-warning"
                    v-if="
                        !timerStore.pageLoader &&
                        !toggleCountDown &&
                        timerStore.timers.length <= 0
                    "
                >
                    No record found
                </h6>
                <div
                    v-if="timerStore.pageLoader"
                    class="spinner-border text-warning"
                    role="status"
                ></div>
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

.svg-orange-edit {
    fill: #f5a623;
    color: #f5a623;
    cursor: pointer;
}
</style>
