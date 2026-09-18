<template>
  <div class="overflow-x-auto bg-white shadow-md rounded-xl p-4">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold text-gray-800">Experience List</h2>
    </div>
    
    <table class="min-w-full text-left text-sm whitespace-nowrap">
      <thead class="border-b bg-gray-50/50">
        <tr>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">No</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">User</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Company (Work)</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Position</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Work Type</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Project</th>
          <th scope="col" class="px-6 py-4 font-semibold text-gray-600">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr 
          v-for="(item, index) in experiences" 
          :key="item.id_experience || index" 
          class="border-b hover:bg-gray-50/50 transition-colors"
        >
          <th scope="row" class="px-6 py-4 font-medium text-gray-900">{{ index + 1 }}</th>
          <td class="px-6 py-4 font-medium text-gray-900">{{ item.user?.username || '-' }}</td>
          <td class="px-6 py-4 font-medium text-gray-900">
            {{ item.work?.name || '-' }}
            <div class="text-xs text-gray-500">{{ item.work?.place || '' }}</div>
          </td>
          <td class="px-6 py-4 text-gray-600">{{ item.positionType?.name || '-' }}</td>
          <td class="px-6 py-4 text-gray-600">{{ item.workType?.name || '-' }}</td>
          <td class="px-6 py-4 text-gray-600">{{ item.project?.name || '-' }}</td>
          <td class="px-6 py-4 flex gap-3">
            <button class="bg-[#5660F9] text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium text-xs">
              Edit
            </button>
            <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors font-medium text-xs">
              Delete
            </button>
          </td>
        </tr>
        
        <!-- Baris saat data kosong -->
        <tr v-if="experiences.length === 0">
          <td colspan="7" class="text-center py-6 text-gray-500 font-medium">
            No experience available.
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
    experiences.value = response.data.data || response.data;
  } catch (error) {
    console.error("Failed to fetch experience data:", error);
  }
};

onMounted(() => {
  getExperiences();
});
</script>