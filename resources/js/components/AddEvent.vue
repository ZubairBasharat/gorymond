<script setup>
import { ref, defineEmits, onMounted } from "vue";
import moment from "moment";
import { DatePicker } from "v-calendar";
import PowerOffIcon from "@/img/svgicons/power-off.svg?url";
import CancelIcon from "@/img/svgicons/cancel_normal.svg?url";
import saveIcon from "@/img/svgicons/save_normal.svg?url";
import Reminder from "./Reminder.vue";
import { ErrorMessage, Field, Form } from "vee-validate";
import * as yup from "yup";
import { useEventsStore } from "../stores/events";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Swal from "sweetalert2";
import { storeToRefs } from "pinia";
const emit = defineEmits(["close:eventForm"]);
const eventsStore = useEventsStore();
const { events } = storeToRefs(eventsStore);
const days = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];
const recurring = ref("");
const isRecurringDisable = ref(false);
const selectedDay = ref("");
const eventUpdateType = ref("all");
const endDateOptions = ref({ type: "", days: "", end_on: new Date() });
const extra = ref({
    start: eventsStore.singleEvent?.start || "",
    end: eventsStore.singleEvent?.end || "",
});
const schema = yup.object({
    name: yup.string().required("Event Name is required"),
    end: yup
        .string()
        .required("End date is required")
        .test(
            "is-greater",
            "End date must be greater than start date",
            function (end) {
                const start = this.resolve(yup.ref("start"));
                return moment(end).isAfter(moment(start));
            }
        ),
    start: yup.string().required("Start date is required"),
    reminders: yup.array().nullable(),
    repeat_options: yup.string().required("Recurring is required"),
    recurring_active: yup.boolean().default(false),
    location_name: yup.string().nullable(),
    meet_with: yup.string().nullable(),
    notes: yup.string().nullable(),
});
const onSubmit = async (values) => {
    let end_date_options = null;
    if (endDateOptions.value.type) {
        end_date_options = {
            ...end_date_options,
            type: endDateOptions.value.type,
        };
        if (endDateOptions.value.type == "end_on") {
            end_date_options = {
                ...end_date_options,
                end_on: endDateOptions.value.end_on,
            };
        }
        if (endDateOptions.value.type == "end_after") {
            end_date_options = {
                ...end_date_options,
                days: endDateOptions.value.days,
            };
        }
    }
    eventsStore
        .addPlayerEvents({
            ...values,
            recurring_active: values.recurring_active == "true" ? true : false,
            ...extra.value,
            start: moment(extra.value.start)
                .utc(true)
                .format("YYYY-MM-DD HH:mm:ss"),
            end: moment(extra.value.end)
                .utc(true)
                .format("YYYY-MM-DD HH:mm:ss"),
            end_date_options,
            type: eventUpdateType.value,
            oldDate: moment(eventsStore.singleEvent?.start)
                .utc(true)
                .format("YYYY-MM-DD HH:mm:ss"),
            repeat_options:
                eventUpdateType.value == "spec"
                    ? "None"
                    : values.repeat_options,
        })
        .then(() => {
            emit("close:eventForm");
        });
};

