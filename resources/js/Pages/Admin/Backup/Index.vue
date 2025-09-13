<template>
    <Head title="Backup Management" />
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
                    <Link :href="route('backup.index')">Backup</Link>
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
                        <h5>Backup History</h5>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button
                                    class="btn btn-outline-primary dropdown-toggle"
                                    type="button"
                                    id="backupDropdown"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    Backup
                                </button>
                                <ul
                                    class="dropdown-menu"
                                    aria-labelledby="backupDropdown"
                                >
                                    <!-- Full Files Backup -->
                                    <li>
                                        <a
                                            class="dropdown-item d-flex align-items-center"
                                            href="#"
                                            @click="startFullBackup"
                                        >
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-outline-success me-2"
                                                title="Full Files Backup"
                                            >
                                                <i
                                                    class="bx bx-folder-open"
                                                ></i>
                                            </button>
                                            Full Files
                                        </a>
                                    </li>

                                    <!-- Database Backup -->
                                    <li>
                                        <a
                                            class="dropdown-item d-flex align-items-center"
                                            href="#"
                                            @click="startDbBackup"
                                            :disabled="isBackingUp"
                                        >
                                            <button
                                                type="button"
                                                class="btn btn-icon btn-outline-warning me-2"
                                                title="Database Backup"
                                            >
                                                <i class="bx bx-data"></i>
                                            </button>
                                            Database
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <DynamicTable
                            :columns="columns"
                            :data="props.backups"
                            :filters="filters"
                            :route="route('backup.index')"
                            search-placeholder="Search backups"
                        >
                            <template #cell-actions="{ row }">
                                <div class="text-center">
                                    <a
                                        :href="
                                            '/admin/backups/download/' +
                                            row.name
                                        "
                                        class="badge bg-label-primary p-1_5 me-2 cursor-pointer"
                                    >
                                        <i class="bx bx-download icon-xs"></i>
                                    </a>

                                    <span
                                        @click="confirmDelete(row.path, row.id)"
                                        class="badge bg-label-danger p-1_5 cursor-pointer"
                                    >
                                        <i class="bx bx-trash icon-xs"></i>
                                    </span>
                                </div>
                            </template>
                        </DynamicTable>
                        <Pagination
                            :data="props.backups"
                            :filters="filters"
                            route-name="backup.index"
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
import { defineProps, reactive, ref, computed } from "vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import DynamicTable from "@/Components/Admin/DynamicTable.vue";
import Pagination from "@/Components/Admin/Pagination.vue";

const props = defineProps({
    setting: Object,
    user: Object,
    permissions: Array,
    backups: Object,
    errors: Object,
});

const isBackingUp = ref(false);
const toast = useToast();

const dbForm = useForm({});
const fullForm = useForm({});
const deleteForm = useForm({ path: "" });

const filters = reactive({
    search: props.backups.filters?.search || "",
    perPage: props.backups.filters?.perPage || 10,
    sort: props.backups.filters?.sort || "id", 
    order: props.backups.filters?.order || "asc",
});
const formatImage = (val, row) => {
    const fallback = "/backend/assets/img/image_preview.jpg";
    const src = val ? `/storage/${row.user.profile_image}` : fallback;

    return `
        <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
            <img 
                src="${src}" 
                alt="${row.user}" 
                class="rounded-circle" 
                style="width:35px; height:35px; object-fit:cover;" 
            />
            <span>${row.user.name}</span>
        </div>
    `;
};

const columns = computed(() => [
    { key: "id", label: "#", sortable: true },
    {
        key: "user_id",
        label: "User",
        sortable: true,
        format: formatImage,
    },
    {
        key: "name",
        label: "Name",
        sortable: false,
    },

    {
        key: "created_at",
        label: "Date Created",
        sortable: true,
        format: (value) => {
            if (!value) return "-";
            return new Date(value).toLocaleString("en-GB", {
                year: "numeric",
                month: "short",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
    },
    { key: "actions", label: "Actions" },
]);

// === Actions ===
const startDbBackup = () => {
    if (isBackingUp.value) return;
    isBackingUp.value = true;

    dbForm.post("/admin/backup/db/now", {
        onSuccess: () => {
            toast.success("Database backup has started.");
            setTimeout(() => window.location.reload(), 4000);
        },
        onError: () => {
            toast.error("Failed to start database backup.");
            setTimeout(() => window.location.reload(), 1000);
        },
        onFinish: () => (isBackingUp.value = false),
    });
};

const startFullBackup = () => {
    if (isBackingUp.value) return;
    isBackingUp.value = true;

    fullForm.post("/admin/backup/full/now", {
        onSuccess: () => {
            toast.success("Full files backup has started.");
            setTimeout(() => window.location.reload(), 10000);
        },
        onError: () => {
            toast.error("Failed to start full files backup.");
            setTimeout(() => window.location.reload(), 7000);
        },
        onFinish: () => (isBackingUp.value = false),
    });
};

const confirmDelete = (path, id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "This backup file will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteFile(path, id);
        }
    });
};

const deleteFile = (path, id) => {
    deleteForm.path = path;
    deleteForm.delete(route("backup.delete"), {
        preserveScroll: true,
        onSuccess: () => toast.success("Backup deleted successfully"),
        onError: () => toast.error("Failed to delete backup"),
    });
};
</script>
