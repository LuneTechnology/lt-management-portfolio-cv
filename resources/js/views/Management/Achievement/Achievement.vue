<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="pageTitle" />

    <div class="space-y-5 sm:space-y-6">
      <ManagementSummaryCard
        title="Total Achievement"
        :value="summary.total"
        :icon="TrophyIcon"
        quote="Achievements are proof of consistency, learning, and growth."
        :change="summary.newThisMonth"
        color="blue"
      />

      <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <header class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
          <div>
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Achievement List</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage awards, certifications, training, and other achievements.</p>
          </div>
          <button type="button" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:opacity-60" @click="openCreate" :disabled="saving">
            <span class="text-lg leading-none">+</span> Add Achievement
          </button>
        </header>

        <div class="flex flex-col gap-3 px-6 py-4 lg:flex-row lg:items-center">
          <input v-model="filters.search" type="search" placeholder="Search achievement..." class="h-11 min-w-0 flex-1 rounded-lg border border-gray-200 bg-white px-4 text-sm text-gray-700 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
          <select v-model="filters.category" class="h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="">All categories</option>
            <option v-for="category in categories" :key="category.id_category" :value="String(category.id_category)">{{ category.name }}</option>
          </select>
          <select v-model="filters.type" class="h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="">All types</option><option value="Award">Award</option><option value="Training">Training</option>
          </select>
          <select v-model="filters.sort" class="h-11 rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white">
            <option value="newest">Newest first</option><option value="oldest">Oldest first</option>
          </select>
          <button type="button" class="h-11 rounded-lg border border-gray-200 px-4 text-sm text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.05]" @click="resetFilters">Reset</button>
        </div>

        <div v-if="error" class="mx-6 mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">{{ error }}</div>
        <div v-if="loading" class="px-6 py-14 text-center text-sm text-gray-500">Loading achievements...</div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-gray-50 text-gray-600 dark:bg-white/[0.03] dark:text-gray-300"><tr>
              <th class="px-5 py-3 font-medium">#</th><th class="px-5 py-3 font-medium">Achievement</th><th class="px-5 py-3 font-medium">Category</th><th class="px-5 py-3 font-medium">Type</th><th class="px-5 py-3 font-medium">Date</th><th class="px-5 py-3 font-medium">Issuer / Organizer</th><th class="px-5 py-3 font-medium">Status</th><th class="px-5 py-3 text-center font-medium">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
              <tr v-for="(item, index) in pageItems" :key="item.id_achievement" class="hover:bg-gray-50/70 dark:hover:bg-white/[0.02]">
                <td class="px-5 py-4 text-gray-500">{{ (page - 1) * perPage + index + 1 }}</td>
                <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50 text-xs font-semibold text-gray-500 dark:border-gray-700 dark:bg-gray-800"><img v-if="item.logo" :src="logoUrl(item.logo)" :alt="item.name" class="h-full w-full object-contain p-1" /><span v-else>{{ initials(item.name) }}</span></div><div class="min-w-0"><p class="font-semibold text-gray-800 dark:text-white/90">{{ item.name }}</p><p class="max-w-[260px] truncate text-xs text-gray-500">{{ item.description || 'No description' }}</p></div></div></td>
                <td class="px-5 py-4"><span class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ item.category?.name || categoryName(item.id_category) }}</span></td>
                <td class="px-5 py-4"><span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="item.type === 'Award' ? 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' : 'bg-violet-50 text-violet-700 dark:bg-violet-500/10 dark:text-violet-300'">{{ item.type }}</span></td>
                <td class="whitespace-nowrap px-5 py-4 text-gray-600 dark:text-gray-300">{{ formatDate(item.date) }}</td>
                <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ item.place }}</td>
                <td class="px-5 py-4"><span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="item.status === 'Verified' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300'">{{ item.status }}</span></td>
                <td class="px-5 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <button type="button" title="Edit achievement" aria-label="Edit achievement" class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 disabled:opacity-50 dark:bg-blue-500/10 dark:text-blue-400" @click="openEdit(item)" :disabled="saving">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 2.651 2.651M4 20l4.2-.8L19.2 8.2a1.875 1.875 0 0 0-2.65-2.65L5.55 16.55 4 20Z"/></svg>
                    </button>
                    <button type="button" title="View achievement" aria-label="View achievement" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400" @click="openView(item)">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6.25 9.75-6.25S21.75 12 21.75 12 18.25 18.25 12 18.25 2.25 12 2.25 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                    <button type="button" title="Delete achievement" aria-label="Delete achievement" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 disabled:opacity-50 dark:bg-red-500/10 dark:text-red-400" @click="openDeleteModal(item)" :disabled="saving">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M10 11v6m4-6v6M5.5 7l.8 13h11.4l.8-13M9 7V4h6v3"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredItems.length === 0"><td colspan="8" class="px-6 py-14 text-center text-gray-500">No achievements found. Try changing your filters or add a new achievement.</td></tr>
            </tbody>
          </table>
        </div>
        <footer v-if="!loading" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
          <span>Showing {{ filteredItems.length ? (page - 1) * perPage + 1 : 0 }}–{{ Math.min(page * perPage, filteredItems.length) }} of {{ filteredItems.length }} results</span>
          <div class="flex items-center gap-2"><button class="rounded-lg border border-gray-200 px-3 py-2 disabled:opacity-40 dark:border-gray-700" :disabled="page <= 1" @click="page--">Previous</button><span>Page {{ page }} / {{ totalPages }}</span><button class="rounded-lg border border-gray-200 px-3 py-2 disabled:opacity-40 dark:border-gray-700" :disabled="page >= totalPages" @click="page++">Next</button></div>
        </footer>
      </section>
    </div>

    <Teleport to="body">
      <div v-if="modalOpen" class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto bg-black/50 px-4 py-6" @click.self="closeModal">
        <section class="my-auto max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
          <header class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-5 dark:border-gray-800 dark:bg-gray-900"><div><h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ modalMode === 'create' ? 'Add Achievement' : modalMode === 'edit' ? 'Edit Achievement' : 'Achievement Detail' }}</h3><p class="mt-1 text-sm text-gray-500">{{ modalMode === 'view' ? 'Review achievement information.' : 'Fill in the achievement details below.' }}</p></div><button type="button" class="rounded-lg px-3 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5" @click="closeModal">✕</button></header>
          <form class="space-y-5 px-6 py-6" @submit.prevent="submitForm">
            <div v-if="formError" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ formError }}</div>
            <div class="grid gap-5 sm:grid-cols-2">
              <label class="block sm:col-span-2"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Achievement name *</span><input v-model.trim="form.name" required maxlength="255" :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="e.g. 1st Place Final Project Competition" /></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Issuer / Organizer *</span><input v-model.trim="form.place" required maxlength="255" :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Organization or institution" /></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Date *</span><input v-model="form.date" type="date" required :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-4 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category *</span><select v-model="form.id_category" required :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="">Select category</option><option v-for="category in categories" :key="category.id_category" :value="String(category.id_category)">{{ category.name }}</option></select></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type *</span><select v-model="form.type" required :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="Award">Award</option><option value="Training">Training</option></select></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</span><select v-model="form.status" required :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="Verified">Verified</option><option value="Completed">Completed</option></select></label>
              <label class="block"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Related Project (optional)</span><select v-model="form.id_project" :disabled="isView || saving" class="h-11 w-full rounded-lg border border-gray-200 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option value="">No related project</option><option v-for="project in projects" :key="project.id_project" :value="String(project.id_project)">{{ project.name || project.title || `Project #${project.id_project}` }}</option></select></label>
              <label class="block sm:col-span-2"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</span><textarea v-model.trim="form.description" rows="3" maxlength="255" :disabled="isView || saving" class="w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Short description (max. 255 characters)"></textarea></label>
              <div class="sm:col-span-2"><span class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Logo / Certificate image</span><input v-if="!isView" type="file" accept="image/*" :disabled="saving" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-medium dark:text-gray-300 dark:file:bg-white/10" @change="onLogoChange" /><div v-if="logoPreview" class="mt-3 flex items-center gap-3"><img :src="logoPreview" alt="Achievement logo preview" class="h-20 w-20 rounded-lg border border-gray-200 object-contain p-2 dark:border-gray-700" /><span class="text-xs text-gray-500">Image preview</span></div></div>
            </div>
            <footer class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800"><button type="button" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300" @click="closeModal">{{ isView ? 'Close' : 'Cancel' }}</button><button v-if="!isView" type="submit" :disabled="saving" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-60">{{ saving ? 'Saving...' : modalMode === 'create' ? 'Save Achievement' : 'Save Changes' }}</button></footer>
          </form>
        </section>
      </div>
      <div v-if="deleteModalOpen" class="fixed inset-0 z-[100000] flex items-center justify-center bg-black/50 px-4 py-6" @click.self="closeDeleteModal">
        <section class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900" role="alertdialog" aria-modal="true" aria-labelledby="delete-achievement-title">
          <div class="px-6 pt-6">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 2.82 17a2 2 0 0 0 1.74 3h14.88a2 2 0 0 0 1.74-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
            </div>
            <div class="mt-5 text-center">
              <h3 id="delete-achievement-title" class="text-lg font-semibold text-gray-800 dark:text-white">Delete Achievement?</h3>
              <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">Are you sure you want to delete this achievement? This action cannot be undone.</p>
            </div>
            <div v-if="selectedAchievement" class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
              <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ selectedAchievement.name }}</p>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ selectedAchievement.place }} · {{ formatDate(selectedAchievement.date) }}</p>
            </div>
            <p v-if="deleteError" class="mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">{{ deleteError }}</p>
          </div>
          <div class="mt-6 flex gap-3 border-t border-gray-200 px-6 py-5 dark:border-gray-800">
            <button type="button" :disabled="deleting" class="flex-1 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]" @click="closeDeleteModal">Cancel</button>
            <button type="button" :disabled="deleting" class="flex-1 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-50" @click="confirmDelete">
              <span v-if="deleting">Deleting...</span><span v-else>Delete Achievement</span>
            </button>
          </div>
        </section>
      </div>
    </Teleport>
  </AdminLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import axios from '@/services/axios'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import { GridIcon as TrophyIcon } from '@/icons'

