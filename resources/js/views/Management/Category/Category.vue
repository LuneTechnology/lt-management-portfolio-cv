<script setup lang="ts">
import { ref } from 'vue'
import axios from '@/services/axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import CategoryTable from '@/components/tables/management/CategoryTable.vue'

import { DocsIcon } from '@/icons'
import { useAuth } from '@/composables/useAuth'

interface Category {
  id_category: number
  name: string
}

const { isSuperAdmin } = useAuth()

const currentPageTitle = 'Category'

const totalCategory = ref(0)
const refreshKey = ref(0)

const showFormModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)

const isEditMode = ref(false)
const isSaving = ref(false)
const isDeleting = ref(false)

const selectedCategory = ref<Category | null>(null)

const form = ref({
  name: '',
})

const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| Open Create Modal
|--------------------------------------------------------------------------
*/
const addCategory = () => {
  isEditMode.value = false

  selectedCategory.value = null

  form.value = {
    name: '',
  }

  errorMessage.value = ''

  showFormModal.value = true
}

/*
|--------------------------------------------------------------------------
| Open Edit Modal
|--------------------------------------------------------------------------
*/
const editCategory = (category: Category) => {
  isEditMode.value = true

  selectedCategory.value = category

  form.value = {
    name: category.name,
  }

  errorMessage.value = ''

  showFormModal.value = true
}

/*
|--------------------------------------------------------------------------
| Open View Modal
|--------------------------------------------------------------------------
*/
const viewCategory = (category: Category) => {
  selectedCategory.value = category

  showViewModal.value = true
}

/*
|--------------------------------------------------------------------------
| Open Delete Modal
|--------------------------------------------------------------------------
*/
const deleteCategory = (category: Category) => {
  selectedCategory.value = category

  showDeleteModal.value = true
}

