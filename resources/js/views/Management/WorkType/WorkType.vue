<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Work Types"
        :value="totalWorkTypes"
        :icon="DocsIcon"
        quote="Every role has its own way of working."
        :change="1"
        color="blue"
      />

      <!-- Work Type List -->
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <!-- Header -->
        <div
          class="border-b border-gray-200 px-6 py-5 dark:border-gray-800"
        >
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >
            <!-- Title -->
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10"
              >
                <DocsIcon
                  class="h-5 w-5 text-blue-500"
                />
              </div>

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Work Type List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  View, add, edit, or delete work types.
                </p>
              </div>
            </div>

            <!-- Add Button -->
            <button
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="addWorkType"
            >
              <span class="text-lg leading-none">
                +
              </span>

              Add Work Type
            </button>
          </div>
        </div>

        <!-- Table -->
        <WorkTypeTable
          @total-changed="totalWorkTypes = $event"
          @edit="editWorkType"
          @view="viewWorkType"
        />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import WorkTypeTable from '@/components/tables/management/WorkTypeTable.vue'
import { DocsIcon } from '@/icons'

interface WorkType {
  id_work_type: number
  name: string
}

const currentPageTitle = ref(
  'Work Types'
)

const totalWorkTypes = ref(0)

/**
 * Add
 */
const addWorkType = () => {
  console.log(
    'Open Add Work Type modal'
  )
}

/**
 * Edit
 */
const editWorkType = (
  workType: WorkType
) => {
  console.log(
    'Edit work type:',
    workType
  )
}

/**
 * View
 */
const viewWorkType = (
  workType: WorkType
) => {
  console.log(
    'View work type:',
    workType
  )
}
</script>