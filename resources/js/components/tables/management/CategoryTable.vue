<template>
  <div>

    <!-- Filters -->
    <div
      class="flex flex-col gap-3 border-b border-gray-200 px-6 py-5 dark:border-gray-800 xl:flex-row xl:items-center xl:justify-between"
    >

      <!-- Search -->
      <div class="relative w-full xl:max-w-md">
        <span
          class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        >
          🔍
        </span>

        <input
          v-model="search"
          type="text"
          placeholder="Search category..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3">

        <select
          v-model="sortBy"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="name">Name</option>
        </select>

        <button
          type="button"
          class="h-11 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
          @click="resetFilters"
        >
          ↻ Reset
        </button>

      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

       <table class="min-w-[1100px] w-full">

        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >
            <!-- Fixed Width # -->
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              #
            </th>

            <!-- Flexible Category Name -->
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Category Name
            </th>

            <!-- Fixed Width Actions -->
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Actions
            </th>
          </tr>
        </thead>

        <tbody>

          <!-- Loading -->
          <tr v-if="loading">
            <td
              colspan="8"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              Loading categories...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredCategories.length === 0">
            <td
              colspan="8"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No categories found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(category, index) in filteredCategories"
            v-else
            :key="category.id_category"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <!-- Number (Presisi Jarak) -->
            <td
              class="w-6 px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <!-- Category Name -->
            <td class="px-6 py-5">
              <p
                class="font-semibold text-gray-800 dark:text-white/90"
              >
                {{ category.name }}
              </p>
            </td>

            <!-- Actions (Posisi Rata Kanan Presisi) -->
            <td class="w-48 px-6 py-5">
              <div class="flex justify-end gap-2">

                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editCategory(category)"
                >
                  ✎
                </button>

                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="viewCategory(category)"
                >
                  👁
                </button>

                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="deleteCategory(category.id_category)"
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
      class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800"
    >

      <p class="text-sm text-gray-500 dark:text-gray-400">
        Showing {{ filteredCategories.length }}
        of {{ categories.length }} results
      </p>

      <div class="flex items-center gap-2">

        <button
          type="button"
          disabled
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700"
        >
          ‹
        </button>

        <button
          type="button"
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white"
        >
          1
        </button>

        <button
          type="button"
          disabled
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700"
        >
          ›
        </button>

      </div>

    </div>

  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import axios from '@/services/axios'

interface Category {
  id_category: number
  name: string
}

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
  (event: 'edit', category: Category): void
  (event: 'view', category: Category): void
}>()

const categories = ref<Category[]>([])
const loading = ref(true)

const search = ref('')
const sortBy = ref('newest')

const filteredCategories = computed(() => {
  let result = [...categories.value]

  // Search
  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()
    result = result.filter((category) =>
      category.name.toLowerCase().includes(keyword)
    )
  }

  // Sort
  if (sortBy.value === 'newest') {
    result.sort((a, b) => b.id_category - a.id_category)
  }

  if (sortBy.value === 'oldest') {
    result.sort((a, b) => a.id_category - b.id_category)
  }

  if (sortBy.value === 'name') {
    result.sort((a, b) => a.name.localeCompare(b.name))
  }

  return result
})

const fetchCategories = async () => {
  loading.value = true

  try {
    const response = await axios.get('/api/categories')
    const data = response.data.data ? response.data.data : response.data
    categories.value = Array.isArray(data) ? data : []

    emit('total-changed', categories.value.length)
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  search.value = ''
  sortBy.value = 'newest'
}

const editCategory = (category: Category) => {
  emit('edit', category)
}

const viewCategory = (category: Category) => {
  emit('view', category)
}

const deleteCategory = async (id: number) => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this category?'
  )

  if (!confirmed) return

  try {
    await axios.delete(`/api/categories/${id}`)

    categories.value = categories.value.filter(
      (category) => category.id_category !== id
    )

    emit('total-changed', categories.value.length)
  } catch (error) {
    console.error('Failed to delete category:', error)
  }
}

watch(
  () => categories.value.length,
  (total) => {
    emit('total-changed', total)
  }
)

defineExpose({
  fetchCategories
})

onMounted(fetchCategories)
</script>