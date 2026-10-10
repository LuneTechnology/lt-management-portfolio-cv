<template>

  <div>

    <!-- ====================================================== -->
    <!-- FILTERS -->
    <!-- ====================================================== -->

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

        <!-- Level -->
        <select
          v-model="selectedLevel"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >

          <option value="">
            All Level
          </option>

          <option
            v-for="level in levels"
            :key="level"
            :value="level"
          >
            {{ level }}
          </option>

        </select>


        <!-- Status -->
        <select
          v-model="selectedStatus"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >

          <option value="">
            All Status
          </option>

          <option value="Ongoing">
            Ongoing
          </option>

          <option value="Completed">
            Completed
          </option>

        </select>


        <!-- Sort -->
        <select
          v-model="sortBy"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >

          <option value="newest">
            Newest
          </option>

          <option value="oldest">
            Oldest
          </option>

          <option value="name">
            Name
          </option>

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


    <!-- ====================================================== -->
    <!-- TABLE -->
    <!-- ====================================================== -->

    <div class="overflow-x-auto">

      <table class="min-w-[1100px] w-full">

        <!-- Header -->
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
              Institution
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Program / Major
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Level
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Period
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              GPA
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Status
            </th>

            <th
              class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500"
            >
              Actions
            </th>

          </tr>

        </thead>


        <!-- Body -->
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
          <tr
            v-else-if="filteredEducations.length === 0"
          >

            <td
              colspan="8"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No education found.
            </td>

          </tr>


          <!-- Data -->
          <tr
            v-for="(education, index) in filteredEducations"
            v-else
            :key="education.id_education"
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
                  {{ education.work?.name ?? '-' }}
                </p>

                <p
                  class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                >
                  {{ education.work?.place ?? '-' }}
                </p>

              </div>

            </td>


            <!-- Major -->
            <td class="px-6 py-5">

              <p
                class="font-medium text-gray-800 dark:text-white/90"
              >
                {{ education.major }}
              </p>

            </td>


            <!-- Level -->
            <td class="px-6 py-5">

              <span
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="
                  getLevelClass(
                    education.level
                  )
                "
              >
                {{ education.level }}
              </span>

            </td>


            <!-- Period -->
            <td
              class="px-6 py-5 text-sm text-gray-600 dark:text-gray-400"
            >

              {{ formatDate(education.date_in) }}

              –

              {{
                education.date_out
                  ? formatDate(
                      education.date_out
                    )
                  : 'Present'
              }}

            </td>


            <!-- GPA -->
            <td class="px-6 py-5">

              <span
                v-if="
                  education.gpa !== null &&
                  education.gpa !== undefined &&
                  education.gpa !== ''
                "
                class="text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                {{
                  Number(
                    education.gpa
                  ).toFixed(2)
                }}
                / 4.00
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
                :class="
                  getStatusClass(
                    education
                  )
                "
              >
                {{
                  getStatus(
                    education
                  )
                }}
              </span>

            </td>


            <!-- Actions -->
            <td class="px-6 py-5">

              <div
                class="flex justify-end gap-2"
              >

                <!-- Edit -->
                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="
                    editEducation(
                      education
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
                    viewEducation(
                      education
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
                    openDeleteModal(education)
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


    <!-- ====================================================== -->
    <!-- FOOTER -->
    <!-- ====================================================== -->

    <div
      class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800"
    >

      <p
        class="text-sm text-gray-500 dark:text-gray-400"
      >
        Showing
        {{ filteredEducations.length }}
        of
        {{ educations.length }}
        results
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

    <!-- ====================================================== -->
    <!-- DELETE CONFIRMATION MODAL -->
    <!-- ====================================================== -->

    <Teleport to="body">

      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
        @click.self="closeDeleteModal"
      >

        <div
          class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
        >

          <!-- Content -->
          <div class="px-6 pt-6">

            <!-- Icon -->
            <div
              class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10"
            >

              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-7 w-7 text-red-500"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 9v4m0 4h.01M10.29 3.86 2.82 17a2 2 0 0 0 1.74 3h14.88a2 2 0 0 0 1.74-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
                />
              </svg>

            </div>


            <!-- Title -->
            <div class="mt-5 text-center">

              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white"
              >
                Delete Education?
              </h3>

              <p
                class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400"
              >
                Are you sure you want to delete this education
                record? This action cannot be undone.
              </p>

            </div>


            <!-- Selected Education -->
            <div
              v-if="selectedEducation"
              class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]"
            >

              <p
                class="text-sm font-semibold text-gray-800 dark:text-white"
              >
                {{
                  selectedEducation.work?.name ??
                  'Unknown Institution'
                }}
              </p>

              <p
                class="mt-1 text-xs text-gray-500 dark:text-gray-400"
              >
                {{
                  selectedEducation.major
                }}
                ·
                {{
                  selectedEducation.level
                }}
              </p>

            </div>

          </div>


          <!-- Footer -->
          <div
            class="mt-6 flex gap-3 border-t border-gray-200 px-6 py-5 dark:border-gray-800"
          >

            <!-- Cancel -->
            <button
              type="button"
              :disabled="deleting"
              class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              @click="closeDeleteModal"
            >
              Cancel
            </button>


            <!-- Delete -->
            <button
              type="button"
              :disabled="deleting"
              class="flex-1 rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600 disabled:cursor-not-allowed disabled:opacity-50"
              @click="confirmDelete"
            >

              <span
                v-if="deleting"
              >
                Deleting...
              </span>

              <span
                v-else
              >
                Delete
              </span>

            </button>

          </div>

        </div>

      </div>

    </Teleport>
    
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


/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Work {
  id_work: number
  name: string
  place: string

  work_tag?: {
    id_work_tag: number
    name: string
  }
}

interface Education {
  id_education: number
  id_work: number
  major: string
  level: string
  date_in: string
  date_out: string | null
  gpa: number | string | null

  work?: Work

  user?: {
    id_user: number
    username: string | null
  }
}


/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  refreshKey: number
}>()


