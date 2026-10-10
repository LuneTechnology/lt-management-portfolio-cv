<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import axios from '@/services/axios'

interface Category { id_category: number; name: string }
interface User { id_user: number; username: string | null; email: string; id_role?: number }
interface Work { id_work: number; name: string; place?: string }
interface PositionType { id_position_type: number; name: string }
interface WorkType { id_work_type: number; name: string }
interface Stack { id_stack: number; nama: string; stack_type?: { name: string } | null }
interface Task { id_task: number; id_experience: number; desc: string }
interface Experience {
  id_experience: number
  id_user: number
  id_work: number
  id_position_type: number
  id_work_type: number
  id_project: number
  user?: User | null
  work?: Work | null
  position_type?: PositionType | null
  work_type?: WorkType | null
  tasks?: Task[]
  stacks?: Stack[]
}
interface Project {
  id_project: number
  name: string
  date_in: string
  date_out: string | null
  id_category: number
  id_work?: number
  category?: Category | null
  work?: Work | null
  experiences?: any[]
}

interface ProjectImage {
  id_project_image: number
  id_project: number
  path: string
  image_url: string
  alt_text: string | null
  is_primary: boolean
  sort_order: number
}

interface ProjectLink {
  id_link: number
  id_project: number
  name: string
  link: string
  type: 'live_demo' | 'source_code' | 'video' | 'documentation' | 'article' | 'other'
  sort_order: number
}

interface PendingImage {
  file: File
  previewUrl: string
}

const props = defineProps<{ show: boolean; project?: Project | null; categories: Category[] }>()
const emit = defineEmits<{ (e: 'close'): void; (e: 'saved'): void }>()

const activeTab = ref('information')
const isSaving = ref(false)
const errorMessage = ref('')
const form = ref({ name: '', id_category: '', id_work: '', date_in: '', date_out: '' })

// Project image gallery state
const images = ref<ProjectImage[]>([])
const imageInput = ref<HTMLInputElement | null>(null)
const pendingImages = ref<PendingImage[]>([])
const isUploadingImages = ref(false)
const imageError = ref('')

// Project link state
const links = ref<ProjectLink[]>([])
const showLinkForm = ref(false)
const editingLinkId = ref<number | null>(null)
const isSavingLink = ref(false)
const linkError = ref('')
const linkForm = ref({ name: '', link: '', type: 'other' as ProjectLink['type'], sort_order: 0 })
const linkTypes: Array<{ value: ProjectLink['type']; label: string }> = [
  { value: 'live_demo', label: 'Live Demo' },
  { value: 'source_code', label: 'Source Code' },
  { value: 'video', label: 'Video Demo' },
  { value: 'documentation', label: 'Documentation' },
  { value: 'article', label: 'Article / Publication' },
  { value: 'other', label: 'Other' },
]

const pendingDelete = ref<{ type: 'contributor' | 'image' | 'link'; id: number; label: string } | null>(null)
const isDeletingItem = ref(false)
const deleteError = ref('')

const tabs = [
  { id: 'information', label: 'Project Information', icon: '▣' },
  { id: 'gallery', label: 'Gallery', icon: '▧' },
  { id: 'contributors', label: 'Contributors', icon: '♧' },
  { id: 'links', label: 'Links', icon: '▤' },
]

const contributors = ref<Experience[]>([])
const users = ref<User[]>([])
const works = ref<Work[]>([])
const positionTypes = ref<PositionType[]>([])
const workTypes = ref<WorkType[]>([])
const stacks = ref<Stack[]>([])
const currentUser = ref<User | null>(null)
const isSuperAdmin = computed(() => Number(currentUser.value?.id_role) === 2)
const isLoadingContributors = ref(false)
const isSavingContributor = ref(false)
const showContributorForm = ref(false)
const editingExperienceId = ref<number | null>(null)
const contributorError = ref('')
const contributorForm = ref({ id_user: '', id_position_type: '', id_work_type: '', tasks: [''], stack_ids: [] as string[] })

const emptyContributorForm = () => ({ id_user: '', id_position_type: '', id_work_type: '', tasks: [''], stack_ids: [] as string[] })

const getErrorMessage = (error: any, fallback: string) => {
  if (error.response?.data?.errors) return Object.values(error.response.data.errors).flat().join(' ')
  return error.response?.data?.message || fallback
}

const resetContributorForm = () => {
  contributorForm.value = emptyContributorForm()
  editingExperienceId.value = null
  showContributorForm.value = false
  contributorError.value = ''
}

const resetForm = () => {
  form.value = { name: '', id_category: '', id_work: '', date_in: '', date_out: '' }
  errorMessage.value = ''
  activeTab.value = 'information'
  contributors.value = []
  resetContributorForm()
}

