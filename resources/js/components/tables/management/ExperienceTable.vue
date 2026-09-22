<template>
  <div
    class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="max-w-full overflow-x-auto custom-scrollbar">
      <table class="w-full table-auto">
        <thead>
          <tr class="border-b border-gray-200 dark:border-gray-700">
            <th class="w-16 px-5 py-3 text-center sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                No
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                User
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Company (Work)
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Position
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Work Type
              </p>
            </th>
            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Project
              </p>
            </th>
            <th class="w-44 px-5 py-3 text-center sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Action
              </p>
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="(experience, index) in experiences"
            :key="experience.id_experience || index"
            class="border-t border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors"
          >
            <td class="px-5 py-4 text-center sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ index + 1 }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ experience.user?.username || '-' }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ experience.work?.name || '-' }}
              </p>
              <p class="text-gray-500 text-theme-xs dark:text-gray-400 mt-0.5">
                {{ experience.work?.place || '' }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-700 text-theme-sm dark:text-gray-300">
                {{ experience.positionType?.name || experience.position_type?.name || '-' }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-700 text-theme-sm dark:text-gray-300">
                {{ experience.workType?.name || experience.work_type?.name || '-' }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-700 text-theme-sm dark:text-gray-300">
                {{ experience.project?.name || '-' }}
              </p>
            </td>

            <td class="px-5 py-4 text-center sm:px-6">
              <div class="flex items-center justify-center gap-2">
                <Button size="sm" variant="warning"> Edit </Button>
                <Button size="sm" variant="danger"> Delete </Button>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="experiences.length === 0">
            <td colspan="7" class="px-5 py-8 text-center sm:px-6 border-t border-gray-100 dark:border-gray-800">
              <p class="font-medium text-gray-500 text-theme-sm dark:text-gray-400">
                No experience available.
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