<template>
    <Head title="Roles" />
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
                    <Link :href="route('roles.index')">Roles</Link>
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
                        <h5>Roles List</h5>
                        <div class="d-flex align-items-center gap-2">
                            <Link
                                v-if="canCreate"
                                :href="route('roles.create')"
                                class="btn btn-icon btn-primary"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Add New Role"
                            >
                                <span class="tf-icons bx bx-plus"></span>
                            </Link>

                            <Link
                                v-if="canView"
                                :href="route('roles.trash')"
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
                            :data="props.roles"
                            v-model:filters="filters"
                            :route="route('roles.index')"
                            search-placeholder="Search roles"
                            v-model:selectedIds="selectedIds"
                        >
                            <template #cell-actions="{ row }">
                                <div class="text-center">
                                    <Link
                                        v-if="hasPermission('roles.edit')"
                                        :href="route('roles.edit', row.id)"
                                        class="badge bg-label-primary p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-pencil icon-xs"></i>
                                    </Link>
                                    <span
                                        v-if="hasPermission('roles.delete')"
                                        @click="confirmDelete(row.id)"
                                        class="badge bg-label-danger p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-trash icon-xs"></i>
                                    </span>
                                </div>
                            </template>
                        </DynamicTable>
                        <Pagination
                            :data="props.roles"
                            :filters="filters"
                            route-name="roles.index"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import { defineProps, reactive, computed, ref } from "vue";
import { useToast } from "vue-toastification";
import DynamicTable from "@/Components/Admin/DynamicTable.vue";
import { useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Admin/Pagination.vue";
const props = defineProps({
    roles: { type: Object, required: true },
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
    search: props.roles.filters?.search || "",
    perPage: props.roles.filters?.perPage || 10,
    sort: props.roles.filters?.sort || "id", 
    order: props.roles.filters?.order || "asc",
});

function hasPermission(permission) {
    return props.permissions.includes(permission);
}

const canCreate = computed(() => hasPermission("roles.create"));
const canView = computed(() => hasPermission("roles.view"));
const canDelete = computed(() => hasPermission("roles.delete"));
const hasSelectedItems = computed(() => selectedIds.value.length > 0);

const columns = computed(() => {
    const baseCols = [
        {
            key: "id",
            label: "#",
            sortable: true,
            format: (val, row) => val,
        },
        {
            key: "name",
            label: "Role Name",
            sortable: true,
        },
    ];

    if (hasPermission("roles.edit") || hasPermission("roles.delete")) {
        baseCols.push({ key: "actions", label: "Actions" });
    }

    return baseCols;
});

function deleteRole(id) {
    form.delete(route("roles.destroy", id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Role deleted successfully"),
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
            deleteRole(id);
        }
    });
}

function confirmBulkAction(action) {
    if (action === "delete") {
        if (!hasSelectedItems.value) {
            toast.warning("Please select roles first.");
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.value.length} role(s). This action cannot be undone!`,
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
    form.post(route("roles.bulkDelete"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Selected roles deleted successfully");
            selectedIds.value = [];
        },
        onError: (err) => {
            toast.error("Error deleting roles: " + err.message);
        },
    });
}
</script>
