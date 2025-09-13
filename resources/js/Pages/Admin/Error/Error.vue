<template>
  <!-- Error -->
   <Head title="Page Not Found"/>
  <div class="container-xxl container-p-y py-5">
    <div class="misc-wrapper text-center">
      <h2 class="mb-2 mx-2">Page Not Found :(</h2>
      <p class="mb-4 mx-2">
        Oops! 😖 The requested URL was not found on this server.
      </p>
      <!-- Back button using history -->
      <button class="btn btn-primary" @click="goBack">
        Go Back
      </button>
      <div class="mt-3">
        <img
          src="/backend/assets/img/illustrations/page-misc-error-light.png"
          alt="page-misc-error-light"
          width="500"
          class="img-fluid"
          data-app-dark-img="illustrations/page-misc-error-dark.png"
          data-app-light-img="illustrations/page-misc-error-light.png"
        />
      </div>
    </div>
  </div>
  <!-- /Error -->
</template>

<script setup>
import { Head } from '@inertiajs/vue3';

const goBack = () => {
  window.history.length > 1 ? window.history.back() : window.location.href = '/';
};
</script>
