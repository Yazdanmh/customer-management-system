<template>
    <Head title="Settings" />
    <AdminLayout
        :setting="props.settings"
        :user="props.user"
        :permissions="props.permissions"
    >
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item">
                    <Link :href="route('settings.index')">Settings</Link>
                </li>
                <li
                    class="breadcrumb-item active text-muted"
                    aria-current="page"
                >
                    App Settings
                </li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div
                class="card-header d-flex justify-content-between align-items-center"
            >
                <h5 class="mb-0">App Settings</h5>
            </div>

            <div class="card-body">
                <form @submit.prevent="submit">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Language Tabs -->
                            <ul
                                class="nav nav-pills flex-column flex-md-row mb-3"
                                role="tablist"
                            >
                                <li
                                    class="nav-item"
                                    v-for="lang in languages"
                                    :key="lang.code"
                                    role="presentation"
                                >
                                    <button
                                        type="button"
                                        class="nav-link"
                                        :class="{
                                            active: activeTab === lang.code,
                                        }"
                                        @click="activeTab = lang.code"
                                    >
                                        {{ lang.label }}
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content p-0">
                                <div
                                    v-for="lang in languages"
                                    :key="lang.code"
                                    class="tab-pane fade"
                                    :class="{
                                        'show active': activeTab === lang.code,
                                    }"
                                >
                                    <!-- Site Name -->
                                    <div class="mb-3">
                                        <label
                                            :for="'site_name_' + lang.code"
                                            class="form-label"
                                            >Site Name ({{ lang.label }})</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :id="'site_name_' + lang.code"
                                            v-model="form.site_name[lang.code]"
                                            :placeholder="`Enter site name in ${lang.label}`"
                                        />
                                        <div
                                            v-if="
                                                form.errors[
                                                    'site_name.' + lang.code
                                                ]
                                            "
                                            class="text-danger mt-1"
                                        >
                                            {{
                                                form.errors[
                                                    "site_name." + lang.code
                                                ]
                                            }}
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="mb-3">
                                        <label
                                            :for="'address_' + lang.code"
                                            class="form-label"
                                            >Address ({{ lang.label }})</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :id="'address_' + lang.code"
                                            v-model="form.address[lang.code]"
                                            :placeholder="`Enter address in ${lang.label}`"
                                        />
                                        <div
                                            v-if="
                                                form.errors[
                                                    'address.' + lang.code
                                                ]
                                            "
                                            class="text-danger mt-1"
                                        >
                                            {{
                                                form.errors[
                                                    "address." + lang.code
                                                ]
                                            }}
                                        </div>
                                    </div>

                                    <!-- About -->
                                    <div class="mb-3">
                                        <label
                                            :for="'about_' + lang.code"
                                            class="form-label"
                                            >About ({{ lang.label }})</label
                                        >
                                        <textarea
                                            rows="6"
                                            class="form-control"
                                            :id="'about_' + lang.code"
                                            v-model="form.about[lang.code]"
                                            :placeholder="`Write about in ${lang.label}`"
                                        ></textarea>
                                        <div
                                            v-if="
                                                form.errors[
                                                    'about.' + lang.code
                                                ]
                                            "
                                            class="text-danger mt-1"
                                        >
                                            {{
                                                form.errors[
                                                    "about." + lang.code
                                                ]
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <!-- Logo -->
                                <div class="col-md-12 mb-3">
                                    <label for="site_logo" class="form-label"
                                        >Site Logo</label
                                    >
                                    <div
                                        class="d-flex align-items-start align-items-sm-center gap-4"
                                    >
                                        <img
                                            :src="imagePreview || logoUrl"
                                            alt="site-logo"
                                            class="d-block rounded"
                                            height="100"
                                            width="100"
                                            style="object-fit: contain"
                                        />
                                        <div
                                            class="button-wrapper"
                                            v-if="
                                                hasPermission('site_data.edit')
                                            "
                                        >
                                            <label
                                                for="site_logo"
                                                class="btn btn-primary me-2 mb-4"
                                                tabindex="0"
                                            >
                                                <span class="d-none d-sm-block"
                                                    >Upload Logo</span
                                                >
                                                <input
                                                    type="file"
                                                    id="site_logo"
                                                    hidden
                                                    accept="image/png, image/jpeg"
                                                    @change="handleImageUpload"
                                                />
                                            </label>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary account-image-reset mb-4"
                                                @click="resetImage"
                                            >
                                                <span class="d-none d-sm-block"
                                                    >Reset</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        v-if="form.errors.site_logo"
                                        class="text-danger mt-1"
                                    >
                                        {{ form.errors.site_logo }}
                                    </div>
                                </div>

                                <!-- Favicon -->
                                <div class="col-md-12 mb-3">
                                    <label for="site_favicon" class="form-label"
                                        >Site Favicon</label
                                    >
                                    <div
                                        class="d-flex align-items-start align-items-sm-center gap-4"
                                    >
                                        <img
                                            :src="faviconPreview || faviconUrl"
                                            alt="site-favicon"
                                            class="d-block rounded"
                                            height="100"
                                            width="100"
                                            style="object-fit: contain"
                                        />
                                        <div
                                            class="button-wrapper"
                                            v-if="
                                                hasPermission('site_data.edit')
                                            "
                                        >
                                            <label
                                                for="site_favicon"
                                                class="btn btn-primary me-2 mb-4"
                                                tabindex="0"
                                            >
                                                <span class="d-none d-sm-block"
                                                    >Upload Favicon</span
                                                >
                                                <input
                                                    type="file"
                                                    id="site_favicon"
                                                    hidden
                                                    accept="image/png, image/x-icon"
                                                    @change="
                                                        handleFaviconUpload
                                                    "
                                                />
                                            </label>
                                            <button
                                                type="button"
                                                class="btn btn-outline-secondary account-image-reset mb-4"
                                                @click="resetFavicon"
                                            >
                                                <span class="d-none d-sm-block"
                                                    >Reset</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        v-if="form.errors.site_favicon"
                                        class="text-danger mt-1"
                                    >
                                        {{ form.errors.site_favicon }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email, Phone, Socials -->
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input
                                type="text"
                                class="form-control"
                                id="email"
                                v-model="form.email"
                            />
                            <div
                                v-if="form.errors.email"
                                class="text-danger mt-1"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input
                                type="text"
                                class="form-control"
                                id="phone"
                                v-model="form.phone"
                            />
                            <div
                                v-if="form.errors.phone"
                                class="text-danger mt-1"
                            >
                                {{ form.errors.phone }}
                            </div>
                        </div>

                    
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary mt-3"
                        v-if="hasPermission('site_data.edit')"
                    >
                        Save Settings
                    </button>
                </form>
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
    settings: Object,
    user: Object,
    permissions: Array,
});

const toast = useToast();
const activeTab = ref("en");

const languages = [
    { code: "en", label: "English (EN)" },
    { code: "fa", label: "فارسی (FA)" },
    { code: "ps", label: "پښتو (PS)" },
];

const socials = ["facebook", "twitter", "instagram", "linkedin", "youtube"];

const form = useForm({
    site_name: props.settings?.site_name || { en: "", fa: "", ps: "" },
    address: props.settings?.address || { en: "", fa: "", ps: "" },
    about: props.settings?.about || { en: "", fa: "", ps: "" },
    email: props.settings?.email || "",
    phone: props.settings?.phone || "",
    site_logo: null,
    site_favicon: null,
});

const imagePreview = ref(null);
const faviconPreview = ref(null);

const logoUrl = props.settings?.site_logo
    ? `/storage/${props.settings.site_logo}`
    : "/backend/assets/img/image_preview.jpg";
const faviconUrl = props.settings?.site_favicon
    ? `/storage/${props.settings.site_favicon}`
    : "/backend/assets/img/image_preview.jpg";

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.site_logo = file;
        const reader = new FileReader();
        reader.onload = (e) => (imagePreview.value = e.target.result);
        reader.readAsDataURL(file);
    }
};

const handleFaviconUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.site_favicon = file;
        const reader = new FileReader();
        reader.onload = (e) => (faviconPreview.value = e.target.result);
        reader.readAsDataURL(file);
    }
};

const resetImage = () => {
    form.site_logo = null;
    imagePreview.value = logoUrl;
};

const resetFavicon = () => {
    form.site_favicon = null;
    faviconPreview.value = faviconUrl;
};

const hasPermission = (perm) => props.permissions.includes(perm);

const submit = () => {
    const data = { ...form };
    if (!form.site_logo) delete data.site_logo;
    if (!form.site_favicon) delete data.site_favicon;
    form.post(route("settings.update"), {
        preserveScroll: true,
        data,
        onSuccess: () => toast.success("Settings updated successfully"),
        onError: (errors) => {
            if (!Object.keys(errors).length) {
                toast.error(Object.values(errors)[0]);
            }
        },
    });
};
</script>
