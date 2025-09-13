<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const toast = useToast();

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const updatePassword = () => {
  form.put(route("password.update"), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      toast.success("Password updated successfully!");
    },
    onError: () => {
      toast.error("Please check the form for errors.");
      if (form.errors.password) {
        form.reset("password", "password_confirmation");
        passwordInput.value?.focus();
      }
      if (form.errors.current_password) {
        form.reset("current_password");
        currentPasswordInput.value?.focus();
      }
    },
  });
};
</script>


<template>
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Update Password</h5>
      <p class="text-muted small">
        Ensure your account is using a long, random password to stay secure.
      </p>
    </div>
    <div class="card-body">
      <form @submit.prevent="updatePassword" class="row g-3">
        <!-- Current Password -->
        <div class="mb-3 col-md-12">
          <label for="current_password" class="form-label"
            >Current Password</label
          >
          <input
            id="current_password"
            ref="currentPasswordInput"
            v-model="form.current_password"
            type="password"
            class="form-control"
            placeholder="Enter current password"
            autocomplete="current-password"
          />
          <div
            class="text-danger small mt-1"
            v-if="form.errors.current_password"
          >
            {{ form.errors.current_password }}
          </div>
        </div>

        <!-- New Password -->
        <div class="mb-3 col-md-12">
          <label for="password" class="form-label">New Password</label>
          <input
            id="password"
            ref="passwordInput"
            v-model="form.password"
            type="password"
            class="form-control"
            placeholder="Enter new password"
            autocomplete="new-password"
          />
          <div class="text-danger small mt-1" v-if="form.errors.password">
            {{ form.errors.password }}
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3 col-md-12">
          <label for="password_confirmation" class="form-label"
            >Confirm Password</label
          >
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            class="form-control"
            placeholder="Confirm new password"
            autocomplete="new-password"
          />
          <div
            class="text-danger small mt-1"
            v-if="form.errors.password_confirmation"
          >
            {{ form.errors.password_confirmation }}
          </div>
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="col-md-12 d-flex justify-content-start mt-2">
          <button
            type="submit"
            class="btn btn-primary me-2"
            :disabled="form.processing"
          >
            Save Changes
          </button>
          <button
            type="reset"
            class="btn btn-outline-secondary"
            @click="form.reset()"
          >
            Cancel
          </button>
        </div>

        <!-- Success Message -->
        <div class="text-success small mt-2" v-if="form.recentlySuccessful">
          Password updated successfully.
        </div>
      </form>
    </div>
  </div>
</template>
