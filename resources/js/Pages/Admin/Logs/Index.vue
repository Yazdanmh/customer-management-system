<template>
    <Head title="Logs" />
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link href="">Settings</Link>
                </li>
                <li
                    class="breadcrumb-item active text-muted"
                    aria-current="page"
                >
                    Logs
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5>System Logs</h5>
                    </div>

                    <div class="card-body">
                        <DynamicTable
                            :columns="columns"
                            :data="props.logs"
                            :filters="filters"
                            :route="route('system.logs')"
                            search-placeholder="Search logs"
                        >
                            <template #cell-level="{ row }">
                                <span
                                    :class="{
                                        'badge bg-label-success':
                                            row.level === 'info',
                                        'badge bg-label-warning':
                                            row.level === 'warning',
                                        'badge bg-label-danger':
                                            row.level === 'error',
                                    }"
                                >
                                    {{ row.level }}
                                </span>
                            </template>
                        </DynamicTable>

                        <Pagination
                            :data="props.logs"
                            :filters="filters"
                            route-name="system.logs"
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
import { defineProps, reactive, computed } from "vue";
import DynamicTable from "@/Components/Admin/DynamicTable.vue";
import Pagination from "@/Components/Admin/Pagination.vue";

const props = defineProps({
    logs: { type: Object, required: true },
    setting: { type: Object, required: true },
    user: { type: Object, required: true },
    permissions: { type: Array, required: true },
});

const filters = reactive({
    search: props.logs.filters?.search || "",
    perPage: props.logs.filters?.perPage || 10,
    sort: props.logs.filters?.sort || "id", 
    order: props.logs.filters?.order || "asc",
});


const columns = computed(() => [
    { key: "id", label: "#", sortable: true },
    {
        key: "causer",
        label: "User",
        sortable: false,
        format: (val, row) => {
            const fallback = "/backend/assets/img/image_preview.jpg";
            const src = row.causer?.profile_image
                ? `/storage/${row.causer.profile_image}`
                : fallback;

            return `
            <div style="display:flex; align-items:center; justify-content:flex-start; gap:8px;">
                <img 
                    src="${src}" 
                    alt="${row.causer?.name || "System"}" 
                    class="rounded-circle" 
                    style="width:35px; height:35px; object-fit:cover;" 
                />
                <span>${row.causer?.name || "System"}</span>
            </div>
        `;
        },
    },
    { key: "description", label: "Description", sortable: true },
    { key: "event", label: "Event", sortable: true },
    

    { key: "properties", label: "Properties", sortable: true },
    {
        key: "created_at",
        label: "Date",
        sortable: true,
        format: (value) => {
            if (!value) return "-";
            const date = new Date(value);
            return date.toLocaleString("en-GB");
        },
    },
]);
</script>
