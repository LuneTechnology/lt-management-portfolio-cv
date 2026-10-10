<template>
  <AdminLayout>
    <div class="space-y-6">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Dashboard</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Ringkasan data portfolio dan aktivitas terbaru.
          </p>
        </div>
        <div
          v-if="dashboard"
          class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:text-gray-300"
        >
          {{ dashboard.role?.isSuperAdmin ? 'Semua pengguna' : 'Portfolio saya' }}
        </div>
      </div>

      <div
        v-if="loading"
        class="rounded-xl border border-gray-200 bg-white p-6 text-sm text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400"
      >
        Memuat statistik dashboard...
      </div>

      <div
        v-else-if="error"
        class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/10 dark:text-red-300"
      >
        {{ error }}
        <button class="ml-2 font-semibold underline" type="button" @click="loadDashboard">Coba lagi</button>
      </div>

      <template v-else-if="dashboard">
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
          <ManagementSummaryCard
            title="Total Project"
            :value="dashboard.cards.projects.total"
            :icon="FolderKanban"
            :change="dashboard.cards.projects.change"
            :trend="dashboard.cards.projects.trend"
            :change-label="dashboard.cards.projects.changeLabel"
            :chart-data="dashboard.cards.projects.chartData"
            :chart-labels="dashboard.cards.projects.chartLabels"
          />
          <ManagementSummaryCard
            title="Total Work"
            :value="dashboard.cards.works.total"
            :icon="BriefcaseBusiness"
            :change="dashboard.cards.works.change"
            :trend="dashboard.cards.works.trend"
            :change-label="dashboard.cards.works.changeLabel"
            :chart-data="dashboard.cards.works.chartData"
            :chart-labels="dashboard.cards.works.chartLabels"
            color="green"
          />
          <ManagementSummaryCard
            title="Total Experience"
            :value="dashboard.cards.experiences.total"
            :icon="UsersRound"
            :change="dashboard.cards.experiences.change"
            :trend="dashboard.cards.experiences.trend"
            :change-label="dashboard.cards.experiences.changeLabel"
            :chart-data="dashboard.cards.experiences.chartData"
            :chart-labels="dashboard.cards.experiences.chartLabels"
            color="purple"
          />
          <ManagementSummaryCard
            v-if="dashboard.cards.users"
            title="Total User"
            :value="dashboard.cards.users.total"
            :icon="UserRound"
            :change="dashboard.cards.users.change"
            :trend="dashboard.cards.users.trend"
            :change-label="dashboard.cards.users.changeLabel"
            :chart-data="dashboard.cards.users.chartData"
            :chart-labels="dashboard.cards.users.chartLabels"
            color="orange"
          />
        </section>

        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
              <div class="rounded-xl bg-blue-50 p-2.5 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                <FolderPlus class="h-5 w-5" />
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Project bulan ini</p>
                <p class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.projectsAddedThisMonth }}</p>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
              <div class="rounded-xl bg-green-50 p-2.5 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                <ListChecks class="h-5 w-5" />
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Task</p>
                <p class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.taskCount }}</p>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
              <div class="rounded-xl bg-purple-50 p-2.5 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                <GraduationCap class="h-5 w-5" />
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendidikan</p>
                <p class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.educationCount }}</p>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-3">
              <div class="rounded-xl bg-orange-50 p-2.5 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                <Award class="h-5 w-5" />
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Achievement</p>
                <p class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.achievementCount }}</p>
              </div>
            </div>
          </div>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Tren Aktivitas Portfolio</h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Project dan Work yang aktif pada setiap bulan, serta Experience yang terkait.</p>
            </div>
            <div class="flex flex-wrap gap-4 text-xs text-gray-600 dark:text-gray-300">
              <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>Work</span>
              <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span>Project</span>
              <span class="inline-flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-violet-500"></span>Experience</span>
            </div>
          </div>
          <div v-if="dashboard.activityTrend?.months?.length" class="relative">
            <div class="grid grid-cols-6 gap-2 text-center text-sm font-normal normal-case tracking-normal text-gray-500 dark:text-gray-400">
              <span v-for="month in dashboard.activityTrend.months" :key="month.label">{{ month.label }}</span>
            </div>
            <div class="relative mt-3 h-64 w-full">
              <svg class="h-full w-full overflow-visible" viewBox="0 0 900 250" preserveAspectRatio="none" role="img" aria-label="Grafik tren aktivitas enam bulan">
                <g v-for="grid in [0,1,2,3,4]" :key="grid">
                  <line x1="20" :y1="20 + grid * 50" x2="880" :y2="20 + grid * 50" stroke="currentColor" stroke-opacity=".12" stroke-dasharray="4 5" />
                </g>
                <g v-for="(month, i) in dashboard.activityTrend.months" :key="month.key">
                  <line :x1="20 + i * 172" y1="20" :x2="20 + i * 172" y2="220" stroke="currentColor" stroke-opacity=".08" />
                </g>
                <path :d="trendPath('projects')" fill="none" stroke="#3B82F6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <path :d="trendPath('works')" fill="none" stroke="#10B981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <path :d="trendPath('experiences')" fill="none" stroke="#8B5CF6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                <!-- Round hover targets are HTML overlays so they stay circular when the SVG stretches responsively. -->
              </svg>
              <div v-for="(month, i) in dashboard.activityTrend.months" :key="month.key + '-hover'" class="pointer-events-none absolute inset-0">
                <button type="button" aria-label="Work pada bulan ini" class="pointer-events-auto absolute h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-emerald-500 shadow-sm" :style="{ left: `${2.222 + (i / Math.max(1, dashboard.activityTrend.months.length - 1)) * 95.556}%`, top: `${(trendY(month.works) / 250) * 100}%` }" @mouseenter="showTrendTooltip(month, i, 'works')" @mouseleave="activeTrend = null" />
                <button type="button" aria-label="Project pada bulan ini" class="pointer-events-auto absolute h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-blue-500 shadow-sm" :style="{ left: `${2.222 + (i / Math.max(1, dashboard.activityTrend.months.length - 1)) * 95.556}%`, top: `${(trendY(month.projects) / 250) * 100}%` }" @mouseenter="showTrendTooltip(month, i, 'projects')" @mouseleave="activeTrend = null" />
                <button type="button" aria-label="Experience pada bulan ini" class="pointer-events-auto absolute h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white bg-violet-500 shadow-sm" :style="{ left: `${2.222 + (i / Math.max(1, dashboard.activityTrend.months.length - 1)) * 95.556}%`, top: `${(trendY(month.experiences) / 250) * 100}%` }" @mouseenter="showTrendTooltip(month, i, 'experiences')" @mouseleave="activeTrend = null" />
              </div>
              <div v-if="activeTrend" class="pointer-events-none absolute z-10 w-64 rounded-xl border border-gray-200 bg-white p-3 text-sm shadow-xl dark:border-gray-700 dark:bg-gray-900"
                :style="activeTrend.index >= 4 ? { right: '0px', top: '8px' } : { left: `calc(${(activeTrend.index / Math.max(1, dashboard.activityTrend.months.length - 1)) * 100}% + 12px)`, top: '8px' }">
                <p class="font-semibold text-gray-900 dark:text-white">{{ activeTrend.month.label }} · {{ trendTitle(activeTrend.type) }}</p>
                <p class="mt-1 text-gray-600 dark:text-gray-300">Data baru: <strong>{{ activeTrend.count }}</strong></p>
                <div class="mt-2 max-h-36 space-y-1 overflow-auto">
                  <p v-for="(item, idx) in activeTrend.month[activeTrend.type + 'Items']" :key="idx" class="text-xs text-gray-500 dark:text-gray-400">• {{ item }}</p>
                  <p v-if="!activeTrend.month[activeTrend.type + 'Items']?.length" class="text-xs text-gray-400">Tidak ada data baru pada bulan ini.</p>
                </div>
              </div>
            </div>
          </div>
          <p v-else class="rounded-xl bg-gray-50 p-6 text-sm text-gray-500 dark:bg-gray-800/50">Data tren belum tersedia.</p>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Ringkasan Portfolio</h2>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Jumlah data yang tercatat saat ini.</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-6">
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Contributor</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.contributorCount }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Stack digunakan</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.secondary.stackCount }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Category</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.masterData.categories }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Stack</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.masterData.stacks }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Work Type</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.masterData.workTypes }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/50">
              <p class="text-xs text-gray-500 dark:text-gray-400">Position Type</p>
              <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ dashboard.masterData.positionTypes }}</p>
            </div>
          </div>
        </section>
      </template>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import axios from '@/services/axios'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import {
  Award,
  BriefcaseBusiness,
  FolderKanban,
  FolderPlus,
  GraduationCap,
  ListChecks,
  UserRound,
  UsersRound,
} from 'lucide-vue-next'

