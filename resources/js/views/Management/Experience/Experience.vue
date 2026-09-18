<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <ExperienceTable />
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from "vue";

import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import AdminLayout from "@/components/layout/AdminLayout.vue";
import ExperienceTable from "@/components/tables/management/ExperienceTable.vue";
import Button from '@/components/ui/Button.vue';

const currentPageTitle = ref("Experience");
</script>