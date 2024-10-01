<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/auth";
import SvgLogoImage from '@/img/svgicons/logo.svg?url';
import SvgEmailImage from '@/img/svgicons/email.svg?url';

const authStore = useAuthStore();

const form = ref({
  email: "",
});

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

          <form class="mx-auto max-w-md bg-slate-100 p-4 mt-12 rounded-lg"
            @submit.prevent="authStore.handleForgotPassword(form)">

            <!-- Email -->
            <div class="position-relative mt-4">
              <div class="input-group col-md-4">
                <div class="position-absolute fixed-top fixed-bottom px-2 py-2 d-flex" :style="{ 'width': 10 + 'px' }">

                  <img :src="SvgEmailImage" class="input-icon" alt="">

                </div>

                <div class="form-row">
                  <input v-model="form.email" class="form-control py-2 px-4  pl-4 border" style="text-indent:20px;"
                    type="email" placeholder="Email" id="email">
                </div>
              </div>
            </div>

            <button type="button" class="btn btn-cancel  mt-4" @click="authStore.handleCancel">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary mt-4">
              Submit
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
</template>




<style>
.form-row {
  display: flex;
  flex-wrap: wrap;
}

svg {
  fill: #f5a623;
}

img .svg-orange {
  fill: #f5a623;
  color: #f5a623;
}
</style>