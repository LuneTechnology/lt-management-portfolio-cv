<script setup lang="ts">
import {
  computed,
  onMounted,
  ref,
} from 'vue'

import axios from '@/services/axios'

import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ManagementSummaryCard from '@/components/management/ManagementSummaryCard.vue'
import ProjectTable from '@/components/tables/management/ProjectTable.vue'
import ProjectModal from '@/components/management/project/ProjectModal.vue'

import { DocsIcon } from '@/icons'
import { useAuth } from '@/composables/useAuth'

/*
|--------------------------------------------------------------------------
| Interfaces
|--------------------------------------------------------------------------
*/

interface Category {
  id_category: number
  name: string
}

interface User {
  id_user: number
  username: string | null
  email: string
}

interface PositionType {
  id_position_type: number
  name: string
}

interface WorkType {
  id_work_type: number
  name: string
}

interface Experience {
  id_experience: number
  id_user: number
  id_project: number

  user?: User | null

  position_type?: PositionType | null

  work_type?: WorkType | null
}

interface ProjectImage {
  id_project_image: number
  image_url: string
  path?: string
  alt_text?: string | null
  is_primary: boolean
  sort_order: number
}

interface ProjectLink {
  id_link: number
  name: string
  link: string
  type?: string | null
  sort_order?: number
}

interface Project {
  id_project: number
  name: string
  date_in: string
  date_out: string | null
  id_category: number

  category?: Category | null
  work?: { id_work: number; name: string; place?: string; image_url?: string | null } | null
  images?: ProjectImage[]
  links?: ProjectLink[]
  experiences?: Experience[]
}

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

const { isSuperAdmin } = useAuth()

/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

const currentPageTitle = 'Project'

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const totalProject = ref(0)

const totalContributors = ref(0)

const refreshKey = ref(0)

const categories = ref<Category[]>([])

const projects = ref<Project[]>([])

/*
|--------------------------------------------------------------------------
| Modal State
|--------------------------------------------------------------------------
*/

const showProjectModal = ref(false)

const showDeleteModal = ref(false)

const showViewModal = ref(false)

const selectedProject =
  ref<Project | null>(null)

const primaryProjectImage = computed(() => {
  const images = selectedProject.value?.images ?? []
  return images.find((image) => image.is_primary) ?? images[0] ?? null
})

const additionalProjectImages = computed(() => {
  const images = selectedProject.value?.images ?? []
  const primaryId = primaryProjectImage.value?.id_project_image
  return images.filter((image) => image.id_project_image !== primaryId)
})

const projectLinkTypeLabel = (type?: string | null) => {
  const labels: Record<string, string> = {
    live_demo: 'Live Demo',
    source_code: 'Source Code',
    video: 'Video Demo',
    documentation: 'Documentation',
    article: 'Article / Publication',
    other: 'Other',
  }
  return type ? labels[type] ?? type : 'Project Link'
}

const openProjectLink = (url: string) => {
  window.open(url, '_blank', 'noopener,noreferrer')
}

const hideBrokenImage = (event: Event) => {
  const target = event.target
  if (target instanceof HTMLImageElement) target.classList.add('hidden')
}

/*
|--------------------------------------------------------------------------
| Loading State
|--------------------------------------------------------------------------
*/

const loadingCategories = ref(false)

const loadingView = ref(false)

const deleting = ref(false)

/*
|--------------------------------------------------------------------------
| Error
|--------------------------------------------------------------------------
*/

const errorMessage = ref('')

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const contributorCount = computed(() => {
  const uniqueUsers = new Set<number>()

  projects.value.forEach(
    (project) => {
      project.experiences?.forEach(
        (experience) => {
          if (experience.id_user) {
            uniqueUsers.add(
              experience.id_user
            )
          }
        }
      )
    }
  )

  return uniqueUsers.size
})

