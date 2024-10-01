<template>
    <div>
        <div class="d-flex align-items-center h-100">

            <input class="me-4  py-2" type="text" id="location-name-container" @keyup="handleNameFieldTyping"
                :value="curPlayerLocationName" placeholder="Location Name" v-if="!playerMode" />

            <h5 class="me-4 py-2 cur-location playerLocationFullwidth" v-tippy="{
                    content: `${curPlayerLocationName}<br>${displayTime}`,
                    theme: 'tippy-goraymond',
                    delay: [1000, 500],
                    duration: [100, 200],
                    role: 'tooltip',

                    arrow: true,
                    animation: 'scale',
                    maxWidth: 'none',
                }" v-else>

                {{ locationDisplay }} <br>
                {{ timeDisplay }}
            </h5>

            <input type="text" id="autocomplete-container" class="playerAutoComplete" v-if="!playerMode"
                placeholder="Enter an address" />
            <input type="text" id="autocomplete-container" class="playerAutoCompleteTransparent" v-if="playerMode" />
        </div>
        <div id="showmap">
            <div id="map-container"></div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col">
                    <select id="fenceSelect" class="btn mt-2 text-orange btnstd btnstd__white radius-control"
                        @change="handleChangeFence" v-model="selectedZoomFenceIndex" v-if="editMode">
                        <option v-for="key in zoomIndexes" :value="key" :key="key">
                            {{ zoomOptions[key].label }}
                        </option>
                    </select>

                    <button v-else class="btn mt-2 text-orange btnstd radius-control" @click="handleAddLocation">
                        Add Location
                    </button>
                </div>

                <div class="col align-self-end zoom-controls d-flex justify-content-end">
                    <button type="button" class="btn btn-map-control text-orange m-2" @click="zoomOut">
                        <SvgMinusImage class="svg-orange" />
                    </button>
                    <button type="button" class="btn btn-map-control text-orange m-2" @click="zoomIn">
                        <SvgPlusImage class="svg-orange" />
                    </button>

                    <button v-if="!editMode" type="button"
                        class="btn btn-map-control player-location-btn text-orange m-2" @click="centerPlayerLocation"
                        async :disabled="editMode">
                        <SvgLocationCrosshairImage class="svg-orange" />
                    </button>
                </div>

                <div v-if="!editMode" class="d-flex align-items-center mb-0">
                    <SvgLocationImage class="svg-info-color me-2" />
                    <h5 class="mb-0">Locations</h5>
                </div>

                <div v-if="!editMode" class="row mt-2" v-for="(curLocation, idx) in  locations   ">
                    <div class="d-flex align-items-center align-content-center location-row" v-tippy="{
                    content: `${curLocation.address} <br>${radiusToLabel[curLocation.radius]}`,
                    theme: 'tippy-goraymond',
                    delay: [200, 100],
                    role: 'tooltip',
                    animation: 'scale',
                }" @click="handleLocationFocus(curLocation.id)">

                        <div class="location-label location-focus-pointer">
                            <div
                                :class="['locationRadio', 'me-2', { curLocationActive: curLocation.id === activeLocationID }]">
                            </div>

                            <span class="location-name">
                                {{ curLocation.name }}
                            </span>

                        </div>
                        <div class="location-edit">
                            <SvgEditImage class="svg-info-color me-2" @click="handleLocationEdit(curLocation.id)" />
                            <SvgTrashImage class="svg-info-color me-2" @click="handleLocationDelete(idx)" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4" v-if="editMode">
                <div class="col-6 d-flex align-items-center">
                    <SvgFenceImage class="svg-info-color me-2" />
                    <label for="fenceToggle" class="form-label me-2 mb-0">Fence</label>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" id="fenceToggle"
                            v-model="isFenceEnabled" @change="handleFenceToggle" />
                    </div>
                </div>
                <div class="col-6 d-flex align-items-center">
                    <SvgAlertsImage class="svg-info-color me-2" />
                    <label for="alertsToggle" class="form-label me-2 mb-0">Alerts</label>
                    <div class="form-check form-switch mb-0">

                        <input v-if="isFenceEnabled" class="form-check-input" type="checkbox" role="switch"
                            id="alertsToggle" v-model="isAlertsEnabled" @change="handleAlertsToggle" />
                        <input v-if="!isFenceEnabled" class="form-check-input" type="checkbox" role="switch"
                            id="alertsToggle" disabled v-model="isAlertsEnabled" @change="handleAlertsToggle" />
                    </div>
                </div>
            </div>
            <!-- save and cancel buttons -->
            <div class="row" v-if="editMode">
                <div class="col align-self-end zoom-controls d-flex justify-content-end">
                    <button type="button" class="btn text-orange m-2" @click="handleSaveLocation">
                        <SvgSaveImage class="svg-orange-color" />
                    </button>
                    <button type="button" class="btn text-orange m-2" @click="handleCancel">
                        <SvgCancelImage class="svg-orange-color" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { mapSettings } from "@/constants/mapSettings";

import { ref, onMounted, inject, computed } from "vue";
import { Loader } from "@googlemaps/js-api-loader";

import SvgLocationCrosshairImage from "@/img/svgicons/location-crosshairs.svg";
import SvgMinusImage from "@/img/svgicons/minus.svg";
import SvgPlusImage from "@/img/svgicons/plus.svg";
import SvgCancelImage from "@/img/svgicons/cancel_normal.svg";
import SvgSaveImage from "@/img/svgicons/save_normal.svg";
import SvgFenceImage from "@/img/svgicons/fence.svg";
import SvgAlertsImage from "@/img/svgicons/alerts.svg";
import SvgLocationImage from "@/img/svgicons/location.svg";
import SvgEditImage from "@/img/svgicons/edit.svg";
import SvgTrashImage from "@/img/svgicons/trashorange.svg";
import { usePlayerStore } from "../stores/player";

import { toast } from "../service/toastWrapper";
import Swal from "sweetalert2";

import moment from "moment";

// may not need this
import { useAuthStore } from "../stores/auth";
import { useRoute } from "vue-router";

const meta = import.meta.env;

// these may go away - using the api marker instead
const locationFill = meta.VITE_LOCATION_FILL || "#0d3817";
const locationFillOpacity = meta.VITE_LOCATION_FILL_OPACITY || "0.25";
const locationStroke = meta.VITE_LOCATION_STROKE || "#0d37";
const locationStrokeOpacity = meta.VITE_LOCATION_STROKE_OPACITY || "0.25";
const locationStrokeWidth = meta.VITE_LOCATION_STROKE_WIDTH || 2;

