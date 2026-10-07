<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <ManagementSummaryCard
        title="Total Achievement"
        :value="totalAchievements"
        :icon="TrophyIcon"
        quote="Achievements are proof of consistency, learning, and growth."
        :change="newThisMonth"
        color="blue"
      />

      <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
          <!-- header + tombol Add: tetap sama seperti punyamu -->
        </div>

        <!-- Filter bar -->
        <div class="flex flex-wrap items-end gap-3 px-6 py-4">
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search achievement..."
            class="min-w-[240px] flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-transparent"
          />
          <select v-model="filters.category" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-transparent">
            <option value="">All Category</option>
            <option v-for="c in categories" :key="c.id_category" :value="c.id_category">{{ c.name }}</option>
          </select>
          <select v-model="filters.type" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-transparent">
            <option value="">All Type</option>
            <option value="Award">Award</option>
            <option value="Training">Training</option>
          </select>
          <select v-model="filters.sort" class="rounded-lg border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-transparent">
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
          </select>
          <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm dark:border-gray-700" @click="resetFilters">
            Reset
          </button>
        </div>

        <AchievementTable
          :items="items"
          :total="total"
          :per-page="perPage"
          v-model:page="page"
          @edit="handleEditAchievement"
          @view="handleViewAchievement"
          @delete="handleDeleteAchievement"
        />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import axios from 'axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import AchievementTable from "@/components/tables/management/AchievementTable.vue"  // <- sesuaikan
import type { Achievement } from './Achievement.ts'              // <- sama dengan './index' di tabel
import { GridIcon as TrophyIcon } from '@/icons'

const currentPageTitle = ref('Achievement')

const items = ref<Achievement[]>([])
const total = ref(0)              // jumlah data setelah filter (untuk pagination)
const page = ref(1)
const perPage = 5

const totalAchievements = ref(0)  // total keseluruhan (untuk kartu ringkasan)
const newThisMonth = ref(0)
const categories = ref<{ id_category: number; name: string }[]>([])

const filters = reactive({ search: '', category: '', type: '', sort: 'newest' })

// Ubah bentuk data API menjadi bentuk yang dipakai AchievementTable
function toAchievement(a: any): Achievement {
  return {
    id: a.id_achievement,
    title: a.name,
    description: a.description ?? '',
    category: a.category?.name,
    type: a.type,
    issuer: a.place,
    date: a.date,
    status: a.status,
    logo: a.logo_url,
  } as Achievement // sesuaikan field dengan tipe Achievement di index.ts
}

async function fetchAchievements() {
  const { data } = await axios.get('/api/achievements', {
    params: { ...filters, page: page.value, per_page: perPage },
  })
  items.value = data.data.map(toAchievement)
  total.value = data.meta.total
  totalAchievements.value = data.stats.total
  newThisMonth.value = data.stats.new_this_month
}

async function fetchCategories() {
  const { data } = await axios.get('/api/categories')
  categories.value = data.data
}

// pindah halaman -> ambil ulang data
watch(page, fetchAchievements)

// filter berubah -> kembali ke halaman 1 (search di-debounce)
let timer: ReturnType<typeof setTimeout>
watch(filters, () => {
  clearTimeout(timer)
  timer = setTimeout(() => {
    if (page.value === 1) fetchAchievements()
    else page.value = 1 // memicu watch(page)
  }, 300)
})

function resetFilters() {
  Object.assign(filters, { search: '', category: '', type: '', sort: 'newest' })
}

const addAchievement = () => console.log('Open Add Achievement modal')
const handleEditAchievement = (a: Achievement) => console.log('Open Edit Achievement modal:', a)
const handleViewAchievement = (a: Achievement) => console.log('Open View Achievement modal:', a)

async function handleDeleteAchievement(a: Achievement) {
  if (!confirm(`Hapus "${a.title}"?`)) return
  await axios.delete(`/api/achievements/${a.id}`)
  fetchAchievements()
}

onMounted(() => {
  fetchCategories()
  fetchAchievements()
})
</script>