<template>
  <div>
    <!-- Tombol tambah -->
    <div class="flex justify-end mb-4">
      <button
        @click="openCreateModal"
        class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
      >
        + Tambah User
      </button>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-gray-200">
            <th class="py-3 px-4">Foto</th>
            <th class="py-3 px-4">Email</th>
            <th class="py-3 px-4">Username</th>
            <th class="py-3 px-4">Contact</th>
            <th class="py-3 px-4">Role</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id_user" class="border-b border-gray-100">
            <td class="py-3 px-4">
            <img
            :src="getPhotoUrl(user.photo)"
            alt="Foto profil"
            class="w-10 h-10 rounded-full object-cover"
            />
            </td>
            <td class="py-3 px-4">{{ user.email }}</td>
            <td class="py-3 px-4">{{ user.username ?? '-' }}</td>
            <td class="py-3 px-4">{{ user.contact ?? '-' }}</td>
            <td class="py-3 px-4">{{ user.role?.name ?? '-' }}</td>
            <td class="py-3 px-4 space-x-2">
              <!-- <button @click="openEditModal(user)" class="text-blue-500 hover:underline">
                Edit
              </button>
              <button @click="confirmDelete(user.id_user)" class="text-red-500 hover:underline">
                Hapus
              </button> -->
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="6" class="py-6 text-center text-gray-400">
              Belum ada data user.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

 <!-- Modal Form -->
<div
  v-if="showModal"
  class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
>
   <div
    class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh]"
  >

    <!-- Judul -->
    <h3 class="text-lg font-semibold mb-1">
      {{ isEdit ? "Edit User" : "Tambah User" }}
    </h3>

    <!-- Form -->
    <form
      @submit.prevent="submitForm"
      class="space-y-4 overflow-y-auto max-h-[75vh] pr-2"
      enctype="multipart/form-data"
    >

      <div>
        <label class="block text-sm mb-1">
          Email
        </label>

        <input
          v-model="form.email"
          type="email"
          required
          class="w-full border rounded-lg px-3 py-2"
        />

        <span
          v-if="errors.email"
          class="text-red-500 text-sm"
        >
          {{ errors.email[0] }}
        </span>
      </div>

      <div>
        <label class="block text-sm mb-1">
          Password {{ isEdit ? "(kosongkan jika tidak diubah)" : "" }}
        </label>

        <input
          v-model="form.password"
          type="password"
          :required="!isEdit"
          class="w-full border rounded-lg px-3 py-2"
        />

        <span
          v-if="errors.password"
          class="text-red-500 text-sm"
        >
          {{ errors.password[0] }}
        </span>
      </div>

      <div>
        <label class="block text-sm mb-1">
          Username
        </label>

        <input
          v-model="form.username"
          type="text"
          class="w-full border rounded-lg px-3 py-2"
        />
      </div>

      <div>
        <label class="block text-sm mb-1">
          Foto Profil
        </label>

        <div class="mb-2">
          <img
            :src="photoPreview || getPhotoUrl(form.photo)"
            alt="Preview"
            class="w-20 h-20 rounded-full object-cover border"
          />
        </div>

        <input
          type="file"
          accept="image/png, image/jpeg, image/jpg"
          @change="handlePhotoChange"
          class="w-full border rounded-lg px-3 py-2"
        />
      </div>

      <div>
        <label class="block text-sm mb-1">
          Contact
        </label>

        <input
          v-model="form.contact"
          type="text"
          class="w-full border rounded-lg px-3 py-2"
        />
      </div>

      <div>
        <label class="block text-sm mb-1">
          About Me
        </label>

        <textarea
          v-model="form.aboutme"
          class="w-full border rounded-lg px-3 py-2"
        ></textarea>
      </div>

      <div>
        <label class="block text-sm mb-1">
          Role
        </label>

        <select
          v-model="form.id_role"
          class="w-full border rounded-lg px-3 py-2"
        >
          <option value="">
            -- Pilih Role --
          </option>

          <option
            v-for="role in roles"
            :key="role.id_role"
            :value="role.id_role"
          >
            {{ role.name }}
          </option>
        </select>
      </div>

      <!-- Tombol -->
      <div class="flex justify-end gap-2 pt-2 pb-2">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 border rounded-lg"
        >
          Batal
        </button>

        <button
          type="submit"
          :disabled="loading"
          class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
        >
          {{ loading ? "Menyimpan..." : "Simpan" }}
        </button>
      </div>
    </form>
  </div>
</div>
</div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import axios from "axios";

const users = ref([]);
const roles = ref([]);
const showModal = ref(false);
const isEdit = ref(false);
const loading = ref(false);
const errors = ref({});
const editingId = ref(null);
const photoFile = ref(null);
const photoPreview = ref(null);

const form = reactive({
  email: "",
  password: "",
  username: "",
  contact: "",
  aboutme: "",
  id_role: "",
});

const resetForm = () => {
  form.email = "";
  form.password = "";
  form.username = "";
  form.contact = "";
  form.aboutme = "";
  form.id_role = "";
  form.photo = "";
  photoFile.value = null;
  photoPreview.value = null;
  errors.value = {};
};

const getPhotoUrl = (photos) => {
  if (!photos) {
    return "/images/default-avatar.png";
  }
  return `${import.meta.env.VITE_API_URL}/storage/${photos}`;
};

const handlePhotoChange = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  photoFile.value = file;
  photoPreview.value = URL.createObjectURL(file);
};

const fetchUsers = async () => {
  const response = await axios.get("/api/users");
  users.value = response.data;
};

const fetchRoles = async () => {
  const response = await axios.get("/api/roles");
  roles.value = response.data;
};

const openCreateModal = () => {
  resetForm();
  isEdit.value = false;
  editingId.value = null;
  showModal.value = true;
};

const openEditModal = (user) => {
  resetForm();
  isEdit.value = true;
  editingId.value = user.id_user;
  Object.assign(form, user);
  form.password = ""; // jangan tampilkan password lama
  photoPreview.value = null; // reset preview, fallback ke foto lama dari server
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const submitForm = async () => {
  loading.value = true;
  errors.value = {};

  const formData = new FormData();
  formData.append("email", form.email);
  formData.append("username", form.username || "");
  formData.append("contact", form.contact || "");
  formData.append("aboutme", form.aboutme || "");
  formData.append("id_role", form.id_role || "");

  if (form.password) {
    formData.append("password", form.password);
  }

  if (photoFile.value) {
    formData.append("photo", photoFile.value);
  }

  try {
    if (isEdit.value) {
      formData.append("_method", "PUT");
      await axios.post(`/api/users/${editingId.value}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
    } else {
      await axios.post("/api/users", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });
    }
    await fetchUsers();
    closeModal();
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error("Gagal menyimpan user:", error);
    }
  } finally {
    loading.value = false;
  }
};

const confirmDelete = async (id) => {
  if (!confirm("Yakin ingin menghapus user ini?")) return;

  try {
    await axios.delete(`/api/users/${id}`);
    await fetchUsers();
  } catch (error) {
    console.error("Gagal menghapus user:", error);
  }
};

onMounted(() => {
  fetchUsers();
  fetchRoles();
});
</script>