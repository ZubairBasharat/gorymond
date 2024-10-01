<script setup>
import Header from "./layouts/Header.vue";
import Logo from "@/img/svgicons/logo.svg?url";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { usePlayerStore } from "../stores/player";
const playerStore = usePlayerStore();
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
});
function onSubmit(values) {
    playerStore.handleCreatePlayer(values);
}
</script>
<template>
    <Header />
    <div class="pt-5">
        <div class="profile-card px-lg-5 mx-auto mt-5">
            <div class="text-center">
                <img
                    :src="Logo"
                    width="136px"
                    height="136px"
                    class="rounded-circle profile-avatar"
                    alt="avatar"
                />
            </div>
            <Form keep-values :validation-schema="schema" @submit="onSubmit">
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
                    <label class="mb-2 form-text-label"
                        >Create Unlock Pin</label
                    >
                    <Field
                        name="pin"
                        :class="'form-control'"
                        type="password"
                        placeholder="Create Unlock Pin"
                    />
                    <ErrorMessage className="text-danger mt-2" name="pin" />
                </div>
                <div class="mt-3">
                    <label class="mb-2 form-text-label">E-mail address</label>
                    <Field
                        name="email"
                        :class="'form-control'"
                        type="email"
                        placeholder="E-mail address"
                    />
                    <ErrorMessage className="text-danger mt-2" name="email" />
                </div>
                <div class="row pt-3 pb-2">
                    <div class="col-lg-6 mt-3">
                        <button
                            type="button"
                            class="btn btnstd text-orange w-100 btn-block"
                            @click="playerStore.handleCancel"
                            :disabled="playerStore.actionLoader ? true : false"
                        >
                            Cancel
                        </button>
                    </div>
                    <div class="col-lg-6 mt-3">
                        <button
                            type="submit"
                            :disabled="playerStore.actionLoader ? true : false"
                            class="btn btnstd btnstd__orange w-100 btn-block"
                        >
                            Create
                        </button>
                    </div>
                </div>
            </Form>
        </div>
    </div>
</template>
