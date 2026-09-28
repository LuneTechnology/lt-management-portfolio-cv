<template>
  <div class="overflow-x-auto">
    <table class="min-w-full text-left text-sm">
      <thead class="border-b border-gray-200 dark:border-gray-700">
        <tr>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Name</th>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Major</th>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Place</th>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">Start</th>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">End</th>
          <th class="px-4 py-3 font-medium text-gray-500 dark:text-gray-400">GPA</th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td colspan="7" class="px-4 py-6 text-center text-gray-400">Loading...</td>
        </tr>
        <tr v-else-if="educations.length === 0">
          <td colspan="7" class="px-4 py-6 text-center text-gray-400">No data found.</td>
        </tr>
        <tr
          v-for="edu in educations"
          :key="edu.id_education"
          class="border-b border-gray-100 dark:border-gray-800"
        >
          <td class="px-4 py-3">{{ edu.name }}</td>
          <td class="px-4 py-3">{{ edu.major }}</td>
          <td class="px-4 py-3">{{ edu.place }}</td>
          <td class="px-4 py-3">{{ formatDate(edu.start_date) }}</td>
          <td class="px-4 py-3">{{ formatDate(edu.end_date) }}</td>
          <td class="px-4 py-3">{{ edu.gpa ?? "-" }}</td>
          <!-- <td class="px-4 py-3 space-x-2">
            <button
              class="text-blue-500 hover:underline"
              @click="editEducation(edu)"
            >
              Edit
            </button>
            <button
              class="text-red-500 hover:underline"
              @click="deleteEducation(edu.id_education)"
            >
              Delete
            </button>
          </td> -->
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const educations = ref([]);
const loading = ref(true);

const fetchEducations = async () => {
  loading.value = true;
  try {
    const response = await axios.get("/api/educations");
    educations.value = response.data;
  } catch (error) {
    console.error("Failed to fetch educations:", error);
  } finally {
    loading.value = false;
  }
};

function formatDate(dateStr) {
  if (!dateStr) return "-";
  return dateStr.split("T")[0];
}

// const deleteEducation = async (id) => {
//   if (!confirm("Delete this education record?")) return;
//   try {
//     await axios.delete(`/api/educations/${id}`);
//     educations.value = educations.value.filter((e) => e.id_education !== id);
//   } catch (error) {
//     console.error("Failed to delete education:", error);
//   }
// };

// const editEducation = (edu) => {
//   // arahkan ke halaman edit, atau buka modal — sesuaikan dengan pola project Anda
//   console.log("Edit education:", edu);
// };

onMounted(fetchEducations);
</script>