/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/

const emit = defineEmits<{
  (
    event: 'total-changed',
    total: number
  ): void

  (
    event: 'edit',
    education: Education
  ): void

  (
    event: 'view',
    education: Education
  ): void
}>()


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const educations =
  ref<Education[]>([])

const loading = ref(true)


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const search = ref('')

const selectedLevel = ref('')

const selectedStatus = ref('')

const sortBy = ref('newest')


/*
|--------------------------------------------------------------------------
| Levels
|--------------------------------------------------------------------------
*/

const levels = computed(() => {

  return [
    ...new Set(
      educations.value
        .map(
          (education) =>
            education.level
        )
        .filter(Boolean)
    ),
  ]

})


/*
|--------------------------------------------------------------------------
| Filtered Education
|--------------------------------------------------------------------------
*/

const filteredEducations = computed(() => {

  let result = [
    ...educations.value,
  ]


  /*
  |--------------------------------------------------------------------------
  | Search
  |--------------------------------------------------------------------------
  */

  if (search.value.trim()) {

    const keyword =
      search.value
        .trim()
        .toLowerCase()

    result =
      result.filter(
        (education) => {

          const institution =
            education.work?.name
              ?.toLowerCase() ?? ''

          const place =
            education.work?.place
              ?.toLowerCase() ?? ''

          const major =
            education.major
              ?.toLowerCase() ?? ''

          const level =
            education.level
              ?.toLowerCase() ?? ''

          return (
            institution.includes(
              keyword
            ) ||
            place.includes(
              keyword
            ) ||
            major.includes(
              keyword
            ) ||
            level.includes(
              keyword
            )
          )

        }
      )

  }


  /*
  |--------------------------------------------------------------------------
  | Level
  |--------------------------------------------------------------------------
  */

  if (selectedLevel.value) {

    result =
      result.filter(
        (education) =>
          education.level ===
          selectedLevel.value
      )

  }


  /*
  |--------------------------------------------------------------------------
  | Status
  |--------------------------------------------------------------------------
  */

  if (selectedStatus.value) {

    result =
      result.filter(
        (education) =>
          getStatus(
            education
          ) ===
          selectedStatus.value
      )

  }


  /*
  |--------------------------------------------------------------------------
  | Sort
  |--------------------------------------------------------------------------
  */

  if (
    sortBy.value ===
    'newest'
  ) {

    result.sort(
      (a, b) =>
        new Date(
          b.date_in
        ).getTime() -
        new Date(
          a.date_in
        ).getTime()
    )

  }


  if (
    sortBy.value ===
    'oldest'
  ) {

    result.sort(
      (a, b) =>
        new Date(
          a.date_in
        ).getTime() -
        new Date(
          b.date_in
        ).getTime()
    )

  }


  if (
    sortBy.value ===
    'name'
  ) {

    result.sort(
      (a, b) =>
        (
          a.work?.name ?? ''
        ).localeCompare(
          b.work?.name ?? ''
        )
    )

  }


  return result

})


