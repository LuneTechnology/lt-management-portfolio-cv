<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import axios from '@/services/axios'

import { useAuth } from '@/composables/useAuth'

interface Category {
  id_category: number
  name: string
}

const props = defineProps<{
  refreshKey?: number
}>()

const emit = defineEmits<{
  (e: 'total-changed', total: number): void
  (e: 'edit', category: Category): void
  (e: 'view', category: Category): void
  (e: 'delete', category: Category): void
}>()

const { isSuperAdmin } = useAuth()

const categories = ref<Category[]>([])

const search = ref('')

const loading = ref(false)

const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| Fetch Categories
|--------------------------------------------------------------------------
*/
const fetchCategories = async () => {
  try {
    loading.value = true
    errorMessage.value = ''

    const response = await axios.get('/api/category')

    const data =
      response.data?.data ??
      response.data

    categories.value = Array.isArray(data)
      ? data
      : []

    emit(
      'total-changed',
      categories.value.length,
    )
  } catch (error: any) {
    console.error(
      'Failed to fetch categories:',
      error,
    )

    categories.value = []

    emit('total-changed', 0)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load categories.'
  } finally {
    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/
const filteredCategories = computed(() => {
  const keyword = search.value
    .trim()
    .toLowerCase()

  if (!keyword) {
    return categories.value
  }

  return categories.value.filter((category) =>
    category.name
      .toLowerCase()
      .includes(keyword),
  )
})

/*
|--------------------------------------------------------------------------
| Reset Search
|--------------------------------------------------------------------------
*/
const resetSearch = () => {
  search.value = ''
}

/*
|--------------------------------------------------------------------------
| Refresh
|--------------------------------------------------------------------------
*/
watch(
  () => props.refreshKey,
  () => {
    fetchCategories()
  },
)

/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/
onMounted(() => {
  fetchCategories()
})
</script>

<template>
  <div
    class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <!-- Header -->
    <div
      class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6"
    >
      <div>
        <h3
          class="text-base font-semibold text-gray-800 dark:text-white/90"
        >
          Category
        </h3>

        <p
          class="mt-1 text-sm text-gray-500 dark:text-gray-400"
        >
          List of available project categories.
        </p>
      </div>

      <!-- Search -->
      <div
        class="flex flex-col gap-2 sm:flex-row"
      >
        <div class="relative">
          <input
            v-model="search"
            type="text"
            placeholder="Search category..."
            class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 pr-10 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-gray-500 sm:w-64"
          />

          <span
            class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"
          >
            🔍
          </span>
        </div>

        <button
          type="button"
          class="h-10 rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
          @click="resetSearch"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Error -->
    <div
      v-if="errorMessage"
      class="mx-5 mt-5 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400 sm:mx-6"
    >
      {{ errorMessage }}
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full min-w-[500px]">
        <thead>
          <tr
            class="border-b border-gray-200 dark:border-gray-800"
          >
            <th
              class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6"
            >
              #
            </th>

            <th
              class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
            >
              Category
            </th>

            <th
              class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6"
            >
              Actions
            </th>
          </tr>
        </thead>

        <tbody>
          <!-- Loading -->
          <tr v-if="loading">
            <td
              colspan="3"
              class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              Loading categories...
            </td>
          </tr>

          <!-- Empty -->
          <tr
            v-else-if="filteredCategories.length === 0"
          >
            <td
              colspan="3"
              class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              {{
                search
                  ? 'No category found.'
                  : 'No categories available.'
              }}
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(category, index) in filteredCategories"
            :key="category.id_category"
            class="border-b border-gray-100 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >
            <!-- Number -->
            <td
              class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6"
            >
              {{ index + 1 }}
            </td>

            <!-- Name -->
            <td
              class="px-5 py-4 text-base font-semibold text-gray-800 dark:text-white/90"
            >
              {{ category.name }}
            </td>

            <!-- Actions -->
            <td
              class="px-5 py-4 sm:px-6"
            >
              <div
                class="flex justify-end gap-2"
              >
                <!-- View -->
                <button
                  type="button"
                  title="View"
                  aria-label="View category"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="emit('view', category)"
                >
                  👁
                </button>

                <!-- Edit -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Edit"
                  aria-label="Edit category"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="emit('edit', category)"
                >
                  ✎
                </button>

                <!-- Delete -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Delete"
                  aria-label="Delete category"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="emit('delete', category)"
                >
                  🗑
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <div
      class="border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6"
    >
      <p
        class="text-sm text-gray-500 dark:text-gray-400"
      >
        Showing
        <span
          class="font-medium text-gray-700 dark:text-gray-300"
        >
          {{ filteredCategories.length }}
        </span>
        of
        <span
          class="font-medium text-gray-700 dark:text-gray-300"
        >
          {{ categories.length }}
        </span>
        categories
      </p>
    </div>
  </div>
</template>