<script setup>
import { useAuthStore } from "../stores/auth";
import { ref } from "vue";
import { useRoute } from "vue-router";

import SvgLogoImage from '@/img/svgicons/logo.svg?url';
import SvgPasswordImage from '@/img/svgicons/password.svg?url';
import SvgEyeImage from '@/img/svgicons/eye.svg?url'; // Add eye-open icon


const route = useRoute();
const authStore = useAuthStore();

const form = ref({
  password: "",
  password_confirmation: "",
  email: route.query.email,
  token: route.params.token,
});


let passwordVisible = ref(false);

function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value;
}
</script>



<template>
  <div class="container">
    <div class="w-100 mt-6">
      <div class="d-flex justify-content-center">

        <div class="position-relative d-flex flex-column align-items-center form_bg">
          <figure>
            <img :src="SvgLogoImage" alt="">
          </figure>

          <h3>
            Password Reset
          </h3>




          <form class="max-w-md mx-auto bg-slate-100 p-4 rounded-lg mt-12"
            @submit.prevent="authStore.handleResetPassword(form)">

            <!-- Password -->
            <div class="position-relative mt-4">
              <div class="input-group col-md-4">
                <div class="position-absolute fixed-top fixed-bottom px-2   d-flex" :style="{ 'width': 10 + 'px' }">
                  <img :src="SvgPasswordImage" alt="">
                </div>

                <div class="form-row">
                  <input v-model="form.password" class="form-control py-2 px-4 pl-4 mr-2 border fixed-left"
                    style="text-indent:20px;" :type="passwordVisible ? 'text' : 'password'" placeholder="Password"
                    id="password">

                </div>
                <div id="toggle-password" class="py-2 px-2 fixed-right align-items-center svg-orange" style="width: 30px;"
                  @click="togglePasswordVisibility">

                  <img :src="SvgEyeImage" class="svg-orange" alt="">
                </div>
              </div>
            </div>

            <!-- Password Confirmation -->
            <div class="position-relative mt-4">
              <div class="input-group col-md-4">
                <div class="position-absolute fixed-top fixed-bottom px-2   d-flex" :style="{ 'width': 10 + 'px' }">
                  <img :src="SvgPasswordImage" alt="">
                </div>

                <div class="form-row">
                  <input v-model="form.password_confirmation" class="form-control py-2 px-4 pl-4 mr-2 border fixed-left"
                    style="text-indent:20px;" :type="passwordVisible ? 'text' : 'password'"
                    placeholder="Password Confirmation" id="password_confirmation">

                </div>
                <div id="toggle-password" class="py-2 px-2 fixed-right align-items-center svg-orange" style="width: 30px;"
                  @click="togglePasswordVisibility">

                  <img :src="SvgEyeImage" class="svg-orange" alt="">
                </div>
              </div>
              <button type="submit" class="mt-4 btn btn-primary">Reset Password</button>
            </div>


          </form>
        </div>
      </div>
    </div>
  </div>
</template>