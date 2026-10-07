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
          placeholder="Search experience..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3">
        <!-- Work Type -->
        <select
          v-model="selectedWorkType"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">All Work Type</option>

          <option
            v-for="type in availableWorkTypes"
            :key="type"
            :value="type"
          >
            {{ type }}
          </option>
        </select>

        <!-- Position -->
        <select
          v-model="selectedPosition"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">All Position</option>

          <option
            v-for="position in availablePositions"
            :key="position"
            :value="position"
          >
            {{ position }}
          </option>
        </select>

        <!-- Sort -->
        <select
          v-model="sortBy"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="company">Company</option>
          <option value="position">Position</option>
        </select>

        <!-- Reset -->
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
      <table class="min-w-[1200px] w-full">
        <!-- Header -->
        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >
            <th
              class="w-16 px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              #
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              User
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Company / Organization
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Position
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Work Type
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Project
            </th>

            <th
              class="w-44 px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500"
            >
              Actions
            </th>
          </tr>
        </thead>

        <tbody>
          <!-- Loading -->
          <tr v-if="loading">
            <td
              colspan="7"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              Loading experiences...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredExperiences.length === 0">
            <td
              colspan="7"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No experience found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(experience, index) in filteredExperiences"
            v-else
            :key="experience.id_experience"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >
            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <!-- User -->
            <td class="px-6 py-5">
              <p class="font-medium text-gray-800 dark:text-white/90">
                {{ getUserName(experience) }}
              </p>
            </td>

            <!-- Company -->
            <td class="px-6 py-5">
              <div class="flex items-center gap-3">
                <!-- Company Initial -->
                <div
                  class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300"
                >
                  {{ getInitial(getWorkName(experience)) }}
                </div>

                <!-- Company Information -->
                <div class="min-w-0">
                  <p
                    class="font-semibold text-gray-800 dark:text-white/90"
                  >
                    {{ getWorkName(experience) }}
                  </p>

                  <!-- Location -->
                  <div
                    v-if="getWorkPlace(experience) !== '-'"
                    class="mt-1 flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400"
                  >
                    <span>{{ getWorkPlace(experience) }}</span>
                  </div>
                </div>
              </div>
            </td>

            <!-- Position -->
            <td
              class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400"
            >
              {{ getPositionName(experience) }}
            </td>

            <!-- Work Type -->
            <td class="px-6 py-5">
              <span
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="
                  getWorkTypeClass(
                    getWorkTypeName(experience)
                  )
                "
              >
                {{ getWorkTypeName(experience) }}
              </span>
            </td>

            <!-- Project -->
            <td
              class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400"
            >
              {{ getProjectName(experience) }}
            </td>

            <!-- Actions -->
            <td class="px-6 py-5">
              <div class="flex justify-end gap-2">
                <!-- Edit -->
                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editExperience(experience)"
                >
                  ✎
                </button>

                <!-- View -->
                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300"
                  @click="viewExperience(experience)"
                >
                  👁
                </button>

                <!-- Delete -->
                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="
                    deleteExperience(
                      experience.id_experience
                    )
                  "
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
        Showing {{ filteredExperiences.length }}
        of {{ experiences.length }} results
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

interface Relation {
  id?: number
  name?: string
  username?: string
  place?: string
}

interface Experience {
  id_experience: number
  user?: Relation
  work?: Relation
  position_type?: Relation
  work_type?: Relation
  project?: Relation
}

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
  (event: 'edit', experience: Experience): void
  (event: 'view', experience: Experience): void
}>()

const experiences = ref<Experience[]>([])
const loading = ref(true)

const search = ref('')
const selectedWorkType = ref('')
const selectedPosition = ref('')
const sortBy = ref('newest')

/**
 * User
 */
const getUserName = (experience: Experience): string => {
  return (
    experience.user?.name ||
    experience.user?.username ||
    'Unknown User'
  )
}

/**
 * Work / Company
 */
const getWorkName = (experience: Experience): string => {
  return experience.work?.name || '-'
}

/**
 * Work Location
 */
const getWorkPlace = (experience: Experience): string => {
  return experience.work?.place || '-'
}

/**
 * Position
 */
const getPositionName = (experience: Experience): string => {
  return experience.position_type?.name || '-'
}

/**
 * Work Type
 */
const getWorkTypeName = (experience: Experience): string => {
  return experience.work_type?.name || 'General'
}

/**
 * Project
 */
const getProjectName = (
  experience: Experience
): string => {
  return experience.project?.name || '-'
}

/**
 * Initial Company
 */
const getInitial = (name: string): string => {
  if (!name || name === '-') return 'E'

  return name.charAt(0).toUpperCase()
}

/**
 * Dynamic Work Type Filter
 */
const availableWorkTypes = computed(() => {
  return [
    ...new Set(
      experiences.value
        .map((experience) =>
          getWorkTypeName(experience)
        )
        .filter(Boolean)
    ),
  ]
})

