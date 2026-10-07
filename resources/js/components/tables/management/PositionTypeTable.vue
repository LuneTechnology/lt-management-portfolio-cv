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
          placeholder="Search position type..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3">
        <!-- Sort -->
        <select
          v-model="sortBy"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="name">Name A-Z</option>
          <option value="name-desc">Name Z-A</option>
        </select>

        <!-- Reset -->
        <button
          type="button"
          class="h-11 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
          @click="resetFilters"
        >
          ↻ Reset
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full">
        <!-- Header -->
        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >
            <!-- Number -->
            <th
              class="w-16 px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400"
            >
              #
            </th>

            <!-- Position -->
            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400"
            >
              Position Type
            </th>

            <!-- Actions -->
            <th
              class="w-44 px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500 dark:text-gray-400"
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
              class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              Loading position types...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredPositionTypes.length === 0">
            <td
              colspan="3"
              class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              No position type found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(position, index) in filteredPositionTypes"
            v-else
            :key="position.id_position_type"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >
            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <!-- Position Name -->
            <td class="px-6 py-5">
              <div class="flex items-center gap-3">
                <!-- Initial -->
                <div
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                >
                  {{ getInitial(position.name) }}
                </div>

                <!-- Name -->
                <div class="min-w-0">
                  <p
                    class="font-semibold text-gray-800 dark:text-white/90"
                  >
                    {{ position.name }}
                  </p>

                  <p
                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                  >
                    Position Type
                  </p>
                </div>
              </div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-5">
              <div class="flex justify-end gap-2">
                <!-- Edit -->
                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editPositionType(position)"
                >
                  ✎
                </button>

                <!-- View -->
                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300"
                  @click="viewPositionType(position)"
                >
                  👁
                </button>

                <!-- Delete -->
                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="deletePositionType(position.id_position_type)"
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
        Showing {{ filteredPositionTypes.length }}
        of {{ positionTypes.length }} results
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

interface PositionType {
  id_position_type: number
  name: string
}

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
  (event: 'edit', positionType: PositionType): void
  (event: 'view', positionType: PositionType): void
}>()

const positionTypes = ref<PositionType[]>([])
const loading = ref(true)

const search = ref('')
const sortBy = ref('newest')

/**
 * Initial
 */
const getInitial = (name: string): string => {
  if (!name || name === '-') {
    return 'P'
  }

  return name.charAt(0).toUpperCase()
}

/**
 * Filter + Search + Sort
 */
const filteredPositionTypes = computed(() => {
  let result = [...positionTypes.value]

  // Search
  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    result = result.filter((position) =>
      position.name.toLowerCase().includes(keyword)
    )
  }

  // Sort
  if (sortBy.value === 'newest') {
    result.sort(
      (a, b) =>
        (b.id_position_type || 0) -
        (a.id_position_type || 0)
    )
  }

  if (sortBy.value === 'oldest') {
    result.sort(
      (a, b) =>
        (a.id_position_type || 0) -
        (b.id_position_type || 0)
    )
  }

  if (sortBy.value === 'name') {
    result.sort((a, b) =>
      a.name.localeCompare(b.name)
    )
  }

  if (sortBy.value === 'name-desc') {
    result.sort((a, b) =>
      b.name.localeCompare(a.name)
    )
  }

  return result
})

/**
 * Fetch Position Types
 */
const fetchPositionTypes = async () => {
  loading.value = true

  try {
    const response =
      await axios.get('/api/position-types')

    const data = response.data

    if (Array.isArray(data)) {
      positionTypes.value = data
    } else if (Array.isArray(data?.data)) {
      positionTypes.value = data.data
    } else if (
      Array.isArray(data?.data?.data)
    ) {
      positionTypes.value =
        data.data.data
    } else {
      positionTypes.value = []
    }

    emit(
      'total-changed',
      positionTypes.value.length
    )
  } catch (error) {
    console.error(
      'Failed to fetch position types:',
      error
    )

    positionTypes.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Reset
 */
const resetFilters = () => {
  search.value = ''
  sortBy.value = 'newest'
}

/**
 * Edit
 */
const editPositionType = (
  positionType: PositionType
) => {
  emit('edit', positionType)
}

/**
 * View
 */
const viewPositionType = (
  positionType: PositionType
) => {
  emit('view', positionType)
}

/**
 * Delete
 */
const deletePositionType = async (
  id: number
) => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this position type?'
  )

  if (!confirmed) return

  try {
    await axios.delete(
      `/api/position-types/${id}`
    )

    positionTypes.value =
      positionTypes.value.filter(
        (position) =>
          position.id_position_type !== id
      )

    emit(
      'total-changed',
      positionTypes.value.length
    )
  } catch (error) {
    console.error(
      'Failed to delete position type:',
      error
    )
  }
}

/**
 * Watch
 */
watch(
  () => positionTypes.value.length,
  (total) => {
    emit('total-changed', total)
  }
)

onMounted(fetchPositionTypes)
</script>