const locationUseAPIMarker = false;

const locationCircleStrokeWeight = meta.VITE_LOCATION_CIRCLE_STROKE_WEIGHT || 3;
const locationCircleFillColor = meta.VITE_LOCATION_CIRCLE_FILL || "#0d3817";
const locationActiveCircleFillColor = meta.VITE_LOCATION_ACTIVE_CIRCLE_FILL || "#5d854d";
const locationCircleFillOpacity = meta.VITE_LOCATION_CIRCLE_FILL_OPACITY || "0.5"
const locationCircleStroke = meta.VITE_LOCATION_CIRCLE_STROKE || "#0d3817";
const locationCircleStrokeOpacity = meta.VITE_LOCATION_CIRCLE_STROKE_OPACITY || "0.5"

const locationStatus = inject("locationStatus");

const playerFill = meta.VITE_PLAYER_FILL || "#ffcccc";
const playerFillOpacity = meta.VITE_PLAYER_FILL_OPACITY || "0.25";
const playerStroke = meta.VITE_PLAYER_STROKE || "#0d37";
const playerStrokeOpacity = meta.VITE_PLAYER_STROKE_OPACITY || "0.25";
const playerStrokeWidth = meta.VITE_PLAYER_STROKE_WIDTH || 2;

const playerCircleStrokeWidth = meta.VITE_PLAYER_CIRCLE_STROKE_WEIGHT || 3;
const playerCircleFillColor = meta.VITE_PLAYER_CIRCLE_FILL || "#0d3817";
const playerCircleFillOpacity = meta.VITE_PLAYER_CIRCLE_FILL_OPACITY || "0.5"
const playerCircleStroke = meta.VITE_PLAYER_CIRCLE_STROKE || "#0d3817";
const playerCircleStrokeOpacity = meta.VITE_LOCATION_CIRCLE_STROKE_OPACITY || "0.5"

const submitButtonColor = meta.VITE_BUTTON_PRIMARY_SUBMIT || "#ff9e27";
const cancelButtonColor = meta.VITE_BUTTON_CANCEL || "#6e7881";

const playerZoomLevel = +meta.VITE_PLAYER_ZOOM_LEVEL || 15;

const usePlayerCircle = meta.VITE_USE_PLAYER_CIRCLE === "true" || false;

const defaultRadius = meta.VITE_DEFAULT_RADIUS || 100;

const defaultFence = true;
const defaultAlerts = true;

// for more flexibility, we define the SVGs here with a template literal
const locationSVG = `
<svg width="24px" height="30px" viewBox="0 0 24 30" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
  <title>E6120FD7-12D1-41BD-B75F-B739123A38F7-669-0000C902CBAA33F2@1x</title>

// these may go away - using the api marker instead
  <desc>Created with sketchtool.</desc>
  <defs></defs>
  <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <g id="Style-guide" transform="translate(-784.000000, -1153.000000)" stroke="${locationStroke}" stroke-width="${locationStrokeWidth}" stroke-opacity="${locationStrokeOpacity}">
      <g  transform="translate(785.000000, 1154.000000)">
        <path fill="${locationFill}" fill-opacity="${locationFillOpacity}" stroke="${locationStroke}" d="M20.2223297,16.0368345 C19.2496108,17.4547848 18.2744347,18.8712363 17.297039,20.2863763 C15.7420284,22.5378683 14.1869385,24.7893604 12.6237237,27.0358315 C12.4661816,27.2622959 12.2821247,27.4886478 12.0603371,27.6557609 C11.2979914,28.2303149 10.1777972,28.0680729 9.61524283,27.3002148 C9.08185852,26.5721491 8.57784245,25.8248241 8.06486927,25.0835317 C6.17900023,22.3582031 4.30034444,19.6284156 2.40650912,16.9080329 C1.86234457,16.1263112 1.29677806,15.3611509 0.90096077,14.4958352 C-1.8668274,8.4452946 2.01184119,1.46813857 8.85309539,0.199128739 C14.6935703,-0.88429571 20.3376625,2.5551226 21.7158692,8.03942728 C21.9104289,8.81373011 21.9933812,9.60125959 22,10.3963204 Z"/>
      </g>
    </g>
  </g>
</svg>
`;

const playerSVG = `
<svg width="45" height="45" viewBox="0 0 35 35" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
  <title>E6120FD7-12D1-41BD-B75F-B739123A38F7-669-0000C902CBAA33F2@1x</title>
  <desc>Created with sketchtool.</desc>
  <defs></defs>
  <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
    <g id="Style-guide" transform="translate(-784.000000, -1153.000000)" stroke="${playerStroke}" stroke-width="${playerStrokeWidth}" stroke-opacity="${playerStrokeOpacity}">
      <g  transform="translate(785.000000, 1154.000000)">
        <path fill="${playerFill}" fill-opacity="${playerFillOpacity}" stroke="${playerStroke}" d="M20.2223297,16.0368345 C19.2496108,17.4547848 18.2744347,18.8712363 17.297039,20.2863763 C15.7420284,22.5378683 14.1869385,24.7893604 12.6237237,27.0358315 C12.4661816,27.2622959 12.2821247,27.4886478 12.0603371,27.6557609 C11.2979914,28.2303149 10.1777972,28.0680729 9.61524283,27.3002148 C9.08185852,26.5721491 8.57784245,25.8248241 8.06486927,25.0835317 C6.17900023,22.3582031 4.30034444,19.6284156 2.40650912,16.9080329 C1.86234457,16.1263112 1.29677806,15.3611509 0.90096077,14.4958352 C-1.8668274,8.4452946 2.01184119,1.46813857 8.85309539,0.199128739 C14.6935703,-0.88429571 20.3376625,2.5551226 21.7158692,8.03942728 C21.9104289,8.81373011 21.9933812,9.60125959 22,10.3963204 Z"/>
      </g>
    </g>
  </g>
</svg>
`;


const router = useRoute();
const playerStore = usePlayerStore();

const apiKey = meta.VITE_GOOGLE_MAPS_API_KEY;
const address = ref("");
const map = ref(null);

const geoCode = ref(null);

const playerCircle = ref(null);
const playerMarker = ref(null);
const FEET_TO_METERS_CONVERSION = 0.3048;

