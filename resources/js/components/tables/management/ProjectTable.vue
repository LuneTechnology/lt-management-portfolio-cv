<script setup lang="ts">
import {
  computed,
  onMounted,
  ref,
  watch,
} from 'vue'

import axios from '@/services/axios'

interface Category {
  id_category: number
  name: string
}

interface User {
  id_user: number
  username: string | null
  email: string
}

interface Experience {
  id_experience: number
  user?: User | null
}

interface Project {
  id_project: number
  name: string
  date_in: string
  date_out: string | null
  id_category: number
  category?: Category | null
  experiences?: Experience[]
}

const props = defineProps<{
  refreshKey?: number
}>()

const emit = defineEmits<{
  (e: 'total-changed', total: number): void
  (e: 'edit', project: Project): void
  (e: 'view', project: Project): void
  (e: 'delete', project: Project): void
}>()

const projects = ref<Project[]>([])

const search = ref('')

const categoryFilter = ref('')

const loading = ref(false)

const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| Fetch Projects
|--------------------------------------------------------------------------
*/

const fetchProjects = async () => {
  try {
    loading.value = true

    errorMessage.value = ''

    const response =
      await axios.get('/api/projects')

    const data =
      response.data?.data ??
      response.data ??
      []

    projects.value = Array.isArray(data)
      ? data
      : []

    emit(
      'total-changed',
      projects.value.length
    )
  } catch (error: any) {
    console.error(
      'Failed to fetch projects:',
      error
    )

    projects.value = []

    emit('total-changed', 0)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to load projects.'
  } finally {
    loading.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

const categories = computed(() => {
  const map = new Map<
    number,
    Category
  >()

  projects.value.forEach((project) => {
    if (project.category) {
      map.set(
        project.category.id_category,
        project.category
      )
    }
  })

  return Array.from(map.values()).sort(
    (a, b) =>
      a.name.localeCompare(b.name)
  )
})

/*
|--------------------------------------------------------------------------
| Filtered Projects
|--------------------------------------------------------------------------
*/

const filteredProjects = computed(() => {
  const keyword =
    search.value.trim().toLowerCase()

  return projects.value.filter(
    (project) => {
      const matchesSearch =
        !keyword ||
        project.name
          .toLowerCase()
          .includes(keyword)

      const matchesCategory =
        !categoryFilter.value ||
        String(project.id_category) ===
          categoryFilter.value

      return (
        matchesSearch &&
        matchesCategory
      )
    }
  )
})

/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

const formatDate = (
  date: string | null
) => {
  if (!date) {
    return 'Present'
  }

  const value = new Date(date)

  if (Number.isNaN(value.getTime())) {
    return date
  }

  return value.toLocaleDateString(
    'en-GB',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }
  )
}

/*
|--------------------------------------------------------------------------
| Contributors
|--------------------------------------------------------------------------
*/

const contributorNames = (
  project: Project
) => {
  return (
    project.experiences
      ?.map(
        (experience) =>
          experience.user?.username ||
          experience.user?.email
      )
      .filter(Boolean) || []
  )
}

/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetFilters = () => {
  search.value = ''
  categoryFilter.value = ''
}

/*
|--------------------------------------------------------------------------
| Watch Refresh
|--------------------------------------------------------------------------
*/

watch(
  () => props.refreshKey,
  () => {
    fetchProjects()
  }
)

/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchProjects()
})
</script>

