<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Work Type"
        :value="totalWorkTypes"
        :icon="DocsIcon"
        quote="Every experience tells a story."
        :change="1"
        color="blue"
      />

      <!-- Work Type Table -->
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
                  Work Type List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage types of work used in your experience.
                </p>
              </div>

            </div>

            <!-- Add Button -->
            <button
              v-if="isSuperAdmin()"
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Work Type
            </button>

          </div>
        </div>

        <!-- Table -->
        <WorkTypeTable
          :refresh-key="refreshKey"
          @total-changed="totalWorkTypes = $event"
          @edit="openEditModal"
          @view="openViewModal"
          @delete="openDeleteModal"
        />

      </div>

    </div>

    <!-- ===================================================== -->
    <!-- CREATE / EDIT MODAL -->
    <!-- ===================================================== -->

    <Teleport to="body">
      <div
        v-if="showFormModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
      >

        <div
          class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="flex items-center justify-between">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              {{ editingWorkType ? 'Edit Work Type' : 'Add Work Type' }}
            </h3>

            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
              @click="closeFormModal"
            >
              ✕
            </button>

          </div>

          <!-- Form -->
          <div class="mt-6">

            <label
              class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              Work Type
            </label>

            <input
              v-model="form.name"
              type="text"
              maxlength="50"
              placeholder="Example: Internship"
              class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            />

            <p
              v-if="formError"
              class="mt-2 text-sm text-error-500"
            >
              {{ formError }}
            </p>

          </div>

          <!-- Footer -->
          <div class="mt-6 flex justify-end gap-3">

            <button
              type="button"
              class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              @click="closeFormModal"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="saving"
              class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
              @click="saveWorkType"
            >
              {{ saving ? 'Saving...' : 'Save' }}
            </button>

          </div>

        </div>

      </div>
    </Teleport>


    <!-- ===================================================== -->
    <!-- VIEW MODAL -->
    <!-- ===================================================== -->

    <Teleport to="body">
      <div
        v-if="showViewModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
      >

        <div
          class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="flex items-center justify-between">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Work Type Detail
            </h3>

            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
              @click="showViewModal = false"
            >
              ✕
            </button>

          </div>

          <!-- Detail -->
          <div class="mt-6">

            <p class="text-xs font-medium uppercase text-gray-400">
              Work Type
            </p>

            <p
              class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
            >
              {{ viewingWorkType?.name || '-' }}
            </p>

          </div>

          <!-- Footer -->
          <div class="mt-6 flex justify-end">

            <button
              type="button"
              class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              @click="showViewModal = false"
            >
              Close
            </button>

          </div>

        </div>

      </div>
    </Teleport>


    <!-- ===================================================== -->
    <!-- DELETE MODAL -->
    <!-- ===================================================== -->

    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
      >

        <div
          class="w-full max-w-md rounded-2xl bg-white p-6 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="flex items-center justify-between">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Delete Work Type
            </h3>

            <button
              type="button"
              class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
              @click="showDeleteModal = false"
            >
              ✕
            </button>

          </div>

          <!-- Message -->
          <div class="mt-5">

            <p class="text-sm text-gray-600 dark:text-gray-400">
              Are you sure you want to delete this Work Type?
            </p>

            <p
              class="mt-2 font-semibold text-gray-800 dark:text-white/90"
            >
              {{ deletingWorkType?.name }}
            </p>

            <p
              v-if="deleteError"
              class="mt-3 text-sm text-error-500"
            >
              {{ deleteError }}
            </p>

          </div>

          <!-- Footer -->
          <div class="mt-6 flex justify-end gap-3">

            <button
              type="button"
              class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              @click="showDeleteModal = false"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="deleting"
              class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-error-600 disabled:cursor-not-allowed disabled:opacity-50"
              @click="deleteWorkType"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>

          </div>

        </div>

      </div>
    </Teleport>

  </AdminLayout>
</template>


<script setup lang="ts">

import { ref } from 'vue'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'

import WorkTypeTable from '@/components/tables/management/WorkTypeTable.vue'

import { DocsIcon } from '@/icons'

import { useAuth } from '@/composables/useAuth'

import axios from '@/services/axios'


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

const { isSuperAdmin } = useAuth()


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

const currentPageTitle = ref('Work Type')

const totalWorkTypes = ref(0)

const refreshKey = ref(0)


/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/

const showFormModal = ref(false)

const showViewModal = ref(false)

const showDeleteModal = ref(false)


/*
|--------------------------------------------------------------------------
| Selected Data
|--------------------------------------------------------------------------
*/

const editingWorkType = ref<any>(null)

const viewingWorkType = ref<any>(null)

const deletingWorkType = ref<any>(null)


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({
  name: '',
})

const saving = ref(false)

const formError = ref('')


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleting = ref(false)

const deleteError = ref('')


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {

  editingWorkType.value = null

  form.value = {
    name: '',
  }

  formError.value = ''

  showFormModal.value = true
}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEditModal = (workType: any) => {

  editingWorkType.value = workType

  form.value = {
    name: workType.name,
  }

  formError.value = ''

  showFormModal.value = true
}


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const openViewModal = (workType: any) => {

  viewingWorkType.value = workType

  showViewModal.value = true
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const openDeleteModal = (workType: any) => {

  deletingWorkType.value = workType

  deleteError.value = ''

  showDeleteModal.value = true
}


/*
|--------------------------------------------------------------------------
| Close Form
|--------------------------------------------------------------------------
*/

const closeFormModal = () => {

  showFormModal.value = false

  editingWorkType.value = null

  form.value = {
    name: '',
  }

  formError.value = ''
}


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const saveWorkType = async () => {

  if (!form.value.name.trim()) {

    formError.value = 'Work Type is required.'

    return
  }

  try {

    saving.value = true

    formError.value = ''

    if (editingWorkType.value) {

      await axios.put(
        `/api/work-types/${editingWorkType.value.id_work_type}`,
        form.value
      )

    } else {

      await axios.post(
        '/api/work-types',
        form.value
      )

    }

    closeFormModal()

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to save work type:',
      error
    )

    formError.value =
      error.response?.data?.message ||
      'Failed to save Work Type.'

  } finally {

    saving.value = false

  }
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteWorkType = async () => {

  if (!deletingWorkType.value) {
    return
  }

  try {

    deleting.value = true

    deleteError.value = ''

    await axios.delete(
      `/api/work-types/${deletingWorkType.value.id_work_type}`
    )

    showDeleteModal.value = false

    deletingWorkType.value = null

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to delete work type:',
      error
    )

    deleteError.value =
      error.response?.data?.message ||
      'Failed to delete Work Type.'

  } finally {

    deleting.value = false

  }
}

</script>