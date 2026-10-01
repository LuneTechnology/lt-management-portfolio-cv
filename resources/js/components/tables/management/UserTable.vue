<template>
  <div>
    <!-- ====================================================== -->
    <!-- FILTER -->
    <!-- ====================================================== -->

    <div
      class="flex flex-col gap-3 border-b border-gray-200 px-6 py-5 dark:border-gray-800 xl:flex-row xl:items-center xl:justify-between"
    >
      <!-- Search -->
      <div class="relative w-full xl:max-w-md">
        <span
          class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"
        >
          🔍
        </span>

        <input
          v-model="search"
          type="text"
          placeholder="Search user..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />
      </div>

      <!-- Filter -->
      <div class="flex flex-wrap gap-3">
        <select
          v-model="selectedRole"
          class="h-11 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
        >
          <option value="">
            All Roles
          </option>

          <option
            v-for="role in roles"
            :key="role.id_role"
            :value="String(role.id_role)"
          >
            {{ role.name }}
          </option>
        </select>

        <button
          type="button"
          @click="resetFilters"
          class="h-11 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
        >
          ↻ Reset
        </button>
      </div>
    </div>

    <!-- ====================================================== -->
    <!-- TABLE -->
    <!-- ====================================================== -->

    <div class="overflow-x-auto">
      <table class="min-w-[950px] w-full">
        <thead>
          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >
            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              #
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              User
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              Email
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              Contact
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              Role
            </th>

            <th
              class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-500"
            >
              Actions
            </th>
          </tr>
        </thead>

        <tbody>
          <!-- Loading -->
          <tr v-if="loadingUsers">
            <td
              colspan="6"
              class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              Loading users...
            </td>
          </tr>

          <!-- Empty -->
          <tr v-else-if="filteredUsers.length === 0">
            <td
              colspan="6"
              class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
            >
              No users found.
            </td>
          </tr>

          <!-- Data -->
          <tr
            v-for="(user, index) in filteredUsers"
            v-else
            :key="user.id_user"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >
            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-500 dark:text-gray-400"
            >
              {{ index + 1 }}
            </td>

            <!-- User -->
            <td class="px-6 py-5">
              <div class="flex items-center gap-3">
                <img
                  :src="getPhotoUrl(user.photo)"
                  :alt="user.username || 'User'"
                  class="h-11 w-11 rounded-full border border-gray-200 object-cover dark:border-gray-700"
                  @error="handleImageError"
                />

                <div class="min-w-0">
                  <p
                    class="truncate font-semibold text-gray-800 dark:text-white/90"
                  >
                    {{ user.username || 'Unnamed User' }}
                  </p>

                  <p
                    class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                  >
                    ID #{{ user.id_user }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Email -->
            <td class="px-6 py-5">
              <span
                class="text-sm text-gray-700 dark:text-gray-300"
              >
                {{ user.email }}
              </span>
            </td>

            <!-- Contact -->
            <td class="px-6 py-5">
              <span
                class="text-sm text-gray-600 dark:text-gray-400"
              >
                {{ user.contact || '-' }}
              </span>
            </td>

            <!-- Role -->
            <td class="px-6 py-5">
              <span
                class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold"
                :class="getRoleClass(user.role?.name)"
              >
                {{ user.role?.name || '-' }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-6 py-5">
              <div class="flex justify-end gap-2">
                <!-- Edit -->
                <button
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="openEditModal(user)"
                >
                  ✎
                </button>

                <!-- Delete -->
                <button
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="openDeleteModal(user)"
                >
                  🗑
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ====================================================== -->
    <!-- TABLE FOOTER -->
    <!-- ====================================================== -->

    <div
      class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800"
    >
      <p
        class="text-sm text-gray-500 dark:text-gray-400"
      >
        Showing
        {{ filteredUsers.length }}
        of
        {{ users.length }}
        users
      </p>

      <div
        class="flex h-9 items-center rounded-lg bg-gray-50 px-3 text-sm font-medium text-gray-600 dark:bg-white/[0.03] dark:text-gray-400"
      >
        Total {{ users.length }}
      </div>
    </div>

    <!-- ====================================================== -->
    <!-- CREATE / EDIT MODAL -->
    <!-- ====================================================== -->

    <Teleport to="body">
      <div
        v-if="showModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4 py-6"
        @click.self="closeModal"
      >
        <div
          class="max-h-[90vh] w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
        >
          <!-- Header -->
          <div
            class="flex items-center justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-800"
          >
            <div>
              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white"
              >
                {{ isEdit ? 'Edit User' : 'Add User' }}
              </h3>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                {{
                  isEdit
                    ? 'Update user account information.'
                    : 'Create a new user account.'
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
            @submit.prevent="submitForm"
            class="max-h-[calc(90vh-90px)] space-y-5 overflow-y-auto px-6 py-6"
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
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

              <span
                v-if="errors.email"
                class="mt-1 block text-xs text-error-500"
              >
                {{ errors.email[0] }}
              </span>
            </div>

            <!-- Password -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                Password
              </label>

              <input
                v-model="form.password"
                type="password"
                :required="!isEdit"
                :placeholder="
                  isEdit
                    ? 'Leave empty to keep current password'
                    : 'Enter password'
                "
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />

              <span
                v-if="errors.password"
                class="mt-1 block text-xs text-error-500"
              >
                {{ errors.password[0] }}
              </span>
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
                placeholder="Enter username"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
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
                  alt="Preview"
                  class="h-16 w-16 rounded-full border border-gray-200 object-cover dark:border-gray-700"
                  @error="handleImageError"
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
                placeholder="Enter contact"
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
              />
            </div>

            <!-- About Me -->
            <div>
              <label
                class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
              >
                About Me
              </label>

              <textarea
                v-model="form.aboutme"
                rows="4"
                placeholder="Short description about the user"
                class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
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
                class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
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

              <span
                v-if="errors.id_role"
                class="mt-1 block text-xs text-error-500"
              >
                {{ errors.id_role[0] }}
              </span>
            </div>

            <!-- Footer -->
            <div
              class="flex justify-end gap-3 border-t border-gray-200 pt-5 dark:border-gray-800"
            >
              <button
                type="button"
                :disabled="loading"
                @click="closeModal"
                class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
              >
                Cancel
              </button>

              <button
                type="submit"
                :disabled="loading"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
              >
                {{
                  loading
                    ? 'Saving...'
                    : isEdit
                      ? 'Update User'
                      : 'Save User'
                }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ====================================================== -->
    <!-- DELETE MODAL -->
    <!-- ====================================================== -->

    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 px-4"
        @click.self="closeDeleteModal"
      >
        <div
          class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
        >
          <div class="px-6 pt-6">
            <!-- Icon -->
            <div
              class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-7 w-7 text-red-500"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 9v4m0 4h.01M10.29 3.86 2.82 17a2 2 0 0 0 1.74 3h14.88a2 2 0 0 0 1.74-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
                />
              </svg>
            </div>

            <!-- Title -->
            <div class="mt-5 text-center">
              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white"
              >
                Delete User?
              </h3>

              <p
                class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400"
              >
                Are you sure you want to delete this user?
                This action cannot be undone.
              </p>
            </div>

            <!-- User -->
            <div
              v-if="selectedUser"
              class="mt-5 flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]"
            >
              <img
                :src="getPhotoUrl(selectedUser.photo)"
                :alt="selectedUser.username || 'User'"
                class="h-11 w-11 rounded-full object-cover"
                @error="handleImageError"
              />

              <div>
                <p
                  class="text-sm font-semibold text-gray-800 dark:text-white"
                >
                  {{
                    selectedUser.username ||
                    'Unnamed User'
                  }}
                </p>

                <p
                  class="mt-0.5 text-xs text-gray-500 dark:text-gray-400"
                >
                  {{ selectedUser.email }}
                </p>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div
            class="mt-6 flex gap-3 border-t border-gray-200 px-6 py-5 dark:border-gray-800"
          >
            <button
              type="button"
              :disabled="deleting"
              @click="closeDeleteModal"
              class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="deleting"
              @click="confirmDelete"
              class="flex-1 rounded-lg bg-error-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-error-600 disabled:cursor-not-allowed disabled:opacity-50"
            >
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import {
  computed,
  onMounted,
  reactive,
  ref,
} from 'vue'

import axios from 'axios'


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const users = ref([])
const roles = ref([])

const loadingUsers = ref(false)
const loading = ref(false)
const deleting = ref(false)

const showModal = ref(false)
const showDeleteModal = ref(false)

const isEdit = ref(false)

const editingId = ref(null)

const selectedUser = ref(null)

const errors = ref({})

const photoFile = ref(null)
const photoPreview = ref(null)

const search = ref('')
const selectedRole = ref('')
const emit = defineEmits([
  'total-changed',
  'edit',
  'view',
])

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = reactive({
  email: '',
  password: '',
  username: '',
  photo: '',
  contact: '',
  aboutme: '',
  id_role: '',
})


/*
|--------------------------------------------------------------------------
| Filtered Users
|--------------------------------------------------------------------------
*/

const filteredUsers = computed(() => {
  let result = [...users.value]

  const keyword =
    search.value.trim().toLowerCase()

  if (keyword) {
    result = result.filter((user) => {
      const username =
        user.username?.toLowerCase() || ''

      const email =
        user.email?.toLowerCase() || ''

      const contact =
        user.contact?.toLowerCase() || ''

      const role =
        user.role?.name?.toLowerCase() || ''

      return (
        username.includes(keyword) ||
        email.includes(keyword) ||
        contact.includes(keyword) ||
        role.includes(keyword)
      )
    })
  }

  if (selectedRole.value) {
    result = result.filter(
      (user) =>
        String(user.id_role) ===
        String(selectedRole.value)
    )
  }

  return result
})


/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

const resetForm = () => {
  form.email = ''
  form.password = ''
  form.username = ''
  form.photo = ''
  form.contact = ''
  form.aboutme = ''
  form.id_role = ''

  photoFile.value = null
  photoPreview.value = null

  errors.value = {}
}


/*
|--------------------------------------------------------------------------
| Photo URL
|--------------------------------------------------------------------------
*/

const getPhotoUrl = (photo) => {
  if (!photo) {
    return '/images/default-avatar.png'
  }

  const baseUrl =
    import.meta.env.VITE_API_URL ||
    ''

  return `${baseUrl}/storage/${photo}`
}


/*
|--------------------------------------------------------------------------
| Image Error
|--------------------------------------------------------------------------
*/

const handleImageError = (event) => {
  event.target.src =
    '/images/default-avatar.png'
}


/*
|--------------------------------------------------------------------------
| Photo Change
|--------------------------------------------------------------------------
*/

const handlePhotoChange = (event) => {
  const file =
    event.target.files?.[0]

  if (!file) {
    return
  }

  photoFile.value = file

  photoPreview.value =
    URL.createObjectURL(file)
}


/*
|--------------------------------------------------------------------------
| Fetch Users
|--------------------------------------------------------------------------
*/

const fetchUsers = async () => {
  loading.value = true

  try {
    const response = await axios.get('/api/users')

    users.value = Array.isArray(response.data)
      ? response.data
      : response.data?.data ?? []

    emit('total-changed', users.value.length)

  } catch (error) {
    console.error('Failed to fetch users:', error)

    users.value = []

    emit('total-changed', 0)

  } finally {
    loading.value = false
  }
}


/*
|--------------------------------------------------------------------------
| Fetch Roles
|--------------------------------------------------------------------------
*/

const fetchRoles = async () => {
  try {
    const response =
      await axios.get('/api/roles')

    roles.value =
      Array.isArray(response.data)
        ? response.data
        : response.data?.data || []
  } catch (error) {
    console.error(
      'Gagal mengambil role:',
      error
    )

    roles.value = []
  }
}


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

const openCreateModal = () => {
  resetForm()

  isEdit.value = false
  editingId.value = null

  showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEditModal = (user) => {
  resetForm()

  isEdit.value = true

  editingId.value =
    user.id_user

  form.email =
    user.email || ''

  form.username =
    user.username || ''

  form.photo =
    user.photo || ''

  form.contact =
    user.contact || ''

  form.aboutme =
    user.aboutme || ''

  form.id_role =
    user.id_role
      ? String(user.id_role)
      : ''

  form.password = ''

  photoPreview.value = null

  showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Close Form Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {
  if (loading.value) {
    return
  }

  showModal.value = false
}


/*
|--------------------------------------------------------------------------
| Submit Form
|--------------------------------------------------------------------------
*/

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  const formData =
    new FormData()

  formData.append(
    'email',
    form.email
  )

  formData.append(
    'username',
    form.username || ''
  )

  formData.append(
    'contact',
    form.contact || ''
  )

  formData.append(
    'aboutme',
    form.aboutme || ''
  )

  formData.append(
    'id_role',
    form.id_role || ''
  )

  if (form.password) {
    formData.append(
      'password',
      form.password
    )
  }

  if (photoFile.value) {
    formData.append(
      'photo',
      photoFile.value
    )
  }

  try {
    if (isEdit.value) {
      formData.append(
        '_method',
        'PUT'
      )

      await axios.post(
        `/api/users/${editingId.value}`,
        formData,
        {
          headers: {
            'Content-Type':
              'multipart/form-data',
          },
        }
      )
    } else {
      await axios.post(
        '/api/users',
        formData,
        {
          headers: {
            'Content-Type':
              'multipart/form-data',
          },
        }
      )
    }

    await fetchUsers()

    closeModal()
    resetForm()
  } catch (error) {
    if (
      error.response?.status === 422
    ) {
      errors.value =
        error.response.data.errors || {}
    } else {
      console.error(
        'Gagal menyimpan user:',
        error
      )
    }
  } finally {
    loading.value = false
  }
}


/*
|--------------------------------------------------------------------------
| Delete Modal
|--------------------------------------------------------------------------
*/

const openDeleteModal = (user) => {
  selectedUser.value = user

  showDeleteModal.value = true
}


const closeDeleteModal = () => {
  if (deleting.value) {
    return
  }

  showDeleteModal.value = false

  selectedUser.value = null
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const confirmDelete = async () => {
  if (!selectedUser.value) {
    return
  }

  deleting.value = true

  try {
    await axios.delete(
      `/api/users/${selectedUser.value.id_user}`
    )

    users.value =
      users.value.filter(
        (user) =>
          user.id_user !==
          selectedUser.value.id_user
      )

    closeDeleteModal()
  } catch (error) {
    console.error(
      'Gagal menghapus user:',
      error
    )
  } finally {
    deleting.value = false
  }
}


/*
|--------------------------------------------------------------------------
| Role Badge
|--------------------------------------------------------------------------
*/

const getRoleClass = (role) => {
  switch (
    role?.toLowerCase()
  ) {
    case 'super admin':
      return 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400'

    case 'admin':
      return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'

    default:
      return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
  }
}


/*
|--------------------------------------------------------------------------
| Reset Filters
|--------------------------------------------------------------------------
*/

const resetFilters = () => {
  search.value = ''
  selectedRole.value = ''
}


/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>