const loadProject = () => {
  if (!props.project) {
    resetForm()
    return
  }
  form.value = {
    name: props.project.name ?? '',
    id_category: String(props.project.id_category ?? ''),
    id_work: String(props.project.id_work ?? props.project.work?.id_work ?? ''),
    date_in: props.project.date_in ? props.project.date_in.substring(0, 10) : '',
    date_out: props.project.date_out ? props.project.date_out.substring(0, 10) : '',
  }
  errorMessage.value = ''
  activeTab.value = 'information'
  resetContributorForm()
}

const unwrapList = <T,>(data: any): T[] => Array.isArray(data) ? data : (Array.isArray(data?.data) ? data.data : [])

const loadContributorOptions = async () => {
  try {
    const meResponse = await axios.get('/api/me')
    currentUser.value = meResponse.data?.user ?? meResponse.data?.data?.user ?? meResponse.data?.data ?? null

    const requests = await Promise.all([
      axios.get('/api/works'),
      axios.get('/api/position-types'),
      axios.get('/api/work-types'),
      axios.get('/api/stacks'),
    ])
    works.value = unwrapList<Work>(requests[0].data)
    positionTypes.value = unwrapList<PositionType>(requests[1].data)
    workTypes.value = unwrapList<WorkType>(requests[2].data)
    stacks.value = unwrapList<Stack>(requests[3].data)

    if (isSuperAdmin.value) {
      const usersResponse = await axios.get('/api/users')
      users.value = unwrapList<User>(usersResponse.data)
    } else {
      users.value = []
    }
  } catch (error: any) {
    const message = getErrorMessage(error, 'Gagal memuat pilihan form.')
    errorMessage.value = message
    contributorError.value = message
  }
}

const loadContributors = async () => {
  const projectId = props.project?.id_project
  if (!projectId) { contributors.value = []; return }

  isLoadingContributors.value = true
  contributorError.value = ''
  try {
    const response = await axios.get(`/api/projects/${projectId}`)
    const projectData = response.data?.data ?? response.data
    contributors.value = Array.isArray(projectData?.experiences) ? projectData.experiences : []
  } catch (error: any) {
    contributorError.value = getErrorMessage(error, 'Gagal memuat contributor.')
  } finally {
    isLoadingContributors.value = false
  }
}

const startAddContributor = async () => {
  if (!props.project?.id_project) {
    contributorError.value = 'Simpan Project terlebih dahulu sebelum menambahkan Contributor.'
    return
  }
  resetContributorForm()
  showContributorForm.value = true
  await loadContributorOptions()
}

const startEditContributor = async (experience: Experience) => {
  await loadContributorOptions()
  editingExperienceId.value = experience.id_experience
  contributorForm.value = {
    id_user: String(experience.id_user ?? ''),
    id_position_type: String(experience.id_position_type ?? ''),
    id_work_type: String(experience.id_work_type ?? ''),
    tasks: experience.tasks?.length ? experience.tasks.map((task) => task.desc) : [''],
    stack_ids: experience.stacks?.map((stack) => String(stack.id_stack)) ?? [],
  }
  contributorError.value = ''
  showContributorForm.value = true
}

const addTaskInput = () => contributorForm.value.tasks.push('')
const removeTaskInput = (index: number) => {
  contributorForm.value.tasks.splice(index, 1)
  if (!contributorForm.value.tasks.length) contributorForm.value.tasks.push('')
}

const saveContributor = async () => {
  const projectId = props.project?.id_project
  if (!projectId) { contributorError.value = 'Simpan Project terlebih dahulu.'; return }
  if (!contributorForm.value.id_position_type || !contributorForm.value.id_work_type) {
    contributorError.value = 'Position Type dan Work Type wajib dipilih.'
    return
  }
  if (isSuperAdmin.value && !contributorForm.value.id_user) {
    contributorError.value = 'Pilih User yang akan menjadi Contributor.'
    return
  }

  isSavingContributor.value = true
  contributorError.value = ''
  const payload: Record<string, unknown> = {
    id_project: Number(projectId),
    id_position_type: Number(contributorForm.value.id_position_type),
    id_work_type: Number(contributorForm.value.id_work_type),
    tasks: contributorForm.value.tasks.map((task) => task.trim()).filter(Boolean),
    stack_ids: contributorForm.value.stack_ids.map(Number),
  }
  if (isSuperAdmin.value) payload.id_user = Number(contributorForm.value.id_user)

  try {
    if (editingExperienceId.value) {
      await axios.put(`/api/experiences/${editingExperienceId.value}`, payload)
    } else {
      await axios.post('/api/experiences', payload)
    }
    resetContributorForm()
    await loadContributors()
  } catch (error: any) {
    contributorError.value = getErrorMessage(error, 'Gagal menyimpan Contributor.')
  } finally {
    isSavingContributor.value = false
  }
}

