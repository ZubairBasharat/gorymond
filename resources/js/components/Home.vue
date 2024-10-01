<script setup>

import { onMounted } from "vue";
import { useAuthStore } from "../stores/auth";

const authStore = useAuthStore();

onMounted(async () => {
  await authStore.getUser();

  console.log(authStore.user);
});

</script>

<template>
  <div>
    <header class="d-flex justify-content-between align-items-md-center p-3 mb-5 border border-3 border-primary">
      <div>HOME</div>
    </header>

    GoRaymond Coach Portal


    <div class="max-w-7xl mx-auto">
      <div v-if="authStore.user">
        <h1>{{ authStore.user.first_name }}</h1>
        <h1>{{ authStore.user.last_name }}</h1>
        <h1>{{ authStore.user.phone }}</h1>
        <p>{{ authStore.user.email }}</p>
      </div>
      <div v-else>
        <h1>Go and login</h1>
      </div>
    </div>
  </div>
</template>