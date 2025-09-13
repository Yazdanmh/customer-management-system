<template>
    <Head title="Trashed Users" />
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
                    Trashed Users
                </li>
            </ol>
        </nav>

        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <h5>Trashed Users List</h5>
                <div class="d-flex align-items-center gap-2">
                    <form class="mb-0" @submit.prevent>
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
                                        @click.prevent="confirmBulkAction('delete')"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-icon btn-outline-danger mb-1"
                                        >
                                            <i class="bx bx-trash"></i>
                                        </button>
                                        Delete Selected
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="#"
                                        @click.prevent="confirmBulkAction('restore')"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-icon btn-outline-success mb-1"
                                        >
                                            <i class="bx bx-reset"></i>
                                        </button>
                                        Restore Selected
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
                    :route="route('users.trash')"
                    search-placeholder="Search users..."
                    v-model:selectedIds="selectedIds"
                >
                    <template #cell-actions="{ row }">
                        <div class="text-center">
                            <Link
                                v-if="hasPermission('users.edit')"
                                class="badge bg-label-success p-1_5 me-2 cursor-pointer"
                                @click="confirmRestore(row.id)"
                                title="Restore"
                            >
                                <i class="bx bx-reset"></i>
                            </Link>

                            <Link
                                v-if="hasPermission('users.delete')"
                                class="badge bg-label-danger p-1_5 me-2 cursor-pointer"
                                @click="confirmDelete(row.id)"
                                title="Delete Permanently"
                            >
                                <i class="bx bx-trash icon-xs"></i>
                            </Link>
                        </div>
                    </template>
                </DynamicTable>
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

const props = defineProps({
    users: { type: Object, required: true },
    setting: { type: Object, required: true },
    user: { type: Object, required: true },
    permissions: { type: Array, required: true },
});

const toast = useToast();
const selectedIds = ref([]);

const filters = reactive({
    search: props.users.filters?.search || "",
    perPage: props.users.filters?.perPage || 10,
    sort: props.users.filters?.sort || "id", 
    order: props.users.filters?.order || "asc",
});
function hasPermission(permission) {
    return props.permissions.includes(permission);
}
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
        { key: "id", label: "#", sortable: true },
        {
            key: "profile_image",
            label: "User",
            sortable: false,
            format: formatImage,
        },
        { key: "email", label: "Email", sortable: true },
    ];
    if (hasPermission("users.edit") || hasPermission("users.delete")) {
        baseCols.push({
            key: "actions",
            label: "Actions",
            class: "text-center",
        });
    }
    return baseCols;
});

function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteUser(id);
        }
    });
}

function confirmRestore(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "Restore this user?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, restore",
    }).then((result) => {
        if (result.isConfirmed) {
            restoreUser(id);
        }
    });
}

function confirmBulkAction(action) {
    if (!hasSelectedItems.value) {
        toast.warning("Please select users first.");
        return;
    }

    const messages = {
        restore: {
            title: "Restore selected users?",
            icon: "info",
            confirmText: "Yes, restore",
            color: "#198754",
        },
        delete: {
            title: "Permanently delete selected users?",
            icon: "warning",
            confirmText: "Yes, delete",
            color: "#d33",
        },
    };

    Swal.fire({
        title: "Are you sure?",
        text: messages[action].title,
        icon: messages[action].icon,
        showCancelButton: true,
        confirmButtonColor: messages[action].color,
        cancelButtonColor: "#6c757d",
        confirmButtonText: messages[action].confirmText,
    }).then((result) => {
        if (result.isConfirmed) {
            action === "restore" ? performBulkRestore() : performBulkDelete();
        }
    });
}

function deleteUser(id) {
    useForm({}).delete(route("users.forceDelete", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("User deleted successfully"),
        onError: (errors) =>
            toast.error(Object.values(errors)[0] || "Unexpected error occurred"),
    });
}

function restoreUser(id) {
    useForm({}).post(route("users.restore", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("User restored successfully"),
        onError: (errors) =>
            toast.error(Object.values(errors)[0] || "Unexpected error occurred"),
    });
}

function performBulkDelete() {
    useForm({ ids: selectedIds.value }).delete(route("users.bulkForceDelete"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected users deleted successfully");
            selectedIds.value = [];
        },
        onError: (errors) =>
            toast.error(Object.values(errors)[0] || "Unexpected error occurred"),
    });
}

function performBulkRestore() {
    useForm({ ids: selectedIds.value }).post(route("users.bulkRestore"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected users restored successfully");
            selectedIds.value = [];
        },
        onError: (errors) =>
            toast.error(Object.values(errors)[0] || "Unexpected error occurred"),
    });
}
</script>
