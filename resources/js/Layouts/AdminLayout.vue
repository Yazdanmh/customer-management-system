<template>
    <Head title="Dashboard" />

    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <Sidebar
                :setting="props.setting"
                :permissions="props.permissions"
            />
            <div class="layout-page">
                <Navbar :user="props.user" />
                <!-- / Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <slot></slot>
                    </div>
                    <!-- Content -->
                    <!-- / Content -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
</template>

<script setup>
import { onMounted } from "vue";
import Sidebar from "@/Components/Admin/Sidebar.vue";
import Navbar from "@/Components/Admin/Navbar.vue";
import { Head } from "@inertiajs/vue3";
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    setting: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
});
</script>
