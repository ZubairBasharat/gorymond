<script setup>
import { ref, watch } from "vue";
import { timeZonesArray } from "../../constants/timezone";
import TextIcon from "@/img/svgicons/text.svg?url";
import TrashIcon from "@/img/svgicons/trashorange.svg?url";
import EmailIcon from "@/img/svgicons/email.svg?url";
import MasterCardIcon from "@/img/svgicons/mastercard.svg?url";
import { Form, Field, ErrorMessage, useForm } from "vee-validate";
import * as yup from "yup";
import { useAuthStore } from "../../stores/auth";
const authStore = useAuthStore();
const togglePassword = ref(false);
const loader = ref(false);
const schema = yup.object({
    email: yup
        .string()
        .required("Email is required")
        .email("Email should be valid"),
    first_name: yup.string().required("First Name is required"),
    last_name: yup.string().required("Last Name is required"),
    phone: yup
        .string()
        .matches(
            /^\d{3}-\d{3}-\d{4}$/,
            "Phone must be in the format XXX-XXX-XXXX"
        )
        .required("Phone number is required"),
    timezone: yup.string().required("Timezone is required"),
    sms_notifications: yup.number().default(0).nullable(),
    email_notifications: yup.number().default(0).nullable(),
    password: yup.string().when([], {
        is: () => togglePassword.value,
        then: (schema) =>
            schema
                .required("Password is required")
                .min(8, "Password should be atleast 8 characters"),
        otherwise: (schema) => schema,
    }),
    password_confirmation: yup.string().when([], {
        is: () => togglePassword.value,
        then: (schema) =>
            schema
                .required("Confirm Password is required")
                .oneOf([yup.ref("password")], "Passwords must match"),
        otherwise: (schema) => schema,
    }),
});
const onSubmit = (values) => {
    loader.value = true;
    const payload = { ...values };
    if (!payload.password || !togglePassword.value) {
        delete payload.password;
        delete payload.password_confirmation;
    }
    authStore.updateUser(payload).finally(() => {
        loader.value = false;
    });
};
const formValues = ref(authStore.userData);
watch(
    () => authStore.userData,
    (newVal, oldVal) => {
        formValues.value = newVal;
    }
);
function formatPhoneNumber(e) {
    let text = e.toString().replace(/\D/g, "");
    if (text.length > 3) text = text.replace(/.{3}/, "$&-");
    if (text.length > 7) text = text.replace(/.{7}/, "$&-");
    return text;
}
</script>
<template>
    <div>
        <Form
            v-if="authStore.userData"
            :initial-values="formValues"
            keep-values
            :validation-schema="schema"
            @submit="onSubmit"
        >
            <div class="mt-3">
                <label class="mb-2 form-text-label">First name</label>
                <Field
                    name="first_name"
                    :class="'form-control'"
                    type="text"
                    placeholder="First name"
                />
                <ErrorMessage className="text-danger mt-2" name="first_name" />
            </div>
            <div class="mt-3">
                <label class="mb-2 form-text-label">Last name</label>
                <Field
                    name="last_name"
                    :class="'form-control'"
                    type="text"
                    placeholder="Last name"
                />
                <ErrorMessage className="text-danger mt-2" name="last_name" />
            </div>
            <div class="mt-3">
                <label class="mb-2 form-text-label">Email</label>
                <Field
                    name="email"
                    :class="'form-control'"
                    type="email"
                    placeholder="Email address"
                />
                <ErrorMessage className="text-danger mt-2" name="email" />
            </div>
            <div class="mt-3">
                <label class="mb-2 form-text-label">Phone</label>
                <Field v-slot="{ handleChange, field }" name="phone">
                    <input
                        v-bind="field"
                        type="text"
                        class="form-control"
                        placeholder="Phone Number"
                        maxlength="12"
                        @input="
                            (e) =>
                                handleChange(formatPhoneNumber(e.target.value))
                        "
                    />
                </Field>
                <ErrorMessage className="text-danger mt-2" name="phone" />
            </div>
            <div class="mt-3">
                <label class="mb-2 form-text-label">Timezone</label>
                <Field as="select" class="form-select" name="timezone">
                    <option value="" disabled selected>Select value</option>
                    <option :value="list.zone" v-for="list in timeZonesArray">
                        {{ list.zone }}
                    </option>
                </Field>
                <ErrorMessage className="text-danger mt-2" name="timzezone" />
            </div>
            <div class="mt-4">
                <h6 class="pb-2">Account Notifications</h6>
                <div class="record-item w-100 mt-3">
                    <div
                        class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                    >
                        <div class="flex-1">
                            <img :src="TextIcon" alt="Calendar" class="me-2" />
                            <span class="align-middle"
                                >Send Text Notification</span
                            >
                        </div>
                        <div class="align-self-start">
                            <div class="form-check form-switch">
                                <Field
                                    name="sms_notifications"
                                    v-slot="{ handleChange, field }"
                                >
                                    <input
                                        v-bind="field"
                                        class="form-check-input"
                                        type="checkbox"
                                        @change="
                                            (e) => {
                                                handleChange(
                                                    e.target.checked ? 1 : 0
                                                );
                                            }
                                        "
                                    />
                                </Field>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="record-item w-100 mt-3">
                    <div
                        class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                    >
                        <div class="flex-1">
                            <img :src="EmailIcon" alt="Calendar" class="me-2" />
                            <span class="align-middle"
                                >Send Email Notification</span
                            >
                        </div>
                        <div class="align-self-start">
                            <div class="form-check form-switch">
                                <Field
                                    name="email_notifications"
                                    v-slot="{ handleChange, field }"
                                >
                                    <input
                                        v-bind="field"
                                        class="form-check-input"
                                        type="checkbox"
                                        @change="
                                            (e) => {
                                                console.log(e.target.checked);
                                                handleChange(
                                                    e.target.checked ? 1 : 0
                                                );
                                            }
                                        "
                                    />
                                </Field>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a
                        class="text-underline text-italic px-2"
                        href="javascript:void(0)"
                        style="font-size: 14px"
                        @click="togglePassword = !togglePassword"
                    >
                        <i>Change password</i>
                    </a>
                </div>
                <div v-if="togglePassword">
                    <div class="mt-3">
                        <label class="mb-2 form-text-label">Password</label>
                        <Field
                            name="password"
                            type="password"
                            class="form-control"
                            placeholder="Password"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="password"
                        />
                    </div>
                    <div class="mt-3">
                        <label class="mb-2 form-text-label"
                            >Password Confirm</label
                        >
                        <Field
                            type="password"
                            class="form-control"
                            placeholder="Confirm Password"
                            name="password_confirmation"
                        />
                        <ErrorMessage
                            className="text-danger mt-2"
                            name="password_confirmation"
                        />
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h6 class="pb-2">Credit Cards</h6>
                <div class="record-item w-100 mt-3">
                    <div
                        class="d-flex m-1 mt-3 record-row px-2 justify-content-between align-items-center"
                    >
                        <div>
                            <img
                                :src="MasterCardIcon"
                                alt="Calendar"
                                class="me-1"
                            />
                        </div>
                        <span class="align-middle">Testing: 15 min</span>
                        <div class="align-self-center">
                            <img :src="TrashIcon" alt="Trash icon" />
                        </div>
                    </div>
                </div>
                <a
                    class="text-underline text-italic px-2"
                    href="javascript:void(0)"
                    style="font-size: 14px"
                >
                    <i>Add new Credit card</i>
                </a>
            </div>
            <div class="row pt-3">
                <div class="col-lg-6 mt-3">
                    <button
                        :disabled="loader"
                        type="button"
                        class="btn btnstd text-orange w-100 btn-block"
                    >
                        Cancel
                    </button>
                </div>
                <div class="col-lg-6 mt-3">
                    <button
                        :disabled="loader"
                        type="submit"
                        class="btn btnstd btnstd__orange w-100 btn-block"
                    >
                        Save
                    </button>
                </div>
            </div>
        </Form>
    </div>
</template>
