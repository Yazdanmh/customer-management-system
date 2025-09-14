<template>
    <Head :title="`Customer Details - ${props.customer.name}`" />
    <AdminLayout
        :setting="props.setting"
        :user="props.user"
        :permissions="props.permissions"
    >
        <!-- Breadcrumb -->
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
                    Details
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Customer Details</h5>
                        <small class="text-muted"
                            >View customer information</small
                        >
                    </div>

                    <div class="card-body">
                        <!-- Customer Image -->
                        <div class="d-flex align-items-center mb-4">
                            <img
                                :src="imageSrc"
                                alt="Customer Image"
                                class="rounded-circle"
                                style="
                                    width: 100px;
                                    height: 100px;
                                    object-fit: cover;
                                "
                            />
                            <div class="ms-3">
                                <h4 class="mb-0">{{ props.customer.name }}</h4>
                                <small class="text-muted"
                                    >@{{ props.customer.username }}</small
                                >
                            </div>
                        </div>

                        <div class="row">
                            <!-- Customer Info -->
                            <div class="mb-3 col-md-8">
                                <p class="fw-semibold mb-1">Link:</p>
                                <div class="d-flex align-items-center gap-2">
                                    <a
                                        :href="props.customer.link"
                                        target="_blank"
                                        class="text-decoration-none"
                                        v-if="props.customer.link"
                                    >
                                        {{ truncatedLink }}
                                    </a>
                                    <span v-else>-</span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        v-if="props.customer.link"
                                        @click="copyLink"
                                        title="Copy Link"
                                    >
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="fw-semibold mb-1">Password</p>
                                <div class="d-flex align-items-center gap-2">
                                    <span>{{
                                        props.customer.password || "-"
                                    }}</span>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        v-if="props.customer.password"
                                        @click="copyPassword"
                                        title="Copy Password"
                                    >
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <p class="fw-semibold mb-1">Created At:</p>
                                <p>
                                    {{ formatDate(props.customer.created_at) }}
                                </p>
                            </div>
                            <div class="col-md-4 mb-2">
                                <p class="fw-semibold mb-1">Last Updated:</p>
                                <p>
                                    {{ formatDate(props.customer.updated_at) }}
                                </p>
                            </div>
                            <div class="mb-3 col-md-4">
                                <p class="fw-semibold mb-1">Status:</p>
                                <span
                                    class="badge"
                                    :class="
                                        props.customer.status
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        props.customer.status
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 d-flex gap-2">
                            <Link
                                v-if="hasPermission('customers.edit')"
                                :href="
                                    route('customers.edit', props.customer.id)
                                "
                                class="btn btn-primary"
                            >
                                <i class="bx bx-pencil"></i> Edit
                            </Link>
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                @click="goBack"
                            >
                                Back
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    setting: Object,
    user: Object,
    permissions: Array,
    customer: Object,
});

const toast = useToast();

const hasPermission = (permission) => props.permissions.includes(permission);

const imageSrc = computed(() => {
    return props.customer.image_url
        ? `/storage/${props.customer.image_url}`
        : "/backend/assets/img/image_preview.jpg";
});

const truncatedLink = computed(() => {
    if (!props.customer.link) return "-";
    return props.customer.link.length > 50
        ? props.customer.link.substring(0, 50) + "..."
        : props.customer.link;
});

const copyLink = () => {
    if (props.customer.link) {
        navigator.clipboard.writeText(props.customer.link); 
        toast.success("Customer link copied to clipboard");
    }
};
const copyPassword = () => {
    if (props.customer.password) {
        navigator.clipboard.writeText(props.customer.password);
        toast.success("Password copied to clipboard");
    }
};

const formatDate = (date) => {
    if (!date) return "-";
    return new Date(date).toLocaleString("en-GB", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const goBack = () => window.history.back();
</script>