interface Category { id_category: number; name: string }
interface Project { id_project: number; name?: string; title?: string }
interface AchievementItem {
  id_achievement: number
  name: string
  place: string
  date: string
  id_project: number | null
  id_category: number
  description: string | null
  type: 'Award' | 'Training'
  status: 'Verified' | 'Completed'
  logo: string | null
  category?: Category
}
interface AchievementForm {
  name: string; place: string; date: string; id_project: string; id_category: string
  description: string; type: 'Award' | 'Training'; status: 'Verified' | 'Completed'
}

const pageTitle = ref('Achievement')
const loading = ref(false)
const saving = ref(false)
const error = ref('')
const formError = ref('')
const items = ref<AchievementItem[]>([])
const categories = ref<Category[]>([])
const projects = ref<Project[]>([])
const summary = reactive({ total: 0, newThisMonth: 0 })
const filters = reactive({ search: '', category: '', type: '', sort: 'newest' })
const page = ref(1)
const perPage = 8
const modalOpen = ref(false)
const deleteModalOpen = ref(false)
const selectedAchievement = ref<AchievementItem | null>(null)
const deleting = ref(false)
const deleteError = ref('')
const modalMode = ref<'create' | 'edit' | 'view'>('create')
const editingId = ref<number | null>(null)
const logoFile = ref<File | null>(null)
const logoPreview = ref('')
const form = reactive<AchievementForm>(emptyForm())
const isView = computed(() => modalMode.value === 'view')

