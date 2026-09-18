<template>
  <div class="overflow-x-auto bg-white shadow-md rounded-xl p-4">
   
    
    <table class="min-w-full text-left text-sm whitespace-nowrap">
      <thead class="border-b bg-gray-50/50">
        <tr>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">No</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Company Name</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Location</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="(item, index) in works" 
          :key="item.id_work || index" 
          class="border-b hover:bg-gray-50/50 transition-colors"
        >
          <th scope="row" class="px-6 py-4 font-medium text-gray-900">{{ index + 1 }}</th>
          <td class="px-6 py-4 font-medium text-gray-900">{{ item.name || '-' }}</td>
          <td class="px-6 py-4 text-gray-600">{{ item.place || '-' }}</td>
          <td class="px-6 py-4 flex gap-3">
            <button class="bg-[#5660F9] text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs">
              Edit
            </button>
            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors font-medium text-xs">
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="works.length === 0">
          <td colspan="4" class="text-center py-4 text-gray-500">No company data available.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const works = ref([]);

const getWorks = async () => {
  try {
    const response = await axios.get("/api/works");
    works.value = response.data.data || response.data;
  } catch (error) {
    console.error("Failed to fetch work data:", error);
  }
};

onMounted(() => {
  getWorks();
});
</script>