<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <!-- Content -->
        <div class="container-xxl">
            <div class="authentication-wrapper authentication-basic container-p-y">
                <div class="authentication-inner py-4">
                    <!-- Forgot Password -->
                    <div class="card">
                        <div class="card-body">
                            

                            <h4 class="mb-2">Security</h4>
                            <p class="mb-4">
                                This is a secure area of the application. Please confirm your
                                password before continuing.
                            </p>

                            <form @submit.prevent="submit" class="mb-3">
                              

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        placeholder="Enter your password"
                                        v-model="form.password"
                                        required
                                        autocomplete="current-password"
                                    />
                                    <InputError class="mt-2 text-danger" :message="form.errors.password" />
                                </div>

                        

                                <PrimaryButton
                                    class="btn btn-primary d-grid w-100"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Reset Password
                                </PrimaryButton>
                            </form>

                      
                        </div>
                    </div>
                    <!-- /Forgot Password -->
                </div>
            </div>
        </div>
        <!-- / Content -->

    </GuestLayout>
</template>
