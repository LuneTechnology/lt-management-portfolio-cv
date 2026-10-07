<!-- src/views/management/achievement/AchievementTable.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { ChevronLeft, ChevronRight, Eye, Pencil, Trash2 } from 'lucide-vue-next'
import {
  CATEGORY_STYLES,
  STATUS_STYLES,
  TYPE_STYLES,
  formatAchievementDate,
  getInitials,
  type Achievement,
} from '@/views/Management/Achievement/Achievement'

const props = defineProps<{
  items: Achievement[] // data untuk halaman aktif
  total: number // jumlah seluruh data setelah filter
  page: number
  perPage: number
}>()

const emit = defineEmits<{
  (e: 'edit', item: Achievement): void
  (e: 'view', item: Achievement): void
  (e: 'delete', item: Achievement): void
  (e: 'update:page', page: number): void
}>()

const totalPages = computed(() => Math.max(1, Math.ceil(props.total / props.perPage)))
const from = computed(() => (props.total === 0 ? 0 : (props.page - 1) * props.perPage + 1))
const to = computed(() => Math.min(props.page * props.perPage, props.total))

function goTo(p: number) {
  if (p >= 1 && p <= totalPages.value) emit('update:page', p)
}

const actionBtn =
  'grid h-8 w-8 place-items-center rounded-lg transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'
</script>

<template>
  <div>
    <div class="overflow-x-auto">
      <table class="w-full min-w-[56rem] text-left text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="w-12 px-4 py-3 font-medium">#</th>
            <th class="px-4 py-3 font-medium">Title</th>
            <th class="px-4 py-3 font-medium">Category</th>
            <th class="px-4 py-3 font-medium">Type</th>
            <th class="px-4 py-3 font-medium">Date</th>
            <th class="px-4 py-3 font-medium">Issuer / Organizer</th>
            <th class="px-4 py-3 font-medium">Status</th>
            <th class="px-4 py-3 text-center font-medium">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          <tr v-for="(item, i) in items" :key="item.id" class="hover:bg-slate-50/60">
            <td class="px-4 py-3 font-medium text-slate-700">
              {{ (page - 1) * perPage + i + 1 }}
            </td>

            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div
                  class="grid h-11 w-11 shrink-0 place-items-center overflow-hidden rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-500"
                >
                  <img v-if="item.logo" :src="item.logo" :alt="item.issuer" class="h-full w-full object-contain p-1" />
                  <span v-else>{{ getInitials(item.issuer) }}</span>
                </div>
                <div class="min-w-0">
                  <p class="font-semibold text-slate-900">{{ item.title }}</p>
                  <p class="truncate text-xs text-slate-500">{{ item.description }}</p>
                </div>
              </div>
            </td>

            <td class="px-4 py-3">
              <span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="CATEGORY_STYLES[item.category]">
                {{ item.category }}
              </span>
            </td>

            <td class="px-4 py-3">
              <span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="TYPE_STYLES[item.type]">
                {{ item.type }}
              </span>
            </td>

            <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ formatAchievementDate(item) }}</td>
            <td class="px-4 py-3 text-slate-600">{{ item.issuer }}</td>

            <td class="px-4 py-3">
              <span class="rounded-md px-2.5 py-1 text-xs font-medium" :class="STATUS_STYLES[item.status]">
                {{ item.status }}
              </span>
            </td>

            <td class="px-4 py-3">
              <div class="flex items-center justify-center gap-2">
                <button type="button" aria-label="Edit achievement" :class="[actionBtn, 'bg-blue-50 text-blue-600 hover:bg-blue-100']" @click="emit('edit', item)">
                  <Pencil class="h-4 w-4" />
                </button>
                <button type="button" aria-label="View achievement" :class="[actionBtn, 'bg-slate-100 text-slate-600 hover:bg-slate-200']" @click="emit('view', item)">
                  <Eye class="h-4 w-4" />
                </button>
                <button type="button" aria-label="Delete achievement" :class="[actionBtn, 'bg-red-50 text-red-500 hover:bg-red-100']" @click="emit('delete', item)">
                  <Trash2 class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="items.length === 0">
            <td colspan="8" class="px-4 py-12 text-center text-slate-500">
              No achievements found. Change your search or filters, or add a new achievement.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-4 text-sm text-slate-600">
      <p>Showing {{ from }} to {{ to }} of {{ total }} results</p>

      <nav class="flex items-center gap-2" aria-label="Pagination">
        <button
          type="button"
          aria-label="Previous page"
          class="grid h-9 w-9 place-items-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 disabled:opacity-40"
          :disabled="page === 1"
          @click="goTo(page - 1)"
        >
          <ChevronLeft class="h-4 w-4" />
        </button>

        <button
          v-for="p in totalPages"
          :key="p"
          type="button"
          class="grid h-9 w-9 place-items-center rounded-lg text-sm font-medium"
          :class="p === page ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
          :aria-current="p === page ? 'page' : undefined"
          @click="goTo(p)"
        >
          {{ p }}
        </button>

        <button
          type="button"
          aria-label="Next page"
          class="grid h-9 w-9 place-items-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 disabled:opacity-40"
          :disabled="page === totalPages"
          @click="goTo(page + 1)"
        >
          <ChevronRight class="h-4 w-4" />
        </button>
      </nav>
    </div>
  </div>
</template>