<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Users"
        :value="totalUsers"
        :icon="UserCircleIcon"
        quote="Manage administrator accounts and their access."
        :change="1"
        color="purple"
      />

      <!-- User Table -->
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
                  User List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  View, add, edit, or delete administrator accounts.
                </p>
              </div>

            </div>

            <!-- Add -->
            <button
              type="button"
              class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
              @click="openCreateModal"
            >
              <span class="text-lg leading-none">+</span>
              Add User
            </button>

          </div>
        </div>

        <!-- Table -->
        <UserTable
          :refresh-key="refreshKey"
          @total-changed="totalUsers = $event"
          @edit="openEditModal"
          @view="openViewModal"
        />

      </div>

    </div>


    <!-- ====================================================== -->
    <!-- USER MODAL -->
    <!-- ====================================================== -->

    <Teleport to="body">

      <div
        v-if="showModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4 py-6"
        @click.self="closeModal"
      >

        <div
          class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900"
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
                    ? 'Add User'
                    : modalMode === 'edit'
                      ? 'Edit User'
                      : 'User Detail'
                }}
              </h3>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                {{
                  modalMode === 'create'
                    ? 'Create a new administrator account.'
                    : modalMode === 'edit'
                      ? 'Update administrator account information.'
                      : 'View administrator account information.'
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
            @submit.prevent="submitUser"
            enctype="multipart/form-data"
          >

            <!-- Email -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Email
              </label>

              <input
                v-model="form.email"
                type="email"
                required
                :disabled="isViewMode || saving"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />

              <p
                v-if="errors.email"
                class="mt-1 text-xs text-error-500"
              >
                {{ errors.email[0] }}
              </p>
            </div>


            <!-- Password -->
            <div v-if="!isViewMode">
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Password
              </label>

              <input
                v-model="form.password"
                type="password"
                :required="modalMode === 'create'"
                :placeholder="
                  modalMode === 'edit'
                    ? 'Leave empty to keep current password'
                    : 'Enter password'
                "
                :disabled="saving"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />

              <p
                v-if="errors.password"
                class="mt-1 text-xs text-error-500"
              >
                {{ errors.password[0] }}
              </p>
            </div>


            <!-- Username -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Username
              </label>

              <input
                v-model="form.username"
                type="text"
                maxlength="50"
                :disabled="isViewMode || saving"
                placeholder="Enter username"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />
            </div>


            <!-- Photo -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Profile Photo
              </label>

              <div class="mb-3 flex items-center gap-4">

                <img
                  :src="
                    photoPreview ||
                    getPhotoUrl(form.photo)
                  "
                  alt="Profile Preview"
                  class="h-16 w-16 rounded-full border border-gray-200 object-cover dark:border-gray-700"
                />

                <div>
                  <p
                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                  >
                    Profile image
                  </p>

                  <p
                    class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                  >
                    PNG, JPG or JPEG
                  </p>
                </div>

              </div>

              <input
                v-if="!isViewMode"
                type="file"
                accept="image/png, image/jpeg, image/jpg"
                @change="handlePhotoChange"
                class="block w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"
              />
            </div>


            <!-- Contact -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Contact
              </label>

              <input
                v-model="form.contact"
                type="text"
                :disabled="isViewMode || saving"
                placeholder="Enter contact"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              />
            </div>


            <!-- About -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                About Me
              </label>

              <textarea
                v-model="form.aboutme"
                rows="4"
                :disabled="isViewMode || saving"
                placeholder="Short description about the user"
                class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              ></textarea>
            </div>


            <!-- Role -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Role
              </label>

              <select
                v-model="form.id_role"
                required
                :disabled="isViewMode || saving"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:disabled:bg-gray-800"
              >
                <option value="">
                  Select role
                </option>

                <option
                  v-for="role in roles"
                  :key="role.id_role"
                  :value="role.id_role"
                >
                  {{ role.name }}
                </option>
              </select>

              <p
                v-if="errors.id_role"
                class="mt-1 text-xs text-error-500"
              >
                {{ errors.id_role[0] }}
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
                {{ saving ? 'Saving...' : 'Save User' }}
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
import UserTable from '@/components/tables/management/UserTable.vue'

import {
  UserCircleIcon,
} from '@/icons'


/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Role {
  id_role: number
  name: string
}

interface User {
  id_user: number
  email: string
  username: string | null
  photo: string | null
  contact: string | null
  aboutme: string | null
  id_role: number
  role?: Role
}

interface UserForm {
  email: string
  password: string
  username: string
  photo: string
  contact: string
  aboutme: string
  id_role: number | ''
}


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

const currentPageTitle =
  ref('User')

const totalUsers =
  ref(0)

const refreshKey =
  ref(0)


/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/

const showModal =
  ref(false)

const modalMode =
  ref<
    'create' |
    'edit' |
    'view'
  >('create')

const selectedId =
  ref<number | null>(null)


/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const saving =
  ref(false)


/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

const roles =
  ref<Role[]>([])


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form =
  ref<UserForm>({
    email: '',
    password: '',
    username: '',
    photo: '',
    contact: '',
    aboutme: '',
    id_role: '',
  })


const errors =
  ref<Record<string, string[]>>({})


/*
|--------------------------------------------------------------------------
| Photo
|--------------------------------------------------------------------------
*/

const photoFile =
  ref<File | null>(null)

const photoPreview =
  ref<string | null>(null)


/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const isViewMode =
  computed(() => {
    return modalMode.value === 'view'
  })


/*
|--------------------------------------------------------------------------
| Fetch Roles
|--------------------------------------------------------------------------
*/

const fetchRoles =
  async () => {

    try {

      const response =
        await axios.get(
          '/api/roles'
        )

      roles.value =
        Array.isArray(
          response.data
        )
          ? response.data
          : response.data?.data ?? []

    } catch (error) {

      console.error(
        'Failed to fetch roles:',
        error
      )

      roles.value = []

    }

  }


/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

const resetForm =
  () => {

    form.value = {
      email: '',
      password: '',
      username: '',
      photo: '',
      contact: '',
      aboutme: '',
      id_role: '',
    }

    selectedId.value =
      null

    photoFile.value =
      null

    photoPreview.value =
      null

    errors.value = {}

  }


/*
|--------------------------------------------------------------------------
| Open Create
|--------------------------------------------------------------------------
*/

const openCreateModal =
  async () => {

    resetForm()

    modalMode.value =
      'create'

    if (
      roles.value.length === 0
    ) {
      await fetchRoles()
    }

    showModal.value =
      true

  }


/*
|--------------------------------------------------------------------------
| Open Edit
|--------------------------------------------------------------------------
*/

const openEditModal =
  async (
    user: User
  ) => {

    resetForm()

    modalMode.value =
      'edit'

    selectedId.value =
      user.id_user

    form.value = {
      email:
        user.email ?? '',

      password: '',

      username:
        user.username ?? '',

      photo:
        user.photo ?? '',

      contact:
        user.contact ?? '',

      aboutme:
        user.aboutme ?? '',

      id_role:
        user.id_role ?? '',
    }

    if (
      roles.value.length === 0
    ) {
      await fetchRoles()
    }

    showModal.value =
      true

  }


/*
|--------------------------------------------------------------------------
| Open View
|--------------------------------------------------------------------------
*/

const openViewModal =
  (
    user: User
  ) => {

    resetForm()

    modalMode.value =
      'view'

    selectedId.value =
      user.id_user

    form.value = {
      email:
        user.email ?? '',

      password: '',

      username:
        user.username ?? '',

      photo:
        user.photo ?? '',

      contact:
        user.contact ?? '',

      aboutme:
        user.aboutme ?? '',

      id_role:
        user.id_role ?? '',
    }

    showModal.value =
      true

  }


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal =
  () => {

    if (saving.value) {
      return
    }

    showModal.value =
      false

    resetForm()

  }


/*
|--------------------------------------------------------------------------
| Photo Change
|--------------------------------------------------------------------------
*/

const handlePhotoChange =
  (
    event: Event
  ) => {

    const target =
      event.target as HTMLInputElement

    const file =
      target.files?.[0]

    if (!file) {
      return
    }

    photoFile.value =
      file

    photoPreview.value =
      URL.createObjectURL(file)

  }


/*
|--------------------------------------------------------------------------
| Photo URL
|--------------------------------------------------------------------------
*/

const getPhotoUrl =
  (
    photo: string
      | null
      | undefined
  ) => {

    if (!photo) {
      return '/images/default-avatar.png'
    }

    return `${
      import.meta.env.VITE_API_URL || ''
    }/storage/${photo}`

  }


/*
|--------------------------------------------------------------------------
| Submit User
|--------------------------------------------------------------------------
*/

const submitUser =
  async () => {

    if (
      modalMode.value ===
      'view'
    ) {
      return
    }

    saving.value =
      true

    errors.value = {}

    const formData =
      new FormData()

    formData.append(
      'email',
      form.value.email
    )

    formData.append(
      'username',
      form.value.username
    )

    formData.append(
      'contact',
      form.value.contact
    )

    formData.append(
      'aboutme',
      form.value.aboutme
    )

    formData.append(
      'id_role',
      String(form.value.id_role)
    )

    if (
      form.value.password
    ) {
      formData.append(
        'password',
        form.value.password
      )
    }

    if (
      photoFile.value
    ) {
      formData.append(
        'photo',
        photoFile.value
      )
    }


    try {

      /*
      |--------------------------------------------------------------------------
      | Create
      |--------------------------------------------------------------------------
      */

      if (
        modalMode.value ===
        'create'
      ) {

        await axios.post(
          '/api/users',
          formData
        )

      }


      /*
      |--------------------------------------------------------------------------
      | Update
      |--------------------------------------------------------------------------
      */

      if (
        modalMode.value ===
          'edit' &&
        selectedId.value
      ) {

        formData.append(
          '_method',
          'PUT'
        )

        await axios.post(
          `/api/users/${selectedId.value}`,
          formData
        )

      }


      /*
      |--------------------------------------------------------------------------
      | Success
      |--------------------------------------------------------------------------
      */

      showModal.value =
        false

      resetForm()

      refreshKey.value++

    } catch (
      error: any
    ) {

      if (
        error.response?.status ===
        422
      ) {

        errors.value =
          error.response.data.errors ??
          {}

      } else {

        console.error(
          'Failed to save user:',
          error
        )

      }

    } finally {

      saving.value =
        false

    }

  }


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchRoles()
})

</script>