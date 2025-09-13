<template>
    <Head title="Create User" />
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link href="">Settings</Link>
                </li>
                <li class="breadcrumb-item">
                    <Link :href="route('users.index')">Users</Link>
                </li>
                <li
                    class="breadcrumb-item active text-muted"
                    aria-current="page"
                >
                    Create
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <!-- Create User Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Create New User</h5>
                        <small class="text-muted">
                            Fill in the details to create a new user
                        </small>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <!-- First Row: Name & Email -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Name</label>
                                    <input
                                        type="text"
                                        id="name"
                                        class="form-control"
                                        v-model="form.name"
                                        placeholder="Enter full name"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="text-danger mt-2">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="email"
                                        id="email"
                                        class="form-control"
                                        v-model="form.email"
                                        placeholder="Enter email address"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="text-danger mt-2">
                                        {{ form.errors.email }}
                                    </div>
                                </div>
                            </div>

                            <!-- Second Row: Password & Confirm Password -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <input
                                            :type="passwordVisible ? 'text' : 'password'"
                                            id="password"
                                            class="form-control"
                                            v-model="form.password"
                                            placeholder="Enter password"
                                            required
                                        />
                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            @click="togglePasswordVisibility"
                                        >
                                            <i
                                                :class="passwordVisible ? 'bx bx-hide' : 'bx bx-show'"
                                            ></i>
                                        </button>
                                    </div>
                                    <div v-if="form.errors.password" class="text-danger mt-2">
                                        {{ form.errors.password }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input
                                        :type="passwordVisible ? 'text' : 'password'"
                                        id="password_confirmation"
                                        class="form-control"
                                        v-model="form.password_confirmation"
                                        placeholder="Confirm password"
                                        required
                                    />
                                    <div
                                        v-if="form.errors.password_confirmation"
                                        class="text-danger mt-2"
                                    >
                                        {{ form.errors.password_confirmation }}
                                    </div>
                                </div>
                            </div>

                            <!-- Roles Selection (Radio Buttons) -->
                            <div class="mb-4">
                                <label class="form-label">Assign Role</label>
                                <div class="d-flex flex-column gap-2">
                                    <div
                                        v-for="role in roles"
                                        :key="role.id"
                                        class="form-check"
                                    >
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            :id="'role-' + role.id"
                                            name="role"
                                            :value="role.id"
                                            v-model="form.role_id"
                                            required
                                        />
                                        <label
                                            class="form-check-label"
                                            :for="'role-' + role.id"
                                        >
                                            {{ role.name }}
                                        </label>
                                    </div>
                                </div>
                                <div v-if="form.errors.role_id" class="text-danger mt-2">
                                    {{ form.errors.role_id }}
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Save User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const props = defineProps({
    roles: Array,
    setting: Object,
    user: Object,
    permissions: Array,
});

const toast = useToast();

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role_id: "",
});

const passwordVisible = ref(false);
const togglePasswordVisibility = () => {
    passwordVisible.value = !passwordVisible.value;
};

const submit = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            toast.success("User created successfully");
        },
        onError: (errors) => {
            if (!Object.keys(errors).length) {
                toast.error(Object.values(errors)[0]);
            }
        },
    });
};
</script>
