<script setup lang="ts">
import { defineProps } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";

// Define the props type
const props = defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
    setting: object;
    user: object;
    permissions: string[];
}>();
const hasPermission = (permission: string) => {
    return props.permissions.includes(permission);
};
</script>

<template>
    <Head title="Profile" />

    <!-- Pass props to AdminLayout -->
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link href="#">Account Settings</Link>
                </li>
                <li
                    class="breadcrumb-item active text-muted"
                    aria-current="page"
                >
                    Profile
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-pills flex-column flex-md-row mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);"
                            ><i class="bx bx-user me-1"></i> Account</a
                        >
                    </li>
                </ul>

                <div class="my-3">
                    <UpdateProfileInformationForm
                        :must-verify-email="props.mustVerifyEmail"
                        :status="props.status"
                        class="max-w-xl"
                    />
                    <UpdatePasswordForm class="max-w-xl" />
                </div>
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AdminLayout>
</template>
