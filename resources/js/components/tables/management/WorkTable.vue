<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import axios from '@/services/axios'
import { useAuth } from '@/composables/useAuth'

interface WorkTag {
  id_work_tag: number
  name: string
}

interface Work {
  id_work: number
  name: string
  place: string
  id_work_tag: number | null
  work_tag?: WorkTag | null
}

const props = defineProps<{
  refreshKey?: number
}>()

const emit = defineEmits<{
  (event: 'total-changed', total: number): void
  (event: 'edit', work: Work): void
  (event: 'view', work: Work): void
  (event: 'delete', work: Work): void
}>()

const { isSuperAdmin } = useAuth()

const works = ref<Work[]>([])
const loading = ref(true)

const search = ref('')
const selectedTag = ref('')
const sortBy = ref('newest')

const tags = computed(() => {
  return [
    ...new Set(
      works.value
        .map((work) => work.work_tag?.name)
        .filter((name): name is string => Boolean(name))
    ),
  ]
})

const filteredWorks = computed(() => {
  let result = [...works.value]

  if (search.value.trim()) {
    const keyword = search.value.toLowerCase()

    result = result.filter(
      (work) =>
        work.name.toLowerCase().includes(keyword) ||
        work.place?.toLowerCase().includes(keyword) ||
        work.work_tag?.name.toLowerCase().includes(keyword)
    )
  }

  if (selectedTag.value) {
    result = result.filter(
      (work) => work.work_tag?.name === selectedTag.value
    )
  }

  if (sortBy.value === 'newest') {
    result.sort((a, b) => b.id_work - a.id_work)
  }

  if (sortBy.value === 'oldest') {
    result.sort((a, b) => a.id_work - b.id_work)
  }

  if (sortBy.value === 'name') {
    result.sort((a, b) => a.name.localeCompare(b.name))
  }

  return result
})

const fetchWorks = async () => {
  loading.value = true

  try {
    const response = await axios.get('/api/works')

    const data = response.data.data ?? response.data

    works.value = data

    emit('total-changed', works.value.length)
  } catch (error) {
    console.error('Failed to fetch works:', error)
  } finally {
    loading.value = false
  }
}

const getTagClass = (tagName?: string) => {
  switch (tagName?.toUpperCase()) {
    case 'COMPANY':
      return 'bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400'

    case 'EDUCATION':
      return 'bg-cyan-50 text-cyan-600 dark:bg-cyan-500/10 dark:text-cyan-400'

    case 'ORGANIZATION':
      return 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'

    case 'COMMUNITY':
      return 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400'

    default:
      return 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
  }
}

const resetFilters = () => {
  search.value = ''
  selectedTag.value = ''
  sortBy.value = 'newest'
}

const editWork = (work: Work) => {
  emit('edit', work)
}

const viewWork = (work: Work) => {
  emit('view', work)
}

const deleteWork = (work: Work) => {
  emit('delete', work)
}

watch(
  () => props.refreshKey,
  () => {
    fetchWorks()
  }
)

onMounted(fetchWorks)
</script>