onMounted(() => {
    if (eventsStore.singleEvent?.id) {
        const recItem = eventsStore.recurring.find(
            (x) => x.event_id == eventsStore.singleEvent?.id
        );

        const findOne = events.value.find(
            (x) =>
                x.id == eventsStore.singleEvent?.id &&
                x.repeat_options !== "None"
        );
        if (findOne) {
            Swal.fire({
                text: "Do you want to edit the series or just this one occurrence?",
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
                    eventUpdateType.value = "all";
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    eventUpdateType.value = "";
                    emit("close:eventForm");
                    eventsStore.singleEvent = { repeat_options: "None" };
                } else {
                    isRecurringDisable.value = true;
                    eventUpdateType.value = "spec";
                }
            });
        } else if (recItem && recItem.events.repeat_options == "None") {
            isRecurringDisable.value = true;
            eventUpdateType.value = "spec";
        }
    }
});
</script>
<template>
    <Form
        :validation-schema="schema"
        class="row"
        @submit="onSubmit"
        keep-values
        :initial-values="eventsStore.singleEvent"
        v-slot="{ values, setValues }"
    >
        <div class="mt-3">
            <Field
                name="name"
                class="w-100 form-control"
                type="text"
                placeholder="Event name"
            />
        </div>
        <ErrorMessage className="text-danger small  mt-2" name="name" />
        <div class="col-md-6 col-12 mt-3">
            <label class="mb-1 sm-label"> Start time: </label>
            <Field v-slot="{ handleChange }" name="start">
                <VueDatePicker
                    @update:model-value="
                        (date) => {
                            handleChange(date);
                            const res = moment(date)
                                .utc(false)
                                .add(1, 'hour')
                                .toDate();
                            extra.end = res;
                            setValues({ end: res });
                        }
                    "
                    v-model="extra.start"
                    :min-date="new Date()"
                    :is-24="false"
                />
            </Field>
            <ErrorMessage className="text-danger small  mt-2" name="start" />
        </div>
        <div class="col-md-6 col-12 mt-3">
            <label class="mb-1 sm-label"> End time: </label>
            <Field v-slot="{ handleChange, field }" name="end">
                <VueDatePicker
                    @update:model-value="(date) => handleChange(date)"
                    v-model="extra.end"
                    :min-date="new Date()"
                    :is-24="false"
                />
            </Field>
            <ErrorMessage className="text-danger small  mt-2" name="end" />
        </div>
        <div class="col-12">
            <label class="mb-1 mt-3 sm-label"> Reminder: </label>
            <Field v-slot="{ handleChange, field }" name="reminders">
                <Reminder
                    @update:reminders="(e) => handleChange(e)"
                    :remindersData="field.value"
                />
            </Field>
        </div>
        <div class="col-12 mt-3">
            <label class="mb-1 sm-label">Recurring:</label>
            <div class="d-flex align-items-center">
                <div class="flex-1 me-2">
                    <Field
                        v-slot="{ handleChange, field }"
                        name="repeat_options"
                    >
                        <select
                            class="form-select"
                            @change="(e) => handleChange(e.target.value)"
                            :value="field.value"
                            :disabled="isRecurringDisable"
                        >
                            <option value="None">None</option>
                            <option value="Every Day">Every Day</option>
                            <option value="Every Week">Every Week</option>
                            <option value="Every Month">Every Month</option>
                            <option value="Every Year">Every Year</option>
                        </select>
                    </Field>
                </div>
                <Field v-slot="{ handleChange, field }" name="recurring_active">
                    <select
                        class="form-select me-2"
                        @change="(e) => handleChange(e.target.value)"
                        :value="field.value"
                        style="width: 140px"
                        :disabled="isRecurringDisable"
                    >
                        <option value="" selected disabled hidden>
                            Select Status
                        </option>
                        <option :value="true">Active</option>
                        <option :value="false">Deactivate</option>
                    </select>
                </Field>
                <div class="align-self-end">
                    <button
                        type="button"
                        class="btn_sm d-flex align-items-center justify-content-center"
                        data-bs-toggle="dropdown"
                        style="width: 37px; height: 37px; margin-bottom: 3px"
                        :disabled="isRecurringDisable"
                    >
                        <img :src="PowerOffIcon" width="18px" alt="" />
                    </button>

                    <div
                        class="dropdown-menu dropdown-menu-end px-3 py-3"
                        style="width: 300px"
                    >
                        <h6 class="mb-3">End Date</h6>
                        <div class="pl-3 mr-3">
                            <div class="form-check">
                                <input
                                    type="radio"
                                    name="power"
                                    value="no_end"
                                    class="form-check-input"
                                    @change="endDateOptions.type = 'no_end'"
                                    v-model="endDateOptions.type"
                                />
                                <label class="form-check-label">
                                    No end date
                                </label>
                            </div>
                            <div class="form-check mt-2 pt-1">
                                <input
                                    name="power"
                                    type="radio"
                                    value="end_after"
                                    class="form-check-input"
                                    @change="endDateOptions.type = 'end_after'"
                                    v-model="endDateOptions.type"
                                />
                                <label class="form-check-label d-flex">
                                    End after
                                    <input
                                        type="number"
                                        class="days-input mx-2 form-control-sm w-25"
                                        :disabled="
                                            endDateOptions.type !== 'end_after'
                                        "
                                        v-model="endDateOptions.days"
                                    />
                                    days.
                                </label>
                            </div>
                            <div
                                class="form-check d-flex align-items-center mt-2 pt-1"
                            >
                                <input
                                    name="power"
                                    type="radio"
                                    value="end_on"
                                    class="form-check-input me-2"
                                    @change="endDateOptions.type = 'end_on'"
                                    v-model="endDateOptions.type"
                                />
                                <label
                                    class="form-check-label d-flex align-items-center"
                                >
                                    End on date
                                    <DatePicker
                                        :popover="{
                                            visibility: 'click',
                                        }"
                                        v-model="endDateOptions.end_on"
                                    >
                                        <template
                                            v-slot="{ inputValue, inputEvents }"
                                        >
                                            <input
                                                type="text"
                                                :value="inputValue"
                                                v-on="inputEvents"
                                                :disabled="
                                                    endDateOptions.type !==
                                                    'end_on'
                                                "
                                                class="ms-2 form-control w-50"
                                            />
                                        </template>
                                    </DatePicker>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ErrorMessage
                className="text-danger small  mt-2"
                name="repeat_options"
            />
            <div class="mt-3" v-if="recurring == 'select-days'">
                <div type="text" class="form-control match-border days-list">
                    <div class="d-flex justify-content-between pl-5">
                        <div class="col-1-7" v-for="list in days">
                            <span
                                :class="
                                    selectedDay == list ? 'text-orange' : ''
                                "
                                @click="selectedDay = list"
                                >{{ list }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="flex-1 me-2">
                <label class="mb-1 sm-label">Optional:</label>
            </div>
            <div class="mt-3">
                <Field
                    name="location_name"
                    class="w-100 form-control"
                    type="text"
                    placeholder="Location"
                />
                <ErrorMessage
                    className="text-danger small mt-2"
                    name="location_name"
                />
            </div>
            <div class="mt-3">
                <Field
                    name="meet_with"
                    class="w-100 form-control"
                    type="text"
                    placeholder="Who will player meet?"
                />
                <ErrorMessage
                    className="text-danger small mt-2"
                    name="meet_with"
                />
            </div>
            <div class="mt-3">
                <Field
                    name="notes"
                    class="w-100 form-control"
                    type="text"
                    placeholder="Additional Notes?"
                />
                <ErrorMessage className="text-danger small mt-2" name="notes" />
            </div>
        </div>
        <div class="btn-group col-12 justify-content-end w-100">
            <button
                type="submit"
                class="btn_sm d-flex align-items-center justify-content-center mt-3 me-3"
                :disabled="eventsStore.actionLoader"
            >
                <img :src="saveIcon" alt="save" />
            </button>
            <button
                type="button"
                class="btn_sm ml-2 d-flex align-items-center justify-content-center mt-3"
                :disabled="eventsStore.actionLoader"
                @click="
                    emit('close:eventForm');
                    eventsStore.singleEvent = { repeat_options: 'None' };
                "
            >
                <img :src="CancelIcon" alt="cancel" />
            </button>
        </div>
    </Form>
</template>
