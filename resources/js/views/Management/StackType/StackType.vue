<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Stack Types"
        :value="totalStackTypes"
        :icon="DocsIcon"
        quote="Organize every project with meaningful stack types."
        :change="1"
        color="blue"
      />

      <!-- Stack Type -->
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <!-- Header -->
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10"
              >
                <DocsIcon class="h-5 w-5 text-blue-500" />
              </div>

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Stack Type List
                </h3>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Manage stack types used in projects and other data.
                </p>
              </div>
            </div>

            <button
              v-if="isSuperAdmin()"
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Stack Type
            </button>
          </div>
        </div>

        <!-- Table -->
        <StackTypeTable
          :refresh-key="refreshKey"
          @total-changed="totalStackTypes = $event"
          @edit="openEditModal"
          @view="openViewModal"
          @delete="openDeleteModal"
        />
      </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    <Teleport to="body">
      <div
        v-if="showFormModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
        @click.self="closeFormModal"
      >
        <div
          class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
        >
          <div class="mb-6 flex items-center justify-between">
            <div>
              <h2
                class="text-xl font-semibold text-gray-800 dark:text-white"
              >
                {{ isEditMode ? 'Edit Stack Type' : 'Add Stack Type' }}
              </h2>

              <p class="mt-1 text-sm text-gray-500">
                {{
                  isEditMode
                    ? 'Update stack type information.'
                    : 'Create a new stack type.'
                }}
              </p>
            </div>

            <button
              type="button"
              class="text-2xl text-gray-400 hover:text-gray-700"
              @click="closeFormModal"
            >
              ×
            </button>
          </div>

          <form class="space-y-5" @submit.prevent="submitForm">
            <div>
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Stack Type
              </label>

              <input
                v-model="form.name"
                type="text"
                placeholder="e.g. Frontend"
                maxlength="50"
                required
                class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />
            </div>

            <div
              v-if="errorMessage"
              class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600"
            >
              {{ errorMessage }}
            </div>

            <div class="flex justify-end gap-3 pt-2">
              <button
                type="button"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                @click="closeFormModal"
              >
                Cancel
              </button>

              <button
                type="submit"
                :disabled="saving"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50"
              >
                {{
                  saving
                    ? 'Saving...'
                    : isEditMode
                      ? 'Update'
                      : 'Create'
                }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- VIEW MODAL -->
    <Teleport to="body">
      <div
        v-if="showViewModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
        @click.self="showViewModal = false"
      >
        <div
          class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
        >
          <div class="mb-6 flex items-center justify-between">
            <h2
              class="text-xl font-semibold text-gray-800 dark:text-white"
            >
              Stack Type Details
            </h2>

            <button
              type="button"
              class="text-2xl text-gray-400 hover:text-gray-700"
              @click="showViewModal = false"
            >
              ×
            </button>
          </div>

          <div
            v-if="selectedStackType"
            class="space-y-4"
          >
            <div>
              <p class="text-xs uppercase text-gray-400">
                Stack Type
              </p>

              <p
                class="mt-1 text-lg font-semibold text-gray-800 dark:text-white"
              >
                {{ selectedStackType.name }}
              </p>
            </div>

            <div>
              <p class="text-xs uppercase text-gray-400">
                ID
              </p>

              <p
                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
              >
                #{{ selectedStackType.id_stack_type }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- DELETE MODAL -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
      >
        <div
          class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
        >
          <div class="flex items-start gap-4">
            <div
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-500"
            >
              !
            </div>

            <div>
              <h2
                class="text-lg font-semibold text-gray-800 dark:text-white"
              >
                Delete Stack Type?
              </h2>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                Are you sure you want to delete
                <strong>
                  {{ selectedStackType?.name }}
                </strong>?
              </p>

              <p class="mt-2 text-xs text-gray-400">
                Stack Type that is already being used cannot be deleted.
              </p>
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700"
              @click="showDeleteModal = false"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="deleting"
              class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-error-600 disabled:opacity-50"
              @click="confirmDelete"
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
import axios from '@/services/axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import StackTypeTable from '@/components/tables/management/StackTypeTable.vue'

import { DocsIcon } from '@/icons'
import { useAuth } from '@/composables/useAuth'

interface StackType {
  id_stack_type: number
  name: string
}

const { isSuperAdmin } = useAuth()

const currentPageTitle = ref('Stack Type')

const totalStackTypes = ref(0)
const refreshKey = ref(0)

const showFormModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)

const isEditMode = ref(false)
const saving = ref(false)
const deleting = ref(false)

const selectedStackType = ref<StackType | null>(null)

const errorMessage = ref('')

const form = ref({
  id_stack_type: null as number | null,
  name: '',
})

const resetForm = () => {
  form.value = {
    id_stack_type: null,
    name: '',
  }

  errorMessage.value = ''
}

const openCreateModal = () => {
  resetForm()
  isEditMode.value = false
  showFormModal.value = true
}

const openEditModal = (stackType: StackType) => {
  form.value = {
    id_stack_type: stackType.id_stack_type,
    name: stackType.name,
  }

  isEditMode.value = true
  errorMessage.value = ''
  showFormModal.value = true
}

const openViewModal = (stackType: StackType) => {
  selectedStackType.value = stackType
  showViewModal.value = true
}

const openDeleteModal = (stackType: StackType) => {
  selectedStackType.value = stackType
  errorMessage.value = ''
  showDeleteModal.value = true
}

const closeFormModal = () => {
  showFormModal.value = false
  resetForm()
}

const submitForm = async () => {
  if (!isSuperAdmin()) return

  saving.value = true
  errorMessage.value = ''

  try {
    const payload = {
      name: form.value.name,
    }

    if (isEditMode.value && form.value.id_stack_type) {
      await axios.put(
        `/api/stack-types/${form.value.id_stack_type}`,
        payload
      )
    } else {
      await axios.post(
        '/api/stack-types',
        payload
      )
    }

    refreshKey.value++
    closeFormModal()
  } catch (error: any) {
    console.error(
      'Failed to save stack type:',
      error
    )

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save stack type.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  if (
    !selectedStackType.value ||
    !isSuperAdmin()
  ) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(
      `/api/stack-types/${selectedStackType.value.id_stack_type}`
    )

    refreshKey.value++

    showDeleteModal.value = false
    selectedStackType.value = null
  } catch (error: any) {
    console.error(
      'Failed to delete stack type:',
      error
    )

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete stack type.'

    showDeleteModal.value = false
  } finally {
    deleting.value = false
  }
}
</script>