const deleteContributor = (experience: Experience) => {
  pendingDelete.value = { type: 'contributor', id: experience.id_experience, label: experience.user?.username || experience.user?.email || 'Contributor' }
  deleteError.value = ''
}

const clearPendingImages = () => {
  pendingImages.value.forEach((item) => URL.revokeObjectURL(item.previewUrl))
  pendingImages.value = []
  if (imageInput.value) imageInput.value.value = ''
}

const loadImages = async () => {
  const projectId = props.project?.id_project
  if (!projectId) { images.value = []; return }
  imageError.value = ''
  try {
    const response = await axios.get(`/api/projects/${projectId}/images`)
    images.value = unwrapList<ProjectImage>(response.data)
  } catch (error: any) {
    imageError.value = getErrorMessage(error, 'Gagal memuat gambar project.')
  }
}

const onStoredImageError = (event: Event) => {
  const image = event.target as HTMLImageElement
  image.alt = 'Image could not be loaded. Check storage:link and APP_URL.'
  image.classList.add('hidden')
}

const openImagePicker = () => imageInput.value?.click()
const openProjectLink = (url: string) => window.open(url, '_blank', 'noopener,noreferrer')

const onImageFilesSelected = (event: Event) => {
  const input = event.target as HTMLInputElement
  const files = Array.from(input.files ?? [])
  imageError.value = ''

  for (const file of files) {
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
      imageError.value = `${file.name}: gunakan JPG, PNG, atau WebP.`
      continue
    }
    if (file.size > 5 * 1024 * 1024) {
      imageError.value = `${file.name}: ukuran maksimal 5 MB.`
      continue
    }
    pendingImages.value.push({ file, previewUrl: URL.createObjectURL(file) })
  }
  input.value = ''
}

const removePendingImage = (index: number) => {
  const [removed] = pendingImages.value.splice(index, 1)
  if (removed) URL.revokeObjectURL(removed.previewUrl)
}

const uploadImages = async () => {
  const projectId = props.project?.id_project
  if (!projectId) {
    imageError.value = 'Simpan Project terlebih dahulu, lalu buka Edit untuk mengunggah gambar.'
    return
  }
  if (!pendingImages.value.length) return

  isUploadingImages.value = true
  imageError.value = ''
  const queue = [...pendingImages.value]
  try {
    for (let index = 0; index < queue.length; index++) {
      const item = queue[index]
      const data = new FormData()
      data.append('image', item.file)
      data.append('alt_text', item.file.name.replace(/\.[^.]+$/, '').slice(0, 255))
      data.append('is_primary', images.value.length === 0 && index === 0 ? '1' : '0')
      data.append('sort_order', String(images.value.length + index))
      await axios.post(`/api/projects/${projectId}/images`, data)
    }
    clearPendingImages()
    await loadImages()
  } catch (error: any) {
    imageError.value = getErrorMessage(error, 'Gagal mengunggah gambar. Gambar yang berhasil diunggah tetap tersimpan.')
    await loadImages()
  } finally {
    isUploadingImages.value = false
  }
}

const setPrimaryImage = async (image: ProjectImage) => {
  imageError.value = ''
  try {
    await axios.put(`/api/project-images/${image.id_project_image}`, { is_primary: true })
    await loadImages()
  } catch (error: any) {
    imageError.value = getErrorMessage(error, 'Gagal mengatur thumbnail utama.')
  }
}

const deleteImage = (image: ProjectImage) => {
  pendingDelete.value = { type: 'image', id: image.id_project_image, label: image.alt_text || 'Project image' }
  deleteError.value = ''
}

const moveImage = async (image: ProjectImage, direction: -1 | 1) => {
  const ordered = [...images.value].sort((a, b) => a.sort_order - b.sort_order || a.id_project_image - b.id_project_image)
  const index = ordered.findIndex((item) => item.id_project_image === image.id_project_image)
  const target = ordered[index + direction]
  if (!target) return

  imageError.value = ''
  try {
    await Promise.all([
      axios.put(`/api/project-images/${image.id_project_image}`, { sort_order: target.sort_order }),
      axios.put(`/api/project-images/${target.id_project_image}`, { sort_order: image.sort_order }),
    ])
    await loadImages()
  } catch (error: any) {
    imageError.value = getErrorMessage(error, 'Gagal mengubah urutan gambar.')
  }
}

const loadLinks = async () => {
  const projectId = props.project?.id_project
  if (!projectId) { links.value = []; return }
  linkError.value = ''
  try {
    const response = await axios.get('/api/link', { params: { id_project: projectId } })
    links.value = unwrapList<ProjectLink>(response.data)
  } catch (error: any) {
    linkError.value = getErrorMessage(error, 'Gagal memuat link project.')
  }
}

