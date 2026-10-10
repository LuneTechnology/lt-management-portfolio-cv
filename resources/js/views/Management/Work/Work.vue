<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- ===================================================== -->
      <!-- SUMMARY -->
      <!-- ===================================================== -->

      <ManagementSummaryCard
        title="Total Work"
        :value="totalWork"
        :icon="DocsIcon"
        quote="Every role is a step toward growth."
        :change="1"
        color="blue"
      />

      <!-- ===================================================== -->
      <!-- WORK MANAGEMENT -->
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
                  Work List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  Manage companies, institutions, and organizations.
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
              Add Work
            </button>

          </div>
        </div>


        <!-- Work Table -->
        <WorkTable
          :refresh-key="refreshKey"
          @total-changed="totalWork = $event"
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
              {{ editingWork ? 'Edit Work' : 'Add Work' }}
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

            <!-- Name -->
            <div>

              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Name
              </label>

              <input
                v-model="form.name"
                type="text"
                maxlength="50"
                placeholder="Example: PT. Molca Teknologi Nusantara"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

            </div>


            <!-- Place -->
            <div>

              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Place
              </label>

              <input
                v-model="form.place"
                type="text"
                maxlength="50"
                placeholder="Example: Surabaya"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

            </div>


            <!-- Work Tag -->
            <div>

              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Work Tag
              </label>

              <select
                v-model="form.id_work_tag"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              >

                <option
                  value=""
                  disabled
                >
                  Select Work Tag
                </option>

                <option
                  v-for="workTag in workTags"
                  :key="workTag.id_work_tag"
                  :value="workTag.id_work_tag"
                >
                  {{ workTag.name }}
                </option>

              </select>

            </div>


            <!-- Optional Work Image -->
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Image <span class="font-normal text-gray-400">(optional)</span></label>
              <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                  <img v-if="imagePreview" :src="imagePreview" alt="Work image preview" class="h-full w-full object-cover" />
                  <span v-else class="text-xs text-gray-400">No image</span>
                </div>
                <div class="min-w-0 flex-1">
                  <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:file:bg-gray-800 dark:file:text-gray-200" @change="onWorkImageSelected" />
                  <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP · maksimal 5 MB. Kosongkan jika tidak ingin mengubah gambar.</p>
                  <div v-if="selectedImage" class="mt-2 flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800/70">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ selectedImage.name }}</span>
                    <span class="ml-auto rounded-full bg-brand-50 px-2 py-1 text-[10px] font-semibold text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">Ready to upload</span>
                  </div>
                  <button v-if="selectedImage" type="button" class="mt-2 text-xs font-medium text-error-500 hover:text-error-600" @click="clearSelectedImage">Remove selected image</button>
                </div>
              </div>
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
              @click="saveWork"
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
              Work Detail
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
            v-if="viewingWork"
            class="mt-6 space-y-5"
          >

            <div v-if="viewingWork.image_url" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
              <img :src="viewingWork.image_url" :alt="viewingWork.name" class="max-h-56 w-full object-cover" />
            </div>

            <!-- Name -->
            <div>

              <p
                class="text-xs font-medium uppercase text-gray-400"
              >
                Name
              </p>

              <p
                class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
              >
                {{ viewingWork.name || '-' }}
              </p>

            </div>


            <!-- Place -->
            <div>

              <p
                class="text-xs font-medium uppercase text-gray-400"
              >
                Place
              </p>

              <p
                class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
              >
                {{ viewingWork.place || '-' }}
              </p>

            </div>


            <!-- Work Tag -->
            <div>

              <p
                class="text-xs font-medium uppercase text-gray-400"
              >
                Work Tag
              </p>

              <p
                class="mt-1 text-base font-semibold text-gray-800 dark:text-white/90"
              >
                {{ viewingWork.workTag?.name || viewingWork.work_tag?.name || '-' }}
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
              Delete Work
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
              Are you sure you want to delete this Work?
            </p>

            <p
              class="mt-2 font-semibold text-gray-800 dark:text-white/90"
            >
              {{ deletingWork?.name }}
            </p>

            <p
              class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
              Work yang masih digunakan oleh Experience tidak dapat dihapus.
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
              @click="deleteWork"
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

import WorkTable from '@/components/tables/management/WorkTable.vue'

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

const currentPageTitle = ref('Work')

const totalWork = ref(0)

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

const editingWork = ref<any>(null)

const viewingWork = ref<any>(null)

const deletingWork = ref<any>(null)


/*
|--------------------------------------------------------------------------
| Work Tags
|--------------------------------------------------------------------------
*/