<template>
  <div
    class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >

    <!-- Header -->
    <div
      class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 dark:border-gray-800 sm:px-6"
    >

      <div
        class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
      >

        <div>
          <h3
            class="text-base font-semibold text-gray-800 dark:text-white/90"
          >
            Projects
          </h3>

          <p
            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
          >
            Manage your portfolio projects.
          </p>
        </div>

        <!-- Filters -->
        <div
          class="flex flex-col gap-2 sm:flex-row"
        >

          <input
            v-model="search"
            type="text"
            placeholder="Search project..."
            class="h-10 rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"
          />

          <select
            v-model="categoryFilter"
            class="h-10 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
          >
            <option value="">
              All Categories
            </option>

            <option
              v-for="category in categories"
              :key="category.id_category"
              :value="
                String(category.id_category)
              "
            >
              {{ category.name }}
            </option>
          </select>

          <button
            type="button"
            class="h-10 rounded-lg border border-gray-300 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
            @click="resetFilters"
          >
            Reset
          </button>

        </div>

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

      <table
        class="w-full min-w-[950px]"
      >

        <thead>
          <tr
            class="border-b border-gray-200 dark:border-gray-800"
          >

            <th
              class="px-5 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6"
            >
              #
            </th>

            <th
              class="px-5 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
            >
              Project
            </th>

            <th
              class="px-5 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
            >
              Category
            </th>

            <th
              class="px-5 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
            >
              Period
            </th>

            <th
              class="px-5 py-4 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"
            >
              Contributors
            </th>

            <th
              class="px-5 py-4 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sm:px-6"
            >
              Actions
            </th>

          </tr>
        </thead>

        <tbody>

          <!-- Loading -->
          <tr v-if="loading">

            <td
              colspan="6"
              class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              Loading projects...
            </td>

          </tr>

          <!-- Empty -->
          <tr
            v-else-if="
              filteredProjects.length === 0
            "
          >

            <td
              colspan="6"
              class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              {{
                search ||
                categoryFilter
                  ? 'No project found.'
                  : 'No projects available.'
              }}
            </td>

          </tr>

          <!-- Data -->
          <tr
            v-for="(
              project, index
            ) in filteredProjects"
            :key="project.id_project"
            class="border-b border-gray-100 transition hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <!-- Number -->
            <td
              class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 sm:px-6"
            >
              {{ index + 1 }}
            </td>

            <!-- Project -->
            <td
              class="px-5 py-4"
            >
              <p
                class="text-sm font-semibold text-gray-800 dark:text-white/90"
              >
                {{ project.name }}
              </p>
            </td>

            <!-- Category -->
            <td
              class="px-5 py-4"
            >
              <span
                class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/10 dark:text-brand-400"
              >
                {{
                  project.category?.name ||
                  '-'
                }}
              </span>
            </td>

            <!-- Period -->
            <td
              class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400"
            >
              {{ formatDate(project.date_in) }}
              -
              {{ formatDate(project.date_out) }}
            </td>

            <!-- Contributors -->
            <td
              class="px-5 py-4"
            >
              <div
                v-if="
                  contributorNames(project).length
                "
                class="flex items-center gap-2"
              >

                <span
                  class="text-sm text-gray-700 dark:text-gray-300"
                >
                  {{
                    contributorNames(project)
                      .slice(0, 2)
                      .join(', ')
                  }}
                </span>

                <span
                  v-if="
                    contributorNames(project)
                      .length > 2
                  "
                  class="rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400"
                >
                  +{{
                    contributorNames(project)
                      .length - 2
                  }}
                </span>

              </div>

              <span
                v-else
                class="text-sm text-gray-400"
              >
                No contributor
              </span>
            </td>

            <!-- Actions -->
            <td
              class="px-5 py-4 sm:px-6"
            >
              <div
                class="flex justify-end gap-2"
              >

                <!-- Edit -->
                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="
                    emit(
                      'edit',
                      project
                    )
                  "
                >
                  ✎
                </button>

                <!-- View -->
                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="
                    emit(
                      'view',
                      project
                    )
                  "
                >
                  👁
                </button>

                <!-- Delete -->
                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="
                    emit(
                      'delete',
                      project
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
      class="border-t border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6"
    >
      <p
        class="text-sm text-gray-500 dark:text-gray-400"
      >
        Showing
        <span
          class="font-medium text-gray-700 dark:text-gray-300"
        >
          {{ filteredProjects.length }}
        </span>
        of
        <span
          class="font-medium text-gray-700 dark:text-gray-300"
        >
          {{ projects.length }}
        </span>
        projects
      </p>
    </div>

  </div>
</template> 