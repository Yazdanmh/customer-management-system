<template>
    <Head title="Edit Role" />
    <AdminLayout :setting="props.setting" :user="props.user" :permissions="props.permissions">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link href="">Settings</Link>
                </li>
                <li class="breadcrumb-item">
                    <Link :href="route('roles.index')">Roles</Link>
                </li>
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Edit
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Role</h5>
                        <small class="text-muted">Update role name and permissions</small>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input
                                    type="text"
                                    id="name"
                                    class="form-control"
                                    placeholder="Enter Role Name"
                                    v-model="form.name"
                                    style="max-width: 250px"
                                />
                                <div v-if="form.errors.name" class="text-danger mt-2">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <div class="card mt-4">
                                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h5 class="mb-0">Permissions</h5>
                                        <small class="text-muted">Assign permissions to this role</small>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="selectAll"
                                            v-model="selectAll"
                                            @change="toggleAllPermissions"
                                        />
                                        <label class="form-check-label" for="selectAll">
                                            Select / Unselect All
                                        </label>
                                    </div>
                                </div>

                                <div class="card-body table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Service</th>
                                                <th class="text-center">View</th>
                                                <th class="text-center">Create</th>
                                                <th class="text-center">Edit</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in services" :key="service">
                                                <td class="text-capitalize">{{ service.replace('_', ' ') }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" :value="`${service}.view`" v-model="form.permissions" />
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" :value="`${service}.create`" v-model="form.permissions" />
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" :value="`${service}.edit`" v-model="form.permissions" />
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" :value="`${service}.delete`" v-model="form.permissions" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h6 class="mt-4">System Permissions</h6>
                                    <table class="table table-bordered mt-2">
                                        <thead>
                                            <tr>
                                                <th>Permission</th>
                                                <th class="text-center">Allow</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="perm in systemPermissions" :key="perm.key">
                                                <td>{{ perm.label }}</td>
                                                <td class="text-center">
                                                    <input type="checkbox" :value="perm.key" v-model="form.permissions" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Update Role
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
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { computed, ref, watch } from "vue";

const props = defineProps({
    setting: Object,
    user: Object,
    permissions: { type: Array, required: true },
    assignedPermissions: { type: Array, required: true },
    role: Object
});

const toast = useToast();
const selectAll = ref(false);

const services = [
    "site_data",
    "customers",
    "users",
    "roles",
];

const systemPermissions = [
    { label: "Take Backup", key: "system.backup" },
    { label: "View Logs", key: "system.view_logs" },
];

const form = useForm({
    name: props.role.name,
    permissions: [...props.assignedPermissions],
});

const allPermissions = computed(() => {
    const perms = [];
    services.forEach(service => {
        perms.push(`${service}.view`, `${service}.create`, `${service}.edit`, `${service}.delete`);
    });
    systemPermissions.forEach(perm => perms.push(perm.key));
    return perms;
});

const toggleAllPermissions = () => {
    form.permissions = selectAll.value ? [...allPermissions.value] : [];
};

watch(() => form.permissions, newVal => {
    selectAll.value = newVal.length === allPermissions.value.length;
});

const submit = () => {
    form.put(route("roles.update", props.role.id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Role updated successfully"),
        onError: (errors) => {
            // if (!Object.keys(errors).length) {
                toast.error(Object.values(errors)[0]);
            // }
        },
    });
};
</script>