function emptyForm(): AchievementForm { return { name: '', place: '', date: '', id_project: '', id_category: '', description: '', type: 'Award', status: 'Verified' } }
const filteredItems = computed(() => {
  const search = filters.search.trim().toLowerCase()
  const result = items.value.filter(item => {
    const matchesSearch = !search || [item.name, item.place, item.description || '', item.category?.name || ''].some(value => value.toLowerCase().includes(search))
    return matchesSearch && (!filters.category || String(item.id_category) === filters.category) && (!filters.type || item.type === filters.type)
  })
  result.sort((a, b) => {
    const comparison = new Date(a.date).getTime() - new Date(b.date).getTime()
    return filters.sort === 'oldest' ? comparison : -comparison
  })
  return result
})
const totalPages = computed(() => Math.max(1, Math.ceil(filteredItems.value.length / perPage)))
const pageItems = computed(() => filteredItems.value.slice((page.value - 1) * perPage, page.value * perPage))
watch([() => filters.search, () => filters.category, () => filters.type, () => filters.sort], () => { page.value = 1 })
watch(totalPages, value => { if (page.value > value) page.value = value })

function categoryName(id: number) { return categories.value.find(category => category.id_category === id)?.name || 'Uncategorized' }
function initials(value?: string | null) { return (value || 'Achievement').split(/\s+/).filter(Boolean).slice(0, 2).map(part => part[0]).join('').toUpperCase() }
function formatDate(value?: string | null) { if (!value) return '—'; const date = new Date(`${value.slice(0, 10)}T00:00:00`); return Number.isNaN(date.getTime()) ? value : new Intl.DateTimeFormat('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).format(date) }
function logoUrl(value: string) { if (/^https?:\/\//i.test(value) || value.startsWith('data:')) return value; return `/storage/${value.replace(/^public\//, '').replace(/^storage\//, '')}` }
function onLogoChange(event: Event) { const file = (event.target as HTMLInputElement).files?.[0] || null; logoFile.value = file; if (logoPreview.value.startsWith('blob:')) URL.revokeObjectURL(logoPreview.value); logoPreview.value = file ? URL.createObjectURL(file) : '' }
function resetForm() { Object.assign(form, emptyForm()); logoFile.value = null; if (logoPreview.value.startsWith('blob:')) URL.revokeObjectURL(logoPreview.value); logoPreview.value = ''; formError.value = '' }
function closeModal() { modalOpen.value = false; resetForm() }
function openCreate() { resetForm(); editingId.value = null; modalMode.value = 'create'; modalOpen.value = true }
function setForm(item: AchievementItem) { form.name = item.name || ''; form.place = item.place || ''; form.date = (item.date || '').slice(0, 10); form.id_project = item.id_project ? String(item.id_project) : ''; form.id_category = String(item.id_category ?? ''); form.description = item.description || ''; form.type = item.type || 'Award'; form.status = item.status || 'Verified'; logoPreview.value = item.logo ? logoUrl(item.logo) : '' }
function openEdit(item: AchievementItem) { resetForm(); editingId.value = item.id_achievement; setForm(item); modalMode.value = 'edit'; modalOpen.value = true }
function openView(item: AchievementItem) { resetForm(); setForm(item); modalMode.value = 'view'; modalOpen.value = true }
async function fetchAll() {
  loading.value = true; error.value = ''
  try {
    const response = await axios.get('/api/achievements', { params: { per_page: 1000, sort: 'newest' } })
    const data = response.data
    items.value = Array.isArray(data.data) ? data.data : Array.isArray(data) ? data : []
    summary.total = Number(data.stats?.total ?? data.meta?.total ?? items.value.length)
    summary.newThisMonth = Number(data.stats?.new_this_month ?? 0)
  } catch (e: any) { error.value = e?.response?.data?.message || 'Failed to load achievements. Please check your connection and API access.' }
  finally { loading.value = false }
}
async function fetchOptions() {
  try { const response = await axios.get('/api/category'); const data = response.data; categories.value = Array.isArray(data.data) ? data.data : Array.isArray(data) ? data : [] }
  catch { categories.value = [] }
  try { const response = await axios.get('/api/projects', { params: { per_page: 1000 } }); const data = response.data; projects.value = Array.isArray(data.data) ? data.data : Array.isArray(data) ? data : [] }
  catch { projects.value = [] }
}
async function submitForm() {
  formError.value = ''
  if (!form.name || !form.place || !form.date || !form.id_category) { formError.value = 'Please fill in all required fields.'; return }
  saving.value = true
  try {
    const payload = new FormData()
    payload.append('name', form.name); payload.append('place', form.place); payload.append('date', form.date)
    payload.append('id_category', form.id_category); payload.append('type', form.type); payload.append('status', form.status)
    payload.append('description', form.description)
    if (form.id_project) payload.append('id_project', form.id_project)
    if (logoFile.value) payload.append('logo', logoFile.value)
    if (modalMode.value === 'edit' && editingId.value !== null) { payload.append('_method', 'PUT'); await axios.post(`/api/achievements/${editingId.value}`, payload) }
    else await axios.post('/api/achievements', payload)
    closeModal(); await fetchAll()
  } catch (e: any) {
    const validation = e?.response?.data?.errors
    formError.value = validation ? Object.values(validation).flat().join(' ') : e?.response?.data?.message || 'Could not save achievement. Please try again.'
  } finally { saving.value = false }
}
function openDeleteModal(item: AchievementItem) {
  selectedAchievement.value = item
  deleteError.value = ''
  deleteModalOpen.value = true
}
function closeDeleteModal() {
  if (deleting.value) return
  deleteModalOpen.value = false
  selectedAchievement.value = null
  deleteError.value = ''
}
async function confirmDelete() {
  if (!selectedAchievement.value) return
  deleting.value = true
  deleteError.value = ''
  try {
    await axios.delete(`/api/achievements/${selectedAchievement.value.id_achievement}`)
    deleteModalOpen.value = false
    selectedAchievement.value = null
    await fetchAll()
  } catch (e: any) {
    deleteError.value = e?.response?.data?.message || 'Could not delete achievement. Please try again.'
  } finally {
    deleting.value = false
  }
}
function resetFilters() { filters.search = ''; filters.category = ''; filters.type = ''; filters.sort = 'newest' }
onMounted(() => { void fetchOptions(); void fetchAll() })
</script>
