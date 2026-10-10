<template>
  <div
    class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-white/[0.03]"
  >
    <div class="flex items-center justify-between gap-4">
      <!-- Left: metric -->
      <div class="flex min-w-0 items-center gap-3">
        <div
          class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
          :class="iconBgClass"
        >
          <component :is="icon" class="h-6 w-6" :class="iconClass" />
        </div>

        <div class="min-w-0">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
            {{ title }}
          </p>

          <h2 class="mt-0.5 text-2xl font-bold leading-tight text-gray-900 dark:text-white">
            {{ value }}
          </h2>

          <div v-if="change !== undefined && change !== null" class="mt-1 flex items-center gap-1.5">
            <span
              class="inline-flex shrink-0 items-center gap-1 rounded-full px-1.5 py-0.5 text-xs font-medium"
              :class="changeBadgeClass"
            >
              <component :is="changeIcon" class="h-3.5 w-3.5" />
              {{ formattedChange }}
            </span>
            <span class="whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
              {{ changeLabel }}
            </span>
          </div>
        </div>
      </div>

      <!-- Right: compact six-month trend chart, always blue -->
      <div class="w-28 shrink-0 sm:w-32">
        <svg
          v-if="hasChartData"
          viewBox="0 0 128 44"
          class="block h-11 w-full overflow-visible"
          role="img"
          :aria-label="`Trend for the last six months: ${chartData.join(', ')}`"
        >
          <defs>
            <linearGradient :id="gradientId" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.18" />
              <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.015" />
            </linearGradient>
          </defs>

          <path
            :d="areaPath"
            :fill="`url(#${gradientId})`"
          />

          <line
            v-for="(point, index) in chartPoints"
            :key="`guide-${index}`"
            :x1="point.x"
            :y1="point.y"
            :x2="point.x"
            y2="35"
            stroke="#93C5FD"
            stroke-width="0.8"
          />

          <path
            :d="linePath"
            fill="none"
            stroke="#1677FF"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
          <circle
            v-for="(point, index) in chartPoints"
            :key="`point-${index}`"
            :cx="point.x"
            :cy="point.y"
            r="2.6"
            fill="#1677FF"
            stroke="white"
            stroke-width="0.8"
            class="cursor-crosshair"
          >
            <title>{{ `${point.label}: ${point.value} data` }}</title>
          </circle>
        </svg>

        <div
          v-else
          class="flex h-11 items-center justify-center rounded-md border border-dashed border-gray-200 text-[10px] text-gray-400 dark:border-gray-700 dark:text-gray-500"
        >
          Data tren belum tersedia
        </div>

        <p class="mt-1 text-center text-[10px] leading-3 text-gray-500 dark:text-gray-400">
          6 bulan terakhir
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, useId } from 'vue'
import { TrendingDown, TrendingUp, Minus } from 'lucide-vue-next'

type Trend = 'up' | 'down' | 'same'

interface Props {
  title: string
  value: number | string
  icon: object
  /** Absolute difference versus the previous calendar month. Negative values indicate a decrease. */
  change?: number | string
  /** Optional explicit trend, useful when `change` is formatted text. */
  trend?: Trend
  /** Six monthly values, oldest to newest, fetched from the backend. */
  chartData?: number[]
  /** Labels aligned with chartData, oldest to newest. */
  chartLabels?: string[]
  changeLabel?: string
  color?: 'blue' | 'green' | 'purple' | 'orange'
}

const props = withDefaults(defineProps<Props>(), {
  color: 'blue',
  chartData: () => [],
  chartLabels: () => [],
  changeLabel: 'dari bulan lalu',
})

const chartId = useId()
const gradientId = computed(() => `summary-chart-gradient-${chartId.replace(/:/g, '')}`)

const numericChange = computed(() => {
  if (typeof props.change === 'number') return props.change
  if (typeof props.change === 'string') {
    const parsed = Number(props.change.replace(/[^\d.-]/g, ''))
    return Number.isFinite(parsed) ? parsed : null
  }
  return null
})

const resolvedTrend = computed<Trend>(() => {
  if (props.trend) return props.trend
  if (numericChange.value === null || numericChange.value === 0) return 'same'
  return numericChange.value > 0 ? 'up' : 'down'
})

const formattedChange = computed(() => {
  if (numericChange.value !== null) {
    if (numericChange.value > 0) return `+${numericChange.value}`
    if (numericChange.value < 0) return `−${Math.abs(numericChange.value)}`
    return '0'
  }
  return props.change ?? '0'
})

const changeIcon = computed(() => {
  if (resolvedTrend.value === 'up') return TrendingUp
  if (resolvedTrend.value === 'down') return TrendingDown
  return Minus
})

const changeBadgeClass = computed(() => {
  if (resolvedTrend.value === 'up') {
    return 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400'
  }
  if (resolvedTrend.value === 'down') {
    return 'bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400'
  }
  return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300'
})

const iconBgClass = computed(() => ({
  blue: 'bg-blue-50 dark:bg-blue-500/10',
  green: 'bg-success-50 dark:bg-success-500/10',
  purple: 'bg-purple-50 dark:bg-purple-500/10',
  orange: 'bg-orange-50 dark:bg-orange-500/10',
}[props.color]))

const iconClass = computed(() => ({
  blue: 'text-blue-500',
  green: 'text-success-500',
  purple: 'text-purple-500',
  orange: 'text-orange-500',
}[props.color]))

const hasChartData = computed(() => props.chartData.length >= 2)

const chartPoints = computed(() => {
  if (!hasChartData.value) return []

  const values = props.chartData.slice(-6)
  const min = Math.min(...values)
  const max = Math.max(...values)
  const range = max - min || 1
  const left = 2
  const right = 126
  const top = 3
  const bottom = 32

  return values.map((value, index) => ({
    x: left + (index * (right - left)) / (values.length - 1),
    y: bottom - ((value - min) / range) * (bottom - top),
    value,
    label: props.chartLabels.slice(-6)[index] ?? `Bulan ${index + 1}`,
  }))
})

const linePath = computed(() =>
  chartPoints.value
    .map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`)
    .join(' '),
)

const areaPath = computed(() => {
  const points = chartPoints.value
  if (!points.length) return ''
  return `${linePath.value} L ${points[points.length - 1].x} 35 L ${points[0].x} 35 Z`
})
</script>