const curPlayerLocationName = ref("");
const curPlayerLocation = ref(null);
const selectedFenceIndex = ref("");
const selectedZoomFenceIndex = ref("zoom500");
const editMode = ref(false);
const playerMode = ref(false);
const editExistingMode = ref(false);

const isFenceEnabled = ref(true);
const isAlertsEnabled = ref(true);

const currentLocationID = ref(null);
const currentEditLocationID = ref(null);

const addingNewLocation = ref(false);

const curRadius = ref(defaultRadius);
let placesService = null;

const placeNamePreserve = ref(true)
const nameFieldTyping = ref(false);
const initialPreserve = ref(false);

const curMapZoom = ref(10);

const locations = ref([]);
const placeName = ref(null);

const lastClickedLat = ref(null);
const lastClickedLng = ref(null);

const inDeleteMode = ref(false);
const activeLocationID = ref(0);
const previousFocusID = ref(0);

const displayTime = ref("");
const dummyTrigger = ref(0); // Dummy reactive variable

let intervalId = null;
let pollIntervalId = null;

const locationDisplay = computed(() => {
    if (activeLocationID.value !== 0 || playerMode.value) {
        const addressRegex = /,\s*\w{2}\s*(?:\d{5}(?:-\d{4})?\s*)?,\s*USA$/;
        const regexpResult = curPlayerLocationName.value.replace(addressRegex, '');

        const segments = regexpResult.split(",");
        let locationStr = '';

        if (segments.length > 1) {
            segments.pop();
        }
        const trimmedAddress = segments.join(", ");

        // loop through the locations to see if the player is within any of them
        const playerInTheseLocations = [];
        allIndicators.forEach((indicator) => {
            if (indicator.status.playerIsWithin) {
                playerInTheseLocations.push(indicator.status.locationName);
            }
        });


        if (playerInTheseLocations.length > 0) {
            let beginParen = "(";
            let endParen = ")";
            if (playerInTheseLocations.length === 1) {
                beginParen = "";
                endParen = "";
            }
            locationStr = `${beginParen}${playerInTheseLocations.join(", ")}${endParen}`;
        }

        if (playerMode.value) {
            if (locationStr.length > 0) {
                return `${playerStore.player?.first_name} was at ${locationStr}`;
            } else {
                return `${playerStore.player?.first_name} was at ${trimmedAddress}`;
            }
            return `${playerStore.player?.first_name} was at ${trimmedAddress} ${locationStr}`;
        } else {
            return `Location: ${trimmedAddress} `;
        }
    } else {
        return "No Location Selected";
    }
});


const updateTime = () => {
    dummyTrigger.value++;
};


const timeDisplay = computed(() => {
    dummyTrigger.value;

    if (activeLocationID.value !== 0 || playerMode.value) {
        if (playerMode.value) {
            const timestamp = playerStore.player?.location_updated_at;
            const momentDate = moment(timestamp * 1000);

            displayTime.value = `at ${momentDate.format("dddd, MMMM Do YYYY, h:mm:ss a")}`;

            const diff = moment().diff(momentDate);
            let duration = moment.duration(diff);
            const seconds = duration.seconds();
            const minutes = duration.minutes();
            const hours = duration.hours();
            const days = duration.days();

            let timeAgo;
            let timeStack = [];

            const years = duration.years();
            if (years) {
                timeStack.push(`${years} ${years === 1 ? 'year' : 'years'} `)

                duration.subtract(years, 'years');
            }

            const months = duration.months();
            if (months) {
                timeStack.push(`${months} ${months === 1 ? 'month' : 'months'}`)
                duration.subtract(months, 'months');
            }

            if (timeStack.length < 2) {
                const weeks = duration.weeks();
                if (weeks) {
                    timeStack.push(`${weeks} ${weeks === 1 ? 'week' : 'weeks'}`)
                    duration.subtract(weeks, 'weeks');
                }
            }

            if (timeStack.length < 2) {
                const days = duration.days();
                if (days) {
                    timeStack.push(`${days} ${days === 1 ? 'day' : 'days'}`)
                    duration.subtract(days, 'days');
                }
            }

            if (timeStack.length < 2) {
                const hours = duration.hours();
                if (hours) {
                    timeStack.push(`${hours} ${hours === 1 ? 'hour' : 'hours'}`)
                    duration.subtract(hours, 'hours');
                }
            }

            if (timeStack.length < 2) {
                const minutes = duration.minutes();
                if (minutes) {
                    timeStack.push(`${minutes} ${minutes === 1 ? 'minute' : 'minutes'}`)
                    duration.subtract(minutes, 'minutes');
                }
            }


            if (timeStack.length < 2) {
                const seconds = duration.seconds();
                if (seconds) {
                    timeStack.push(`${seconds} ${seconds === 1 ? 'second' : 'seconds'}`)
                    duration.subtract(seconds, 'seconds');
                }
            }

            timeAgo = timeStack.join(", ");

            return `${timeAgo} ago`;
        } else {
            return `recently`;
        }
    } else {
        return "";
    }
});

let allIndicators = [];

let undoLocationData = {};

const zoomIndexes = [
    "zoom100",
    "zoom500",
    "zoom1000",
    "zoom5280",
    "zoom26400",
];

const radiusToZoomLevel = {
    100: 19,
    500: 17,
    1000: 16,
    5280: 13,
    26400: 11,
};

const zoomOptions = {
    zoom100: {
        feet: 100,
        label: "100 FT",
        zoomLevel: 19,
    },
    zoom500: {
        feet: 500,
        label: "500 FT",
        zoomLevel: 17,
    },
    zoom1000: {
        feet: 1000,
        label: "1000 FT",
        zoomLevel: 16,
    },
    zoomOneMile: {
        feet: 5280,
        label: "1 MILE",
        zoomLevel: 13,
    },
    zoomFiveMiles: {
        feet: 26400,
        label: "5 MILES",
        zoomLevel: 11,
    },

    // extras for select menu
    zoom5280: {
        feet: 5280,
        label: "1 MILE",
        zoomLevel: 13,
    },
    zoom26400: {
        feet: 26400,
        label: "5 MILES",
        zoomLevel: 11,
    },
};


const radiusToLabel = {
    100: "100 Feet",
    500: "500 Feet",
    1000: "1000 Feet",
    5280: "1 Mile",
    26400: "5 Miles",
};


