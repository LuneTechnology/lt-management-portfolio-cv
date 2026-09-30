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
          placeholder="Search education..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3">

        <select
          v-model="selectedLevel"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">All Level</option>
          <option
            v-for="level in levels"
            :key="level"
            :value="level"
          >
            {{ level }}
          </option>
        </select>

        <select
          v-model="selectedStatus"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">All Status</option>
          <option value="Ongoing">Ongoing</option>
          <option value="Completed">Completed</option>
        </select>

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
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              #
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Institution
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Program / Major
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Level
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Period
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              GPA
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Status
            </th>

            <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">
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
              Loading education...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredEducations.length === 0">
            <td
              colspan="8"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No education found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(edu, index) in filteredEducations"
            v-else
            :key="edu.id_education"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <!-- Institution -->
            <td class="px-6 py-5">
              <div>
                <p
                  class="font-semibold text-gray-800 dark:text-white/90"
                >
                  {{ edu.name }}
                </p>

                <p
                  class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                >
                  {{ edu.place }}
                </p>
              </div>
            </td>

            <!-- Major -->
            <td class="px-6 py-5">
              <p
                class="font-medium text-gray-800 dark:text-white/90"
              >
                {{ edu.major }}
              </p>

              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                {{ edu.major }}
              </p>
            </td>

            <!-- Level -->
            <td class="px-6 py-5">
              <span
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="getLevelClass(edu.level)"
              >
                {{ edu.level }}
              </span>
            </td>

            <!-- Period -->
            <td class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400">
              {{ formatDate(edu.date_in) }}
              –
              {{ edu.date_out ? formatDate(edu.date_out) : 'Present' }}
            </td>

            <!-- GPA -->
            <td class="px-6 py-5">
              <span
                v-if="edu.gpa"
                class="text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                {{ Number(edu.gpa).toFixed(2) }} / 4.00
              </span>

              <span
                v-else
                class="text-sm text-gray-400"
              >
                -
              </span>
            </td>

            <!-- Status -->
            <td class="px-6 py-5">
              <span
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="getStatusClass(edu)"
              >
                {{ getStatus(edu) }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-5">
              <div class="flex justify-end gap-2">

                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editEducation(edu)"
                >
                  ✎
                </button>

                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="viewEducation(edu)"
                >
                  👁
                </button>

                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="deleteEducation(edu.id_education)"
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
        Showing {{ filteredEducations.length }}
        of {{ educations.length }} results
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

interface Education {
  id_education: number
  name: string
  major: string
  place: string
  level: string
  date_in: string
  date_out: string | null
  gpa: number | string | null
}

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
}>()

const educations = ref<Education[]>([])
const loading = ref(true)

const search = ref('')
const selectedLevel = ref('')
const selectedStatus = ref('')
const sortBy = ref('newest')

const levels = computed(() => {
  return [...new Set(
    educations.value
      .map((education) => education.level)
      .filter(Boolean)
  )]
})

const filteredEducations = computed(() => {
  let result = [...educations.value]

  // Search
  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    result = result.filter((education) =>
      education.name.toLowerCase().includes(keyword) ||
      education.major.toLowerCase().includes(keyword) ||
      education.place.toLowerCase().includes(keyword)
    )
  }

  // Level
  if (selectedLevel.value) {
    result = result.filter(
      (education) => education.level === selectedLevel.value
    )
  }

  // Status
  if (selectedStatus.value) {
    result = result.filter(
      (education) => getStatus(education) === selectedStatus.value
    )
  }

  // Sort
  if (sortBy.value === 'newest') {
    result.sort(
      (a, b) =>
        new Date(b.date_in).getTime() -
        new Date(a.date_in).getTime()
    )
  }

  if (sortBy.value === 'oldest') {
    result.sort(
      (a, b) =>
        new Date(a.date_in).getTime() -
        new Date(b.date_in).getTime()
    )
  }

  if (sortBy.value === 'name') {
    result.sort((a, b) =>
      a.name.localeCompare(b.name)
    )
  }

  return result
})

const fetchEducations = async () => {
  loading.value = true

  try {
    const response = await axios.get('/api/educations')

    educations.value = response.data

    emit('total-changed', educations.value.length)
  } catch (error) {
    console.error('Failed to fetch educations:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (date: string | null) => {
  if (!date) return '-'

  const value = new Date(date)

  return value.toLocaleDateString('en-US', {
    month: 'short',
    year: 'numeric',
  })
}

const getStatus = (education: Education) => {
  if (!education.date_out) {
    return 'Ongoing'
  }

  return 'Completed'
}

const getStatusClass = (education: Education) => {
  if (getStatus(education) === 'Ongoing') {
    return 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400'
  }

  return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'
}

const getLevelClass = (level: string) => {
  switch (level?.toUpperCase()) {
    case 'D3':
      return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'

    case 'D4':
      return 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400'

    case 'S1':
      return 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'

    case 'S2':
      return 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400'

    case 'SMK':
      return 'bg-violet-50 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400'

    default:
      return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
  }
}

const resetFilters = () => {
  search.value = ''
  selectedLevel.value = ''
  selectedStatus.value = ''
  sortBy.value = 'newest'
}

const addEducation = () => {
  console.log('Add education')
}

const editEducation = (education: Education) => {
  console.log('Edit education:', education)
}

const viewEducation = (education: Education) => {
  console.log('View education:', education)
}

const deleteEducation = async (id: number) => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this education?'
  )

  if (!confirmed) return

  try {
    await axios.delete(`/api/educations/${id}`)

    educations.value = educations.value.filter(
      (education) => education.id_education !== id
    )

    emit('total-changed', educations.value.length)
  } catch (error) {
    console.error('Failed to delete education:', error)
  }
}

watch(
  () => educations.value.length,
  (total) => {
    emit('total-changed', total)
  }
)

onMounted(fetchEducations)
</script>