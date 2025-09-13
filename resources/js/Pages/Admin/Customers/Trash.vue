<template>
    <Head title="Trashed Customers" />
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link :href="route('customers.index')">Customers</Link>
                </li>
                <li
                    class="breadcrumb-item active text-muted"
                    aria-current="page"
                >
                    Trashed Customers
                </li>
            </ol>
        </nav>

        <div class="card">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <h5>Trashed Customers List</h5>

                <div class="d-flex align-items-center gap-2">
                    <Link
                        :href="route('customers.index')"
                        class="btn btn-icon btn-primary"
                        title="Add New Customer"
                    >
                        <span class="tf-icons bx bx-arrow-back"></span>
                    </Link>
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
                                        @click.prevent="
                                            confirmBulkAction('delete')
                                        "
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
                                        @click.prevent="
                                            confirmBulkAction('restore')
                                        "
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
                    :data="props.customers"
                    :filters="filters"
                    :route="route('customers.trash')"
                    search-placeholder="Search customers..."
                    v-model:selectedIds="selectedIds"
                >
                    <template #cell-actions="{ row }">
                        <div class="text-center">
                           

                            <span
                                v-if="hasPermission('customers.edit')"
                                class="badge bg-label-success p-1_5 me-2 cursor-pointer"
                                @click="confirmRestore(row.id)"
                                title="Restore"
                            >
                                <i class="bx bx-reset"></i>
                            </span>

                            <span
                                v-if="hasPermission('customers.delete')"
                                class="badge bg-label-danger p-1_5 cursor-pointer"
                                @click="confirmDelete(row.id)"
                                title="Delete Permanently"
                            >
                                <i class="bx bx-trash icon-xs"></i>
                            </span>
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
    customers: { type: Object, required: true },
    setting: { type: Object, required: true },
    user: { type: Object, required: true },
    permissions: { type: Array, required: true },
});

const toast = useToast();
const selectedIds = ref([]);

const filters = reactive({
    search: props.customers.filters?.search || "",
    perPage: props.customers.filters?.perPage || 10,
    sort: props.customers.filters?.sort || "id", 
    order: props.customers.filters?.order || "asc",
});
function hasPermission(permission) {
    return props.permissions.includes(permission);
}

const hasSelectedItems = computed(() => selectedIds.value.length > 0);

const columns = computed(() => {
    const baseCols = [
        { key: "id", label: "#", sortable: true },
        {
            key: "image_url",
            label: "Image",
            sortable: false,
            format: (val) => {
                const fallback = "/backend/assets/img/image_preview.jpg";
                const src = val ? `/storage/${val}` : fallback;
                return `<img src="${src}" alt="customer image" style="width:50px; height:50px; object-fit:cover;" class="rounded" />`;
            },
        },
        { key: "name", label: "Name", sortable: true },
        { key: "username", label: "Username", sortable: false },
        {
            key: "deleted_at",
            label: "Deleted At",
            sortable: true,
            format: (value) => (value ? new Date(value).toLocaleString() : "-"),
        },
    ];

    if (hasPermission("customers.edit") || hasPermission("customers.delete")) {
        baseCols.push({ key: "actions", label: "Actions" });
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
        confirmButtonText: "Yes, delete permanently!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCustomer(id);
        }
    });
}

function confirmRestore(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "Restore this customer?",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, restore",
    }).then((result) => {
        if (result.isConfirmed) {
            restoreCustomer(id);
        }
    });
}

function confirmBulkAction(action) {
    if (!hasSelectedItems.value) {
        toast.warning("Please select customers first.");
        return;
    }

    const messages = {
        restore: {
            title: "Restore selected customers?",
            icon: "info",
            confirmText: "Yes, restore",
            color: "#198754",
        },
        delete: {
            title: "Permanently delete selected customers?",
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

function deleteCustomer(id) {
    useForm({}).delete(route("customers.forceDelete", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Customer deleted successfully"),
        onError: (errors) =>
            toast.error(
                Object.values(errors)[0] || "Unexpected error occurred"
            ),
    });
}

function restoreCustomer(id) {
    useForm({}).post(route("customers.restore", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Customer restored successfully"),
        onError: (errors) =>
            toast.error(
                Object.values(errors)[0] || "Unexpected error occurred"
            ),
    });
}

function performBulkDelete() {
    useForm({ ids: selectedIds.value }).delete(
        route("customers.bulkForceDelete"),
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.success("Selected customers deleted successfully");
                selectedIds.value = [];
            },
            onError: (errors) =>
                toast.error(
                    Object.values(errors)[0] || "Unexpected error occurred"
                ),
        }
    );
}

function performBulkRestore() {
    useForm({ ids: selectedIds.value }).post(route("customers.bulkRestore"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected customers restored successfully");
            selectedIds.value = [];
        },
        onError: (errors) =>
            toast.error(
                Object.values(errors)[0] || "Unexpected error occurred"
            ),
    });
}
</script>