/**
 * Dynamic Position Filter
 */
const availablePositions = computed(() => {
  return [
    ...new Set(
      experiences.value
        .map((experience) =>
          getPositionName(experience)
        )
        .filter(
          (position) =>
            position !== '-'
        )
    ),
  ]
})

/**
 * Filter + Search + Sort
 */
const filteredExperiences = computed(() => {
  let result = [...experiences.value]

  // Search
  if (search.value.trim()) {
    const keyword =
      search.value.toLowerCase()

    result = result.filter((experience) => {
      return (
        getUserName(experience)
          .toLowerCase()
          .includes(keyword) ||

        getWorkName(experience)
          .toLowerCase()
          .includes(keyword) ||

        getWorkPlace(experience)
          .toLowerCase()
          .includes(keyword) ||

        getPositionName(experience)
          .toLowerCase()
          .includes(keyword) ||

        getWorkTypeName(experience)
          .toLowerCase()
          .includes(keyword) ||

        getProjectName(experience)
          .toLowerCase()
          .includes(keyword)
      )
    })
  }

  // Work Type
  if (selectedWorkType.value) {
    result = result.filter(
      (experience) =>
        getWorkTypeName(experience) ===
        selectedWorkType.value
    )
  }

  // Position
  if (selectedPosition.value) {
    result = result.filter(
      (experience) =>
        getPositionName(experience) ===
        selectedPosition.value
    )
  }

  // Sort
  if (sortBy.value === 'newest') {
    result.sort(
      (a, b) =>
        (b.id_experience || 0) -
        (a.id_experience || 0)
    )
  }

  if (sortBy.value === 'oldest') {
    result.sort(
      (a, b) =>
        (a.id_experience || 0) -
        (b.id_experience || 0)
    )
  }

  if (sortBy.value === 'company') {
    result.sort((a, b) =>
      getWorkName(a).localeCompare(
        getWorkName(b)
      )
    )
  }

  if (sortBy.value === 'position') {
    result.sort((a, b) =>
      getPositionName(a).localeCompare(
        getPositionName(b)
      )
    )
  }

  return result
})

/**
 * Fetch Experience
 */
const fetchExperiences = async () => {
  loading.value = true

  try {
    const response =
      await axios.get('/api/experiences')

    const data = response.data

    if (Array.isArray(data)) {
      experiences.value = data
    } else if (Array.isArray(data?.data)) {
      experiences.value = data.data
    } else if (
      Array.isArray(data?.data?.data)
    ) {
      experiences.value =
        data.data.data
    } else {
      experiences.value = []
    }

    emit(
      'total-changed',
      experiences.value.length
    )
  } catch (error) {
    console.error(
      'Failed to fetch experiences:',
      error
    )

    experiences.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Reset Filters
 */
const resetFilters = () => {
  search.value = ''
  selectedWorkType.value = ''
  selectedPosition.value = ''
  sortBy.value = 'newest'
}

/**
 * Edit
 */
const editExperience = (
  experience: Experience
) => {
  emit('edit', experience)
}

/**
 * View
 */
const viewExperience = (
  experience: Experience
) => {
  emit('view', experience)
}

/**
 * Delete
 */
const deleteExperience = async (
  id: number
) => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this experience?'
  )

  if (!confirmed) return

  try {
    await axios.delete(
      `/api/experiences/${id}`
    )

    experiences.value =
      experiences.value.filter(
        (experience) =>
          experience.id_experience !== id
      )

    emit(
      'total-changed',
      experiences.value.length
    )
  } catch (error) {
    console.error(
      'Failed to delete experience:',
      error
    )
  }
}

/**
 * Work Type Badge
 */
const getWorkTypeClass = (
  type: string
) => {
  const normalized =
    type?.toLowerCase() || ''

  if (
    normalized.includes('internship') ||
    normalized.includes('magang')
  ) {
    return `
      bg-emerald-50 text-emerald-600
      dark:bg-emerald-500/10
      dark:text-emerald-400
    `
  }

  if (
    normalized.includes('freelance')
  ) {
    return `
      bg-amber-50 text-amber-600
      dark:bg-amber-500/10
      dark:text-amber-400
    `
  }

  if (
    normalized.includes('full') ||
    normalized.includes('employee')
  ) {
    return `
      bg-blue-50 text-blue-600
      dark:bg-blue-500/10
      dark:text-blue-400
    `
  }

  if (
    normalized.includes('part')
  ) {
    return `
      bg-purple-50 text-purple-600
      dark:bg-purple-500/10
      dark:text-purple-400
    `
  }

  return `
    bg-gray-100 text-gray-600
    dark:bg-gray-800
    dark:text-gray-400
  `
}

/**
 * Watch
 */
watch(
  () => experiences.value.length,
  (total) => {
    emit('total-changed', total)
  }
)

onMounted(fetchExperiences)
</script>