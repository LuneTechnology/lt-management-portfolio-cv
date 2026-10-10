<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Position Types"
        :value="totalPositionTypes"
        :icon="DocsIcon"
        quote="Define the roles behind every experience."
        :change="1"
        color="blue"
      />

      <!-- Position Type -->
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

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Position Type List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage position types used in experiences.
                </p>
              </div>

            </div>

            <button
              v-if="isSuperAdmin()"
              type="button"
              class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Position Type
            </button>

          </div>
        </div>

        <PositionTypeTable
          :refresh-key="refreshKey"
          @total-changed="totalPositionTypes = $event"
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
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >

          <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800">

            <div>
              <h2
                class="text-xl font-semibold text-gray-800 dark:text-white"
              >
                {{ isEditMode
                  ? 'Edit Position Type'
                  : 'Add Position Type' }}
              </h2>

              <p class="mt-1 text-sm text-gray-500">
                {{
                  isEditMode
                    ? 'Update position type information.'
                    : 'Create a new position type.'
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

          <form
            class="space-y-5"
            @submit.prevent="submitForm"
          >

            <!-- Name -->
            <div>

              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Position Type
              </label>

              <input
                v-model="form.name"
                type="text"
                placeholder="e.g. Unity Developer"
                maxlength="50"
                required
                class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

            </div>

            <!-- Error -->
            <div
              v-if="errorMessage"
              class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600"
            >
              {{ errorMessage }}
            </div>

            <!-- Buttons -->
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
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >

          <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800">

            <h2
              class="text-xl font-semibold text-gray-800 dark:text-white"
            >
              Position Type Details
            </h2>

            <button
              type="button"
              class="text-2xl text-gray-400"
              @click="showViewModal = false"
            >
              ×
            </button>

          </div>

          <div
            v-if="selectedPositionType"
            class="space-y-4"
          >

            <div>
              <p class="text-xs uppercase text-gray-400">
                Position Type
              </p>

              <p
                class="mt-1 text-lg font-semibold text-gray-800 dark:text-white"
              >
                {{ selectedPositionType.name }}
              </p>
            </div>

            <div>
              <p class="text-xs uppercase text-gray-400">
                ID
              </p>

              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                #{{ selectedPositionType.id_position_type }}
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
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
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
                Delete Position Type?
              </h2>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                Are you sure you want to delete
                <strong>
                  {{ selectedPositionType?.name }}
                </strong>?
              </p>

              <p class="mt-2 text-xs text-gray-400">
                Position Type that is already used by Experience
                cannot be deleted.
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
import PositionTypeTable from '@/components/tables/management/PositionTypeTable.vue'

import { DocsIcon } from '@/icons'
import { useAuth } from '@/composables/useAuth'

interface PositionType {
  id_position_type: number
  name: string
}

const { isSuperAdmin } = useAuth()

const currentPageTitle = ref('Position Type')

const totalPositionTypes = ref(0)
const refreshKey = ref(0)

const showFormModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)

const isEditMode = ref(false)
const saving = ref(false)
const deleting = ref(false)

const selectedPositionType =
  ref<PositionType | null>(null)

const errorMessage = ref('')

const form = ref({
  id_position_type: null as number | null,
  name: '',
})

const resetForm = () => {
  form.value = {
    id_position_type: null,
    name: '',
  }

  errorMessage.value = ''
}

const openCreateModal = () => {
  resetForm()

  isEditMode.value = false
  showFormModal.value = true
}

const openEditModal = (positionType: PositionType) => {
  form.value = {
    id_position_type:
      positionType.id_position_type,
    name: positionType.name,
  }

  isEditMode.value = true
  errorMessage.value = ''
  showFormModal.value = true
}

const openViewModal = (positionType: PositionType) => {
  selectedPositionType.value = positionType
  showViewModal.value = true
}

const openDeleteModal = (positionType: PositionType) => {
  selectedPositionType.value = positionType
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

    if (
      isEditMode.value &&
      form.value.id_position_type
    ) {
      await axios.put(
        `/api/position-types/${form.value.id_position_type}`,
        payload
      )
    } else {
      await axios.post(
        '/api/position-types',
        payload
      )
    }

    refreshKey.value++

    closeFormModal()

  } catch (error: any) {
    console.error(
      'Failed to save position type:',
      error
    )

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save position type.'

  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  if (
    !selectedPositionType.value ||
    !isSuperAdmin()
  ) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(
      `/api/position-types/${selectedPositionType.value.id_position_type}`
    )

    refreshKey.value++

    showDeleteModal.value = false
    selectedPositionType.value = null

  } catch (error: any) {
    console.error(
      'Failed to delete position type:',
      error
    )

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete position type.'

    showDeleteModal.value = false

  } finally {
    deleting.value = false
  }
}
</script>