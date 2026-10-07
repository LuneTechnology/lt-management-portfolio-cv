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

      <!-- Reset -->
      <button
        type="button"
        class="h-11 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
        @click="resetFilters"
      >
        ↻ Reset
      </button>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

      <table class="w-full min-w-[600px]">

        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              #
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Position Type
            </th>

            <th
              class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500"
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
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              Loading position types...
            </td>

          </tr>

          <!-- Empty -->
          <tr
            v-else-if="filteredPositionTypes.length === 0"
          >

            <td
              colspan="3"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No position type found.
            </td>

          </tr>

          <!-- Data -->
          <tr
            v-for="(positionType, index) in filteredPositionTypes"
            v-else
            :key="positionType.id_position_type"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <td class="px-6 py-5">

              <p
                class="font-semibold text-gray-800 dark:text-white/90"
              >
                {{ positionType.name }}
              </p>

            </td>

            <td class="px-6 py-5">

              <div class="flex justify-end gap-2">

                <!-- Edit -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editPositionType(positionType)"
                >
                  ✎
                </button>

                <!-- View -->
                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="viewPositionType(positionType)"
                >
                  👁
                </button>

                <!-- Delete -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="deletePositionType(positionType)"
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

    </div>

  </div>
</template>

<script setup lang="ts">
import {
  computed,
  onMounted,
  ref,
  watch,
} from 'vue'

import axios from '@/services/axios'
import { useAuth } from '@/composables/useAuth'

interface PositionType {
  id_position_type: number
  name: string
}

const props = defineProps<{
  refreshKey: number
}>()

const emit = defineEmits<{
  (
    event: 'total-changed',
    total: number
  ): void

  (
    event: 'edit',
    positionType: PositionType
  ): void

  (
    event: 'view',
    positionType: PositionType
  ): void

  (
    event: 'delete',
    positionType: PositionType
  ): void
}>()

const { isSuperAdmin } = useAuth()

const positionTypes =
  ref<PositionType[]>([])

const loading = ref(true)

const search = ref('')

const filteredPositionTypes = computed(() => {

  let result = [
    ...positionTypes.value,
  ]

  if (search.value.trim()) {

    const keyword =
      search.value
        .toLowerCase()
        .trim()

    result = result.filter(
      (positionType) =>
        positionType.name
          .toLowerCase()
          .includes(keyword)
    )
  }

  return result
})

const fetchPositionTypes = async () => {

  loading.value = true

  try {

    const response =
      await axios.get(
        '/api/position-types'
      )

    positionTypes.value =
      Array.isArray(response.data)
        ? response.data
        : response.data?.data ?? []

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

    emit(
      'total-changed',
      0
    )

  } finally {

    loading.value = false

  }
}

const resetFilters = () => {
  search.value = ''
}

const editPositionType = (
  positionType: PositionType
) => {
  emit(
    'edit',
    positionType
  )
}

const viewPositionType = (
  positionType: PositionType
) => {
  emit(
    'view',
    positionType
  )
}

const deletePositionType = (
  positionType: PositionType
) => {
  emit(
    'delete',
    positionType
  )
}

watch(
  () => props.refreshKey,
  () => {
    fetchPositionTypes()
  }
)

onMounted(fetchPositionTypes)
</script>