<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
  status: {
    type: String,
    required: false, // Optional, no need to mark with `?`
  },
  setting: {
    type: Object,
    required: true, // Required, explicitly marked
  },
});

const form = useForm({
  email: "",
});

const submit = () => {
  form.post(route("password.email"));
};
</script>

<template>
  <GuestLayout>
    <Head title="Forgot Password" />
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Forgot Password -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="index.html" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <img
                      :src="'/storage/' + props.setting.site_logo"
                      alt=""
                      style="width: 100px; height: auto; object-fit: cover"
                    />
                  </span>
                 
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Forgot Password? 🔒</h4>
              <p class="mb-4">
                Forgot your password? No problem. Just let us know your email
                address and we will email you a password reset link that will
                allow you to choose a new one.
              </p>
              <div v-if="status" class="mb-4 text-sm font-medium text-success">
                {{ props.status }}
              </div>
              <form
                @submit.prevent="submit"
                id="formAuthentication"
                class="mb-3"
              >
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    placeholder="Enter your email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                  />
                  <InputError
                    class="mt-2 text-danger"
                    :message="form.errors.email"
                  />
                </div>
                <PrimaryButton
                  class="btn btn-primary d-grid w-100"
                  :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                >
                  Send Reset Link
                </PrimaryButton>
              </form>
              <div class="text-center">
                <Link
                  :href="route('login')"
                  class="d-flex align-items-center justify-content-center"
                >
                  <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                  Back to login
                </Link>
              </div>
            </div>
          </div>
          <!-- /Forgot Password -->
        </div>
      </div>
    </div>
    <!-- / Content -->
  </GuestLayout>
</template>