function playerPollInterval() {
    // Clear any existing interval
    clearInterval(pollIntervalId);

    pollIntervalId = setInterval(() => {
        console.log("Polling for player location...");
        checkPlayerMoved();
    }, 10000);
}


const confirmDeleteLocation = (onConfirm, title, data) => {
    Swal.fire({
        icon: "warning",
        text: `Delete location "${title}" ? `,
        showCancelButton: true,
        confirmButtonText: "Delete",
        confirmButtonColor: submitButtonColor,
        allowOutsideClick: () => !Swal.isLoading(),
    }).then(async (result) => {
        if (result.isConfirmed) {
            if (data) {
                onConfirm(data);
            } else {
                onConfirm();
            }
        }
    });
};


const zoomIn = () => {
    map.value.setZoom(map.value.getZoom() + 1);
    curMapZoom.value = map.value.getZoom();
};

const zoomOut = () => {
    map.value.setZoom(map.value.getZoom() - 1);
    curMapZoom.value = map.value.getZoom();
};

const handleAddLocation = async () => {
    const addressField = document.getElementById("autocomplete-container");

    playerMode.value = false;
    isFenceEnabled.value = true;
    nameFieldTyping.value = false;
    initialPreserve.value = false;
    addingNewLocation.value = true;

    let newLat, newLng;

    if (lastClickedLat.value && lastClickedLng.value) {
        newLat = lastClickedLat.value;
        newLng = lastClickedLng.value;
    } else {
        newLat = +map.value.getCenter().lat();
        newLng = +map.value.getCenter().lng();
    }

    const location = {
        lat: newLat,
        lng: newLng
    };


    // may not always need this.. could have input address as an autocomplete
    const geoRes = await initialGeoCode(newLat, newLng);
    address.value = geoRes;

    // update name and address input fields
    const shortName = updateNameAndAddressFields(geoRes);

    let saveName = address.value;
    if (shortName.length > 0) {
        saveName = shortName;
    }


    const id = await saveNewLocation(saveName, address.value, newLat, newLng, defaultFence, defaultAlerts, defaultRadius);
    editModeOn();
    addingNewLocation.value = true;
    placeNamePreserve.value = false;
    nameFieldTyping.value = false;

    handleLocationFocus(id);
    handleLocationEdit(id);
};

const saveNewLocation = async (name, address, lat, lng, fence, alerts, radius) => {

    const saveData = {
        mode: "save",
        name: name, // || 'fake name',
        address: address, // || 'fake address',
        latitude: lat,
        longitude: lng,

        fence: true,
        alerts: true,

        radius: radius,
        custom: true,
        zoom_level: curMapZoom.value,
        player_id: playerStore.player?.id,
    };

    await playerStore.handleSaveLocation(saveData);
    const saveRes = playerStore.allLocations[playerStore.allLocations.length - 1];

    updateAddressFromCoords(lat, lng);
    updateNameAndAddressFields(address);

    // make a location entry
    const newLocation = {
        id: saveRes.id,
        name: address,
        address: address,
        latitude: lat,
        longitude: lng,
        fence: fence,
        alerts: alerts,
        radius: radius,
        custom: true,
        zoom_level: curMapZoom.value,
    };

    undoLocationData = {
        name: address,
        address: address,
        latitude: lat,
        longitude: lng,
        fence: true,
        alerts: true,
        radius: defaultRadius,
        custom: true,
        zoom_level: curMapZoom.value,
    };

    // push the new location
    locations.value.push(newLocation);

    // make a new indicator
    allIndicators.push(addIndicator(newLocation));

    // reduce the number of these...
    activeLocationID.value = newLocation.id;
    currentEditLocationID.value = newLocation.id;
    currentLocationID.value = newLocation.id;

    curPlayerLocationName.value = address;

    return newLocation.id;
};

const editModeOn = () => {
    editMode.value = true;
    initialPreserve.value = true;
    placeNamePreserve.value = true;
    setSpecificMarkerDraggable(activeLocationID.value, true);

    if (addingNewLocation.value === true) {
        locationStatus.value = "Adding Location";;

    } else {
        locationStatus.value = "Editing Location";
    }

}

const editModeOff = () => {
    editMode.value = false;
    initialPreserve.value = false;
    nameFieldTyping.value = false;
    setMarkersUnDraggable();
    locationStatus.value = "Player Location";
}

const handleCancel = () => {
    editModeOff();

    // reset the values
    curPlayerLocationName.value = undoLocationData.name;
    address.value = undoLocationData.address;
    isFenceEnabled.value = undoLocationData.fence ? 'true' : '';
    curRadius.value = undoLocationData.radius;
    setFence(undoLocationData.zoom_index);

    // if fence is false, alerts should be false as well
    if (isFenceEnabled.value === 'false') {
        isAlertsEnabled.value = 'false';
    } else {
        isAlertsEnabled.value = undoLocationData.alerts ? 'true' : '';
    }

    map.value.setCenter({ lat: +undoLocationData.latitude, lng: +undoLocationData.longitude });
    map.value.setZoom(undoLocationData.zoom_level);

    // get indicator data
    const indicator = allIndicators.find((i) => i.id === activeLocationID.value);

    // update the marker position
    updateMarkerPosition(indicator.marker, +undoLocationData.latitude, +undoLocationData.longitude);
    // update the position of the circle
    indicator.circle.setCenter({ lat: +undoLocationData.latitude, lng: +undoLocationData.longitude });

    if (addingNewLocation.value === true) {
        handleLocationDelete(locations.value.length - 1);
    }

    addingNewLocation.value = false;
    checkAllLocationsForPlayer();
    handleLocationFocus(activeLocationID.value);
};

const setFence = (level) => {

    console.log("setFence", level);
    console.log("currentEditLocationID", currentEditLocationID.value);

    if (currentEditLocationID.value) {
        const zoomOption = zoomOptions[level];

        selectedFenceIndex.value = level;
        map.value.setZoom(zoomOption.zoomLevel);

        changeSelectedFence(selectedFenceIndex.value);

        const indicator = allIndicators.find((i) => i.id === currentEditLocationID.value);
        indicator.circle.setOptions({ strokeWeight: isFenceEnabled.value ? locationCircleStrokeWeight : 0 }); // Update strokeWeight to 4
        // Conversion to meters
        const radiusInMeters = zoomOption.feet * FEET_TO_METERS_CONVERSION;
        indicator.circle.setRadius(radiusInMeters);
        map.value.setCenter(indicator.marker.getPosition());
    }
};

