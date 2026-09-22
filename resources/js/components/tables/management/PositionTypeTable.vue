<template>
  <div
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="min-w-full">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                No
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Position Name
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Action
              </p>
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(position, index) in positionTypes"
            :key="position.id_position_type || index"
            class="border-t border-gray-100 dark:border-gray-800"
          >
            <td class="px-5 py-4 sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ index + 1 }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ position.name }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <div class="flex items-center gap-2">
                <Button size="sm" variant="warning"> Edit </Button>
                <Button size="sm" variant="danger"> Delete </Button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="positionTypes.length === 0">
            <td colspan="3" class="px-5 py-8 text-center sm:px-6 border-t border-gray-100 dark:border-gray-800">
              <p class="font-medium text-gray-500 text-theme-sm dark:text-gray-400">
                No position type available.
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Button from "@/components/ui/Button.vue";

const positionTypes = ref([]);

const getPositionTypes = async () => {
  try {
    const response = await axios.get("/api/position-types");
    positionTypes.value = response.data.data || response.data;
  } catch (error) {
    console.error("Failed to fetch position type data:", error);
  }
};

onMounted(() => {
  getPositionTypes();
});
</script>