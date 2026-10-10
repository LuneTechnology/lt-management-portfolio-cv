<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- ===================================================== -->
      <!-- SUMMARY -->
      <!-- ===================================================== -->

      <ManagementSummaryCard
        title="Total Stack"
        :value="totalStacks"
        :icon="DocsIcon"
        quote="Every stack supports your experience."
        :change="1"
        color="blue"
      />


      <!-- ===================================================== -->
      <!-- STACK MANAGEMENT -->
      <!-- ===================================================== -->

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

            <!-- Title -->
            <div class="flex items-center gap-3">

              <div>

                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Stack List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage technology stacks used in your experience.
                </p>

              </div>

            </div>


            <!-- Add Button -->
            <button
              v-if="isSuperAdmin()"
              type="button"
              class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Stack
            </button>

          </div>
        </div>


        <!-- Stack Table -->
        <StackTable
          :refresh-key="refreshKey"
          @total-changed="totalStacks = $event"
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
        @click.self="closeFormModal"
      >

        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              {{ editingStack ? 'Edit Stack' : 'Add Stack' }}
            </h3>

            <button
              type="button"
              class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
              @click="closeFormModal"
            >
              ✕
            </button>

          </div>


          <!-- Form -->
          <div class="mt-6 space-y-5">

            <!-- Stack -->
            <div>

              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Stack
              </label>

              <input
                v-model="form.nama"
                type="text"
                maxlength="255"
                placeholder="Example: Laravel"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

            </div>


            <!-- Stack Type -->
            <div>

              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Stack Type
              </label>

              <select
                v-model="form.id_stack_type"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              >

                <option
                  value=""
                  disabled
                >
                  Select Stack Type
                </option>

                <option
                  v-for="stackType in stackTypes"
                  :key="stackType.id_stack_type"
                  :value="stackType.id_stack_type"
                >
                  {{ stackType.name }}
                </option>

              </select>

            </div>


            <!-- Error -->
            <p
              v-if="formError"
              class="text-sm text-error-500"
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
              @click="saveStack"
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
        @click.self="showViewModal = false"
      >

        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Stack Detail
            </h3>

            <button
              type="button"
              class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
              @click="showViewModal = false"
            >
              ✕
            </button>

          </div>


          <!-- Detail -->
          <div
            v-if="viewingStack"
            class="mt-6 space-y-5"
          >

            <!-- Stack -->
            <div>

              <p
                class="text-xs font-medium uppercase text-gray-400"
              >
                Stack
              </p>

              <p
                class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
              >
                {{ viewingStack.nama || '-' }}
              </p>

            </div>


            <!-- Stack Type -->
            <div>

              <p
                class="text-xs font-medium uppercase text-gray-400"
              >
                Stack Type
              </p>

              <p
                class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
              >
                {{ viewingStack.stackType?.name || viewingStack.stack_type?.name || '-' }}
              </p>

            </div>

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
        @click.self="showDeleteModal = false"
      >

        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >

          <!-- Header -->
          <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800">

            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Delete Stack
            </h3>

            <button
              type="button"
              class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
              @click="showDeleteModal = false"
            >
              ✕
            </button>

          </div>


          <!-- Message -->
          <div class="mt-5">

            <p
              class="text-sm text-gray-600 dark:text-gray-400"
            >
              Are you sure you want to delete this Stack?
            </p>

            <p
              class="mt-2 font-semibold text-gray-800 dark:text-white/90"
            >
              {{ deletingStack?.nama }}
            </p>

            <p
              class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
              Stack yang masih digunakan oleh Experience tidak dapat dihapus.
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
              @click="deleteStack"
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

import { onMounted, ref } from 'vue'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'

import StackTable from '@/components/tables/management/StackTable.vue'

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

const currentPageTitle = ref('Stack')

const totalStacks = ref(0)

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

const editingStack = ref<any>(null)

const viewingStack = ref<any>(null)

const deletingStack = ref<any>(null)


/*
|--------------------------------------------------------------------------
| Stack Types
|--------------------------------------------------------------------------
*/