const changeSelectedFence = (newIndex) => {
    const selectedFenceElement = document.getElementById("fenceSelect");

    // update the selected index
    selectedFenceElement.selectedIndex = zoomIndexes.indexOf(newIndex);
};


const handleFenceToggle = () => {
    if (currentEditLocationID.value) {
        const indicator = allIndicators.find((i) => i.id === currentEditLocationID.value);
        indicator.circle.setOptions({ strokeWeight: isFenceEnabled.value ? 3 : 0 }); // Update strokeWeight to 4
    }

    if (isFenceEnabled.value === false) {
        isAlertsEnabled.value = false
    }
};

const handleAlertsToggle = () => {
    console.log("Alerts toggle changed!", isAlertsEnabled.value);
};

const handleSaveLocation = async () => {

    addingNewLocation.value = false;
    const saveOrUpdateData = {
        mode: "save",
        name: curPlayerLocationName.value || 'fake name',
        address: address.value,
        latitude: map.value.getCenter().lat(),
        longitude: map.value.getCenter().lng(),
        fence: isFenceEnabled.value || false,
        alerts: isAlertsEnabled.value || false,
        radius: curRadius.value,
        custom: true,
        zoom_level: curMapZoom.value,
        player_id: playerStore.player?.id,
    };

    if (editExistingMode.value) {
        saveOrUpdateData.mode = "update";
        saveOrUpdateData.location_id = currentLocationID.value;

        // this may revert to handleEditLocation
        const editRes = await playerStore.handleSaveLocation(saveOrUpdateData);
    } else {
        console.log("Save location clicked!");

        const saveRes = await playerStore.handleSaveLocation(saveOrUpdateData);

        // need to delete the temporary indicator
        const indicator = allIndicators.find((i) => i.id === currentEditLocationID.value);
        indicator.deleteIndicator();

        // we need the last element in the array
        const mostRecent = playerStore.allLocations[playerStore.allLocations.length - 1]

        // need to move this.. we need the new location id first...
        allIndicators.push(addIndicator(mostRecent));
    }
    locations.value = [...playerStore.allLocations];

    // cancel edit mode
    editModeOff();
    addingNewLocation.value = false;
    editExistingMode.value = false;
};

const findLocationById = (id) => {
    return locations.value.find((loc) => loc.id === id);
};

const findIndexFromId = (id) => {
    return locations.value.findIndex((loc) => loc.id === id);
};

const findPlayerWithinLocations = () => {
    //  return locations.value.find((loc) => loc.id === playerStore.player?.location_id);
};


const handleLocationEdit = (id) => {
    inDeleteMode.value = false;
    playerMode.value = false;

    const curLocation = findLocationById(id);
    const curLocationIndex = findIndexFromId(id);

    const zoomIndex = zoomIndexes.find((z) => zoomOptions[z].feet === curLocation.radius);

    // selectedFenceIndex.value = zoomIndex;
    selectedZoomFenceIndex.value = zoomIndex;

    currentEditLocationID.value = id;
    setMarkersUnDraggable();
    setSpecificMarkerDraggable(id, true);

    currentLocationID.value = id;

    if (currentLocationID.value !== activeLocationID.value) {
        handleLocationFocus(currentLocationID.value);
    }

    const ac = document.getElementById("autocomplete-container");

    curPlayerLocationName.value = curLocation.name;
    ac.value = curLocation.address;

    editModeOn();
    placeNamePreserve.value = true;
    editExistingMode.value = true;

    isAlertsEnabled.value = curLocation.alerts ? 'true' : '';
    isFenceEnabled.value = curLocation.fence ? 'true' : '';

    undoLocationData = {
        name: curLocation.name,
        address: curLocation.address,
        latitude: curLocation.latitude,
        longitude: curLocation.longitude,
        fence: curLocation.fence,
        alerts: curLocation.alerts,
        radius: curLocation.radius,
        custom: true,
        zoom_level: curLocation.zoom_level,
        zoom_index: zoomIndex,

        // need to save current indicator
        indicator: allIndicators.find((i) => i.locationID === id)
    }
};

const handleNameFieldTyping = (event) => {
    if (event.target.value.length == 0) {
        nameFieldTyping.value = false;
        placeNamePreserve.value = false;
        addingNewLocation.value = false;
        initialPreserve.value = false;
    } else {
        nameFieldTyping.value = true;
        placeNamePreserve.value = true;
        initialPreserve.value = true;
    }

    curPlayerLocationName.value = event.target.value;
};


const handleLocationDelete = async (index) => {
    const locationName = locations.value[index].name;

    confirmDeleteLocation(doLocationDelete, locationName, index);
}


const doLocationDelete = async (index) => {
    inDeleteMode.value = true;
    setMarkersUnDraggable();

    const whichLocation = locations.value[index].id;

    // remove the indicator for the location
    const indicator = allIndicators.find((i) => i.id === whichLocation);
    console.log("indicator", indicator);
    indicator.deleteIndicator();

    allIndicators.splice(index, 1);
    // locations.value.splice(index, 1);
    // cancel edit mode
    editMode.value = false;

    if (whichLocation === activeLocationID.value) {
        activeLocationID.value = 0;
    }

    // need to delete the location for db
    await playerStore.handleDeleteLocation({
        player_id: playerStore.player?.id,
        location_id: whichLocation
    });

    locations.value = null;
    await playerStore.getLocations(playerStore.player?.id);
    locations.value = [...playerStore.allLocations];

    console.log("locations", locations.value);

    console.log("whichLocation !== previousFocusID.value", whichLocation, previousFocusID.value)

    console.log("playerMode.value", playerMode.value);


    // this should probably be "was in player mode"
    if (playerMode.value) {
        centerPlayerLocation()
    } else {
        if (whichLocation !== previousFocusID.value) {
            handleLocationFocus(previousFocusID.value);
        }
    }
};

