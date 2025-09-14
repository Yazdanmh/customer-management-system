<template>
    <aside
        id="layout-menu"
        class="layout-menu menu-vertical menu bg-menu-theme"
    >
        <div class="app-brand demo p-2 m-0" style="height: 100px">
            <div class="d-flex flex-column">
                <Link
                    class="app-brand-link p-0 d-flex justify-content-center align-items-center"
                    :href="route('dashboard')"
                    style="height: 60px"
                >
                    <img
                        :src="'/storage/' + props.setting.site_logo"
                        alt="Site Logo"
                        style="
                            max-height: 60px;
                            max-width: 100%;
                            object-fit: contain;
                            margin-left: 20px;
                        "
                    />
                </Link>
            </div>
            <br />
            <a
                href="javascript:void(0);"
                class="layout-menu-toggle menu-link text-large ms-auto d-xl-none close-menu"
            >
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
        </div>
        <div class="menu-container">
            <div class="menu-inner-shadow"></div>
            <ul class="menu-inner py-1">
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Overview</span>
                </li>
                <li
                    :class="{
                        'menu-item': true,
                        active: isActiveRoute('dashboard'),
                    }"
                >
                    <Link :href="route('dashboard')" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div>Dashboard</div>
                    </Link>
                </li>

                <li
                    v-if="
                        hasPermission('customers.view') ||
                        hasPermission('customers.create')
                    "
                    class="menu-header small text-uppercase"
                >
                    <span class="menu-header-text">Pages</span>
                </li>

                <!-- Customers Section -->
                <li
                    v-if="
                        hasPermission('customers.view') ||
                        hasPermission('customers.create')
                    "
                    :class="{
                        'menu-item': true,
                        'active open':
                            isActiveRoute('customers.index') ||
                            isActiveRoute('customers.create') ||
                            isActiveRoute('customers.') ||
                            isActiveRoute('customers.trash'),
                    }"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bxs-news"></i>
                        <div>Customers</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            v-if="hasPermission('customers.create')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('customers.create'),
                            }"
                        >
                            <Link
                                :href="route('customers.create')"
                                class="menu-link"
                            >
                                <div>New Customer</div>
                            </Link>
                        </li>
                        <li
                            v-if="hasPermission('customers.view')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('customers.index'),
                            }"
                        >
                            <Link
                                :href="route('customers.index')"
                                class="menu-link"
                            >
                                <div>All Customers</div>
                            </Link>
                        </li>
                        <li
                            v-if="hasPermission('customers.view')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('customers.trash'),
                            }"
                        >
                            <Link
                                :href="route('customers.trash')"
                                class="menu-link"
                            >
                                <div>Trash</div>
                            </Link>
                        </li>
                    </ul>
                </li>

                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Settings</span>
                </li>

                <!-- Settings -->
                <li
                    v-if="
                        hasPermission('site_data.view') ||
                        hasPermission('users.view') ||
                        hasPermission('system.backup') ||
                        hasPermission('system.view_logs') ||
                        hasPermission('roles.view')
                    "
                    :class="{
                        'menu-item': true,
                        'active open':
                            isActiveRoute('roles.index') ||
                            isActiveRoute('users.index') ||
                            isActiveRoute('backup.index') ||
                            isActiveRoute('system.logs') ||
                            isActiveRoute('settings.index'),
                    }"
                >
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-cog"></i>
                        <div>Settings</div>
                    </a>
                    <ul class="menu-sub">
                        <li
                            v-if="hasPermission('site_data.view')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('settings.index'),
                            }"
                        >
                            <Link
                                :href="route('settings.index')"
                                class="menu-link"
                                ><div>App Settings</div></Link
                            >
                        </li>
                        <li
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('roles.index'),
                            }"
                        >
                            <Link :href="route('roles.index')" class="menu-link"
                                ><div>Roles</div></Link
                            >
                        </li>
                        <li
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('users.index'),
                            }"
                        >
                            <Link :href="route('users.index')" class="menu-link"
                                ><div>Users</div></Link
                            >
                        </li>

                        <li
                            v-if="hasPermission('system.backup')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('backup.index'),
                            }"
                        >
                            <Link
                                :href="route('backup.index')"
                                class="menu-link"
                                ><div>Backups</div></Link
                            >
                        </li>
                        <li
                            v-if="hasPermission('system.view_logs')"
                            :class="{
                                'menu-item': true,
                                active: isActiveRoute('system.logs'),
                            }"
                        >
                            <Link :href="route('system.logs')" class="menu-link"
                                ><div>Logs</div></Link
                            >
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </aside>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
    setting: {
        type: Object,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
});
const { url, currentRouteName } = usePage();

function isActiveRoute(routeName) {
    const currentPath = window.location.pathname.replace(/\/+$/, "");
    const routePath = new URL(
        route(routeName),
        window.location.origin
    ).pathname.replace(/\/+$/, "");
    return currentPath === routePath;
}

const hasPermission = (permission) => {
    return props.permissions.includes(permission);
};
</script>
