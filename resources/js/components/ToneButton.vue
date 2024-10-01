<script setup>
import SvgTimerToneImage from "@/img/svgicons/timertone_normal.svg?url";
import { BASE_URL } from "../constants/variables";

import { onMounted, ref, defineProps, watch } from "vue";
import { useTonesStore } from "../stores/tones";
import { usePlayerStore } from "../stores/player";
import { storeToRefs } from "pinia";
const props = defineProps(["toneType"]);
const toneStore = useTonesStore();
const playerStore = usePlayerStore();
const { player } = storeToRefs(playerStore);
const { playerTones } = storeToRefs(toneStore);
const requestCount = ref(0);
const fieldRef = ref({
    complete: "",
    reminder: "",
});


const activeToneIDs = ref({});

const curDropdownName = ref(`${props.toneType}Dropdown`);

const timerTones = [
    {
        title: "Reminder Tones",
        sectionId: `${props.toneType}Reminder`,
        situation_type: "Reminder",
    },
    {
        title: "Timer Done Tones",
        sectionId: `${props.toneType}Complete`,
        situation_type: "Complete",
    },
];
onMounted(() => {
    toneStore.getTones();

    timerTones.map((curTimer) => {
        activeToneIDs.value[curTimer.sectionId] = 0;
    });
});
watch(player, () => {
    if (player && requestCount.value == 0) {
        toneStore.getPlayerTones();
        requestCount.value = 1;
    }
});

watch(playerTones, () => {
    if (playerTones.value.length > 0) {
        const matchedEle = playerTones.value.filter(
            (x) => x.activity_type.toLowerCase() == props.toneType.toLowerCase()
        );
        if (matchedEle.length > 0) {
            timerTones.map((list) => {
                const current = matchedEle.find(
                    (x) =>
                        x.situation_type.toLowerCase() ==
                        list.situation_type.toLowerCase()
                );
                fieldRef.value[list.situation_type.toLowerCase()] = current.id;
            });
        }
    }
});


const handleToneClick = (timer, tone = null) => {
    console.log("timer", timer);
    console.log("tone", tone);


    if (tone === 0) {
        activeToneIDs.value[timer.sectionId] = 0;
    } else {
        activeToneIDs.value[timer.sectionId] = tone.id;
    }
    console.log("activeToneIDs", activeToneIDs.value);


    if (tone !== 0 && tone.id) {
        playAudio(`${BASE_URL}/storage/${tone.file}`);
    }
};

const handleToneChange = (tone) => {

    // 
    // console.log("tone", tone);
    const inputElement = document.querySelector(`input[value="${tone.id}"]`);

    // Focus and toggle the input
    inputElement.focus();
    inputElement.click();

    toneStore.updatePlayerTones({
        ...fieldRef.value,
        model: props.toneType,
    });

    playAudio(`${BASE_URL}/storage/${tone.file}`);
};

const playAudio = (filePath) => {
    const audioContext = new AudioContext();
    fetch(filePath)
        .then(response => response.arrayBuffer())
        .then(buffer => audioContext.decodeAudioData(buffer))
        .then(audioBuffer => {
            const audioSource = audioContext.createBufferSource();
            audioSource.buffer = audioBuffer;

            audioSource.connect(audioContext.destination);
            audioSource.start();

        })
        .catch(error => console.error('Error decoding audio:', error));
}


const handleDropdownOK = () => {
    document.getElementById(curDropdownName.value).classList.remove("show");
};


</script>

<template>
    <button type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
        class="btn_sm d-flex me-2 align-items-center justify-content-center">
        <img :src="SvgTimerToneImage" class="svg-orange mx-4" alt="" />
    </button>


    <div :id="curDropdownName" class="dropdown-menu dropdown-menu-end p-3" style="width: 400px;">
        <div class="row">
            <div class="col-md-6" v-for="timer in timerTones ">
                <h6>{{ timer.title }} </h6>

                <div class="mb-2 d-flex justify-content-start align-items-center tone-row"
                    @click="handleToneClick(timer, 0)">

                    <div :class="['toneRadio', 'me-2',
            { curToneActive: 0 === activeToneIDs[timer.sectionId] }]">

                    </div>
                    <span class="tone-name">
                        none
                    </span>
                    <!-- <input type="radio" :value="0" v-model="fieldRef[timer.situation_type.toLowerCase()]"
                    :disabled="toneStore.actionLoader">
                    <label for="{{ tone.id }}" @click="handleToneChange(tone)" class="ms-2">
                        none
                    </label> -->
                </div>

                <div v-for="tone in toneStore.tones" @click="handleToneClick(timer, tone)"
                    class="mb-2 d-flex justify-content-start align-items-center tone-row">

                    <div :class="['toneRadio', 'me-2',
            { curToneActive: tone.id === activeToneIDs[timer.sectionId] }]">



                    </div>
                    <span class="tone-name">
                        {{ tone.name }}
                        {{ timer.id }}
                    </span>

                    <!-- <input type="radio" :value="tone.id" v-model="fieldRef[timer.situation_type.toLowerCase()]"
                        :disabled="toneStore.actionLoader">
                    <label for="{{ tone.id }}" @click="handleToneChange(tone)" class="ms-2">
                        {{ tone.name }}
                    </label> -->
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-3">
            <button type="button" @click="handleDropdownOK" class="btn btn-sm btn-primary">OK</button>
        </div>
    </div>
</template>

<style scoped>
.btn-primary {
    --bs-btn-color: #fff;
    --bs-btn-bg: #3085d6;
}

.tone-row {
    cursor: pointer;
}

.tone-row:hover {
    background-color: #dde1e5;
}

.toneRadio {
    padding: 5px 5px;
    width: 12px;
    border-radius: 50%;
    border: solid 1px #f5a623;
    cursor: pointer;
    display: inline-block;
}

.tone-name {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.curToneActive {
    padding: 4px 4px;
    background-color: #f5a623;
    border: solid 2px #f5a623;
}

.dropdown-menu {
    box-shadow: 10px 10px 10px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}
</style>