const handleLocationFocus = (curFocusID) => {

    // squirrel away the previous focus
    previousFocusID.value = activeLocationID.value;

    if (inDeleteMode.value) {
        inDeleteMode.value = false;
    }
    playerMode.value = false;
    const curLocation = findLocationById(curFocusID);

    if (!curLocation) {
        console.log("Location not found!");

        // go to default view
        return;
    }

    activeLocationID.value = curLocation.id;
    const curCoords = { lat: curLocation.latitude, lng: curLocation.longitude };
    const ac = document.getElementById("autocomplete-container");

    curMapZoom.value = radiusToZoomLevel[curLocation.radius] || 15;

    // need to update the radius selection
    const zoomOption = zoomOptions[`zoom${curLocation.radius} `];
    selectedFenceIndex.value = `zoom${curLocation.radius} `;

    console.log("selectedFenceIndex", selectedFenceIndex.value);

    curPlayerLocationName.value = curLocation.name;
    if (curPlayerLocationName.value.length > 0) {
        placeNamePreserve.value = true;
    }

    ac.value = curLocation.address;
    isFenceEnabled.value = curLocation.fence ? 'true' : '';

    if (isFenceEnabled.value === 'false') {
        isAlertsEnabled.value = 'false';
    } else {
        isAlertsEnabled.value = curLocation.alerts ? 'true' : '';
    }

    updateMapCenter(+curCoords.lat, +curCoords.lng);
};

const updateMapCenter = (newLat, newLng) => {
    if (map.value) {
        map.value.setZoom(curMapZoom.value);
        map.value.setCenter({ lat: newLat, lng: newLng });

        curMapZoom.value = map.value.getZoom();
    }
};

const handleChangeFence = (event, id) => {
    const zoomIndex = event.target.value;

    setFence(zoomIndex);
    curRadius.value = zoomOptions[zoomIndex].feet;

    const curLocationIndex = findIndexFromId(id);
    const curLocation = locations.value[curLocationIndex];

    if (curLocation) {
        curLocation.radius = curRadius.value;
        locations.value[curLocationIndex] = curLocation;
    }
};


const updateNameAndAddressFields = (fullAddress, usePlacename = '') => {
    const nameField = document.getElementById("location-name-container");
    const addressField = document.getElementById("autocomplete-container");

    let shortName = '';

    if (usePlacename.length > 0) {
        shortName = usePlacename;
    } else {
        shortName = fullAddress.split(",")[0];
    }

    if (nameField && addressField) {
        if (placeNamePreserve.value === false ||
            nameFieldTyping.value === false) {
            if (shortName.length > 0) {
                nameField.value = shortName;
                curPlayerLocationName.value = shortName;
            }
        }

        addingNewLocation.value = false;

        if (fullAddress.length > 0) {
            addressField.value = fullAddress;
            address.value = fullAddress;
        }
    }

    return shortName;
};

// may delete this
const showDefaultView = () => {
    // no curLocation
    curPlayerLocationName.value = "";
    curPlayerLocation.value = null;

    // no edit mode
    editMode.value = false;
    editExistingMode.value = false;
    inDeleteMode.value = false;

    // set map and zoom to default
    map.value.setCenter({ lat: +mapSettings.defaultLat, lng: +mapSettings.defaultLon });
    map.value.setZoom(13);
};


function addIndicator(curLocation) {

    const iconData = {
        scaledSize: new google.maps.Size(60, 30),
    };

    if (!locationUseAPIMarker) {
        iconData.url = 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(locationSVG);
        iconData.fillColor = "#FF9E27";
        iconData.strokeColor = "#FF0000";
    }

    const coords = {
        lat: +curLocation.latitude,
        lng: +curLocation.longitude,
        radius: +curLocation.radius
    };

    // Create the marker
    const marker = new google.maps.Marker({
        position: {
            lat: +curLocation.latitude,
            lng: +curLocation.longitude,
        },
        map: map.value, // Associate the marker with your map object
        title: curLocation.name,
        draggable: false,
        locationID: curLocation.id,

        // icon: iconData,
        optimized: false
    });

    // Create the circle
    const circle = new google.maps.Circle({
        // strokeColor: "#f5a623",
        strokeColor: locationCircleStroke,
        strokeOpacity: locationCircleStrokeOpacity,
        strokeWeight: curLocation.fence ? locationCircleStrokeWeight : 0,
        fillColor: locationCircleFillColor,
        fillOpacity: locationCircleFillOpacity,
        clickable: false,
        map: map.value, // Associate the circle with the same map as the marker
        center: {
            lat: +curLocation.latitude, // Center the circle on the marker position
            lng: +curLocation.longitude,
        },
        radius: +curLocation.radius * FEET_TO_METERS_CONVERSION, // Radius in meters
    });

    // Click Callback
    marker.addListener('click', () => {
        if (!editMode.value) {
            handleLocationFocus(curLocation.id);
        }
    });

    // Dragend Callback, update position of circle
    marker.addListener('dragend', (event) => {
        const newLat = event.latLng.lat();
        const newLng = event.latLng.lng();
        circle.setCenter({ lat: newLat, lng: newLng });
        map.value.setCenter({ lat: newLat, lng: newLng });

        updateAddressFromCoords(newLat, newLng);

        handleEditClickOrDrag(event);

        console.log("id", curLocation.id);
        // checkAllLocationsForPlayer();
    });

    const id = curLocation.id;

    const deleteIndicator = () => {
        marker.setMap(null);
        circle.setMap(null);
    };

    const status = {
        playerIsWithin: false,
        locationName: curLocation.name,
    };

    return { id, marker, circle, coords, status, deleteIndicator };
}


function updateAddressFromCoords(lat, lng) {
    geoCode.value.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK') {
            if (results[0]) {
                console.log("results", results);
                const formattedAddress = results[0].formatted_address;
                updateNameAndAddressFields(address.value)
            } else {
                console.log("No results found.");
            }
        } else {
            console.error("Geocoder failed:", status);
        }
    });
}


function extractAddress(addressComponents) {
    let street = '';
    let city = '';
    let state = '';
    let zip = '';

    addressComponents.forEach(component => {
        const types = component.types;
        if (types.includes('street_number')) {
            street += component.long_name + ' ';
        } else if (types.includes('route')) {
            street += component.long_name;
        } else if (types.includes('locality')) {
            city = component.long_name;
        } else if (types.includes('administrative_area_level_1')) {
            state = component.short_name;
        } else if (types.includes('postal_code')) {
            zip = component.long_name;
        }
    });

    return `${street}, ${city}`;
    // Determine address format based on available components
    // if (street && city && state && zip) {
    //     return `${street}, ${city}, ${state} ${zip}`;
    // } else if (street && city) {
    //     return `${street}, ${city}`;
    // } else {
    //     // Handle cases with insufficient address information
    //     return 'Address incomplete';
    // }
}

