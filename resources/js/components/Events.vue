<script setup>
import { ref, watch, computed } from "vue";
import { DatePicker } from "v-calendar";
import "v-calendar/style.css";
import TrashIcon from "@/img/svgicons/trashorange.svg?url";

// import editIcon from "@/img/svgicons/edit.svg?url";
import SvgEditImage from "@/img/svgicons/edit.svg";
import SvgAddNewImage from "@/img/svgicons/addnewtimer_normal.svg?url";
import CalendarIcon from "@/img/svgicons/calendar.svg?url";
import SvgEventsImage from "@/img/svgicons/calendarblue.svg?url";
import NextEvent from "./NextEvent.vue";
import AddEvent from "./AddEvent.vue";
import ToneButton from "./ToneButton.vue";
import { useEventsStore } from "../stores/events";
import { usePlayerStore } from "../stores/player";
import { storeToRefs } from "pinia";
import moment from "moment";
import Swal from "sweetalert2";
import {
    ConfirmDeleteModule,
    changeDateKeepingTime,
} from "../constants/helpers";
const eventsStore = useEventsStore();
const playerStore = usePlayerStore();
const { player } = storeToRefs(playerStore);
const { events, calendarDate, recurring, singleEvent } =
    storeToRefs(eventsStore);
const toggleEventForm = ref(false);
const requestCount = ref(0);
const calendarEvents = ref();
watch(player, () => {
    if (player && requestCount.value == 0) {
        eventsStore.getPlayerEvents();
        requestCount.value = 1;
    }
});
const getSelectedDateEvents = (date) => {
    const selectedDate = moment(date || calendarDate.value);
    const isSameDay = (eventDate) => {
        return (
            moment(eventDate, moment.ISO_8601, true).isSame(
                selectedDate,
                "day"
            ) || moment(eventDate, "MM/DD/YYYY").isSame(selectedDate, "day")
        );
    };

    const isRecurringEvent = (event) => {
        if (event.repeat_options === "Every Day") {
            return selectedDate >= moment(event.start);
        }
        if (event.repeat_options === "Every Week") {
            return selectedDate.day() === moment(event.start).day();
        }
        if (event.repeat_options === "Every Month") {
            return (
                selectedDate.format("DD") === moment(event.start).format("DD")
            );
        }
        if (event.repeat_options === "Every Year") {
            return (
                selectedDate.format("DD MM") ===
                moment(event.start).format("DD MM")
            );
        }
        return false;
    };
    const filterdEventsResult = [];
    events.value
        .filter((event) => isSameDay(event.start) || isRecurringEvent(event))
        .map((item) => {
            const recItem = recurring.value.find(
                (x) => x.parent_event == item.id && isSameDay(x.date)
            );

            if (recItem?.events || recItem?.event_id == 0) {
                if (
                    recItem?.parent_event == item.id &&
                    !isSameDay(recItem.date) &&
                    recItem.event_id !== 0
                ) {
                    filterdEventsResult.push({
                        ...recItem.events,
                        ...changeDateKeepingTime(recItem.events, selectedDate),
                    });
                }
            } else {
                const checkDuplicate = new Set(
                    filterdEventsResult.map((event) => event.id)
                );
                if (!checkDuplicate.has(item.id)) {
                    filterdEventsResult.push({
                        ...item,
                        ...changeDateKeepingTime(item, selectedDate),
                    });
                }
            }
        });

    calendarEvents.value = filterdEventsResult;
};
watch(events, (newEvents) => {
    if (newEvents.length > 0) {
        getSelectedDateEvents();
    }
});