const resetLinkForm = () => {
  linkForm.value = { name: '', link: '', type: 'other', sort_order: links.value.length }
  editingLinkId.value = null
  showLinkForm.value = false
  linkError.value = ''
}

const openLinkForm = (link?: ProjectLink) => {
  linkError.value = ''
  if (link) {
    editingLinkId.value = link.id_link
    linkForm.value = { name: link.name, link: link.link, type: link.type || 'other', sort_order: link.sort_order ?? 0 }
  } else {
    editingLinkId.value = null
    linkForm.value = { name: '', link: '', type: 'other', sort_order: links.value.length }
  }
  showLinkForm.value = true
}

const saveLink = async () => {
  const projectId = props.project?.id_project
  if (!projectId) {
    linkError.value = 'Simpan Project terlebih dahulu, lalu buka Edit untuk mengelola link.'
    return
  }
  if (!linkForm.value.name.trim() || !linkForm.value.link.trim()) {
    linkError.value = 'Label dan URL wajib diisi.'
    return
  }
  if (!/^https?:\/\//i.test(linkForm.value.link.trim())) {
    linkError.value = 'URL harus diawali http:// atau https://.'
    return
  }

  isSavingLink.value = true
  linkError.value = ''
  const payload = {
    id_project: projectId,
    name: linkForm.value.name.trim(),
    link: linkForm.value.link.trim(),
    type: linkForm.value.type,
    sort_order: Number(linkForm.value.sort_order) || 0,
  }
  try {
    if (editingLinkId.value) {
      await axios.put(`/api/link/${editingLinkId.value}`, payload)
    } else {
      await axios.post('/api/link', payload)
    }
    resetLinkForm()
    await loadLinks()
  } catch (error: any) {
    linkError.value = getErrorMessage(error, 'Gagal menyimpan link project.')
  } finally {
    isSavingLink.value = false
  }
}

const deleteLink = (link: ProjectLink) => {
  pendingDelete.value = { type: 'link', id: link.id_link, label: link.name }
  deleteError.value = ''
}

const confirmDelete = async () => {
  if (!pendingDelete.value) return
  isDeletingItem.value = true
  deleteError.value = ''
  const item = pendingDelete.value
  try {
    if (item.type === 'contributor') { await axios.delete(`/api/experiences/${item.id}`); await loadContributors() }
    else if (item.type === 'image') { await axios.delete(`/api/project-images/${item.id}`); await loadImages() }
    else { await axios.delete(`/api/link/${item.id}`); await loadLinks() }
    pendingDelete.value = null
  } catch (error: any) {
    deleteError.value = getErrorMessage(error, 'Gagal menghapus item.')
  } finally { isDeletingItem.value = false }
}

const cancelDelete = () => {
  if (isDeletingItem.value) return
  pendingDelete.value = null
  deleteError.value = ''
}

const linkTypeLabel = (type: ProjectLink['type']) =>
  linkTypes.find((item) => item.value === type)?.label ?? 'Other'

const saveProject = async () => {
  isSaving.value = true
  errorMessage.value = ''
  try {
    if (!form.value.name.trim()) throw new Error('Nama Project wajib diisi.')
    if (!form.value.id_category) throw new Error('Category wajib dipilih.')
    if (!form.value.id_work) throw new Error('Work wajib dipilih.')
    if (!form.value.date_in) throw new Error('Start Date wajib diisi.')

    const payload = {
      name: form.value.name.trim(),
      id_category: Number(form.value.id_category),
      id_work: Number(form.value.id_work),
      date_in: form.value.date_in,
      date_out: form.value.date_out || null,
    }

    if (props.project) await axios.put(`/api/projects/${props.project.id_project}`, payload)
    else await axios.post('/api/projects', payload)
    emit('saved')
  } catch (error: any) {
    errorMessage.value = error.message && !error.response ? error.message : getErrorMessage(error, 'Gagal menyimpan Project.')
  } finally {
    isSaving.value = false
  }
}

const close = () => {
  if (isSaving.value || isSavingContributor.value || isDeletingItem.value) return
  pendingDelete.value = null
  clearPendingImages()
  emit('close')
}
const nextTab = () => {
  const index = tabs.findIndex((tab) => tab.id === activeTab.value)
  if (index >= 0 && index < tabs.length - 1) activeTab.value = tabs[index + 1].id
}
const previousTab = () => {
  const index = tabs.findIndex((tab) => tab.id === activeTab.value)
  if (index > 0) activeTab.value = tabs[index - 1].id
}

