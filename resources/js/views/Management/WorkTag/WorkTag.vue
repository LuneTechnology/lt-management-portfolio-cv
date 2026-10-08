<template>

  <AdminLayout>

    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Work Tag"
        :value="totalWorkTags"
        :icon="DocsIcon"
        quote="Every work background tells part of your journey."
        :change="1"
        color="blue"
      />


      <!-- Work Tag Table -->
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
                  Work Tag List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage tags used to categorize work backgrounds.
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
              Add Work Tag
            </button>

          </div>

        </div>


        <!-- Table -->
        <WorkTagTable
          :refresh-key="refreshKey"
          @total-changed="totalWorkTags = $event"
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
              {{ editingWorkTag ? 'Edit Work Tag' : 'Add Work Tag' }}
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
              Work Tag
            </label>

            <input
              v-model="form.name"
              type="text"
              maxlength="50"
              placeholder="Example: Company"
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
              @click="saveWorkTag"
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
              Work Tag Detail
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
              Work Tag
            </p>

            <p
              class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
            >
              {{ viewingWorkTag?.name || '-' }}
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
              Delete Work Tag
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
              Are you sure you want to delete this Work Tag?
            </p>

            <p
              class="mt-2 font-semibold text-gray-800 dark:text-white/90"
            >
              {{ deletingWorkTag?.name }}
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
              @click="deleteWorkTag"
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

import WorkTagTable from '@/components/tables/management/WorkTagTable.vue'

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

const currentPageTitle = ref('Work Tag')

const totalWorkTags = ref(0)

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

const editingWorkTag = ref<any>(null)

const viewingWorkTag = ref<any>(null)

const deletingWorkTag = ref<any>(null)


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

  editingWorkTag.value = null

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

const openEditModal = (workTag: any) => {

  editingWorkTag.value = workTag

  form.value = {
    name: workTag.name,
  }

  formError.value = ''

  showFormModal.value = true
}


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const openViewModal = (workTag: any) => {

  viewingWorkTag.value = workTag

  showViewModal.value = true
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const openDeleteModal = (workTag: any) => {

  deletingWorkTag.value = workTag

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

  editingWorkTag.value = null

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

const saveWorkTag = async () => {

  if (!form.value.name.trim()) {

    formError.value = 'Work Tag is required.'

    return
  }

  try {

    saving.value = true

    formError.value = ''

    if (editingWorkTag.value) {

      await axios.put(
        `/api/work-tags/${editingWorkTag.value.id_work_tag}`,
        form.value
      )

    } else {

      await axios.post(
        '/api/work-tags',
        form.value
      )

    }

    closeFormModal()

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to save work tag:',
      error
    )

    formError.value =
      error.response?.data?.message ||
      'Failed to save Work Tag.'

  } finally {

    saving.value = false

  }
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const deleteWorkTag = async () => {

  if (!deletingWorkTag.value) {
    return
  }

  try {

    deleting.value = true

    deleteError.value = ''

    await axios.delete(
      `/api/work-tags/${deletingWorkTag.value.id_work_tag}`
    )

    showDeleteModal.value = false

    deletingWorkTag.value = null

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to delete work tag:',
      error
    )

    deleteError.value =
      error.response?.data?.message ||
      'Failed to delete Work Tag.'

  } finally {

    deleting.value = false

  }
}

</script>