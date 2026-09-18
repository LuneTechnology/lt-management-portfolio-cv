<template>
  <div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm whitespace-nowrap">
      <thead class="border-b bg-gray-50/50">
        <tr>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">No</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">User</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Perusahaan (Work)</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Jabatan (Position)</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Tipe Kerja</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Proyek</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in experiences" :key="item.id_experience || index" class="border-b hover:bg-gray-50/50">
          <th scope="row" class="px-6 py-4 font-medium text-gray-900">{{ index + 1 }}</th>
          <td class="px-6 py-4 font-medium text-gray-900">{{ item.user?.username || '-' }}</td>
          
          <!-- Menampilkan Nama Perusahaan beserta Lokasinya (place) -->
          <td class="px-6 py-4 text-gray-600">
            <div class="font-medium text-gray-900">{{ item.work?.name || '-' }}</div>
            <div v-if="item.work?.place" class="text-xs text-gray-500">{{ item.work?.place }}</div>
          </td>

          <td class="px-6 py-4 text-gray-600">{{ item.position_type?.name || '-' }}</td>
          <td class="px-6 py-4 text-gray-600">{{ item.work_type?.name || '-' }}</td>
          
          <!-- Fallback jika kolom proyek menggunakan nama 'title' atau 'name' -->
          <td class="px-6 py-4 text-gray-600">
            {{ item.project?.name || item.project?.title || '-' }}
          </td>

          <td class="px-6 py-4 flex gap-3">
            <button class="bg-[#5660F9] text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
              Edit
            </button>
            <button class="bg-red-500 text-white px-5 py-2 rounded-lg hover:bg-red-600 transition-colors font-medium">
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const experiences = ref([]);

const getExperiences = async () => {
  try {
    const response = await axios.get("/api/experiences");
    // Mendukung penanganan jika API dibungkus oleh Eloquent API Resource (response.data.data) atau Array langsung
    experiences.value = response.data.data || response.data;
  } catch (error) {
    console.error("Gagal mengambil data experience:", error);
  }
};

onMounted(() => {
  getExperiences();
});
</script>