/*
|--------------------------------------------------------------------------
| Fetch
|--------------------------------------------------------------------------
*/

const fetchEducations =
  async () => {

    loading.value = true

    try {

      const response =
        await axios.get(
          '/api/educations'
        )

      educations.value =
        response.data

      emit(
        'total-changed',
        educations.value.length
      )

    } catch (error) {

      console.error(
        'Failed to fetch educations:',
        error
      )

    } finally {

      loading.value = false

    }

  }


/*
|--------------------------------------------------------------------------
| Format Date
|--------------------------------------------------------------------------
*/

const formatDate = (
  date: string | null
) => {

  if (!date) {
    return '-'
  }

  const value =
    new Date(date)

  return value.toLocaleDateString(
    'en-US',
    {
      month: 'short',
      year: 'numeric',
    }
  )

}


/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const getStatus = (
  education: Education
) => {

  if (!education.date_out) {
    return 'Ongoing'
  }

  return 'Completed'

}


const getStatusClass = (
  education: Education
) => {

  if (
    getStatus(
      education
    ) === 'Ongoing'
  ) {

    return 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400'

  }

  return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'

}


/*
|--------------------------------------------------------------------------
| Level Color
|--------------------------------------------------------------------------
*/

const getLevelClass = (
  level: string
) => {

  switch (
    level?.toUpperCase()
  ) {

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


    case 'SMA':

      return 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400'


    default:

      return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'

  }

}


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {

  search.value = ''

  selectedLevel.value = ''

  selectedStatus.value = ''

  sortBy.value = 'newest'

}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const editEducation = (
  education: Education
) => {

  emit(
    'edit',
    education
  )

}


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const viewEducation = (
  education: Education
) => {

  emit(
    'view',
    education
  )

}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const showDeleteModal = ref(false)

const deleting = ref(false)

const selectedEducation =
  ref<Education | null>(null)


const openDeleteModal = (
  education: Education
) => {
  selectedEducation.value = education

  showDeleteModal.value = true
}


const closeDeleteModal = () => {
  if (deleting.value) {
    return
  }

  showDeleteModal.value = false

  selectedEducation.value = null
}


const confirmDelete = async () => {

  if (!selectedEducation.value) {
    return
  }

  deleting.value = true

  try {

    await axios.delete(
      `/api/educations/${selectedEducation.value.id_education}`
    )

    educations.value =
      educations.value.filter(
        (education) =>
          education.id_education !==
          selectedEducation.value?.id_education
      )

    emit(
      'total-changed',
      educations.value.length
    )

    showDeleteModal.value = false

    selectedEducation.value = null

  } catch (error: any) {

    console.error(
      'Failed to delete education:',
      error
    )

    const message =
      error?.response?.data?.message ||
      'Failed to delete education.'

    window.alert(message)

  } finally {

    deleting.value = false

  }
}


/*
|--------------------------------------------------------------------------
| Refresh after Create / Update
|--------------------------------------------------------------------------
*/

watch(
  () => props.refreshKey,
  () => {
    fetchEducations()
  }
)


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(
  fetchEducations
)

</script>