interface Metric {
  total: number
  change: number | null
  trend: 'up' | 'down' | 'same'
  changeLabel: string
  chartData: number[]
  chartLabels: string[]
}

interface DashboardData {
  role: { id: number; name: string | null; isSuperAdmin: boolean }
  cards: { projects: Metric; works: Metric; experiences: Metric; users: Metric | null }
  secondary: {
    projectsAddedThisMonth: number
    worksAddedThisMonth: number
    taskCount: number
    educationCount: number
    achievementCount: number
    contributorCount: number
    stackCount: number
  }
  activityTrend?: { months: Array<{ key: string; label: string; projects: number; works: number; experiences: number; projectsItems: string[]; worksItems: string[]; experiencesItems: string[] }> }
  masterData: {
    categories: number
    stacks: number
    stackTypes: number
    workTypes: number
    positionTypes: number
    workTags: number
    roles: number | null
  }
}

const dashboard = ref<DashboardData | null>(null)
const loading = ref(true)
const error = ref('')
const activeTrend = ref<{ month: any; index: number; type: string; count: number } | null>(null)

function trendY(value: number) {
  const months = dashboard.value?.activityTrend?.months ?? []
  const maxValue = Math.max(1, ...months.flatMap((m: any) => [m.projects, m.works, m.experiences]))
  return 220 - (Number(value || 0) / maxValue) * 190
}

function trendPath(type: 'projects' | 'works' | 'experiences') {
  const months = dashboard.value?.activityTrend?.months ?? []
  return months.map((month: any, index: number) =>
    `${index === 0 ? 'M' : 'L'} ${20 + index * 172} ${trendY(month[type])}`
  ).join(' ')
}

function showTrendTooltip(month: any, index: number, type: string) {
  activeTrend.value = { month, index, type, count: Number(month[type] || 0) }
}

function tooltipLeft(index: number) {
  return index >= 4 ? 'auto' : `${Math.max(0, Math.min(68, index * 18))}%`
}

function trendTitle(type: string) {
  return type === 'works' ? 'Work' : type === 'projects' ? 'Project' : 'Experience'
}

async function loadDashboard() {
  loading.value = true
  error.value = ''
  try {
    const response = await axios.get('/api/dashboard/stats')
    dashboard.value = response.data?.data ?? response.data
  } catch (err: any) {
    error.value = err?.response?.data?.message ?? 'Statistik dashboard gagal dimuat. Periksa koneksi dan endpoint API.'
  } finally {
    loading.value = false
  }
}

onMounted(loadDashboard)
</script>
