<template>
    <Head title="Users" />
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
                    All
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5>Users List</h5>
                        <div class="d-flex align-items-center gap-2">
                            <Link
                                v-if="canCreate"
                                :href="route('users.create')"
                                class="btn btn-icon btn-primary"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Add New User"
                            >
                                <span class="tf-icons bx bx-plus"></span>
                            </Link>

                            <Link
                                v-if="canView"
                                :href="route('users.trash')"
                                class="btn btn-icon btn-danger"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="View Trashed Users"
                            >
                                <span class="tf-icons bx bx-trash-alt"></span>
                            </Link>

                            <form
                                id="bulk-action-form"
                                method="POST"
                                class="mb-0"
                                v-if="canDelete"
                                @submit.prevent="performBulkDelete"
                            >
                                <input
                                    type="hidden"
                                    name="_method"
                                    value="DELETE"
                                />
                                <input
                                    type="hidden"
                                    name="_token"
                                    :value="csrfToken"
                                />
                                <input
                                    type="hidden"
                                    name="ids[]"
                                    id="bulk-action-ids"
                                    :value="selectedIds"
                                />
                                <div class="dropdown">
                                    <button
                                        class="btn btn-outline-primary dropdown-toggle"
                                        type="button"
                                        id="bulkActionDropdown"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        :disabled="!hasSelectedItems"
                                    >
                                        Bulk Actions
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        aria-labelledby="bulkActionDropdown"
                                    >
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                href="#"
                                                @click.prevent="
                                                    confirmBulkAction('delete')
                                                "
                                            >
                                                <button
                                                    type="button"
                                                    class="btn btn-icon btn-outline-danger mb-1"
                                                    title="Delete"
                                                >
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                Delete Selected
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <DynamicTable
                            :columns="columns"
                            :data="props.users"
                            :filters="filters"
                            :route="route('users.index')"
                            search-placeholder="Search users"
                            v-model:selectedIds="selectedIds"
                        >
                            <template #cell-actions="{ row }">
                                <div class="text-center">
                                    <Link
                                        v-if="hasPermission('users.edit')"
                                        :href="route('users.edit', row.id)"
                                        class="badge bg-label-primary p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-pencil icon-xs"></i>
                                    </Link>

                                    <span
                                        v-if="hasPermission('users.delete')"
                                        @click="confirmDelete(row.id)"
                                        class="badge bg-label-danger p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-trash icon-xs"></i>
                                    </span>

                                    <span
                                        v-if="hasPermission('users.edit')"
                                        class="badge bg-label-warning p-1_5 cursor-pointer"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changePasswordModal"
                                        @click="setPasswordUser(row.id)"
                                    >
                                        <i class="bx bx-key icon-xs"></i>
                                    </span>
                                </div>
                            </template>
                        </DynamicTable>
                        <Pagination
                            :data="props.users"
                            :filters="filters"
                            route-name="users.index"
                        />
                    </div>
                </div>
            </div>
        </div>
        <!-- Change Password Modal -->
        <div
            class="modal fade"
            id="changePasswordModal"
            tabindex="-1"
            aria-labelledby="changePasswordModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            Change Password
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitPasswordChange">
                            <div class="mb-3">
                                <label for="newPassword" class="form-label"
                                    >New Password</label
                                >
                                <input
                                    type="password"
                                    id="newPassword"
                                    class="form-control"
                                    v-model="passwordForm.password"
                                    placeholder="Enter new password"
                                    required
                                />
                                <div
                                    v-if="passwordForm.errors.password"
                                    class="text-danger mt-1"
                                >
                                    {{ passwordForm.errors.password }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label
                                    for="confirmNewPassword"
                                    class="form-label"
                                    >Confirm Password</label
                                >
                                <input
                                    type="password"
                                    id="confirmNewPassword"
                                    class="form-control"
                                    v-model="passwordForm.password_confirmation"
                                    placeholder="Confirm new password"
                                    required
                                />
                                <div
                                    v-if="
                                        passwordForm.errors
                                            .password_confirmation
                                    "
                                    class="text-danger mt-1"
                                >
                                    {{
                                        passwordForm.errors
                                            .password_confirmation
                                    }}
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    Change Password
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
import Swal from "sweetalert2";
import { defineProps, reactive, computed, ref } from "vue";
import { useToast } from "vue-toastification";
import DynamicTable from "@/Components/Admin/DynamicTable.vue";
import Pagination from "@/Components/Admin/Pagination.vue";

const props = defineProps({
    users: { type: Object, required: true },
    setting: { type: Object, required: true },
    user: { type: Object, required: true },
    permissions: { type: Array, required: true },
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

const selectedIds = ref([]);
const toast = useToast();
const form = useForm({
    ids: "",
});

const filters = reactive({
    search: props.users.filters?.search || "",
    perPage: props.users.filters?.perPage || 10,
    sort: props.users.filters?.sort || "id", 
    order: props.users.filters?.order || "asc",
});

function hasPermission(permission) {
    return props.permissions.includes(permission);
}

const canCreate = computed(() => hasPermission("users.create"));
const canView = computed(() => hasPermission("users.view"));
const canDelete = computed(() => hasPermission("users.delete"));
const hasSelectedItems = computed(() => selectedIds.value.length > 0);
const formatImage = (val, row) => {
    const fallback = "/backend/assets/img/image_preview.jpg";
    const src = val ? `/storage/${val}` : fallback;

    return `
        <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
            <img 
                src="${src}" 
                alt="${row.name}" 
                class="rounded-circle" 
                style="width:35px; height:35px; object-fit:cover;" 
            />
            <span>${row.name}</span>
        </div>
    `;
};

const columns = computed(() => {
    const baseCols = [
        {
            key: "id",
            label: "#",
            sortable: true,
        },
        {
            key: "profile_image",
            label: "User",
            sortable: false,
            format: formatImage,
        },
        {
            key: "email",
            label: "Email",
            sortable: true,
        },
        {
            key: "created_at",
            label: "Date",
            sortable: true,
            format: (value) => {
                if (!value) return "-";
                const date = new Date(value);
                return date.toLocaleDateString("en-GB", {
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                });
            },
        },
    ];

    if (hasPermission("users.edit") || hasPermission("users.delete")) {
        baseCols.push({ key: "actions", label: "Actions" });
    }

    return baseCols;
});

function deleteUser(id) {
    form.delete(route("users.destroy", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("User deleted successfully"),
        onError: (err) => toast.error("Error: " + err.message),
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteUser(id);
        }
    });
}

function confirmBulkAction(action) {
    if (action === "delete") {
        if (!hasSelectedItems.value) {
            toast.warning("Please select users first.");
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.value.length} user(s). This action cannot be undone!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete them!",
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkDelete();
            }
        });
    }
}

function performBulkDelete() {
    form.ids = selectedIds.value;
    form.delete(route("users.bulkDelete"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected users deleted successfully");
            selectedIds.value = [];
        },
        onError: (err) => {
            toast.error("Error deleting users: " + err.message);
        },
    });
}
const passwordForm = useForm({
    user_id: null,
    password: "",
    password_confirmation: "",
});

const setPasswordUser = (userId) => {
    passwordForm.user_id = userId;
    passwordForm.password = "";
    passwordForm.password_confirmation = "";
};

const submitPasswordChange = () => {
    passwordForm.put(route("users.changePassword", passwordForm.user_id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Password changed successfully");
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("changePasswordModal")
            );
            modal.hide();
        },
        onError: (err) => toast.error("Failed to change password"),
    });
};
</script>
