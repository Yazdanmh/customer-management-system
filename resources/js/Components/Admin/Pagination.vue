<template>
  <div v-if="lastPage > 1" class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <div class="text-muted small">
      Showing {{ firstItem }} to {{ lastItem }} of {{ total }}
    </div>
    <nav aria-label="Page navigation">
      <ul class="pagination mb-0">
        <!-- Previous Page Link -->
        <li :class="['page-item', { disabled: onFirstPage }]">
          <button
            class="page-link"
            @click="goToPage(currentPage - 1)"
            :disabled="onFirstPage"
            aria-label="Previous"
          >
            <i class="tf-icon bx bx-chevrons-left"></i>
          </button>
        </li>

        <!-- First two pages -->
        <li
          v-for="page in firstPages"
          :key="'first-'+page"
          :class="['page-item', { active: page === currentPage }]"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <!-- Left Ellipsis -->
        <li v-if="startPage > 3" class="page-item disabled">
          <span class="page-link">...</span>
        </li>

        <!-- Middle pages -->
        <li
          v-for="page in middlePages"
          :key="'middle-'+page"
          :class="['page-item', { active: page === currentPage }]"
          v-if="page > 2 && page < lastPage - 1"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <!-- Right Ellipsis -->
        <li v-if="endPage < lastPage - 2" class="page-item disabled">
          <span class="page-link">...</span>
        </li>

        <!-- Last two pages -->
        <li
          v-for="page in lastPages"
          :key="'last-'+page"
          :class="['page-item', { active: page === currentPage }]"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>

        <!-- Next Page Link -->
        <li :class="['page-item', { disabled: onLastPage }]">
          <button
            class="page-link"
            @click="goToPage(currentPage + 1)"
            :disabled="onLastPage"
            aria-label="Next"
          >
            <i class="tf-icon bx bx-chevrons-right"></i>
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  routeName: {
    type: String,
    required: true,
  },
});

// Pagination metadata
const currentPage = computed(() => props.data.current_page || 1);
const lastPage = computed(() => props.data.last_page || 1);
const total = computed(() => props.data.total || 0);
const perPage = computed(() => props.data.per_page || 10);
const firstItem = computed(() => props.data.from || 0);
const lastItem = computed(() => props.data.to || 0);
const onFirstPage = computed(() => currentPage.value === 1);
const onLastPage = computed(() => currentPage.value === lastPage.value);

const startPage = computed(() => Math.max(1, currentPage.value - 1));
const endPage = computed(() => Math.min(lastPage.value, currentPage.value + 1));

// Pages to show
const firstPages = computed(() => {
  // first two pages (1, 2)
  const pages = [];
  for (let i = 1; i <= Math.min(2, lastPage.value); i++) {
    pages.push(i);
  }
  return pages;
});

const middlePages = computed(() => {
  const pages = [];
  for (let i = startPage.value; i <= endPage.value; i++) {
    if (i > 2 && i < lastPage.value - 1) {
      pages.push(i);
    }
  }
  return pages;
});

const lastPages = computed(() => {
  // last two pages (lastPage-1, lastPage)
  const pages = [];
  for (let i = Math.max(lastPage.value - 1, 3); i <= lastPage.value; i++) {
    pages.push(i);
  }
  return pages;
});

function goToPage(page) {
  if (page < 1 || page > lastPage.value || page === currentPage.value) return;

  const params = { ...props.filters, page };

  router.get(route(props.routeName), params, {
    preserveState: true,
    preserveScroll: true,
  });
}

</script>
