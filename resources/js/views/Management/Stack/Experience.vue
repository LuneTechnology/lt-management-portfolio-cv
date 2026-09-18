<template>
  <AdminLayout>
    <!-- Header judul halaman -->
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    
    <div class="space-y-5 sm:space-y-6">
      <!-- Card kontainer utama -->
      <ComponentCard title="Experience Management">
        <!-- Memanggil komponen tabel experience -->
        <ExperienceTable />
      </ComponentCard>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from "vue";
import PageBreadcrumb from "@/components/common/PageBreadcrumb.vue";
import AdminLayout from "@/components/layout/AdminLayout.vue";
import ComponentCard from "@/components/common/ComponentCard.vue";
import ExperienceTable from "@/components/tables/management/ExperienceTable.vue";

const currentPageTitle = ref("Experience");
</script>