const workTags = ref<any[]>([])


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = ref({
  name: '',
  place: '',
  id_work_tag: '' as number | '',
})
const imageInput = ref<HTMLInputElement | null>(null)
const selectedImage = ref<File | null>(null)
const imagePreview = ref('')

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
| Load Work Tags
|--------------------------------------------------------------------------
*/

const clearSelectedImage = () => {
  selectedImage.value = null
  imagePreview.value = editingWork.value?.image_url || ''
  if (imageInput.value) imageInput.value.value = ''
}

const onWorkImageSelected = (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return
  if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
    formError.value = 'Image must be JPG, PNG, or WebP.'
    input.value = ''
    return
  }
  if (file.size > 5 * 1024 * 1024) {
    formError.value = 'Image size must be 5 MB or less.'
    input.value = ''
    return
  }
  selectedImage.value = file
  imagePreview.value = URL.createObjectURL(file)
  formError.value = ''
}

const loadWorkTags = async () => {

  try {

    const response = await axios.get(
      '/api/work-tags'
    )

    const data =
      response.data.data ??
      response.data

    workTags.value = Array.isArray(data)
      ? data
      : []

  } catch (error) {

    console.error(
      'Failed to load work tags:',
      error
    )

    workTags.value = []

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

  editingWork.value = null

  form.value = {
    name: '',
    place: '',
    id_work_tag: '',
  }
  selectedImage.value = null
  imagePreview.value = ''
  if (imageInput.value) imageInput.value.value = ''

  formError.value = ''

  showFormModal.value = true

}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEditModal = (work: any) => {

  if (!isSuperAdmin()) {
    return
  }

  editingWork.value = work

  form.value = {
    name: work.name,
    place: work.place,
    id_work_tag: work.id_work_tag,
  }
  selectedImage.value = null
  imagePreview.value = work.image_url || ''
  if (imageInput.value) imageInput.value.value = ''

  formError.value = ''

  showFormModal.value = true

}


/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

const openViewModal = (work: any) => {

  viewingWork.value = work

  showViewModal.value = true

}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const openDeleteModal = (work: any) => {

  if (!isSuperAdmin()) {
    return
  }

  deletingWork.value = work

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

  editingWork.value = null

  form.value = {
    name: '',
    place: '',
    id_work_tag: '',
  }
  selectedImage.value = null
  imagePreview.value = ''
  if (imageInput.value) imageInput.value.value = ''

  formError.value = ''

}


/*
|--------------------------------------------------------------------------
| Save Work
|--------------------------------------------------------------------------
*/

const saveWork = async () => {

  if (!isSuperAdmin()) {
    return
  }


  /*
  |--------------------------------------------------------------------------
  | Validation
  |--------------------------------------------------------------------------
  */

  if (!form.value.name.trim()) {

    formError.value =
      'Name is required.'

    return

  }


  if (!form.value.place.trim()) {

    formError.value =
      'Place is required.'

    return

  }


  if (!form.value.id_work_tag) {

    formError.value =
      'Work Tag is required.'

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

    const payload = new FormData()
    payload.append('name', form.value.name.trim())
    payload.append('place', form.value.place.trim())
    payload.append('id_work_tag', String(Number(form.value.id_work_tag)))
    if (selectedImage.value) payload.append('image', selectedImage.value)

    if (editingWork.value) {
      payload.append('_method', 'PUT')
      await axios.post(`/api/works/${editingWork.value.id_work}`, payload)
    } else {
      await axios.post('/api/works', payload)
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
      'Failed to save work:',
      error
    )

    formError.value =
      error.response?.data?.message ||
      'Failed to save Work.'

  } finally {

    saving.value = false

  }

}


/*
|--------------------------------------------------------------------------
| Delete Work
|--------------------------------------------------------------------------
*/

const deleteWork = async () => {

  if (
    !deletingWork.value ||
    !isSuperAdmin()
  ) {
    return
  }


  try {

    deleting.value = true

    deleteError.value = ''


    await axios.delete(
      `/api/works/${deletingWork.value.id_work}`
    )


    /*
    |--------------------------------------------------------------------------
    | Close Delete Modal
    |--------------------------------------------------------------------------
    */

    showDeleteModal.value = false

    deletingWork.value = null


    /*
    |--------------------------------------------------------------------------
    | Refresh Table
    |--------------------------------------------------------------------------
    */

    refreshKey.value++

  } catch (error: any) {

    console.error(
      'Failed to delete work:',
      error
    )

    deleteError.value =
      error.response?.data?.message ||
      'Failed to delete Work.'

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

  loadWorkTags()

})

</script>