/*
|--------------------------------------------------------------------------
| Close Form Modal
|--------------------------------------------------------------------------
*/
const closeFormModal = () => {
  if (isSaving.value) return

  showFormModal.value = false

  errorMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| Close View Modal
|--------------------------------------------------------------------------
*/
const closeViewModal = () => {
  showViewModal.value = false
}

/*
|--------------------------------------------------------------------------
| Close Delete Modal
|--------------------------------------------------------------------------
*/
const closeDeleteModal = () => {
  if (isDeleting.value) return

  showDeleteModal.value = false
}

/*
|--------------------------------------------------------------------------
| Save Category
|--------------------------------------------------------------------------
*/
const saveCategory = async () => {
  if (!form.value.name.trim()) {
    errorMessage.value = 'Category name is required.'
    return
  }

  try {
    isSaving.value = true
    errorMessage.value = ''

    if (isEditMode.value && selectedCategory.value) {
      await axios.put(
        `/api/category/${selectedCategory.value.id_category}`,
        {
          name: form.value.name.trim(),
        },
      )
    } else {
      await axios.post('/api/category', {
        name: form.value.name.trim(),
      })
    }

    showFormModal.value = false

    refreshKey.value++
  } catch (error: any) {
    console.error('Failed to save category:', error)

    if (error.response?.data?.errors?.name) {
      errorMessage.value =
        error.response.data.errors.name[0]
    } else {
      errorMessage.value =
        error.response?.data?.message ||
        'Failed to save category.'
    }
  } finally {
    isSaving.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Confirm Delete
|--------------------------------------------------------------------------
*/
const confirmDelete = async () => {
  if (!selectedCategory.value) return

  try {
    isDeleting.value = true

    await axios.delete(
      `/api/category/${selectedCategory.value.id_category}`,
    )

    showDeleteModal.value = false

    selectedCategory.value = null

    refreshKey.value++
  } catch (error: any) {
    console.error('Failed to delete category:', error)

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete category.'
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Category"
        :value="totalCategory"
        :icon="DocsIcon"
        quote="Every category helps organize your work."
        :change="1"
        color="blue"
      />

      <!-- Header -->
      <div
        class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6"
      >
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h2
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Category List
            </h2>

            <p
              class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
              Manage project categories.
            </p>
          </div>

          <button
            v-if="isSuperAdmin()"
            type="button"
            class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
            @click="addCategory"
          >
            + Add Category
          </button>
        </div>
      </div>

      <!-- Table -->
      <CategoryTable
        :refresh-key="refreshKey"
        @total-changed="totalCategory = $event"
        @edit="editCategory"
        @view="viewCategory"
        @delete="deleteCategory"
      />
    </div>

    <!-- ========================================================= -->
    <!-- CREATE / EDIT MODAL -->
    <!-- ========================================================= -->

    <Teleport to="body">
      <div
        v-if="showFormModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto bg-black/50 p-4"
        @click.self="closeFormModal"
      >
        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >
          <!-- Header -->
          <div
            class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800"
          >
            <div>
              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white/90"
              >
                {{ isEditMode ? 'Edit Category' : 'Add Category' }}
              </h3>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                {{
                  isEditMode
                    ? 'Update category information.'
                    : 'Create a new project category.'
                }}
              </p>
            </div>

            <button
              type="button"
              class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
              @click="closeFormModal"
            >
              ✕
            </button>
          </div>

          <!-- Error -->
          <div
            v-if="errorMessage"
            class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400"
          >
            {{ errorMessage }}
          </div>

          <!-- Form -->
          <div>
            <label
              class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              Category Name
            </label>

            <input
              v-model="form.name"
              type="text"
              maxlength="50"
              placeholder="Enter category name"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-gray-500"
              @keyup.enter="saveCategory"
            />
          </div>

          <!-- Footer -->
          <div
            class="mt-6 flex justify-end gap-3"
          >
            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              :disabled="isSaving"
              @click="closeFormModal"
            >
              Cancel
            </button>

            <button
              type="button"
              class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="isSaving"
              @click="saveCategory"
            >
              {{ isSaving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ========================================================= -->
    <!-- VIEW MODAL -->
    <!-- ========================================================= -->

    <Teleport to="body">
      <div
        v-if="showViewModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
        @click.self="closeViewModal"
      >
        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >
          <div
            class="mb-5 flex items-center justify-between border-b border-gray-200 pb-5 dark:border-gray-800"
          >
            <h3
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Category Details
            </h3>

            <button
              type="button"
              class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
              @click="closeViewModal"
            >
              ✕
            </button>
          </div>

          <div
            v-if="selectedCategory"
            class="space-y-4"
          >
            <div>
              <p
                class="text-xs font-medium uppercase tracking-wide text-gray-400"
              >
                ID
              </p>

              <p
                class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90"
              >
                {{ selectedCategory.id_category }}
              </p>
            </div>

            <div>
              <p
                class="text-xs font-medium uppercase tracking-wide text-gray-400"
              >
                Category Name
              </p>

              <p
                class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90"
              >
                {{ selectedCategory.name }}
              </p>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              @click="closeViewModal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- ========================================================= -->
    <!-- DELETE MODAL -->
    <!-- ========================================================= -->

    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
        @click.self="closeDeleteModal"
      >
        <div
          class="w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-800 dark:bg-gray-900"
        >
          <h3
            class="text-lg font-semibold text-gray-800 dark:text-white/90"
          >
            Delete Category
          </h3>

          <p
            class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400"
          >
            Are you sure you want to delete
            <span
              class="font-semibold text-gray-800 dark:text-white/90"
            >
              "{{ selectedCategory?.name }}"
            </span>
            ?
          </p>

          <div
            v-if="errorMessage"
            class="mt-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400"
          >
            {{ errorMessage }}
          </div>

          <div
            class="mt-6 flex justify-end gap-3"
          >
            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              :disabled="isDeleting"
              @click="closeDeleteModal"
            >
              Cancel
            </button>

            <button
              type="button"
              class="rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-error-600 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="isDeleting"
              @click="confirmDelete"
            >
              {{ isDeleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AdminLayout>
</template>