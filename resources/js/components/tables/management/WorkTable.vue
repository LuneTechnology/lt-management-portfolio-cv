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
          placeholder="Search work..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3">

        <select
          v-model="selectedTag"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">All Tags</option>
          <option
            v-for="tag in tags"
            :key="tag"
            :value="tag"
          >
            {{ tag }}
          </option>
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

      <table class="w-full min-w-[700px]">

        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >
            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              #
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Company / Organization
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Location
            </th>

            <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">
              Tag
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
              colspan="5"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              Loading work...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredWorks.length === 0">
            <td
              colspan="5"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No work found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(work, index) in filteredWorks"
            v-else
            :key="work.id_work"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>

            <!-- Company / Organization -->
            <td class="px-6 py-5">
              <p
                class="font-semibold text-gray-800 dark:text-white/90"
              >
                {{ work.name }}
              </p>
            </td>

            <!-- Location -->
            <td class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400">
              {{ work.place || '-' }}
            </td>

            <!-- Tag -->
            <td class="px-6 py-5">
              <span
                v-if="work.work_tag"
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="getTagClass(work.work_tag.name)"
              >
                {{ work.work_tag.name }}
              </span>
              <span v-else class="text-sm text-gray-400">-</span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-5">
              <div class="flex justify-end gap-2">

                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="editWork(work)"
                >
                  ✎
                </button>

                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="viewWork(work)"
                >
                  👁
                </button>

                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="deleteWork(work.id_work)"
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
        Showing {{ filteredWorks.length }}
        of {{ works.length }} results
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

interface WorkTag {
  id_work_tag: number
  name: string
}

interface Work {
  id_work: number
  name: string
  place: string
  work_tag_id: number | null
  work_tag?: WorkTag | null
}

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
}>()

const works = ref<Work[]>([])
const loading = ref(true)

const search = ref('')
const selectedTag = ref('')
const sortBy = ref('newest')

const tags = computed(() => {
  return [...new Set(
    works.value
      .map((work) => work.work_tag?.name)
      .filter((name): name is string => Boolean(name))
  )]
})

const filteredWorks = computed(() => {
  let result = [...works.value]

  // Search
  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    result = result.filter((work) =>
      work.name.toLowerCase().includes(keyword) ||
      (work.place && work.place.toLowerCase().includes(keyword)) ||
      (work.work_tag && work.work_tag.name.toLowerCase().includes(keyword))
    )
  }

  // Tag
  if (selectedTag.value) {
    result = result.filter(
      (work) => work.work_tag?.name === selectedTag.value
    )
  }

  // Sort
  if (sortBy.value === 'newest') {
    result.sort((a, b) => b.id_work - a.id_work)
  }

  if (sortBy.value === 'oldest') {
    result.sort((a, b) => a.id_work - b.id_work)
  }

  if (sortBy.value === 'name') {
    result.sort((a, b) => a.name.localeCompare(b.name))
  }

  return result
})

const fetchWorks = async () => {
  loading.value = true

  try {
    const response = await axios.get('/api/works')

    // Mendukung format JSON axios { success: true, data: [...] } atau array langsung
    const data = response.data.data ? response.data.data : response.data
    works.value = data

    emit('total-changed', works.value.length)
  } catch (error) {
    console.error('Failed to fetch works:', error)
  } finally {
    loading.value = false
  }
}

const getTagClass = (tagName?: string) => {
  switch (tagName?.toUpperCase()) {
    case 'COMPANY':
      return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'

    case 'EDUCATION':
      return 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400'

    case 'ORGANIZATION':
      return 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'

    case 'COMMUNITY':
      return 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400'

    default:
      return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
  }
}

const resetFilters = () => {
  search.value = ''
  selectedTag.value = ''
  sortBy.value = 'newest'
}

const editWork = (work: Work) => {
  console.log('Edit work:', work)
}

const viewWork = (work: Work) => {
  console.log('View work:', work)
}

const deleteWork = async (id: number) => {
  const confirmed = window.confirm(
    'Are you sure you want to delete this work?'
  )

  if (!confirmed) return

  try {
    await axios.delete(`/api/works/${id}`)

    works.value = works.value.filter(
      (work) => work.id_work !== id
    )

    emit('total-changed', works.value.length)
  } catch (error) {
    console.error('Failed to delete work:', error)
  }
}

watch(
  () => works.value.length,
  (total) => {
    emit('total-changed', total)
  }
)

onMounted(fetchWorks)
</script>