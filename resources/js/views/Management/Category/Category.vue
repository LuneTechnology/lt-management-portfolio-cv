<template>
  <AdminLayout>
    <!-- Breadcrumb -->
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">

      <!-- Summary Card -->
      <ManagementSummaryCard
        title="Total Category"
        :value="totalCategories"
        :icon="GridIcon"
        quote="Categories make content structure clear and manageable."
        :change="1"
        color="blue"
      />

      <!-- Category Table Container -->
      <div
        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
      >
        <!-- Table Header Box -->
        <div
          class="border-b border-gray-200 px-6 py-5 dark:border-gray-800"
        >
          <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
          >

            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10"
              >
                <GridIcon
                  class="h-5 w-5 text-blue-500"
                />
              </div>

              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-white/90"
                >
                  Category List
                </h3>

                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  View, add, edit, or delete your content categories.
                </p>
              </div>
            </div>

            <!-- Add Button -->
            <button
              type="button"
              class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
              @click="addCategory"
            >
              <span class="text-lg leading-none">+</span>
              Add Category
            </button>

          </div>
        </div>

        <!-- Table Component -->
        <CategoryTable
          ref="categoryTableRef"
          @total-changed="totalCategories = $event"
          @edit="handleEditCategory"
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
import CategoryTable from '@/components/tables/management/CategoryTable.vue'

import { GridIcon } from '@/icons'

interface Category {
  id_category: number
  name: string
  slug?: string
}

const currentPageTitle = ref('Category')
const totalCategories = ref(0)
const categoryTableRef = ref<InstanceType<typeof CategoryTable> | null>(null)

const addCategory = () => {
  console.log('Open Add Category modal')
}

const handleEditCategory = (category: Category) => {
  console.log('Open Edit Category modal:', category)
}
</script>