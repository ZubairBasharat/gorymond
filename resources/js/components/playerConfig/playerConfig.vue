<script setup>
import { onMounted, ref, watchEffect } from "vue";
import TrashIcon from "@/img/svgicons/trashorange.svg?url";
import PlusIcon from "@/img/svgicons/plus.svg?url";
import MinusIcon from "@/img/svgicons/minus.svg?url";
import Header from "../layouts/Header.vue";
import avatar from "@/img/avatar.png?url";
import addIcon from "@/img/svgicons/addnewtimer_normal.svg?url";
import QuestionCircle from "@/img/svgicons/question-circle.svg?url";
import editOrange from "@/img/svgicons/editorange.svg?url";
import { Form, Field, ErrorMessage } from "vee-validate";
import { usePlayerStore } from "../../stores/player";
import { useRoute } from "vue-router";
import { BASE_URL } from "../../constants/variables";
import { ConfirmDeleteModule, scrollToBottom } from "../../constants/helpers";
import * as yup from "yup";
const togglePlayerContact = ref(false);
const editData = ref(null);
const counter = ref(1);
const playerStore = usePlayerStore();
const router = useRoute();
const innerFormData = ref({
    avatar: null,
    name: "",
    full_name: "",
    phone: "",
    contact_to_player: 0,
    call_to_contact: 0,
    text_to_contact: 0,
});
const schema = yup.object({
    email: yup
        .string()
        .required("Email is required")
        .email("Email should be valid"),
    pin: yup
        .number()
        .typeError("Pin should be integer.")
        .required("Pin is required")
        .min(3, "Pin should be atleast 3 characters")
        .integer("Pin should be integer."),
    first_name: yup.string().required("First Name is required"),
    last_name: yup.string().required("Last Name is required"),
    phone: yup
        .string()
        .required("Phone number is required")
        .min(6, "Phone number must be atleast 6 characters"),
});
onMounted(() => {
    if (router.params?.id) playerStore.getPlayerConfig(router.params?.id);
});
watchEffect(
    () => playerStore.playerConfig,
    (newVal) => {
        counter.value = newVal.event_display_number || 0;
    }
);
watchEffect(() => {
    if (togglePlayerContact.value) {
        setTimeout(() => {
            scrollToBottom();
        }, 100);
    } else {
        editData.value = null;
    }
    if (editData.value) {
        Object.keys(innerFormData.value).forEach((key) => {
            let value = null;
            let current = editData.value[key];
            if (
                key == "text_to_contact" ||
                key == "call_to_contact" ||
                key == "contact_to_player"
            ) {
                value = current ? true : false;
            } else if (key == "avatar" && editData.value.avatar_url) {
                value = editData.value.avatar_url;
            }
            innerFormData.value = {
                ...innerFormData.value,
                [key]: value || current,
            };
        });
    }
});
const onSubmit = (data) => {
    playerStore.updatePlayerConfig(data);
};

const ObjectUrlCreate = (file) => {
    return URL.createObjectURL(file);
};

