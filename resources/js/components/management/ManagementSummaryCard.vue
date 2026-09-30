<template>
  <div
    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white px-6 py-5 dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

      <!-- Left -->
      <div class="flex items-center gap-4">

        <div
          class="flex h-16 w-16 items-center justify-center rounded-2xl"
          :class="iconBgClass"
        >
          <component
            :is="icon"
            class="h-8 w-8"
            :class="iconClass"
          />
        </div>

        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ title }}
          </p>

          <h2
            class="mt-1 text-3xl font-bold text-gray-900 dark:text-white"
          >
            {{ value }}
          </h2>

          <div class="mt-1 flex items-center gap-2">
            <span
              v-if="change !== undefined"
              class="inline-flex items-center rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/10 dark:text-success-400"
            >
              ↑ {{ change }}
            </span>

            <span
              v-if="change !== undefined"
              class="text-xs text-gray-500 dark:text-gray-400"
            >
              from last month
            </span>
          </div>
        </div>
      </div>

      <!-- Quote -->
      <div class="hidden text-right lg:block">
        <p
          class="text-sm italic text-gray-500 dark:text-gray-400"
        >
          "{{ quote }}"
        </p>

        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
          — Portfolio Management System
        </p>
      </div>

    </div>

    <!-- Simple decorative line -->
    <div
      class="absolute bottom-0 left-1/2 h-1 w-1/3 -translate-x-1/2 rounded-full"
      :class="lineClass"
    />
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  title: string
  value: number | string
  icon: object
  quote?: string
  change?: number | string
  color?: 'blue' | 'green' | 'purple' | 'orange'
}

const props = withDefaults(defineProps<Props>(), {
  quote: '',
  color: 'blue',
})

const iconBgClass = computed(() => {
  return {
    blue: 'bg-blue-50 dark:bg-blue-500/10',
    green: 'bg-success-50 dark:bg-success-500/10',
    purple: 'bg-purple-50 dark:bg-purple-500/10',
    orange: 'bg-orange-50 dark:bg-orange-500/10',
  }[props.color]
})

const iconClass = computed(() => {
  return {
    blue: 'text-blue-500',
    green: 'text-success-500',
    purple: 'text-purple-500',
    orange: 'text-orange-500',
  }[props.color]
})

const lineClass = computed(() => {
  return {
    blue: 'bg-blue-500',
    green: 'bg-success-500',
    purple: 'bg-purple-500',
    orange: 'bg-orange-500',
  }[props.color]
})
</script>