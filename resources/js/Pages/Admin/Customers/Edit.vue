<template>
    <Head :title="`Edit Customer - ${props.customer.name}`" />
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
                <li class="breadcrumb-item active text-muted" aria-current="page">
                    Edit
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Customer</h5>
                        <small class="text-muted float-end">Update customer details</small>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <!-- Customer Image -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo</label>
                                <div class="d-flex align-items-start gap-4">
                                    <img
                                        v-if="imagePreview"
                                        :src="imagePreview"
                                        alt="Customer Image Preview"
                                        class="d-block rounded"
                                        style="width: 150px; height: 150px; object-fit: cover;"
                                    />
                                    <div class="button-wrapper">
                                        <label for="upload" class="btn btn-primary me-2 mb-2">
                                            Upload Image
                                            <input
                                                type="file"
                                                id="upload"
                                                hidden
                                                accept="image/png, image/jpeg"
                                                @change="handleImageUpload"
                                            />
                                        </label>
                                        <button type="button" class="btn btn-outline-secondary mb-2" @click="resetImage">
                                            Reset
                                        </button>
                                        <p class="text-muted mb-0">
                                            Allowed JPG or PNG. Max size 1MB.
                                        </p>
                                        <span v-if="errors.image_url" class="text-danger mt-2">
                                            {{ errors.image_url }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" v-model="form.name" placeholder="Enter customer name" />
                                    <span v-if="errors.name" class="text-danger">{{ errors.name }}</span>
                                </div>

                                <!-- Username -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" v-model="form.username" placeholder="Enter username" />
                                    <span v-if="errors.username" class="text-danger">{{ errors.username }}</span>
                                </div>

                                <!-- Password -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="text" class="form-control" v-model="form.password" placeholder="Enter password (leave blank to keep current)" />
                                    <span v-if="errors.password" class="text-danger">{{ errors.password }}</span>
                                </div>

                                <!-- Link -->
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Link</label>
                                    <input type="text" class="form-control" v-model="form.link" placeholder="Enter link" />
                                    <span v-if="errors.link" class="text-danger">{{ errors.link }}</span>
                                </div>

                                <!-- Status Toggle -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label d-block">Status</label>
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="statusToggle"
                                            v-model="form.status"
                                        />
                                        <label class="form-check-label" for="statusToggle">
                                            {{ form.status ? "Active" : "Inactive" }}
                                        </label>
                                    </div>
                                    <span v-if="errors.status" class="text-danger">{{ errors.status }}</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary" @click="cancel">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save"></i> Update Customer
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
import { ref } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    setting: Object,
    user: Object,
    permissions: Array,
    customer: Object, // existing customer data
});

const toast = useToast();

// pre-fill form with existing customer data
const form = useForm({
    name: props.customer.name || "",
    username: props.customer.username || "",
    password: "", // leave blank unless changing
    link: props.customer.link || "",
    image_url: null, // new image upload
    status: props.customer.status === 1 || props.customer.status === true, 
});

const errors = ref({});
const imagePreview = ref(props.customer.image_url ? `/storage/${props.customer.image_url}` : null);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 1048576) {
        toast.error("Image exceeds 1MB limit.");
        return;
    }
    if (!file.type.startsWith("image/")) {
        toast.error("Invalid file type.");
        return;
    }
    form.image_url = file;
    const reader = new FileReader();
    reader.onload = (e) => (imagePreview.value = e.target.result);
    reader.readAsDataURL(file);
};

const resetImage = () => {
    form.image_url = null;
    imagePreview.value = props.customer.image_url ? `/storage/${props.customer.image_url}` : null;
    errors.value.image_url = null;
};

const cancel = () => window.history.back();

const submit = () => {
    form.post(route("customers.update", props.customer.id), {
        preserveScroll: true,
        onSuccess: () => toast.success("Customer updated successfully"),
        onError: (err) => {
            errors.value = err;
            toast.error("Error updating customer!");
        },
    });
};
</script>