const onSubmitInnerForm = async () => {
    let payload = { ...innerFormData.value };
    if (payload.text_to_contact == true) {
        payload.text_to_contact = 1;
    } else {
        payload.text_to_contact = 0;
    }
    if (payload.contact_to_player == true) {
        payload.contact_to_player = 1;
    } else {
        payload.contact_to_player = 0;
    }
    if (payload.call_to_contact == true) {
        payload.call_to_contact = 1;
    } else {
        payload.call_to_contact = 0;
    }
    if (editData.value) {
        playerStore.addPlayerContact(payload, editData.value.id).then((e) => {
            togglePlayerContact.value = false;
            editData.value = null;
        });
    } else {
        playerStore.addPlayerContact(payload).then((e) => {
            togglePlayerContact.value = false;
        });
    }
};
function formatPhoneNumber() {
    let text = innerFormData.value.phone.toString().replace(/\D/g, "");
    if (text.length > 3) text = text.replace(/.{3}/, "$&-");
    if (text.length > 7) text = text.replace(/.{7}/, "$&-");
    innerFormData.value.phone = text;
}
const onConfirm = (data) => {
    playerStore.deletePlayerContact(data.id);
};
const deleteContact = (contact) => {
    ConfirmDeleteModule(onConfirm, contact.name, contact);
};
</script>
<template>
    <Header />
    <div
        class="text-center my-4"
        v-if="!playerStore.playerConfig && playerStore.pageLoader"
    >
        <div class="spinner-border text-warning" role="status"></div>
    </div>
    <Form
        keep-values
        v-if="playerStore.playerConfig"
        :initial-values="playerStore.playerConfig"
        :validation-schema="schema"
        @submit="onSubmit"
    >
        <div class="pt-5">
            <div class="profile-card mx-auto mt-5">
                <div class="text-center">
                    <img
                        :src="
                            playerStore.playerConfig.avatar_url
                                ? `${BASE_URL}${playerStore.playerConfig.avatar_url}`
                                : avatar
                        "
                        width="136px"
                        height="136px"
                        class="rounded-circle profile-avatar"
                        alt="avatar"
                    />
                    <label>
                        <img
                            :src="editOrange"
                            alt=""
                            class="circle-icon border"
                        />
                        <input
                            type="file"
                            @change="
                                (e) => playerStore.updatePlayerAvatar(e.target)
                            "
                            class="d-none"
                        />
                    </label>
                </div>
                <div class="profile-card-title mt-4 mb-5">
                    <h3 class="text-center">Player Configuration</h3>
                </div>
                <div>
                    <h6 class="pb-2">Player's Profile:</h6>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">First name</label>
                        <Field
                            name="first_name"
                            :class="'form-control'"
                            type="text"
                            placeholder="First name"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="first_name"
                        />
                    </div>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">Last name</label>
                        <Field
                            name="last_name"
                            :class="'form-control'"
                            type="text"
                            placeholder="Last name"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="last_name"
                        />
                    </div>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">Pin</label>
                        <Field
                            name="pin"
                            :class="'form-control'"
                            type="password"
                            placeholder="Create Unlock Pin"
                        />
                        <ErrorMessage className="text-danger mt-2" name="pin" />
                    </div>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">Email</label>
                        <Field
                            name="email"
                            :class="'form-control'"
                            type="email"
                            placeholder="E-mail address"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="email"
                        />
                    </div>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">Phone</label>
                        <Field
                            name="phone"
                            :class="'form-control'"
                            placeholder="Phone number"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="phone"
                        />
                    </div>
                    <div class="mt-4">
                        <h6 class="pb-2">Upcoming Events to Display</h6>
                        <div class="form-row flex-nowrap position-relative">
                            <button
                                class="stepper-control stepper-left"
                                @click="counter = counter - 1"
                                :disabled="counter == 1 ? true : false"
                                type="button"
                            >
                                <img
                                    :src="MinusIcon"
                                    alt=""
                                    height="16"
                                    class="stepper-control-icon"
                                />
                            </button>
                            <input
                                type="text"
                                name="event_display_number"
                                readonly="readonly"
                                class="form-control text-center stepper-input"
                                :value="counter"
                            />
                            <button
                                class="stepper-control stepper-right"
                                :disabled="counter == 5 ? true : false"
                                @click="counter = counter + 1"
                                type="button"
                            >
                                <img
                                    :src="PlusIcon"
                                    alt=""
                                    height="16"
                                    class="stepper-control-icon"
                                />
                            </button>
                        </div>
                    </div>
                    <div
                        class="mt-4 justify-content-between d-flex align-items-center"
                    >
                        <h6 class="pb-0 mb-0">Player's Contacts:</h6>
                        <button
                            type="button"
                            class="bg-transparent border-0"
                            @click="togglePlayerContact = true"
                        >
                            <img :src="addIcon" />
                        </button>
                    </div>
                    <div
                        class="card mt-4"
                        v-for="contact in playerStore.playerConfig.contacts"
                    >
                        <div class="d-flex align-items-start p-3">
                            <div
                                class="d-flex align-items-start cursor-pointer"
                                @click="
                                    () => {
                                        editData = contact;
                                        togglePlayerContact = true;
                                    }
                                "
                            >
                                <img
                                    :src="
                                        contact.avatar_url
                                            ? `${BASE_URL}${contact.avatar_url}`
                                            : avatar
                                    "
                                    width="55px"
                                    height="55px"
                                    class="rounded-circle mt-0 profile-avatar"
                                    alt="avatar"
                                />
                                <div class="mx-4">
                                    <h5 class="mb-0">{{ contact.name }}</h5>
                                    <small>Contact Enabled</small>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="ms-auto p-0 border-0 bg-transparent"
                                @click="
                                    (e) => {
                                        e.stopPropagation;
                                        deleteContact(contact);
                                    }
                                "
                                :disabled="playerStore.actionLoader"
                            >
                                <img
                                    :src="TrashIcon"
                                    alt="trash"
                                    width="16px"
                                />
                            </button>
                        </div>
                    </div>
                    <form
                        class="border player-contact-form mt-4 p-3"
                        v-if="togglePlayerContact"
                        @submit.prevent="onSubmitInnerForm"
                    >
                        <span
                            class="float-end mt-2 cursor-pointer text-orange"
                            @click="togglePlayerContact = false"
                            style="font-size: 30px; line-height: 0"
                            >&times;</span
                        >
                        <div class="text-center mt-5 pb-4">
                            <img
                                :src="
                                    innerFormData.avatar?.name
                                        ? ObjectUrlCreate(innerFormData.avatar)
                                        : innerFormData.avatar
                                        ? innerFormData.avatar
                                        : avatar
                                "
                                width="90px"
                                height="90px"
                                class="rounded-circle mt-0 profile-avatar"
                                alt="avatar"
                            />
                            <label>
                                <img
                                    :src="editOrange"
                                    alt=""
                                    class="circle-icon border"
                                    style="margin-left: -15px; margin-top: 15px"
                                />
                                <input
                                    type="file"
                                    accept="image/*"
                                    class="d-none"
                                    @change="
                                        (e) =>
                                            (innerFormData.avatar =
                                                e.target.files[0])
                                    "
                                />
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="mb-2 form-text-label"
                                >Full Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                v-model="innerFormData.full_name"
                                required="required"
                                placeholder="Full name"
                            />
                        </div>
                        <div class="mt-3">
                            <label class="mb-2 form-text-label"
                                >Display Name</label
                            >
                            <input
                                type="text"
                                class="form-control"
                                required="required"
                                placeholder="Display name"
                                v-model="innerFormData.name"
                            />
                        </div>
                        <div class="mt-3">
                            <label class="mb-2 form-text-label">Phone</label>
                            <input
                                type="text"
                                class="form-control"
                                required="required"
                                placeholder="Phone"
                                v-model="innerFormData.phone"
                                maxlength="12"
                                @input="formatPhoneNumber"
                            />
                        </div>
                        <div class="record-item w-100 mt-3 record-check-item">
                            <div
                                class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                            >
                                <div class="flex-1 d-flex align-items-center">
                                    <VTooltip style="line-height: 1">
                                        <img
                                            :src="QuestionCircle"
                                            alt="Calendar"
                                            class="me-2"
                                            height="16px"
                                            style="filter: opacity(0.4)"
                                        />
                                        <template #popper>
                                            The player is allowed to text the
                                            contact
                                        </template>
                                    </VTooltip>
                                    <span class="align-middle"
                                        >Text to Contact</span
                                    >
                                </div>
                                <div class="align-self-start">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="text_to_contact"
                                            v-model="
                                                innerFormData.text_to_contact
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="record-item w-100 mt-3 record-check-item">
                            <div
                                class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                            >
                                <div class="flex-1 d-flex align-items-center">
                                    <VTooltip style="line-height: 1">
                                        <img
                                            :src="QuestionCircle"
                                            alt="Calendar"
                                            class="me-2"
                                            height="16px"
                                            style="filter: opacity(0.4)"
                                        />
                                        <template #popper>
                                            The player is allowed to phone the
                                            contact
                                        </template>
                                    </VTooltip>
                                    <span class="align-middle">
                                        Call to Contact</span
                                    >
                                </div>
                                <div class="align-self-start">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="call_to_contact"
                                            v-model="
                                                innerFormData.call_to_contact
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="record-item w-100 mt-3 record-check-item">
                            <div
                                class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                            >
                                <div class="flex-1 d-flex align-items-center">
                                    <VTooltip style="line-height: 1">
                                        <img
                                            :src="QuestionCircle"
                                            alt="Calendar"
                                            class="me-2"
                                            height="16px"
                                            style="filter: opacity(0.4)"
                                        />
                                        <template #popper>
                                            When a Player is not allowed to
                                            phone or text the contact, this
                                            allows the contact to still be able
                                            to phone or text the Player. In this
                                            case, the contact will not be
                                            displayed within the Player's
                                            contact list.
                                        </template>
                                    </VTooltip>
                                    <span class="align-middle">
                                        Contact to Player</span
                                    >
                                </div>
                                <div class="align-self-start">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="contact_to_player"
                                            v-model="
                                                innerFormData.contact_to_player
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row pt-2 px-4">
                            <div class="col-lg-6 mt-3">
                                <button
                                    type="button"
                                    class="btn btnstd text-orange w-100 btn-block"
                                    @click="togglePlayerContact = false"
                                    :disabled="playerStore.actionLoader"
                                >
                                    Cancel
                                </button>
                            </div>
                            <div class="col-lg-6 mt-3">
                                <button
                                    type="submit"
                                    class="btn btnstd btnstd__orange w-100 btn-block"
                                    :disabled="playerStore.actionLoader"
                                >
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row pt-3" v-if="!togglePlayerContact">
                    <div class="col-lg-6 mt-3">
                        <router-link to="/dashboard">
                            <button
                                type="button"
                                class="btn btnstd text-orange w-100 btn-block"
                                :disabled="playerStore.actionLoader"
                            >
                                Cancel
                            </button>
                        </router-link>
                    </div>
                    <div class="col-lg-6 mt-3">
                        <button
                            type="submit"
                            class="btn btnstd btnstd__orange w-100 btn-block"
                            :disabled="playerStore.actionLoader"
                        >
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Form>
</template>
