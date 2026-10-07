export interface Achievement {
  id: number
  title: string
  description: string
  category: string          // 'Competition' | 'Certification' | ...
  type: string              // 'Award' | 'Training'
  issuer: string
  date: string              // string tanggal dari API
  status: string            // 'Verified' | 'Completed'
  logo?: string | null
}

export const CATEGORY_STYLES: Record<string, string> = {
  Competition: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
  Certification: 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
}

export const TYPE_STYLES: Record<string, string> = {
  Award: 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
  Training: 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400',
}

export const STATUS_STYLES: Record<string, string> = {
  Verified: 'bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400',
  Completed: 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400',
}

export function formatAchievementDate(item: Achievement): string {
  const d = new Date(item.date)
  if (Number.isNaN(d.getTime())) return item.date
  return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
}

export function getInitials(name: string): string {
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((w) => w[0].toUpperCase())
    .join('')
}