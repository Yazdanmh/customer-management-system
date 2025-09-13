<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
    setting: { site_logo: string; site_name: string };
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <!-- Content -->
        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4">
                    <!-- Forgot Password -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Logo -->
                            <div class="app-brand justify-content-center">
                                <a  class="app-brand-link gap-2">
                                    <span class="app-brand-logo demo">
                                        <img
                                            :src="'/storage/' + props.setting.site_logo"
                                            alt="Site Logo"
                                            style="width: 100px; height: auto; object-fit: cover"
                                        />
                                    </span>
                                    <span class="app-brand-text demo text-body fw-bolder">
                                        {{ props.setting.site_name }}
                                    </span>
                                </a>
                            </div>
                            <!-- /Logo -->

                            <h4 class="mb-2">Reset Password 🔒</h4>
                            <p class="mb-4">
                                Reset your password below. Enter your new password and confirm it to update your account securely.
                            </p>

                            <form @submit.prevent="submit" class="mb-3">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        placeholder="Enter your email"
                                        v-model="form.email"
                                        required
                                        autofocus
                                        autocomplete="username"
                                    />
                                    <InputError class="mt-2 text-danger" :message="form.errors.email" />
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        placeholder="Enter your password"
                                        v-model="form.password"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError class="mt-2 text-danger" :message="form.errors.password" />
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password_confirmation"
                                        placeholder="Confirm your password"
                                        v-model="form.password_confirmation"
                                        required
                                        autocomplete="new-password"
                                    />
                                    <InputError
                                        class="mt-2 text-danger"
                                        :message="form.errors.password_confirmation"
                                    />
                                </div>

                                <PrimaryButton
                                    class="btn btn-primary d-grid w-100"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Reset Password
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
