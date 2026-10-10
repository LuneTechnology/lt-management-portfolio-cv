<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary -->
      <ManagementSummaryCard
        title="Total Experience"
        :value="totalExperience"
        :icon="DocsIcon"
        quote="Experience is the teacher of all things."
        :change="1"
        color="blue"
      />

      <!-- Experience Table -->
      <div
        class="overflow-hidden rounded-2xl border
               border-gray-200 bg-white
               dark:border-gray-800
               dark:bg-white/[0.03]"
      >

        <!-- Header -->
        <div
          class="border-b border-gray-200 px-6 py-5
                 dark:border-gray-800"
        >

          <div
            class="flex flex-col gap-4
                   sm:flex-row sm:items-center
                   sm:justify-between"
          >

            <!-- Title -->
            <div class="flex items-center gap-3">

              <div>

                <h3
                  class="text-lg font-semibold
                         text-gray-800
                         dark:text-white/90"
                >
                  Experience List
                </h3>

                <p
                  class="text-sm text-gray-500
                         dark:text-gray-400"
                >
                  View, add, edit, or delete
                  your work experience.
                </p>

              </div>

            </div>

            <!-- Add Button -->
            <button
              type="button"
              class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-60"
              @click="addExperience"
            >
              <span class="text-lg leading-none">
                +
              </span>

              Add Experience
            </button>

          </div>

        </div>

        <!-- Table -->
        <ExperienceTable
          @total-changed="
            totalExperience = $event
          "
          @edit="editExperience"
          @view="viewExperience"
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

import ExperienceTable from '@/components/tables/management/ExperienceTable.vue'

import { DocsIcon } from '@/icons'

const currentPageTitle =
  ref('Experience')

const totalExperience = ref(0)

const addExperience = () => {
  console.log(
    'Open Add Experience modal'
  )
}

const editExperience = (
  experience: unknown
) => {
  console.log(
    'Edit experience:',
    experience
  )
}

const viewExperience = (
  experience: unknown
) => {
  console.log(
    'View experience:',
    experience
  )
}
</script>