const handlePlayerWthin = (indicator, playerWithinBoolean) => {

    indicator.status.playerIsWithin = playerWithinBoolean;

    if (playerWithinBoolean) {
        indicator.circle.setOptions({ fillColor: locationActiveCircleFillColor });
    } else {
        indicator.circle.setOptions({ fillColor: locationCircleFillColor });
    }
};


const checkEditForPlayer = (indicator, lat, lng) => {
    console.log("checkEditForPlayer indicator", indicator);
    const playerLat = playerStore.player?.latitude || mapSettings.defaultLat;
    const playerLon = playerStore.player?.longitude || mapSettings.defaultLon;

    const playerWithin = isPointWithinRadius(playerLat, playerLon, lat, lng, indicator.coords.radius);

    handlePlayerWthin(indicator, playerWithin);
}

const checkAllLocationsForPlayer = () => {
    // loop through all indicators
    allIndicators.forEach((indicator) => {
        // do a check to see if the player is within the location

        const coords = indicator.coords;
        const playerLat = playerStore.player?.latitude || mapSettings.defaultLat;
        const playerLon = playerStore.player?.longitude || mapSettings.defaultLon;

        const playerWithin = isPointWithinRadius(playerLat, playerLon, coords.lat, coords.lng, coords.radius);
        handlePlayerWthin(indicator, playerWithin);
    });
};

function isPointWithinRadius(originLat, originLon,
    destinationLat, destinationLon,
    radius) {
    const distance = google.maps.geometry.spherical.computeDistanceBetween(
        new google.maps.LatLng(originLat, originLon),
        new google.maps.LatLng(destinationLat, destinationLon)
    );
    return distance <= radius * FEET_TO_METERS_CONVERSION;
}


async function initialGeoCode(lat, lng) {
    let formattedAddress = '';
    console.log("lat", lat, "lng", lng);
    await geoCode.value.geocode({ location: { lat, lng } }, (results, status) => {
        console.log("results", results);
        console.log("status", status);
        if (status === 'OK') {
            if (results[0]) {
                const resultAddress = results[0].formatted_address;
                const addressParts = resultAddress.split(/,\s+/);
                const streetAddress = addressParts[0];

                formattedAddress = streetAddress;
            } else {
                console.log("No results found.");
            }
        } else {
            console.error("Geocoder failed:", status);
        }
    });

    return formattedAddress;
}

const checkPlayerMoved = async () => {

    const existingPlayerLat = playerStore.player?.latitude || mapSettings.defaultLat;
    const existingPlayerLon = playerStore.player?.longitude || mapSettings.defaultLon;
    const existingLat = +existingPlayerLat;
    const existingLng = +existingPlayerLon;

    await playerStore.getPlayers();

    const playerLat = playerStore.player?.latitude || mapSettings.defaultLat;
    const playerLon = playerStore.player?.longitude || mapSettings.defaultLon;
    const lat = +playerLat;
    const lng = +playerLon;

    if (existingLat !== lat || existingLng !== lng) {
        await geoCode.value.geocode({ location: { lat, lng } }, (results, status) => {
            if (status === 'OK') {
                if (results[0]) {
                    const formattedAddress = results[0].formatted_address;
                    address.value = formattedAddress;
                    curPlayerLocationName.value = formattedAddress;
                } else {
                    console.log("No results found.");
                }
            } else {
                console.error("Geocoder failed:", status);
            }

            if (usePlayerCircle) {
                playerCircle.value.setCenter({
                    lat: +playerLat,
                    lng: +playerLon
                });
            }

            playerMarker.value.setPosition({
                lat: +playerLat,
                lng: +playerLon
            });

        });
    }
}


const centerPlayerLocation = async () => {

    playerMode.value = true;

    await playerStore.getPlayers();

    const playerLat = playerStore.player?.latitude || mapSettings.defaultLat;
    const playerLon = playerStore.player?.longitude || mapSettings.defaultLon;
    const lat = +playerLat;
    const lng = +playerLon;

    await geoCode.value.geocode({ location: { lat, lng } }, (results, status) => {
        if (status === 'OK') {
            if (results[0]) {
                const formattedAddress = results[0].formatted_address;
                address.value = formattedAddress;
                curPlayerLocationName.value = formattedAddress;
            } else {
                console.log("No results found.");
            }
        } else {
            console.error("Geocoder failed:", status);
        }
    });

    map.value.setCenter({
        lat: +playerLat,
        lng: +playerLon
    });

    map.value.setZoom(playerZoomLevel);
    activeLocationID.value = 0;

    if (usePlayerCircle) {
        playerCircle.value.setCenter({
            lat: +playerLat,
            lng: +playerLon
        });
    }

    playerMarker.value.setPosition({
        lat: +playerLat,
        lng: +playerLon
    });
};


const updatePlayerMarkerPosition = (lat, lng) => {
    playerMarker.value.setPosition({ lat: lat, lng: lng });
};

function updateMarkerPosition(marker, newLatitude, newLongitude) {
    marker.setPosition({ lat: newLatitude, lng: newLongitude });
}


const setMarkersUnDraggable = () => {
    allIndicators.forEach((indicator) => {
        indicator.marker.setDraggable(false); // Set draggable based on edit mode
    });
}

const setSpecificMarkerDraggable = (locationID, isEditMode) => {
    const indicator = allIndicators.find((i) => i.id === locationID);
    if (indicator.marker) {
        indicator.marker.setDraggable(isEditMode);
    }
};

const getPlaceDetails = (placeId) => {
    placesService.getDetails(
        {
            placeId: placeId,
        },
        (place, status) => {
            if (status === "OK") {
                console.log("Place details:", place);
                address.value = place.formatted_address;

                updateNameAndAddressFields(place.formatted_address, place.name);
            }
        }
    );
};

