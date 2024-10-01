<script setup>
import { computed, defineProps, defineEmits } from "vue";
const emit = defineEmits(["update:reminders"]);
const props = defineProps(["remindersData", "duration"]);
const activeTime = computed(() => props.remindersData || []);
const duration = computed(() => props.duration || "");
const timerArray = [
    { title: "5 min", time: 5 },
    { title: "15 min", time: 15 },
    { title: "30 min", time: 30 },
    { title: "45 min", time: 45 },
    { title: "1 hr", time: 60 },
    { title: "2 hr", time: 120 },
];
const activelist = (val) => {
    const index = activeTime.value.findIndex((x) => x == val);
    if (index > -1) {
        emit(
            "update:reminders",
            activeTime.value.filter((x, i) => i !== index)
        );
    } else {
        emit("update:reminders", [...activeTime.value, val]);
    }
};
</script>
<template>
    <div class="row">
        <div v-for="list in timerArray" class="col-md-6 col-12 col-lg-4 mt-2">
            <button
                class="timer-selection w-100 text-uppercase"
                type="button"
                v-if="!duration"
                :class="
                    activeTime.findIndex((x) => x == list.time) > -1
                        ? 'active'
                        : ''
                "
                @click="() => activelist(list.time)"
            >
                {{ list.title }}
            </button>
            <button
                class="timer-selection w-100 text-uppercase"
                type="button"
                v-if="duration"
                :disabled="list.time >= duration"
                :class="
                    activeTime.findIndex((x) => x == list.time) > -1
                        ? 'active'
                        : ''
                "
                @click="
                    () => (list.time > duration ? false : activelist(list.time))
                "
            >
                {{ list.title }}
            </button>
        </div>
    </div>
</template>