const stackTypes = ref<any[]>([])


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({
  nama: '',
  id_stack_type: '' as number | '',
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
| Load Stack Types
|--------------------------------------------------------------------------
*/

const loadStackTypes = async () => {

  try {

    const response = await axios.get(
      '/api/stack-types'
    )

    const data =
      response.data.data ??
      response.data

    stackTypes.value = Array.isArray(data)
      ? data
      : []

  } catch (error) {

    console.error(
      'Failed to load stack types:',
      error
    )

    stackTypes.value = []

  }

}


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {

  if (!isSuperAdmin()) {
    return
  }

  editingStack.value = null

  form.value = {
    nama: '',
    id_stack_type: '',
  }

  formError.value = ''

  showFormModal.value = true

}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEditModal = (stack: any) => {

  if (!isSuperAdmin()) {
    return
  }

  editingStack.value = stack

  form.value = {
    nama: stack.nama,
    id_stack_type: stack.id_stack_type,
  }

  formError.value = ''

  showFormModal.value = true

}


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const openViewModal = async (stack: any) => {
  try {
    const response = await axios.get(
      `/api/stacks/${stack.id_stack}`
    )

    const data = response.data.data ?? response.data

    viewingStack.value = {
      ...data,

      // Normalisasi relasi Stack Type
      stackType:
        data.stackType ??
        data.stack_type ??
        stack.stackType ??
        stack.stack_type ??
        null,
    }

    showViewModal.value = true

  } catch (error) {
    console.error(
      'Failed to load stack detail:',
      error
    )

    // Tetap tampilkan data dari tabel jika request gagal
    viewingStack.value = {
      ...stack,

      stackType:
        stack.stackType ??
        stack.stack_type ??
        null,
    }

    showViewModal.value = true
  }
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const openDeleteModal = (stack: any) => {

  if (!isSuperAdmin()) {
    return
  }

  deletingStack.value = stack

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

  editingStack.value = null

  form.value = {
    nama: '',
    id_stack_type: '',
  }

  formError.value = ''

}


/*
|--------------------------------------------------------------------------
| Save Stack
|--------------------------------------------------------------------------
*/

const saveStack = async () => {

  if (!isSuperAdmin()) {
    return
  }


  /*
  |--------------------------------------------------------------------------
  | Validation
  |--------------------------------------------------------------------------
  */

  if (!form.value.nama.trim()) {

    formError.value =
      'Stack is required.'

    return

  }


  if (!form.value.id_stack_type) {

    formError.value =
      'Stack Type is required.'

    return

  }


  try {

    saving.value = true

    formError.value = ''


    /*
    |--------------------------------------------------------------------------
    | Payload
    |--------------------------------------------------------------------------
    */

    const payload = {
      nama: form.value.nama.trim(),
      id_stack_type: Number(
        form.value.id_stack_type
      ),
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    if (editingStack.value) {

      await axios.put(
        `/api/stacks/${editingStack.value.id_stack}`,
        payload
      )

    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    else {

      await axios.post(
        '/api/stacks',
        payload
      )

    }


    /*
    |--------------------------------------------------------------------------
    | Close & Refresh
    |--------------------------------------------------------------------------
    */

    closeFormModal()

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to save stack:',
      error
    )

    formError.value =
      error.response?.data?.message ||
      'Failed to save Stack.'

  } finally {

    saving.value = false

  }

}


/*
|--------------------------------------------------------------------------
| Delete Stack
|--------------------------------------------------------------------------
*/

const deleteStack = async () => {

  if (
    !deletingStack.value ||
    !isSuperAdmin()
  ) {
    return
  }


  try {

    deleting.value = true

    deleteError.value = ''


    await axios.delete(
      `/api/stacks/${deletingStack.value.id_stack}`
    )


    /*
    |--------------------------------------------------------------------------
    | Close Delete Modal
    |--------------------------------------------------------------------------
    */

    showDeleteModal.value = false

    deletingStack.value = null


    /*
    |--------------------------------------------------------------------------
    | Refresh Table
    |--------------------------------------------------------------------------
    */

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to delete stack:',
      error
    )

    deleteError.value =
      error.response?.data?.message ||
      'Failed to delete Stack.'

  } finally {

    deleting.value = false

  }

}


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

  loadStackTypes()

})

</script>