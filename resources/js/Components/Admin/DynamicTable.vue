<template>
    <div class="container-fluid p-0">
        <form @submit.prevent="submitFilters" class="row mb-3">
            <div class="col-md-4 mb-2">
                <div class="input-group">
                    <span class="input-group-text menu-icon tf-icons">
                        <i class="bx bx-search"></i>
                    </span>
                    <input
                        v-model="filters.search"
                        type="text"
                        class="form-control"
                        :placeholder="searchPlaceholder"
                    />
                </div>
            </div>

            <div class="col-md-2 mb-2">
                <select v-model="filters.perPage" class="form-select">
                    <option
                        v-for="option in perPageOptions"
                        :key="option"
                        :value="option"
                    >
                        {{ option }} per page
                    </option>
                </select>
            </div>
        </form>

        <div class="table-responsive text-nowrap">
            <table class="table" >
                <thead class="table-light">
                    <tr class="">
                        <th class="text-center checkbox-col" width="20">
                            <input
                                type="checkbox"
                                :checked="allSelected"
                                @change="toggleSelectAll($event)"
                            />
                        </th>
                        <th
                            v-for="col in columns"
                            :key="col.label"
                            :class="[
                                columnClass(col),
                                col.sortable ? 'sortable' : '',
                            ]"
                            @click="col.sortable && changeSort(col.key)"
                            :style="{
                                cursor: col.sortable ? 'pointer' : 'default',
                                width: col.width ? col.width + 'px' : 'auto',
                            }"
                        >
                            {{ col.label }}
                            <span class="sort-icons" v-if="col.sortable">
                                <span
                                    :class="[
                                        'sort-arrow',
                                        {
                                            active:
                                                filters.sortBy === col.key &&
                                                !filters.sortDesc,
                                        },
                                    ]"
                                    >▲</span
                                >
                                <span
                                    :class="[
                                        'sort-arrow',
                                        {
                                            active:
                                                filters.sortBy === col.key &&
                                                filters.sortDesc,
                                        },
                                    ]"
                                    >▼</span
                                >
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in data.data" :key="row.id">
                        <td class="text-center">
                            <input
                                type="checkbox"
                                :value="row.id"
                                v-model="selectedIdsInternal"
                            />
                        </td>
                        <td
                            v-for="col in columns"
                            :key="col.key"
                            :class="columnClass(col)"
                            :style="{
                                width: col.width ? col.width + 'px' : 'auto',
                            }"
                        >
                            <template v-if="$slots['cell-' + col.key]">
                                <slot :name="'cell-' + col.key" :row="row" />
                            </template>
                            <template v-else>
                                <span
                                    v-if="col.format"
                                    v-html="col.format(row[col.key], row)"
                                ></span>
                                <span v-else>{{ row[col.key] }}</span>
                            </template>
                        </td>
                    </tr>
                    <tr v-if="!data.data.length">
                        <td
                            :colspan="columns.length + 1"
                            class="text-center py-4"
                        >
                            No records found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script setup>
import { reactive, watch, computed, ref } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    columns: { type: Array, required: true },
    data: { type: Object, required: true },
    route: { type: String, required: true },
    filters: { type: Object, default: () => ({}) },
    searchPlaceholder: { type: String, default: "Search..." },
    perPageOptions: { type: Array, default: () => [10, 25, 50, 100] },
    selectedIds: { type: Array, default: () => [] },
});

const emit = defineEmits(["update:selectedIds"]);

const filters = reactive({
    ...props.filters,
    sortBy: props.filters.sortBy || "",
    sortDesc: props.filters.sortDesc || false,
});

const selectedIdsInternal = ref([...props.selectedIds]);

const columnClass = (col) => {
    const label = col.label.toString().toLowerCase();
    return label === "actions" || label === "select" ? "text-center" : "";
};

watch(
    selectedIdsInternal,
    (newVal) => {
        emit("update:selectedIds", newVal);
    },
    { deep: true }
);

watch(
    () => props.selectedIds,
    (newVal) => {
        selectedIdsInternal.value.splice(
            0,
            selectedIdsInternal.value.length,
            ...newVal
        );
    }
);

const allSelected = computed(() => {
    if (!props.data.data.length) return false;
    return props.data.data.every((row) =>
        selectedIdsInternal.value.includes(row.id)
    );
});

function toggleSelectAll(event) {
    if (event.target.checked) {
        const newIds = props.data.data.map((row) => row.id);
        const combined = Array.from(
            new Set([...selectedIdsInternal.value, ...newIds])
        );
        selectedIdsInternal.value.splice(
            0,
            selectedIdsInternal.value.length,
            ...combined
        );
    } else {
        const filtered = selectedIdsInternal.value.filter(
            (id) => !props.data.data.some((row) => row.id === id)
        );
        selectedIdsInternal.value.splice(
            0,
            selectedIdsInternal.value.length,
            ...filtered
        );
    }
}

function submitFilters() {
    router.get(
        props.route,
        {
            search: filters.search,
            perPage: filters.perPage,
            sort: filters.sortBy,
            direction: filters.sortDesc ? "desc" : "asc",
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
}

function changeSort(key) {
    if (filters.sortBy === key) {
        filters.sortDesc = !filters.sortDesc;
    } else {
        filters.sortBy = key;
        filters.sortDesc = false;
    }

    router.get(
        props.route,
        {
            search: filters.search,
            perPage: filters.perPage,
            sort: filters.sortBy,
            direction: filters.sortDesc ? "desc" : "asc",
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
}

watch(
    [() => filters.search, () => filters.perPage],
    ([newSearch, newPerPage]) => {
        router.get(
            props.route,
            {
                search: newSearch,
                perPage: newPerPage,
                sort: filters.sortBy,
                direction: filters.sortDesc ? "desc" : "asc",
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                debounce: 500,
            }
        );
    }
);
</script>

<style scoped>
.sortable {
    user-select: none;
}

.sort-icons {
    display: inline-flex;
    flex-direction: column;
    margin-left: 4px;
    font-size: 0.8rem;
    line-height: 0.8rem;
    vertical-align: middle;
}

.sort-arrow {
    color: #ccc;
    font-weight: 100;
    font-size: 10px;
}

.sort-arrow.active {
    color: #000;
    font-weight: bold;
}
</style>
