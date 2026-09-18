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
                Experience
              </p>
            </th>

            <th class="px-5 py-3 text-left sm:px-6">
              <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                Stack
              </p>
            </th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="experienceStack in experienceStacks"
            :key="`${experienceStack.id_experience}-${experienceStack.id_stack}`"
            class="border-t border-gray-100 dark:border-gray-800"
          >
            <td class="px-5 py-4 sm:px-6">
              <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                {{ experienceStack.experience.project?.name }}
              </p>
            </td>

            <td class="px-5 py-4 sm:px-6">
              <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                {{ experienceStack.stack?.nama }}
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

const experienceStacks = ref([]);

const getExperienceStacks = async () => {
  const response = await axios.get("/api/experience-stacks");

  experienceStacks.value = response.data;
};

onMounted(() => {
  getExperienceStacks();
});
</script>