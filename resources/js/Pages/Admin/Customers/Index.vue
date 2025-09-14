<template>
    <Head title="Customers" />
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link href="#">Pages</Link>
                </li>
                <li class="breadcrumb-item">
                    <Link :href="route('customers.index')">Customers</Link>
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
                        <h5>Customers List</h5>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Export/Excel Button -->
                            <a
                                v-if="canView"
                                :href="route('export')"
                                class="btn btn-icon btn-outline-info"
                                title="Export to Excel"
                            >
                                <span class="tf-icons bx bx-export"></span>
                            </a>

                            <!-- Add New Customer -->
                            <Link
                                v-if="canCreate"
                                :href="route('customers.create')"
                                class="btn btn-icon btn-primary"
                                title="Add New Customer"
                            >
                                <span class="tf-icons bx bx-plus"></span>
                            </Link>

                            <!-- View Trashed Customers -->
                            <Link
                                v-if="canView"
                                :href="route('customers.trash')"
                                class="btn btn-icon btn-danger"
                                title="View Trashed Customers"
                            >
                                <span class="tf-icons bx bx-trash-alt"></span>
                            </Link>

                            <!-- Bulk Actions -->
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
                            :data="props.customers"
                            :filters="filters"
                            :route="route('customers.index')"
                            search-placeholder="Search customers"
                            v-model:selectedIds="selectedIds"
                        >
                            <template #cell-actions="{ row }">
                                <div class="text-center">
                                    <Link
                                        v-if="hasPermission('customers.view')"
                                        :href="route('customers.show', row.id)"
                                        class="badge bg-label-warning p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-show icon-xs"></i>
                                    </Link>
                                    <Link
                                        v-if="hasPermission('customers.edit')"
                                        :href="route('customers.edit', row.id)"
                                        class="badge bg-label-primary p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-pencil icon-xs"></i>
                                    </Link>

                                    <span
                                        v-if="hasPermission('customers.delete')"
                                        @click="confirmDelete(row.id)"
                                        class="badge bg-label-danger p-1_5 cursor-pointer"
                                    >
                                        <i class="bx bx-trash icon-xs"></i>
                                    </span>
                                </div>
                            </template>
                        </DynamicTable>
                        <Pagination
                            :data="props.customers"
                            :filters="filters"
                            route-name="customers.index"
                        />
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
    customers: { type: Object, required: true },
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
const form = useForm({ ids: "" });

const filters = reactive({
    search: props.customers.filters?.search || "",
    perPage: props.customers.filters?.perPage || 10,
    sort: props.customers.filters?.sort || "id",
    order: props.customers.filters?.order || "asc",
});

function hasPermission(permission) {
    return props.permissions.includes(permission);
}

const canCreate = computed(() => hasPermission("customers.create"));
const canView = computed(() => hasPermission("customers.view"));
const canDelete = computed(() => hasPermission("customers.delete"));
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
        { key: "username", label: "Username", sortable: true },
        {
            key: "link",
            label: "Link",
            sortable: false,
            format: (val) => {
                if (!val) return "-";

                const truncated =
                    val.length > 30 ? val.substring(0, 30) + "..." : val;

                return `
            <div class="d-flex align-items-center gap-2">
                <a href="${val}" 
                   target="_blank" 
                   class="text-decoration-none" 
                   title="${val}">
                    ${truncated}
                </a>
                <button type="button" 
                    class="btn btn-sm btn-outline-secondary" 
                    title="Copy Link"
                    onclick="navigator.clipboard.writeText('${val}').then(() => { 
                        window.dispatchEvent(new CustomEvent('toast', {detail: 'Link copied to clipboard'})) 
                    })">
                    <i class="bx bx-copy"></i>
                </button>
            </div>
        `;
            },
        },
        {
            key: "status",
            label: "Status",
            sortable: false,
            format: (val, row) => {
                const checked = !!val; // convert to boolean
                return `
            <div class="form-check form-switch text-center">
                <input class="form-check-input" type="checkbox" id="statusSwitch${
                    row.id
                }" ${
                    checked ? "checked" : ""
                } onchange="window.updateCustomerStatus(${
                    row.id
                }, this.checked)">
                <label class="form-check-label" for="statusSwitch${row.id}">${
                    checked ? "Active" : "Inactive"
                }</label>
            </div>
        `;
            },
        },
    ];

    if (hasPermission("customers.edit") || hasPermission("customers.delete")) {
        baseCols.push({ key: "actions", label: "Actions" });
    }

    return baseCols;
});

function deleteCustomer(id) {
    form.delete(route("customers.destroy", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Customer deleted successfully"),
        onError: (err) => toast.error("Error: " + err.message),
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This customer will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteCustomer(id);
        }
    });
}

function confirmBulkAction(action) {
    if (action === "delete") {
        if (!hasSelectedItems.value) {
            toast.warning("Please select customers first.");
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.value.length} customer(s). This cannot be undone!`,
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
    form.delete(route("customers.bulkDelete"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected customers deleted successfully");
            selectedIds.value = [];
        },
        onError: (err) =>
            toast.error("Error deleting customers: " + err.message),
    });
}

const statusForm = useForm({ status: false });

window.updateCustomerStatus = (id, currentStatus) => {
    statusForm.status = currentStatus;
    statusForm.post(route("customers.toggle-status", id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                `Customer ${
                    currentStatus ? "activated" : "deactivated"
                } successfully`
            );
        },
        onError: (err) => {
            toast.error("Error updating status");
            console.error(err);
        },
    });
};
window.addEventListener("toast", (e) => {
    toast.clear(); // removes existing toasts
    toast.success(e.detail);
});
</script>
