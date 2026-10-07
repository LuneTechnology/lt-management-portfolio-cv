<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Work"
        :value="totalWork"
        :icon="DocsIcon"
        quote="Every role is a step toward growth."
        :change="1"
        color="blue"
      />

      <!-- Work Table -->
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
                <DocsIcon class="h-5 w-5 text-blue-500" />
              </div>

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Work List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage companies, institutions, and organizations.
                </p>
              </div>

            </div>

            <button
              v-if="isSuperAdmin"
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add Work
            </button>

          </div>
        </div>

        <WorkTable
          :refresh-key="refreshKey"
          @total-changed="totalWork = $event"
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
                {{ isEditMode ? 'Edit Work' : 'Add Work' }}
              </h2>

              <p class="mt-1 text-sm text-gray-500">
                {{ isEditMode
                  ? 'Update work information.'
                  : 'Add a new work background.' }}
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
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Name
              </label>

              <input
                v-model="form.name"
                type="text"
                placeholder="e.g. PT. Molca Teknologi Nusantara"
                class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                required
              />
            </div>

            <!-- Place -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Place
              </label>

              <input
                v-model="form.place"
                type="text"
                placeholder="e.g. Surabaya"
                class="h-11 w-full rounded-lg border border-gray-200 px-4 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                required
              />
            </div>

            <!-- Work Tag -->
            <div>
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Work Tag
              </label>

              <select
                v-model="form.id_work_tag"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                required
              >
                <option value="">
                  Select Work Tag
                </option>

                <option
                  v-for="tag in workTags"
                  :key="tag.id_work_tag"
                  :value="tag.id_work_tag"
                >
                  {{ tag.name }}
                </option>
              </select>
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
                {{ saving
                  ? 'Saving...'
                  : isEditMode
                    ? 'Update Work'
                    : 'Create Work' }}
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
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
              Work Details
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
            v-if="selectedWork"
            class="space-y-4"
          >

            <div>
              <p class="text-xs uppercase text-gray-400">
                Name
              </p>
              <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                {{ selectedWork.name }}
              </p>
            </div>

            <div>
              <p class="text-xs uppercase text-gray-400">
                Place
              </p>
              <p class="mt-1 text-gray-700 dark:text-gray-300">
                {{ selectedWork.place }}
              </p>
            </div>

            <div>
              <p class="text-xs uppercase text-gray-400">
                Work Tag
              </p>
              <p class="mt-1 text-gray-700 dark:text-gray-300">
                {{ selectedWork.work_tag?.name || '-' }}
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
              <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                Delete Work?
              </h2>

              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Are you sure you want to delete
                <strong>{{ selectedWork?.name }}</strong>?
              </p>

              <p class="mt-2 text-xs text-gray-400">
                Work that is already used by Education or Experience cannot be deleted.
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
              {{ deleting ? 'Deleting...' : 'Delete Work' }}
            </button>

          </div>

        </div>
      </div>
    </Teleport>

  </AdminLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import axios from '@/services/axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import WorkTable from '@/components/tables/management/WorkTable.vue'

import { DocsIcon } from '@/icons'
import { useAuth } from '@/composables/useAuth'

interface WorkTag {
  id_work_tag: number
  name: string
}

interface Work {
  id_work: number
  name: string
  place: string
  id_work_tag: number
  work_tag?: WorkTag | null
}

const { isSuperAdmin } = useAuth()

const currentPageTitle = ref('Work')

const totalWork = ref(0)
const refreshKey = ref(0)

const workTags = ref<WorkTag[]>([])

const showFormModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)

const isEditMode = ref(false)
const saving = ref(false)
const deleting = ref(false)

const selectedWork = ref<Work | null>(null)
const errorMessage = ref('')

const form = ref({
  id_work: null as number | null,
  name: '',
  place: '',
  id_work_tag: '' as number | '',
})

const resetForm = () => {
  form.value = {
    id_work: null,
    name: '',
    place: '',
    id_work_tag: '',
  }

  errorMessage.value = ''
}

const fetchWorkTags = async () => {
  try {
    const response = await axios.get('/api/work-tags')

    workTags.value = Array.isArray(response.data)
      ? response.data
      : response.data.data ?? []
  } catch (error) {
    console.error('Failed to fetch work tags:', error)
  }
}

const openCreateModal = () => {
  resetForm()
  isEditMode.value = false
  showFormModal.value = true
}

const openEditModal = (work: Work) => {
  form.value = {
    id_work: work.id_work,
    name: work.name,
    place: work.place,
    id_work_tag: work.id_work_tag,
  }

  isEditMode.value = true
  errorMessage.value = ''
  showFormModal.value = true
}

const openViewModal = (work: Work) => {
  selectedWork.value = work
  showViewModal.value = true
}

const openDeleteModal = (work: Work) => {
  selectedWork.value = work
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
      place: form.value.place,
      id_work_tag: form.value.id_work_tag,
    }

    if (isEditMode.value && form.value.id_work) {
      await axios.put(
        `/api/works/${form.value.id_work}`,
        payload
      )
    } else {
      await axios.post(
        '/api/works',
        payload
      )
    }

    refreshKey.value++

    closeFormModal()
  } catch (error: any) {
    console.error('Failed to save work:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to save work.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = async () => {
  if (!selectedWork.value || !isSuperAdmin()) return

  deleting.value = true

  try {
    await axios.delete(
      `/api/works/${selectedWork.value.id_work}`
    )

    refreshKey.value++

    showDeleteModal.value = false
    selectedWork.value = null

  } catch (error: any) {
    console.error('Failed to delete work:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete work.'

    showDeleteModal.value = false
    showFormModal.value = true
  } finally {
    deleting.value = false
  }
}

onMounted(fetchWorkTags)
</script>