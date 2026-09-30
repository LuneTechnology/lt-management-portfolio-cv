<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Education"
        :value="totalEducation"
        :icon="DocsIcon"
        quote="Education is the foundation of every opportunity."
        :change="1"
        color="blue"
      />

      <!-- Education Table -->
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >

        <!-- Header -->
        <div
          class="border-b border-gray-200 px-6 py-5 dark:border-gray-800"
        >
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >

            <div class="flex items-center gap-3">

              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10"
              >
                <DocsIcon
                  class="h-5 w-5 text-blue-500"
                />
              </div>

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Education List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  View, add, edit, or delete your educational background.
                </p>
              </div>

            </div>

            <!-- Add -->
            <button
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Education
            </button>

          </div>
        </div>

        <!-- Table -->
        <EducationTable
          :refresh-key="refreshKey"
          @total-changed="totalEducation = $event"
          @edit="openEditModal"
          @view="openViewModal"
        />

      </div>

    </div>

    <!-- ====================================================== -->
    <!-- EDUCATION MODAL -->
    <!-- ====================================================== -->

    <Teleport to="body">

      <div
        v-if="showModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4 py-6"
        @click.self="closeModal"
      >

        <div
          class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-xl dark:bg-gray-900"
        >

          <!-- Modal Header -->
          <div
            class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-5 dark:border-gray-800 dark:bg-gray-900"
          >

            <div>
              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white"
              >
                {{
                  modalMode === 'create'
                    ? 'Add Education'
                    : modalMode === 'edit'
                      ? 'Edit Education'
                      : 'Education Detail'
                }}
              </h3>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                {{
                  modalMode === 'create'
                    ? 'Add a new educational background.'
                    : modalMode === 'edit'
                      ? 'Update educational information.'
                      : 'View educational information.'
                }}
              </p>
            </div>

            <button
              type="button"
              class="flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:hover:bg-white/[0.05] dark:hover:text-white"
              @click="closeModal"
            >
              ✕
            </button>

          </div>

          <!-- Form -->
          <form
            class="space-y-5 px-6 py-6"
            @submit.prevent="submitEducation"
          >

            <!-- Institution -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Institution
              </label>

              <select
                v-model="form.id_work"
                :disabled="isViewMode || loadingWorks || saving"
                required
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:disabled:bg-gray-800"
              >

                <option value="">
                  {{
                    loadingWorks
                      ? 'Loading institutions...'
                      : 'Select institution'
                  }}
                </option>

                <option
                  v-for="work in educationWorks"
                  :key="work.id_work"
                  :value="work.id_work"
                >
                  {{ work.name }} - {{ work.place }}
                </option>

              </select>

              <p
                v-if="educationWorks.length === 0 && !loadingWorks"
                class="mt-1 text-xs text-error-500"
              >
                No education institution found in Work master data.
              </p>
            </div>

            <!-- Major -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Program / Major
              </label>

              <input
                v-model="form.major"
                type="text"
                placeholder="e.g. Game Technology"
                maxlength="50"
                :disabled="isViewMode || saving"
                required
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />
            </div>

            <!-- Level -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Level
              </label>

              <select
                v-model="form.level"
                :disabled="isViewMode || saving"
                required
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              >
                <option value="">
                  Select level
                </option>

                <option value="SMA">
                  SMA
                </option>

                <option value="SMK">
                  SMK
                </option>

                <option value="D3">
                  D3
                </option>

                <option value="D4">
                  D4
                </option>

                <option value="S1">
                  S1
                </option>

                <option value="S2">
                  S2
                </option>
              </select>
            </div>

            <!-- Date -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

              <!-- Start -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  Start Date
                </label>

                <input
                  v-model="form.date_in"
                  type="date"
                  :disabled="isViewMode || saving"
                  required
                  class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
                />
              </div>

              <!-- End -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  End Date
                </label>

                <input
                  v-model="form.date_out"
                  type="date"
                  :disabled="isViewMode || saving"
                  class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
                />

                <p
                  class="mt-1 text-xs text-gray-400"
                >
                  Leave empty if currently studying.
                </p>
              </div>

            </div>

            <!-- GPA -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                GPA
              </label>

              <input
                v-model="form.gpa"
                type="number"
                step="0.01"
                min="0"
                max="4"
                placeholder="e.g. 3.78"
                :disabled="isViewMode || saving"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />
            </div>

            <!-- View mode information -->
            <div
              v-if="isViewMode"
              class="rounded-xl bg-gray-50 p-4 dark:bg-white/[0.03]"
            >
              <p class="text-xs text-gray-500 dark:text-gray-400">
                Status
              </p>

              <p
                class="mt-1 text-sm font-medium text-gray-800 dark:text-white"
              >
                {{
                  form.date_out
                    ? 'Completed'
                    : 'Ongoing'
                }}
              </p>
            </div>

            <!-- Footer -->
            <div
              class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-800"
            >

              <button
                type="button"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                @click="closeModal"
              >
                {{ isViewMode ? 'Close' : 'Cancel' }}
              </button>

              <button
                v-if="!isViewMode"
                type="submit"
                :disabled="saving"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
              >
                {{ saving ? 'Saving...' : 'Save Education' }}
              </button>

            </div>

          </form>

        </div>

      </div>

    </Teleport>

  </AdminLayout>
