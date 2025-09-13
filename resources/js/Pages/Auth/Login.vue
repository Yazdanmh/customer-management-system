<script setup lang="ts">
import { ref } from "vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
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
  password: "",
  remember: false,
});

const passwordVisible = ref(false);

const togglePasswordVisibility = () => {
  passwordVisible.value = !passwordVisible.value;
};

const submit = () => {
  form.post(route("login"), {
    onFinish: () => {
      form.reset("password");
    },
  });
};
</script>

<template>
  <Head title="Login" />
  <div class="container" style="max-width: 700px">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
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
            <h4 class="mb-2">Welcome to {{ props.setting.site_name['en'] }}! 👋</h4>
            <p class="mb-4">
              Please sign-in to your account and start the adventure
            </p>
            <div v-if="status" class="mb-4 text-sm font-medium text-success">
              {{ props.status }}
            </div>
            <!-- Display Error Message -->
            <div class="text-start mb-4">
              <p
                v-if="form.errors.email || form.errors.password"
                class="text-danger"
              >
                {{ form.errors.email || form.errors.password }}
              </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="email" class="form-label">Email or Username</label>
                <input
                  type="email"
                  id="email"
                  class="form-control"
                  placeholder="Enter your email or username"
                  v-model="form.email"
                  required
                  autofocus
                />
              </div>
              <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                  <label class="form-label" for="password">Password</label>
                  <Link :href="route('password.request')">
                    <small>Forgot Password?</small>
                  </Link>
                </div>
                <div class="input-group input-group-merge">
                  <input
                    :type="passwordVisible ? 'text' : 'password'"
                    id="password"
                    class="form-control"
                    v-model="form.password"
                    required
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password"
                  />
                  <span
                    class="input-group-text cursor-pointer"
                    @click="togglePasswordVisibility"
                  >
                    <i
                      :class="passwordVisible ? 'bx bx-show' : 'bx bx-hide'"
                    ></i>
                  </span>
                </div>
              </div>

              <div class="form-check mb-3">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="remember-me"
                  v-model="form.remember"
                />
                <label class="form-check-label" for="remember-me"
                  >Remember Me</label
                >
              </div>

              <button class="btn btn-primary d-grid w-100">Sign in</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

