<template>
  <div>

    <!-- ===================================================== -->
    <!-- FILTER -->
    <!-- ===================================================== -->

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
          placeholder="Search work tag..."
          class="h-11 w-full rounded-lg border border-gray-200 bg-white pl-11 pr-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />

      </div>

      <!-- Reset -->
      <div>

        <button
          type="button"
          class="h-11 rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]"
          @click="resetSearch"
        >
          ↻ Reset
        </button>

      </div>

    </div>


    <!-- ===================================================== -->
    <!-- TABLE -->
    <!-- ===================================================== -->

    <div class="overflow-x-auto">

      <table class="w-full min-w-[600px]">

        <!-- Header -->
        <thead>

          <tr
            class="border-b border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-white/[0.02]"
          >

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              #
            </th>

            <th
              class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500"
            >
              Work Tag
            </th>

            <th
              class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500"
            >
              Actions
            </th>

          </tr>

        </thead>


        <!-- Body -->
        <tbody>

          <!-- Loading -->
          <tr v-if="loading">

            <td
              colspan="3"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              Loading work tags...
            </td>

          </tr>


          <!-- Empty -->
          <tr v-else-if="filteredWorkTags.length === 0">

            <td
              colspan="3"
              class="px-6 py-12 text-center text-sm text-gray-500"
            >
              No work tag found.
            </td>

          </tr>


          <!-- Data -->
          <tr
            v-for="(workTag, index) in filteredWorkTags"
            v-else
            :key="workTag.id_work_tag"
            class="border-b border-gray-100 transition hover:bg-gray-50/70 dark:border-gray-800 dark:hover:bg-white/[0.02]"
          >

            <!-- Number -->
            <td
              class="px-6 py-5 text-sm font-medium text-gray-700 dark:text-gray-300"
            >
              {{ index + 1 }}
            </td>


            <!-- Name -->
            <td class="px-6 py-5">

              <p
                class="font-semibold text-gray-800 dark:text-white/90"
              >
                {{ workTag.name }}
              </p>

            </td>


            <!-- Actions -->
            <td class="px-6 py-5">

              <div class="flex justify-end gap-2">

                <!-- Edit -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Edit"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                  @click="emit('edit', workTag)"
                >
                  ✎
                </button>


                <!-- View -->
                <button
                  type="button"
                  title="View"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400"
                  @click="emit('view', workTag)"
                >
                  👁
                </button>


                <!-- Delete -->
                <button
                  v-if="isSuperAdmin()"
                  type="button"
                  title="Delete"
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                  @click="emit('delete', workTag)"
                >
                  🗑
                </button>

              </div>

            </td>

          </tr>

        </tbody>

      </table>

    </div>


    <!-- ===================================================== -->
    <!-- FOOTER -->
    <!-- ===================================================== -->

    <div
      class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800"
    >

      <p
        class="text-sm text-gray-500 dark:text-gray-400"
      >
        Showing {{ filteredWorkTags.length }}
        of {{ workTags.length }} results
      </p>


      <!-- Pagination placeholder -->
      <div class="flex items-center gap-2">

        <button
          type="button"
          disabled
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700"
        >
          ‹
        </button>

        <button
          type="button"
          class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white"
        >
          1
        </button>

        <button
          type="button"
          disabled
          class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-gray-400 dark:border-gray-700"
        >
          ›
        </button>

      </div>

    </div>

  </div>
</template>


<script setup lang="ts">

import {
  computed,
  onMounted,
  ref,
  watch,
} from 'vue'

import axios from '@/services/axios'

import { useAuth } from '@/composables/useAuth'


/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps<{
  refreshKey: number
}>()


/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
  (event: 'edit', workTag: WorkTag): void
  (event: 'view', workTag: WorkTag): void
  (event: 'delete', workTag: WorkTag): void
}>()


/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

const { isSuperAdmin } = useAuth()


/*
|--------------------------------------------------------------------------
| Interface
|--------------------------------------------------------------------------
*/

interface WorkTag {
  id_work_tag: number
  name: string
}


/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const workTags = ref<WorkTag[]>([])

const loading = ref(true)

const search = ref('')


/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

const filteredWorkTags = computed(() => {

  const keyword = search.value
    .toLowerCase()
    .trim()

  if (!keyword) {
    return workTags.value
  }

  return workTags.value.filter(
    (workTag) =>
      workTag.name
        .toLowerCase()
        .includes(keyword)
  )
})


/*
|--------------------------------------------------------------------------
| Fetch
|--------------------------------------------------------------------------
*/

const fetchWorkTags = async () => {

  loading.value = true

  try {

    const response = await axios.get(
      '/api/work-tags'
    )

    const data =
      response.data.data ??
      response.data

    workTags.value = data

    emit(
      'total-changed',
      workTags.value.length
    )

  } catch (error) {

    console.error(
      'Failed to fetch work tags:',
      error
    )

  } finally {

    loading.value = false

  }
}


/*
|--------------------------------------------------------------------------
| Reset
|--------------------------------------------------------------------------
*/

const resetSearch = () => {

  search.value = ''

}


/*
|--------------------------------------------------------------------------
| Refresh
|--------------------------------------------------------------------------
*/

watch(
  () => props.refreshKey,
  () => {
    fetchWorkTags()
  }
)


/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

  fetchWorkTags()

})

</script>