computed(() => getSelectedDateEvents());
const attr = computed(() => {
    const eventAttributes = events.value.map((event) => {
        const durationType = `${event.repeat_options
            .substring(event.repeat_options.indexOf(" ") + 1)
            .toLowerCase()}`;
        const start = event.start;
        const startDate = moment(start).format("DD-MM-yyy");
        const recItem = recurring.value.filter(
            (x) =>
                x.parent_event == event.id && !moment(x.date).isSame(startDate)
        );

        const recItemParent = recurring.value.find(
            (x) =>
                x.parent_event == event.id &&
                moment(x.date, moment.ISO_8601, true).isSame(event.start, "day")
        );
        var reccuringObj = { dot: false, date: "" };
        if (durationType == "week" && recItem.length > 0) {
            var startOfWeek = moment(start).startOf("day").add(1, "weeks");
            while (startOfWeek.year() === moment().year()) {
                const checkMatched = recItem.find(
                    (x) =>
                        startOfWeek.format("YYYY-MM-DD") ==
                        moment(x.date).format("YYYY-MM-DD")
                );
                if (!checkMatched) {
                    reccuringObj = {
                        dot: true,
                        date: startOfWeek.format("YYYY-MM-DD"),
                    };
                    break;
                }
                startOfWeek.add(1, "weeks");
            }
        }

        if (durationType == "day" && recItem.length > 0) {
            var startOfDay = moment(start);
            var isNotRecDay = false;
            while (!isNotRecDay) {
                const checkMatched = recItem.find(
                    (x) =>
                        startOfDay.format("YYYY-MM-DD") ==
                        moment(x.date).format("YYYY-MM-DD")
                );
                if (!checkMatched) {
                    reccuringObj = {
                        dot: true,
                        date: startOfDay.format("YYYY-MM-DD"),
                    };
                    isNotRecDay = true;
                    break;
                }
                startOfDay.add(1, "day");
            }
        }

        if (durationType == "year" && recItem.length > 0) {
            var startOfYear = moment(start);
            var isNotRecYear = false;
            while (!isNotRecYear) {
                const checkMatched = recItem.find(
                    (x) =>
                        startOfYear.format("YYYY-MM-DD") ==
                        moment(x.date).format("YYYY-MM-DD")
                );
                if (!checkMatched) {
                    reccuringObj = {
                        dot: true,
                        date: startOfYear.format("YYYY-MM-DD"),
                    };
                    isNotRecYear = true;
                    break;
                }
                startOfYear.add(1, "year");
            }
        }

        if (durationType == "month" && recItem.length > 0) {
            var startOfMonth = moment(start);
            var isNotRecMonth = false;
            while (!isNotRecMonth) {
                const checkMatched = recItem.find(
                    (x) =>
                        startOfMonth.format("YYYY-MM-DD") ==
                        moment(x.date).format("YYYY-MM-DD")
                );
                if (!checkMatched) {
                    reccuringObj = {
                        dot: true,
                        date: startOfMonth.format("YYYY-MM-DD"),
                    };
                    isNotRecMonth = true;
                    break;
                }
                startOfMonth.add(1, "month");
            }
        }

        const dotsConfig = !recItemParent
            ? {
                  key: "events",
                  dot: {
                      style: "background-color: #dd6b20; width: 5px; height: 5px;",
                  },
              }
            : reccuringObj.dot
            ? {
                  key: "events",
                  dot: {
                      style: "background-color: #dd6b20; width: 5px; height: 5px;",
                  },
              }
            : {};
        return {
            ...dotsConfig,
            dates: [
                {
                    start: reccuringObj.date || start,
                    repeat: {
                        ...(durationType == "month"
                            ? {
                                  every: [1, durationType + "s"],
                                  days: parseInt(moment(start).format("DD")),
                                  on: ({ date }) => {
                                      const checkRec = recItem.find((x) =>
                                          moment(x.date).isSame(date)
                                      );

                                      if (!checkRec) {
                                          return true;
                                      } else {
                                          return false;
                                      }
                                  },
                              }
                            : durationType == "year"
                            ? {
                                  every: [12, "months"],
                                  days: parseInt(moment(start).format("DD")),
                                  on: ({ date }) => {
                                      const checkRec = recItem.find((x) =>
                                          moment(x.date).isSame(date)
                                      );

                                      if (!checkRec) {
                                          return true;
                                      } else {
                                          return false;
                                      }
                                  },
                              }
                            : durationType == "week"
                            ? {
                                  every: [1, durationType + "s"],
                                  weekdays: 7 - (7 - moment(start).day()) + 1,
                                  on: ({ date }) => {
                                      const checkRec = recItem.find((x) =>
                                          moment(x.date).isSame(date)
                                      );

                                      if (!checkRec) {
                                          return true;
                                      } else {
                                          return false;
                                      }
                                  },
                              }
                            : {
                                  every: [1, durationType + "s"],
                                  on: ({ date }) => {
                                      const checkRec = recItem.find((x) =>
                                          moment(x.date).isSame(date)
                                      );

                                      if (!checkRec) {
                                          return true;
                                      } else {
                                          return false;
                                      }
                                  },
                              }),
                    },
                },
            ],
        };
    });

    const todayAttribute = {
        key: "today",
        highlight: true,
        dates: new Date(),
    };

    return [...eventAttributes, todayAttribute];
});
const onConfirm = (item, type) => {
    eventsStore.deleteEvent(item.id, type);
};
const deleteItem = (item) => {
    if (item?.id) {
        const recItem = recurring.value.find((x) => x.event_id == item.id);

        const findOne = events.value.find(
            (x) => x.id == item?.id && x.repeat_options !== "None"
        );
        if (findOne) {
            Swal.fire({
                text: "Do you want to delete the series or just this one occurrence?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#868686",
                confirmButtonText: "Series",
                cancelButtonText: "Cancel",
                allowOutsideClick: false,
                showDenyButton: true,
                denyButtonText: "Occurence",
            }).then((result) => {
                if (result.isConfirmed) {
                    onConfirm(item, "all");
                } else if (result.dismiss !== Swal.DismissReason.cancel) {
                    onConfirm(item, "spec");
                }
            });
        } else if (recItem && recItem.events.repeat_options == "None") {
            ConfirmDeleteModule(onConfirm, item.name, item, "all");
        } else {
            ConfirmDeleteModule(onConfirm, item.name, item, "all");
        }
    }
};
const checkIsBefore = () =>
    moment(calendarDate.value).isBefore(moment().startOf("day"), "day");