watch(() => [props.show, props.project?.id_project] as const, async ([isVisible]) => {
  if (!isVisible) {
    clearPendingImages()
    return
  }
  loadProject()
  images.value = []
  links.value = []
  resetLinkForm()
  await loadContributorOptions()
  if (props.project?.id_project) {
    await Promise.all([loadContributors(), loadImages(), loadLinks()])
  }
})
watch(activeTab, async (tab) => {
  if (!props.show || !props.project?.id_project) return
  if (tab === 'contributors') await loadContributors()
  if (tab === 'gallery') await loadImages()
  if (tab === 'links') await loadLinks()
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-[99999] overflow-y-auto bg-black/50 p-4"
    >
      <div
        class="mx-auto my-6 w-full max-w-6xl overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
      >

        <!-- HEADER -->
        <div
          class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-gray-800"
        >
          <div>
            <h2
              class="text-xl font-semibold text-gray-800 dark:text-white/90"
            >
              {{
                project
                  ? 'Edit Project'
                  : 'Add Project'
              }}
            </h2>

            <p
              class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
              Manage project information,
              contributors, tasks, stacks,
              and media.
            </p>
          </div>

          <button
            type="button"
            title="Close"
            class="text-xl text-gray-400 transition hover:text-gray-700 dark:hover:text-white"
            @click="close"
          >
            ×
          </button>
        </div>

        <!-- TABS -->
        <div
          class="border-b border-gray-200 px-6 dark:border-gray-800"
        >
          <div
            class="flex overflow-x-auto"
          >
            <button
              v-for="tab in tabs"
              :key="tab.id"
              type="button"
              class="relative flex shrink-0 items-center gap-2 px-5 py-4 text-sm font-medium transition"
              :class="
                activeTab === tab.id
                  ? 'text-brand-600 dark:text-brand-400'
                  : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
              "
              @click="
                activeTab = tab.id
              "
            >
              <span>
                {{ tab.icon }}
              </span>

              {{ tab.label }}

              <span
                v-if="
                  activeTab === tab.id
                "
                class="absolute bottom-0 left-0 right-0 h-0.5 bg-brand-500"
              />
            </button>
          </div>
        </div>

        <!-- BODY -->
        <div class="p-6">

          <!-- ERROR -->
          <div
            v-if="errorMessage"
            class="mb-6 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400"
          >
            {{ errorMessage }}
          </div>

          <!-- ================================================= -->
          <!-- PROJECT INFORMATION -->
          <!-- ================================================= -->

          <div
            v-if="
              activeTab === 'information'
            "
            class="space-y-6"
          >

            <div
              class="grid grid-cols-1 gap-5 lg:grid-cols-2"
            >

              <!-- Project Name -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  Project Name
                  <span class="text-error-500">*</span>
                </label>

                <input
                  v-model="form.name"
                  type="text"
                  maxlength="50"
                  placeholder="Enter project name"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none transition placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"
                />
              </div>

              <!-- Category -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  Category
                  <span class="text-error-500">*</span>
                </label>

                <select
                  v-model="form.id_category"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                  <option value="">
                    Select category
                  </option>

                  <option
                    v-for="category in categories"
                    :key="
                      category.id_category
                    "
                    :value="
                      String(
                        category.id_category
                      )
                    "
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>

              <!-- Work -->
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Work / Company <span class="text-error-500">*</span>
                </label>
                <select
                  v-model="form.id_work"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 outline-none transition focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                >
                  <option value="">Pilih Work / Company</option>
                  <option v-for="work in works" :key="work.id_work" :value="String(work.id_work)">
                    {{ work.name }}{{ work.place ? ` — ${work.place}` : '' }}
                  </option>
                </select>
              </div>

              <!-- Start -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  Start Date
                  <span class="text-error-500">*</span>
                </label>

                <input
                  v-model="form.date_in"
                  type="date"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"
                />
              </div>

              <!-- End -->
              <div>
                <label
                  class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                  End Date
                </label>

                <input
                  v-model="form.date_out"
                  type="date"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 outline-none focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90"
                />
              </div>

            </div>

          </div>

          <!-- GALLERY -->
          <div v-if="activeTab === 'gallery'" class="space-y-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div><h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Project Gallery</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Images are stored in Laravel public storage and linked from the database.</p></div>
              <div class="flex gap-2"><input ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" @change="onImageFilesSelected" /><button type="button" :disabled="!project?.id_project || isUploadingImages" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]" @click="openImagePicker">Select Images</button><button type="button" :disabled="!pendingImages.length || isUploadingImages || !project?.id_project" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-50" @click="uploadImages">{{ isUploadingImages ? 'Uploading...' : `Upload ${pendingImages.length || ''} Images` }}</button></div>
            </div>
            <p v-if="!project?.id_project" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">Save the Project first, then reopen Edit Project to upload images.</p>
            <p v-if="imageError" class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400">{{ imageError }}</p>
            <div v-if="pendingImages.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"><div v-for="(item, index) in pendingImages" :key="item.previewUrl" class="overflow-hidden rounded-xl border border-dashed border-brand-300 dark:border-brand-500/40"><div class="relative"><img :src="item.previewUrl" :alt="item.file.name" class="h-32 w-full object-cover" /><div v-if="isUploadingImages" class="absolute inset-0 flex items-center justify-center bg-black/45"><span class="rounded-full bg-white/95 px-3 py-1.5 text-xs font-semibold text-gray-800">Uploading…</span></div><span v-else class="absolute left-2 top-2 rounded-full bg-gray-900/75 px-2 py-1 text-[10px] font-semibold text-white">Ready to upload</span></div><div class="flex items-center justify-between gap-2 p-3"><p class="truncate text-xs text-gray-600 dark:text-gray-300">{{ item.file.name }}</p><button type="button" :disabled="isUploadingImages" class="text-xs font-medium text-error-500 disabled:opacity-40" @click="removePendingImage(index)">Remove</button></div></div></div>
            <div v-if="images.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"><div v-for="(image, index) in images" :key="image.id_project_image" class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.02]"><div class="relative"><img :src="image.image_url" :alt="image.alt_text || 'Project image'" class="h-36 w-full object-cover" @error="onStoredImageError" /><span v-if="image.is_primary" class="absolute left-2 top-2 rounded-full bg-brand-500 px-2 py-1 text-[10px] font-semibold text-white">Thumbnail</span></div><div class="space-y-3 p-3"><p class="truncate text-xs font-medium text-gray-700 dark:text-gray-300">{{ image.alt_text || `Image ${index + 1}` }}</p><div class="flex flex-wrap gap-1.5"><button v-if="!image.is_primary" type="button" class="rounded-md border border-gray-200 px-2 py-1 text-[11px] font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300" @click="setPrimaryImage(image)">Set thumbnail</button><button type="button" :disabled="index === 0" class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 disabled:opacity-40 dark:border-gray-700 dark:text-gray-300" @click="moveImage(image, -1)">↑</button><button type="button" :disabled="index === images.length - 1" class="rounded-md border border-gray-200 px-2 py-1 text-[11px] text-gray-600 disabled:opacity-40 dark:border-gray-700 dark:text-gray-300" @click="moveImage(image, 1)">↓</button><button type="button" class="rounded-md bg-red-50 px-2 py-1 text-[11px] font-medium text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400" @click="deleteImage(image)">Delete</button></div></div></div></div>
            <div v-if="!images.length && !pendingImages.length" class="flex min-h-[180px] items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700"><div class="text-center"><div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-2xl dark:bg-gray-800">🖼</div><h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">No images yet</h4><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select images to preview them before uploading.</p></div></div>
          </div>

          <!-- ================================================= -->
          <!-- CONTRIBUTORS -->
          <!-- ================================================= -->

            <div
            v-if="activeTab === 'contributors'"
            class="space-y-5"
            >
            <div class="flex items-center justify-between">
                <div>
                <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
                    Contributors
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage people involved in this project.
                </p>
                </div>

                <button
                type="button"
                :disabled="!project?.id_project"
                class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
                @click="startAddContributor"
                >
                + Add Contributor
                </button>
            </div>

            <!-- Contributor error -->
            <div
                v-if="contributorError"
                class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400"
            >
                {{ contributorError }}
            </div>

            <!-- Add/Edit contributor form -->
            <div v-if="showContributorForm" class="rounded-xl border border-brand-200 bg-gray-50 p-5 dark:border-brand-500/30 dark:bg-gray-800/40">
              <div class="mb-4 flex items-center justify-between">
                <h4 class="font-semibold text-gray-800 dark:text-white/90">
                  {{ editingExperienceId ? 'Edit Contributor' : 'Add Contributor' }}
                </h4>
                <button type="button" class="text-sm text-gray-500 hover:text-gray-800 dark:hover:text-white" :disabled="isSavingContributor" @click="resetContributorForm">Cancel</button>
              </div>

              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div v-if="isSuperAdmin">
                  <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">User <span class="text-error-500">*</span></label>
                  <select v-model="contributorForm.id_user" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Pilih User</option>
                    <option v-for="user in users" :key="user.id_user" :value="String(user.id_user)">{{ user.username || user.email }} — {{ user.email }}</option>
                  </select>
                </div>
                <div v-else class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                  <p class="text-xs text-gray-500 dark:text-gray-400">Contributor akan disimpan untuk akun login</p>
                  <p class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90">{{ currentUser?.username || currentUser?.email || 'Akun saat ini' }}</p>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Position Type <span class="text-error-500">*</span></label>
                  <select v-model="contributorForm.id_position_type" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Pilih Position Type</option>
                    <option v-for="position in positionTypes" :key="position.id_position_type" :value="String(position.id_position_type)">{{ position.name }}</option>
                  </select>
                </div>

                <div>
                  <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Work Type <span class="text-error-500">*</span></label>
                  <select v-model="contributorForm.id_work_type" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Pilih Work Type</option>
                    <option v-for="workType in workTypes" :key="workType.id_work_type" :value="String(workType.id_work_type)">{{ workType.name }}</option>
                  </select>
                </div>
              </div>

              <div class="mt-5">
                <div class="mb-2 flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tasks</label>
                  <button type="button" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400" @click="addTaskInput">+ Tambah Task</button>
                </div>
                <div v-for="(task, index) in contributorForm.tasks" :key="index" class="mb-2 flex gap-2">
                  <input v-model="contributorForm.tasks[index]" type="text" maxlength="255" :placeholder="`Task ${index + 1}`" class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                  <button type="button" class="rounded-lg border border-gray-300 px-3 text-sm text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800" :disabled="contributorForm.tasks.length === 1" @click="removeTaskInput(index)">Hapus</button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Maksimal 255 karakter per task. Baris kosong akan diabaikan.</p>
              </div>

              <div class="mt-5">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Technology Stack</label>
                <div v-if="stacks.length" class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                  <label v-for="stack in stacks" :key="stack.id_stack" class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <input v-model="contributorForm.stack_ids" type="checkbox" :value="String(stack.id_stack)" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                    <span>{{ stack.nama }}</span>
                    <span v-if="stack.stack_type?.name" class="ml-auto text-xs text-gray-400">{{ stack.stack_type.name }}</span>
                  </label>
                </div>
                <p v-else class="text-sm text-gray-500 dark:text-gray-400">Belum ada Stack pada master data.</p>
              </div>

              <div class="mt-5 flex justify-end gap-3">
                <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800" :disabled="isSavingContributor" @click="resetContributorForm">Cancel</button>
                <button type="button" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50" :disabled="isSavingContributor" @click="saveContributor">{{ isSavingContributor ? 'Saving...' : 'Save Contributor' }}</button>
              </div>
            </div>

            <!-- Loading -->
            <div
                v-if="isLoadingContributors"
                class="py-10 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                Loading contributors...
            </div>

            <!-- Contributor list -->
            <div
                v-else-if="contributors.length"
                class="space-y-4"
            >
                <div
                v-for="experience in contributors"
                :key="experience.id_experience"
                class="rounded-xl border border-gray-200 p-5 dark:border-gray-800"
                >
                <div class="flex items-start justify-between gap-4">
                    <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90">
                        {{
                        experience.user?.username ||
                        experience.user?.email ||
                        'Unknown User'
                        }}
                    </h4>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ experience.user?.email || '-' }}
                    </p>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                        {{ experience.work?.name || 'Work not available' }}
                        <span v-if="experience.position_type?.name">
                        · {{ experience.position_type.name }}
                        </span>
                        <span v-if="experience.work_type?.name">
                        · {{ experience.work_type.name }}
                        </span>
                    </p>
                    <ul v-if="experience.tasks?.length" class="mt-3 list-disc space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                      <li v-for="task in experience.tasks" :key="task.id_task">{{ task.desc }}</li>
                    </ul>
                    <div v-if="experience.stacks?.length" class="mt-3 flex flex-wrap gap-2">
                      <span v-for="stack in experience.stacks" :key="stack.id_stack" class="rounded-full bg-gray-100 px-2.5 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ stack.nama }}</span>
                    </div>
                    </div>

                    <div class="flex shrink-0 gap-2">
                    <button
                        type="button"
                        title="Edit contributor"
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 transition hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400"
                        @click="startEditContributor(experience)"
                    >
                        ✎
                    </button>

                    <button
                        type="button"
                        title="Delete contributor"
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400"
                        @click="deleteContributor(experience)"
                    >
                        🗑
                    </button>
                    </div>
                </div>
                </div>
            </div>

            <!-- Empty state -->
            <div
                v-else
                class="flex min-h-[220px] items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700"
            >
                <div class="text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-gray-100 text-2xl dark:bg-gray-800">
                    👥
                </div>

                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    No contributors yet
                </h4>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add people who worked on this project.
                </p>
                </div>
            </div>
            
        </div>
          <!-- LINKS -->
          <div v-if="activeTab === 'links'" class="space-y-5">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Project Links</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Live demo, source code, video, documentation, and other links.</p></div><button type="button" :disabled="!project?.id_project" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-50" @click="openLinkForm()">+ Add Link</button></div>
            <p v-if="!project?.id_project" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-300">Save the Project first, then reopen Edit Project to manage links.</p>
            <p v-if="linkError" class="rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400">{{ linkError }}</p>
            <div v-if="showLinkForm" class="space-y-4 rounded-xl border border-brand-200 bg-gray-50 p-5 dark:border-brand-500/30 dark:bg-gray-800/40"><div class="flex items-center justify-between"><h4 class="font-semibold text-gray-800 dark:text-white/90">{{ editingLinkId ? 'Edit Link' : 'Add Link' }}</h4><button type="button" class="text-sm text-gray-500 hover:text-gray-800 dark:hover:text-white" :disabled="isSavingLink" @click="resetLinkForm">Cancel</button></div><div class="grid grid-cols-1 gap-4 md:grid-cols-2"><div><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Label</label><input v-model.trim="linkForm.name" maxlength="100" placeholder="GitHub Repository" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></div><div><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label><select v-model="linkForm.type" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"><option v-for="type in linkTypes" :key="type.value" :value="type.value">{{ type.label }}</option></select></div><div><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">URL</label><input v-model.trim="linkForm.link" type="url" maxlength="2048" placeholder="https://..." class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></div><div><label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Display Order</label><input v-model.number="linkForm.sort_order" type="number" min="0" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-800 outline-none focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white" /></div></div><div class="flex justify-end"><button type="button" :disabled="isSavingLink" class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white disabled:opacity-50" @click="saveLink">{{ isSavingLink ? 'Saving...' : 'Save Link' }}</button></div></div>
            <div v-if="links.length" class="space-y-3"><div v-for="link in links" :key="link.id_link" class="flex flex-col gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between"><div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ link.name }}</p><span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ linkTypeLabel(link.type) }}</span></div><a :href="link.link" target="_blank" rel="noopener noreferrer" class="mt-1 block truncate text-sm text-brand-600 hover:underline dark:text-brand-400">{{ link.link }}</a></div><div class="flex shrink-0 gap-2"><button type="button" title="Open link" class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300" @click="openProjectLink(link.link)">↗</button><button type="button" title="Edit link" class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-500/10 dark:text-blue-400" @click="openLinkForm(link)">✎</button><button type="button" title="Delete link" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400" @click="deleteLink(link)">🗑</button></div></div></div>
            <div v-else-if="!showLinkForm" class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700"><p class="text-sm text-gray-500 dark:text-gray-400">No project links yet.</p></div>
          </div>

        </div>

        <!-- FOOTER -->
        <div
          class="flex items-center justify-between border-t border-gray-200 px-6 py-4 dark:border-gray-800"
        >

          <button
            v-if="
              activeTab !== 'information'
            "
            type="button"
            class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
            @click="previousTab"
          >
            ← Previous
          </button>

          <div
            v-else
          />

          <div
            class="flex gap-3"
          >

            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
              :disabled="isSaving"
              @click="close"
            >
              Cancel
            </button>

            <button
              v-if="
                activeTab !== 'links'
              "
              type="button"
              class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600"
              @click="nextTab"
            >
              Next →
            </button>

            <button
              v-else
              type="button"
              class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-60"
              :disabled="isSaving"
              @click="saveProject"
            >
              {{
                isSaving
                  ? 'Saving...'
                  : 'Save'
              }}
            </button>

          </div>

        </div>

      </div>
    </div>
  </Teleport>

  <Teleport to="body">
    <div v-if="pendingDelete" class="fixed inset-0 z-[100100] flex items-center justify-center bg-black/60 px-4 py-6" @click.self="cancelDelete">
      <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-start gap-4 border-b border-gray-200 px-6 py-5 dark:border-gray-800"><div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-lg font-bold text-red-600 dark:bg-red-500/10 dark:text-red-400">!</div><div class="min-w-0"><h3 class="text-base font-semibold text-gray-900 dark:text-white">Confirm deletion</h3><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete <span class="font-semibold text-gray-700 dark:text-gray-200">{{ pendingDelete.label }}</span>? This action cannot be undone.</p></div><button type="button" class="ml-auto text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" :disabled="isDeletingItem" @click="cancelDelete">✕</button></div>
        <p v-if="deleteError" class="mx-6 mt-4 rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400">{{ deleteError }}</p>
        <div class="flex justify-end gap-3 px-6 py-4"><button type="button" :disabled="isDeletingItem" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/[0.03]" @click="cancelDelete">Cancel</button><button type="button" :disabled="isDeletingItem" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-50" @click="confirmDelete">{{ isDeletingItem ? 'Deleting...' : 'Delete' }}</button></div>
      </div>
    </div>
  </Teleport>
</template>