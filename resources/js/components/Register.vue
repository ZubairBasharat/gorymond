<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";

import SvgLogoImage from '@/img/svgicons/logo.svg?url';
import SvgUserImage from '@/img/svgicons/user.svg?url';
import SvgEmailImage from '@/img/svgicons/email.svg?url';
import SvgPasswordImage from '@/img/svgicons/password.svg?url';
import SvgEyeImage from '@/img/svgicons/eye.svg?url'; // Add eye-open icon


const authStore = useAuthStore();

const form = ref({
  name: "none",
  first_name: "",
  last_name: "",

  email: "",
  password: "",
  password_confirmation: "",
});


let passwordVisible = ref(false);

function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value;
}
</script>

<template>
  <div>
    <div class="container">
      <div class="w-100 mt-6">
        <div class="d-flex justify-content-center">

          <div class="position-relative d-flex flex-column align-items-center form_bg">
            <figure>
              <img :src="SvgLogoImage" alt="">
            </figure>

            <h3>
              Sign Up
            </h3>

            <form @submit.prevent="authStore.handleRegister(form)">

              <!-- First Name -->
              <div class="position-relative mt-4">
                <div class="input-group col-md-4">
                  <div class="position-absolute fixed-top fixed-bottom px-2 py-2  d-flex"
                    :style="{ 'width': 10 + 'px' }">
                    <img :src="SvgUserImage" class="input-icon" alt="">
                  </div>

                  <div class="form-row">
                    <input v-model="form.first_name" class="form-control py-2 px-4  pl-4 border"
                      style="text-indent:20px;" type="text" placeholder="First Name" id="first_name">
                  </div>
                </div>
              </div>

              <!-- Last Name -->
              <div class="position-relative mt-4">
                <div class="input-group col-md-4">
                  <div class="position-absolute fixed-top fixed-bottom px-2 py-2 d-flex"
                    :style="{ 'width': 10 + 'px' }">
                    <img :src="SvgUserImage" class="input-icon" alt="">
                  </div>

                  <div class="form-row">
                    <input v-model="form.last_name" class="form-control py-2 px-4  pl-4 border"
                      style="text-indent:20px;" type="text" placeholder="Last Name" id="last_name">
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div class="position-relative mt-4">
                <div class="input-group col-md-4">
                  <div class="position-absolute fixed-top fixed-bottom px-2 py-2  d-flex"
                    :style="{ 'width': 10 + 'px' }">

                    <img :src="SvgEmailImage" class="input-icon" alt="">
                    <!-- <i class=" input-email-icon  input-icon"></i> -->
                  </div>

                  <div class="form-row">
                    <input v-model="form.email" class="form-control py-2 px-4  pl-4 border" style="text-indent:20px;"
                      type="email" placeholder="Email" id="email">
                  </div>
                </div>
              </div>


              <!-- Password -->
              <div class="position-relative mt-4">
                <div class="input-group col-md-4">
                  <div class="position-absolute fixed-top fixed-bottom px-2 py-2   d-flex"
                    :style="{ 'width': 10 + 'px' }">
                    <img :src="SvgPasswordImage" class="input-icon" alt="">
                  </div>

                  <div class="form-row">
                    <input v-model="form.password" class="form-control py-2 px-4 pl-4 mr-2 border fixed-left"
                      style="text-indent:20px;" :type="passwordVisible ? 'text' : 'password'" placeholder="Password"
                      id="password">

                  </div>
                  <div id="toggle-password" class="py-2 px-2 fixed-right align-items-center svg-orange"
                    style="width: 30px;" @click="togglePasswordVisibility">

                    <img :src="SvgEyeImage" class="input-icon svg-orange" alt="">
                  </div>
                </div>
              </div>

              <!-- Password Confirmation -->
              <div class="position-relative mt-4 mb-4">
                <div class="input-group col-md-4">
                  <div class="position-absolute fixed-top fixed-bottom px-2 py-2  d-flex"
                    :style="{ 'width': 10 + 'px' }">
                    <img :src="SvgPasswordImage" class="input-icon" alt="">
                  </div>

                  <div class="form-row">
                    <input v-model="form.password_confirmation"
                      class="form-control py-2 px-4 pl-4 mr-2 border fixed-left" style="text-indent:20px;"
                      :type="passwordVisible ? 'text' : 'password'" placeholder="Password Confirmation"
                      id="password_confirmation">

                  </div>
                  <div id="toggle-password" class="py-2 px-2 fixed-right align-items-center svg-orange"
                    style="width: 30px;" @click="togglePasswordVisibility">

                    <img :src="SvgEyeImage" class="input-icon svg-orange" alt="">
                  </div>
                </div>
              </div>


              <button type="button" class="btn btn-cancel" @click="authStore.handleCancel">Cancel</button>


              <button type="submit" class="btn btn-primary">Submit</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<style scoped>
.input-icon {
  mask-size: cover;
  display: inline-block;
  width: 20px;
  height: 20px;
}



.input-email-icon {

  background: orange;
  display: inline-block;
  width: 20px;
  height: 20px;
  /* Other styles for the element */
  mask: url(@/img/svgicons/email.svg) no-repeat;
  mask-size: cover;
  /* Adapt mask size as needed */
}
</style>
