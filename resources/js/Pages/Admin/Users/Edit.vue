<template>
    <Head title="Edit User" />
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
                    Edit
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <!-- Edit User Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit User</h5>
                        <small class="text-muted">
                            Update user details below
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
                                    <i class="bx bx-save"></i> Update User
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
    user_x: Object,
    permissions: Array,
});

const toast = useToast();

const form = useForm({
    name: props.user_x.name,
    email: props.user_x.email,
    role_id: props.user_x.roles?.[0]?.id || ""
});

const submit = () => {
    form.put(route("users.update", props.user.id), {
        onSuccess: () => {
            toast.success("User updated successfully");
        },
        onError: (errors) => {
            if (!Object.keys(errors).length) {
                toast.error(Object.values(errors)[0]);
            }
        },
    });
};
</script>