</template>


<script setup lang="ts">

import {
  computed,
  onMounted,
  ref,
} from 'vue'

import axios from '@/services/axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import EducationTable from '@/components/tables/management/EducationTable.vue'

import { DocsIcon } from '@/icons'


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

interface EducationForm {
  id_work: number | ''
  major: string
  level: string
  date_in: string
  date_out: string
  gpa: string
}


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

const currentPageTitle = ref('Education')

const totalEducation = ref(0)

const refreshKey = ref(0)


/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/

const showModal = ref(false)

const modalMode = ref<
  'create' |
  'edit' |
  'view'
>('create')

const selectedId = ref<number | null>(null)


/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const loadingWorks = ref(false)

const saving = ref(false)


/*
|--------------------------------------------------------------------------
| Work
|--------------------------------------------------------------------------
*/

const works = ref<Work[]>([])

const educationWorks = computed(() => {
  return works.value.filter((work) => {
    return (
      work?.work_tag?.name?.toLowerCase() ===
      'education'
    )
  })
})


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref<EducationForm>({
  id_work: '',
  major: '',
  level: '',
  date_in: '',
  date_out: '',
  gpa: '',
})


/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const isViewMode = computed(() => {
  return modalMode.value === 'view'
})


/*
|--------------------------------------------------------------------------
| Fetch Work
|--------------------------------------------------------------------------
*/

const fetchWorks = async () => {
  loadingWorks.value = true

  try {
    const response = await axios.get('/api/works')
    
    console.log(
      'WORK API RESPONSE:',
      response.data
    )

    const data = response.data

    if (Array.isArray(data)) {
      works.value = data
    } else if (Array.isArray(data?.data)) {
      works.value = data.data
    } else {
      works.value = []
    }

  } catch (error) {
    console.error(
      'Failed to fetch works:',
      error
    )

    works.value = []

  } finally {
    loadingWorks.value = false
  }
}


/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

const resetForm = () => {

  form.value = {
    id_work: '',
    major: '',
    level: '',
    date_in: '',
    date_out: '',
    gpa: '',
  }

  selectedId.value = null

}


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreateModal = async () => {
  resetForm()

  modalMode.value = 'create'

  showModal.value = true

  if (works.value.length === 0) {
    await fetchWorks()
  }
}

/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEditModal = async (
  education: Education
) => {

  modalMode.value = 'edit'

  selectedId.value =
    education.id_education

  form.value = {
    id_work: education.id_work,
    major: education.major,
    level: education.level,
    date_in: education.date_in
      ? education.date_in.substring(0, 10)
      : '',
    date_out: education.date_out
      ? education.date_out.substring(0, 10)
      : '',
    gpa:
      education.gpa !== null &&
      education.gpa !== undefined
        ? String(education.gpa)
        : '',
  }

  if (works.value.length === 0) {
    await fetchWorks()
  }

  showModal.value = true

}


/*
|--------------------------------------------------------------------------
| Open View
|--------------------------------------------------------------------------
*/

const openViewModal = (
  education: Education
) => {

  modalMode.value = 'view'

  selectedId.value =
    education.id_education

  form.value = {
    id_work: education.id_work,
    major: education.major,
    level: education.level,
    date_in: education.date_in
      ? education.date_in.substring(0, 10)
      : '',
    date_out: education.date_out
      ? education.date_out.substring(0, 10)
      : '',
    gpa:
      education.gpa !== null &&
      education.gpa !== undefined
        ? String(education.gpa)
        : '',
  }

  showModal.value = true

}


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {

  if (saving.value) {
    return
  }

  showModal.value = false

  resetForm()

}


/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submitEducation = async () => {

  if (modalMode.value === 'view') {
    return
  }

  if (!form.value.id_work) {
    window.alert(
      'Please select an institution.'
    )

    return
  }

  saving.value = true

  try {

    const payload = {
      id_work: form.value.id_work,
      major: form.value.major,
      level: form.value.level,
      date_in: form.value.date_in,
      date_out:
        form.value.date_out || null,
      gpa:
        form.value.gpa !== ''
          ? Number(form.value.gpa)
          : null,
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    if (modalMode.value === 'create') {

      await axios.post(
        '/api/educations',
        payload
      )

    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    if (
      modalMode.value === 'edit' &&
      selectedId.value
    ) {

      await axios.put(
        `/api/educations/${selectedId.value}`,
        payload
      )

    }


    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    showModal.value = false

    resetForm()

    /*
     * Increase key so EducationTable
     * fetches data again.
     */
    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to save education:',
      error
    )

    const message =
      error?.response?.data?.message ||
      'Failed to save education.'

    window.alert(message)

  } finally {

    saving.value = false

  }

}


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchWorks()
})

</script>