</script>

<template>
    <div class="col-md-6 px-4">
        <div class="card card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex">
                    <div class="pe-2">
                        <img :src="SvgEventsImage" class="svg-orange" alt="" />
                    </div>
                    <h4 class="d-inline">
                        {{
                            toggleEventForm
                                ? !singleEvent?.id
                                    ? "Create new Event"
                                    : "Edit Event"
                                : "Events"
                        }}
                    </h4>

                    <div
                        class="ms-auto d-flex align-items-center"
                        v-if="!toggleEventForm"
                    >
                        <ToneButton toneType="Event" />
                        <button
                            type="button"
                            class="btn_sm d-flex align-items-center justify-content-center"
                            @click="
                                () => {
                                    if (!checkIsBefore()) {
                                        const singleEvent =
                                            eventsStore.singleEvent;
                                        toggleEventForm = !toggleEventForm;
                                        eventsStore.singleEvent = {
                                            ...singleEvent,
                                            start: calendarDate,
                                            end: moment(calendarDate).add(
                                                1,
                                                'hour'
                                            ),
                                        };
                                    } else {
                                        return false;
                                    }
                                }
                            "
                            :disabled="
                                eventsStore.initialLoader || checkIsBefore()
                            "
                        >
                            <img
                                :src="SvgAddNewImage"
                                class="svg-orange"
                                alt=""
                            />
                        </button>
                    </div>
                </div>
                <NextEvent v-if="!toggleEventForm" />
                <div class="mt-3" v-if="!toggleEventForm">
                    <DatePicker
                        expanded
                        :attributes="attr"
                        :is-required="true"
                        v-model="eventsStore.calendarDate"
                        @dayclick="
                            (e) => {
                                getSelectedDateEvents(e.date);
                            }
                        "
                    />
                </div>
                <div
                    v-if="eventsStore.initialLoader"
                    class="spinner-border text-warning mt-3"
                    role="status"
                ></div>
                <div
                    class="mt-3"
                    v-if="!toggleEventForm && !eventsStore.initialLoader"
                >
                    <label class="sm-label mb-2"
                        >Upcoming Events Beginning on
                        {{
                            moment(calendarDate).utc(true).format("MM/DD")
                        }}:</label
                    >
                    <div
                        class="record-item w-100"
                        v-for="list in calendarEvents"
                    >
                        <div
                            class="d-flex m-1 record-row px-2 justify-content-between align-items-center"
                            :class="checkIsBefore() ? 'disable-event-tile' : ''"
                        >
                            <div class="flex-1">
                                <img
                                    :src="CalendarIcon"
                                    alt="Calendar"
                                    class="me-1"
                                />
                                <span class="align-middle"
                                    >{{ list.name
                                    }}{{
                                        list.location_name
                                            ? `${" - "}${list.location_name}`
                                            : ""
                                    }}</span
                                >
                            </div>
                            <div class="d-flex me-4">
                                <span class="me-4">
                                    {{
                                        moment(list.start)
                                            .local()
                                            .format("MM/DD")
                                    }}</span
                                >
                                <span>
                                    {{
                                        moment(list.start)
                                            .local()
                                            .format("hh:mm A")
                                    }}&nbsp;-&nbsp;{{
                                        moment(list.end)
                                            .local()
                                            .format("hh:mm A")
                                    }}</span
                                >
                            </div>

                            <div class="align-items-center d-flex">
                                <SvgEditImage
                                    class="svg-orange-edit me-2"
                                    v-if="!checkIsBefore()"
                                    @click="
                                        eventsStore.singleEvent = list;
                                        toggleEventForm = true;
                                    "
                                />
                                <img
                                    @click="deleteItem(list)"
                                    :src="TrashIcon"
                                    alt="Trash icon"
                                    class="cursor-pointer"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <AddEvent
                    @close:eventForm="
                        () => {
                            eventsStore.calendarDate = calendarDate
                                ? moment(calendarDate).toDate()
                                : new Date().setHours(0, 0, 0, 0);
                            eventsStore.singleEvent = {
                                repeat_options: 'None',
                            };
                            toggleEventForm = !toggleEventForm;
                        }
                    "
                    v-if="toggleEventForm"
                />
            </div>
        </div>
    </div>
</template>

<style lang="scss">
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

.vc-dots {
    display: flex;
    align-items: center;
    justify-content: center;

    > :nth-child(n + 6) {
        display: none;
    }
}

.disable-event-tile {
    background: #e5e5e5;
    opacity: 0.8;
}
</style>