/*
|--------------------------------------------------------------------------
| Fetch Categories
|--------------------------------------------------------------------------
|
| Project membutuhkan Category sebagai master data.
|
*/

const fetchCategories = async () => {
  try {
    loadingCategories.value = true
    errorMessage.value = ''

    const response = await axios.get('/api/category')

    const data = response.data?.data ?? response.data

    if (!Array.isArray(data)) {
      throw new Error('Format data category dari API tidak valid.')
    }

    categories.value = data
  } catch (error: any) {
    console.error('Failed to fetch categories:', error)

    categories.value = []

    errorMessage.value =
      error.response?.data?.message ||
      error.message ||
      'Gagal memuat kategori.'
  } finally {
    loadingCategories.value = false
  }
}


/*
|--------------------------------------------------------------------------
| Fetch Project Detail
|--------------------------------------------------------------------------
|
| Digunakan ketika View diklik.
|
*/

const fetchProjectDetail = async (
  project: Project
) => {
  try {
    loadingView.value = true

    const response =
      await axios.get(
        `/api/projects/${project.id_project}`
      )

    selectedProject.value =
      response.data?.data ??
      response.data ??
      project
  } catch (error) {
    console.error(
      'Failed to fetch project detail:',
      error
    )

    /*
    |--------------------------------------------------------------------------
    | Jika detail gagal, tetap tampilkan
    | data yang sudah ada dari table.
    |--------------------------------------------------------------------------
    */

    selectedProject.value =
      project
  } finally {
    loadingView.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Add Project
|--------------------------------------------------------------------------
*/

const addProject = async () => {
  selectedProject.value = null

  errorMessage.value = ''

  await fetchCategories()

  showProjectModal.value = true
}

/*
|--------------------------------------------------------------------------
| Edit Project
|--------------------------------------------------------------------------
*/

const editProject = async (
  project: Project
) => {
  errorMessage.value = ''

  selectedProject.value =
    project

  await fetchCategories()

  showProjectModal.value = true
}

/*
|--------------------------------------------------------------------------
| View Project
|--------------------------------------------------------------------------
*/

const viewProject = async (
  project: Project
) => {
  errorMessage.value = ''

  selectedProject.value =
    project

  showViewModal.value = true

  await fetchProjectDetail(project)
}

/*
|--------------------------------------------------------------------------
| Open Delete Modal
|--------------------------------------------------------------------------
*/

const openDeleteModal = (
  project: Project
) => {
  selectedProject.value =
    project

  errorMessage.value = ''

  showDeleteModal.value = true
}

/*
|--------------------------------------------------------------------------
| Close Project Modal
|--------------------------------------------------------------------------
*/

const closeProjectModal = () => {
  if (showProjectModal.value) {
    showProjectModal.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Project Saved
|--------------------------------------------------------------------------
*/

const projectSaved = () => {
  showProjectModal.value = false

  selectedProject.value = null

  refreshKey.value++
}

/*
|--------------------------------------------------------------------------
| Close View
|--------------------------------------------------------------------------
*/

const closeViewModal = () => {
  if (loadingView.value) {
    return
  }

  showViewModal.value = false

  selectedProject.value = null
}

/*
|--------------------------------------------------------------------------
| Close Delete
|--------------------------------------------------------------------------
*/

const closeDeleteModal = () => {
  if (deleting.value) {
    return
  }

  showDeleteModal.value = false

  selectedProject.value = null

  errorMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| Delete Project
|--------------------------------------------------------------------------
*/

const deleteProject = async () => {
  if (!selectedProject.value) {
    return
  }

  try {
    deleting.value = true

    errorMessage.value = ''

    await axios.delete(
      `/api/projects/${selectedProject.value.id_project}`
    )

    showDeleteModal.value = false

    selectedProject.value = null

    refreshKey.value++
  } catch (error: any) {
    console.error(
      'Failed to delete project:',
      error
    )

    errorMessage.value =
      error.response?.data?.message ||
      'Failed to delete project.'
  } finally {
    deleting.value = false
  }
}

/*
|--------------------------------------------------------------------------
| Table Total
|--------------------------------------------------------------------------
*/

const updateProjectTotal = (
  total: number
) => {
  totalProject.value = total
}

/*
|--------------------------------------------------------------------------
| Table Project Data
|--------------------------------------------------------------------------
|
| ProjectTable saat ini hanya mengirim total.
| Untuk contributor card, kita ambil dari detail
| project yang tersedia setelah table refresh.
|
*/

const updateProjectsFromTable = (
  data: Project[]
) => {
  projects.value = data

  totalContributors.value =
    contributorCount.value
}

/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(async () => {
  await fetchCategories()
})
</script>

<template>
  <AdminLayout>
    <PageBreadcrumb
      :pageTitle="currentPageTitle"
    />

    <div
      class="space-y-5 sm:space-y-6"
    >

      <!-- ===================================================== -->
      <!-- SUMMARY -->
      <!-- ===================================================== -->

      <div
        class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3"
      >

        <!-- Total Project -->
        <ManagementSummaryCard
          title="Total Project"
          :value="totalProject"
          :icon="DocsIcon"
          quote="Every project tells a story."
          :change="1"
          color="blue"
        />

        <!-- Total Contributors -->
        <ManagementSummaryCard
          title="Total Contributors"
          :value="totalContributors"
          :icon="DocsIcon"
          quote="Great projects are built together."
          :change="1"
          color="green"
        />

      </div>

      <!-- ===================================================== -->
      <!-- PROJECT HEADER -->
      <!-- ===================================================== -->

      <div
        class="rounded-2xl border border-gray-200 bg-white px-5 py-5 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6"
      >

        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >

          <div>
            <h2
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Project List
            </h2>

            <p
              class="mt-1 text-sm text-gray-500 dark:text-gray-400"
            >
              Manage projects, contributors,
              tasks, stacks, gallery, and links.
            </p>
          </div>

          <!-- Add Project -->
          <button
            v-if="isSuperAdmin()"
            type="button"
            class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600"
            @click="addProject"
          >
            + Add Project
          </button>

        </div>

      </div>

      <!-- ===================================================== -->
      <!-- PROJECT TABLE -->
      <!-- ===================================================== -->

      <ProjectTable
        :refresh-key="refreshKey"
        @total-changed="
          updateProjectTotal
        "
        @edit="editProject"
        @view="viewProject"
        @delete="openDeleteModal"
      />

    </div>

    <!-- ======================================================= -->
    <!-- ADD / EDIT PROJECT MODAL -->
    <!-- ======================================================= -->

    <ProjectModal
      :show="showProjectModal"
      :project="selectedProject"
      :categories="categories"
      @close="closeProjectModal"
      @saved="projectSaved"
    />

    <!-- ======================================================= -->
    <!-- VIEW PROJECT MODAL -->
    <!-- ======================================================= -->

    <Teleport to="body">
      <div
        v-if="showViewModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center overflow-hidden bg-black/50 p-3 sm:p-4"
        @click.self="closeViewModal"
      >

        <div
          class="flex max-h-[90vh] max-h-[90dvh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900"
        >

          <!-- Header -->
          <div
            class="flex shrink-0 items-start justify-between border-b border-gray-200 px-5 py-4 sm:px-6 sm:py-5 dark:border-gray-800"
          >

            <div>
              <h3
                class="text-xl font-semibold text-gray-800 dark:text-white/90"
              >
                {{ selectedProject?.name }}
              </h3>

              <p
                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
              >
                Project Details
              </p>
            </div>

            <button
              type="button"
              title="Close"
              class="text-xl text-gray-400 transition hover:text-gray-700 dark:hover:text-white"
              @click="closeViewModal"
            >
              ×
            </button>

          </div>

          <!-- Loading -->
          <div
            v-if="loadingView"
            class="flex min-h-[300px] items-center justify-center"
          >
            <div
              class="text-sm text-gray-500 dark:text-gray-400"
            >
              Loading project...
            </div>
          </div>

          <!-- Content -->
          <div
            v-else-if="selectedProject"
            class="min-h-0 flex-1 space-y-6 overflow-y-auto overscroll-contain p-5 sm:p-6"
          >

            <!-- Project Information -->
            <div>
              <h4
                class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90"
              >
                Project Information
              </h4>

              <div
                class="grid grid-cols-1 gap-5 sm:grid-cols-3"
              >

                <!-- Category -->
                <div>
                  <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                  >
                    Category
                  </p>

                  <p
                    class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90"
                  >
                    {{
                      selectedProject.category
                        ?.name || '-'
                    }}
                  </p>
                </div>

                <!-- Start -->
                <div>
                  <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                  >
                    Start Date
                  </p>

                  <p
                    class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90"
                  >
                    {{
                      selectedProject.date_in ||
                      '-'
                    }}
                  </p>
                </div>

                <!-- End -->
                <div>
                  <p
                    class="text-xs font-medium uppercase tracking-wide text-gray-400"
                  >
                    End Date
                  </p>

                  <p
                    class="mt-1 text-sm font-medium text-gray-800 dark:text-white/90"
                  >
                    {{
                      selectedProject.date_out ||
                      'Present'
                    }}
                  </p>
                </div>

              </div>
            </div>

            <!-- Project Images / Thumbnail -->
            <div v-if="selectedProject.images?.length" class="space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Project Images</h4>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ selectedProject.images.length }} image(s)</span>
              </div>

              <div v-if="primaryProjectImage" class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
                <div class="relative">
                  <img
                    :src="primaryProjectImage.image_url"
                    :alt="primaryProjectImage.alt_text || `${selectedProject.name} thumbnail`"
                    class="max-h-[360px] w-full bg-gray-50 object-contain dark:bg-gray-800"
                    @error="hideBrokenImage"
                  />
                  <span class="absolute left-3 top-3 rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold text-white">Thumbnail</span>
                </div>
              </div>

              <div v-if="additionalProjectImages.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div v-for="image in additionalProjectImages" :key="image.id_project_image" class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-800">
                  <img
                    :src="image.image_url"
                    :alt="image.alt_text || 'Project gallery image'"
                    class="h-32 w-full bg-gray-50 object-cover dark:bg-gray-800"
                    @error="hideBrokenImage"
                  />
                  <p v-if="image.alt_text" class="truncate px-3 py-2 text-xs text-gray-500 dark:text-gray-400">{{ image.alt_text }}</p>
                </div>
              </div>
            </div>

            <!-- Project Links -->
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Project Links</h4>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ selectedProject.links?.length || 0 }} link(s)</span>
              </div>

              <div v-if="selectedProject.links?.length" class="space-y-3">
                <div v-for="link in selectedProject.links" :key="link.id_link" class="flex flex-col gap-3 rounded-xl border border-gray-200 p-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between">
                  <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                      <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ link.name }}</p>
                      <span class="rounded-full bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ projectLinkTypeLabel(link.type) }}</span>
                    </div>
                    <a :href="link.link" target="_blank" rel="noopener noreferrer" class="mt-1 block break-all text-sm text-brand-600 hover:underline dark:text-brand-400">{{ link.link }}</a>
                  </div>
                  <button type="button" title="Open link" class="flex h-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 px-3 text-sm font-medium text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300" @click="openProjectLink(link.link)">Open ↗</button>
                </div>
              </div>
              <div v-else class="rounded-xl border border-dashed border-gray-300 p-5 text-center dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">No project links added.</p>
              </div>
            </div>

            <!-- Contributors -->
            <div>

              <div
                class="mb-4 flex items-center justify-between"
              >
                <h4
                  class="text-sm font-semibold text-gray-800 dark:text-white/90"
                >
                  Contributors
                </h4>

                <span
                  class="text-xs text-gray-500 dark:text-gray-400"
                >
                  {{
                    selectedProject
                      .experiences
                      ?.length || 0
                  }}
                  contributor(s)
                </span>
              </div>

              <!-- Contributors -->
              <div
                v-if="
                  selectedProject
                    .experiences
                    ?.length
                "
                class="space-y-3"
              >

                <div
                  v-for="experience in selectedProject.experiences"
                  :key="
                    experience.id_experience
                  "
                  class="rounded-xl border border-gray-200 p-4 dark:border-gray-800"
                >

                  <div
                    class="flex items-center justify-between gap-4"
                  >

                    <div>

                      <p
                        class="text-sm font-semibold text-gray-800 dark:text-white/90"
                      >
                        {{
                          experience.user
                            ?.username ||
                          experience.user
                            ?.email ||
                          'Unknown User'
                        }}
                      </p>

                      <p
                        class="mt-1 text-xs text-gray-500 dark:text-gray-400"
                      >
                        {{
                          experience
                            .position_type
                            ?.name ||
                          '-'
                        }}

                        <span
                          v-if="
                            experience
                              .work_type
                              ?.name
                          "
                        >
                          ·
                          {{
                            experience
                              .work_type
                              .name
                          }}
                        </span>
                      </p>

                    </div>

                  </div>

                </div>

              </div>

              <!-- No Contributors -->
              <div
                v-else
                class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700"
              >
                <p
                  class="text-sm text-gray-500 dark:text-gray-400"
                >
                  No contributors yet.
                </p>
              </div>

            </div>

          </div>

          <!-- Footer -->
          <div
            class="flex shrink-0 justify-end border-t border-gray-200 bg-white px-5 py-3 dark:border-gray-800 dark:bg-gray-900 sm:px-6"
          >

            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300"
              @click="closeViewModal"
            >
              Close
            </button>

          </div>

        </div>

      </div>
    </Teleport>

    <!-- ======================================================= -->
    <!-- DELETE PROJECT MODAL -->
    <!-- ======================================================= -->

    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/50 p-4"
        @click.self="closeDeleteModal"
      >

        <div
          class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900"
        >

          <!-- Header -->
          <div
            class="flex items-start gap-4"
          >

            <div
              class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-50 text-xl dark:bg-red-500/10"
            >
              🗑
            </div>

            <div>
              <h3
                class="text-lg font-semibold text-gray-800 dark:text-white/90"
              >
                Delete Project
              </h3>

              <p
                class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400"
              >
                Are you sure you want to
                delete this project?
              </p>
            </div>

          </div>

          <!-- Project Name -->
          <div
            v-if="selectedProject"
            class="mt-5 rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-800"
          >

            <p
              class="text-sm font-semibold text-gray-800 dark:text-white/90"
            >
              {{ selectedProject.name }}
            </p>

            <p
              class="mt-1 text-xs text-gray-500 dark:text-gray-400"
            >
              {{
                selectedProject.category
                  ?.name || 'No category'
              }}
            </p>

          </div>

          <!-- Error -->
          <div
            v-if="errorMessage"
            class="mt-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-600 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-400"
          >
            {{ errorMessage }}
          </div>

          <!-- Actions -->
          <div
            class="mt-6 flex justify-end gap-3"
          >

            <button
              type="button"
              class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-60 dark:border-gray-700 dark:text-gray-300"
              :disabled="deleting"
              @click="closeDeleteModal"
            >
              Cancel
            </button>

            <button
              type="button"
              class="rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-600 disabled:opacity-60"
              :disabled="deleting"
              @click="deleteProject"
            >
              {{
                deleting
                  ? 'Deleting...'
                  : 'Delete'
              }}
            </button>

          </div>

        </div>

      </div>
    </Teleport>

  </AdminLayout>
</template>