const handleEditClickOrDrag = (event) => {
    const lat = event.latLng.lat();
    const lng = event.latLng.lng();

    lastClickedLat.value = lat;
    lastClickedLng.value = lng;

    console.log("editMode", editMode.value + " currentEditLocationID", currentEditLocationID.value);

    if (editMode.value && currentEditLocationID.value) {
        const latLng = { lat, lng };

        const indicator = allIndicators.find((i) => i.id === currentEditLocationID.value);
        indicator.circle.setCenter({ lat: lat, lng: lng });

        if (event.placeId) {
            event.stop();  // Prevent the default info window from showing
            getPlaceDetails(event.placeId);
        } else {
            updateAddressFromCoords(lat, lng);
            checkAllLocationsForPlayer();
            checkEditForPlayer(indicator, lat, lng);
        }

        updateMarkerPosition(indicator.marker, +lat, +lng);
        map.value.setCenter({ lat: lat, lng: lng });
    } else {
        if (event.placeId) {
            event.stop();  // Prevent the default info window from showing
        }
    }
};


onMounted(async () => {

    intervalId = setInterval(updateTime, 10000); // Update every 10 seconds
    playerPollInterval();

    await playerStore.getPlayers();
    await playerStore.getLocations(playerStore.player?.id);


    locationStatus.value = "Player Location";

    locations.value = [...playerStore.allLocations];

    if (router.params?.id) playerStore.getPlayerConfig(router.params?.id);

    const playerLat = playerStore.player?.latitude || mapSettings.defaultLat;
    const playerLon = playerStore.player?.longitude || mapSettings.defaultLon;

    const loader = new Loader({
        apiKey,
        libraries: ["places", "maps", "geometry"],
    });

    try {
        const google = await loader.load();

        const autocomplete = new google.maps.places.Autocomplete(
            document.getElementById("autocomplete-container"),
            {}
        );

        map.value = new google.maps.Map(
            document.getElementById("map-container"),
            {
                center: {
                    lat: mapSettings.defaultLat,
                    lng: mapSettings.defaultLon,
                },

                mapTypeControl: false,
                fullscreenControl: false,
                streetViewControl: false,
                zoomControl: false,
                scrollwheel: true,

                zoom: 13,
            }
        );

        placesService = new google.maps.places.PlacesService(map.value);
        geoCode.value = new google.maps.Geocoder();

        const playerIconData = {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(playerSVG),
            fillColor: playerFill || "#0000bb",
            strokeColor: playerStroke || "#FF0000",
        };

        if (usePlayerCircle) {
            playerCircle.value = new google.maps.Circle({
                strokeColor: playerCircleStroke,
                strokeOpacity: playerCircleStrokeOpacity,
                strokeWeight: playerCircleStrokeWidth,
                fillColor: playerCircleFillColor,
                fillOpacity: playerCircleFillOpacity,
                map: map.value, // Map where the circle will be displayed
                center: {
                    lat: +playerLat,
                    lng: +playerLon
                },
                radius: 10, // Radius in meters
            });
        }

        playerMarker.value = new google.maps.Marker({
            position: {
                lat: +playerLat,
                lng: +playerLon
            },

            map: map.value, // Your map object
            icon: playerIconData
        });

        allIndicators = [];
        locations.value.forEach((curLocation) => {
            allIndicators.push(addIndicator(curLocation));
        });

        console.log("allIndicators", allIndicators);

        checkAllLocationsForPlayer();

        autocomplete.addListener("place_changed", () => {
            const addressField = document.getElementById("autocomplete-container");

            const place = autocomplete.getPlace();
            if (place) {
                addressField.value = place.formatted_address;
                if (place.geometry) {
                    map.value.setCenter({
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng(),
                    });

                    // are we in edit mode? update the indicator
                    if (editMode.value) {
                        const indicator = allIndicators.find((i) => i.id === activeLocationID.value);
                        updateMarkerPosition(indicator.marker, place.geometry.location.lat(), place.geometry.location.lng());
                        indicator.circle.setCenter({ lat: place.geometry.location.lat(), lng: place.geometry.location.lng() });
                    }
                }
            }
        });

        // Add click listener to the map
        map.value.addListener("click", (event) => {
            handleEditClickOrDrag(event);
        });

        // Add zoom listener to the map
        map.value.addListener("zoom_changed", () => {
            curMapZoom.value = map.value.getZoom();

            console.log(`Map zoom level: ${curMapZoom.value} `);
        });

        // Add pan listener to the map
        map.value.addListener("dragend", () => {
            const center = map.value.getCenter();

            lastClickedLat.value = center.lat();
            lastClickedLng.value = center.lng();
        });


        // Set a higher z-index for the player marker after it's created
        google.maps.event.addListenerOnce(playerMarker.value, 'domready', () => {

            const markerDiv = playerMarker.value.getElement(); // Get the underlying DOM element
            markerDiv.style.zIndex = 999; // Set a high z-index (adjust value as needed)
        });

        // may need to change this
        // setFence("zoom100");

        centerPlayerLocation();
    } catch (error) {
        console.error("Error loading Google Maps API:", error);
    }


    const authCoachId = await playerStore.player.coach_id;

    // Echo.private(`user.${authCoachId}`)
    //     // Echo.join('ptest')
    //     .listen('MessageSent', (e) => {

    //         let locations = JSON.parse(e.locations);
    //         let message = e.message;
    //         console.log(locations);
    //         console.log(message);
    //         centerPlayerLocation();
    //     });
});
</script>

<style scoped>
.svg-logo {
    width: 73px;
    height: 22px;
}

.svg-orange {
    fill: #f5a623;
    color: #f5a623;
    width: 12px;
    height: 12px;
}

.svg-orange-color {
    fill: #f5a623;
    color: #f5a623;
}

/* may delete.. */
.svg-info-color {
    fill: #f5a623;
    color: #f5a623;
}

.svg-small {
    width: 20px;
    height: 15px;
}

.location-label {
    max-width: 70%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.location-edit {
    cursor: pointer;
    margin-left: auto;
}

.location-focus-pointer {
    cursor: pointer;
}

.btn-map-control:hover {
    background-color: #e2dede;
}

.cur-location {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


.location-name {
    width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.locationRadio {
    padding: 5px 5px;
    width: 12px;
    border-radius: 50%;
    border: solid 1px #f5a623;
    cursor: pointer;
    display: inline-block;
}

.location-row:hover {
    background-color: #dde1e5;
}

.curLocationActive {
    padding: 4px 4px;
    background-color: #f5a623;
    border: solid 2px #f5a623;
}

.player-location-btn.disabled {
    border: none !important
}

#map-container {
    height: 375px;
    width: 100%;
}

.playerAutoCompleteTransparent {
    opacity: 0.5;
    display: none;
}

.playerLocationFullwidth {
    width: 100%;

}
</style>
