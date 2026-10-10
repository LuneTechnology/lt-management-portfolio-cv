<template>
  <AdminLayout>
    <PageBreadcrumb pageTitle="Profile" />
    <div class="space-y-6">
      <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="h-36 bg-gradient-to-r from-blue-600 via-indigo-500 to-violet-500 sm:h-44"></div>
        <div class="px-5 pb-6 sm:px-8">
          <div class="-mt-12 flex flex-col gap-4 sm:-mt-14 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-end">
              <div class="relative grid h-24 w-24 shrink-0 place-items-center overflow-hidden rounded-2xl border-4 border-white bg-blue-50 text-2xl font-bold text-blue-700 shadow-sm dark:border-gray-900 dark:bg-blue-500/10 dark:text-blue-300 sm:h-28 sm:w-28">
                <img v-if="photoPreview || user.photo_url" :src="photoPreview || user.photo_url" alt="Profile photo" class="h-full w-full object-cover" /><span v-else>{{ initials }}</span>
              </div>
              <div class="pb-1"><p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ user.username || 'Your profile' }}</p><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ user.email || 'Add your email address' }}</p><div class="mt-2 inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300"><span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>{{ user.role?.name || user.role?.role_name || 'Portfolio account' }}</div></div>
            </div>
            <div class="flex gap-2"><button type="button" @click="reloadProfile" class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-white/5">Discard</button><button type="button" @click="saveProfile" :disabled="loading || saving" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:opacity-60"><SaveIcon class="h-4 w-4" />{{ saving ? 'Saving…' : 'Save profile' }}</button></div>
          </div>
        </div>
      </section>

      <div v-if="notice" class="rounded-xl px-4 py-3 text-sm" :class="noticeType === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-300' : 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-300'">{{ notice }}</div>
      <div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)]">
        <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-7">
          <div class="mb-6"><h2 class="text-lg font-semibold text-gray-900 dark:text-white">Personal information</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage the information used in your portfolio account.</p></div>
          <div class="grid gap-5 sm:grid-cols-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Display name *<input v-model.trim="user.username" required maxlength="50" class="profile-input" placeholder="Your name" /></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200">Email address *<input v-model.trim="user.email" required type="email" class="profile-input" placeholder="name@example.com" /></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200 sm:col-span-2">Contact / WhatsApp<input v-model.trim="user.contact" class="profile-input" placeholder="Phone number or contact information" /></label>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-200 sm:col-span-2">About me<textarea v-model.trim="user.aboutme" rows="5" maxlength="5000" class="profile-input resize-y" placeholder="Write a short introduction about yourself…" /><span class="mt-1 block text-right text-xs font-normal text-gray-400">{{ (user.aboutme || '').length }}/5000</span></label>
          </div>
        </section>

        <div class="space-y-6">
          <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-5"><h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profile photo</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use a clear square image for the best result.</p></div>
            <div class="flex items-center gap-4"><div class="grid h-16 w-16 shrink-0 place-items-center overflow-hidden rounded-xl bg-gray-100 text-lg font-semibold text-gray-500 dark:bg-gray-800"><img v-if="photoPreview || user.photo_url" :src="photoPreview || user.photo_url" class="h-full w-full object-cover" alt="Preview" /><span v-else>{{ initials }}</span></div><label class="flex-1 cursor-pointer rounded-xl border border-dashed border-gray-300 px-4 py-3 text-center text-sm text-gray-600 hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-blue-500/5"><UploadIcon class="mx-auto mb-1 h-5 w-5" />Choose image<input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onPhotoChange" /></label></div><p class="mt-3 text-xs text-gray-400">JPG, PNG, or WebP · max 5 MB</p>
          </section>
          <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-5"><h2 class="text-lg font-semibold text-gray-900 dark:text-white">Social links</h2><p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add links that visitors can use to find you.</p></div>
            <div class="space-y-4">
              <label v-for="field in socialFields" :key="field.key" class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ field.label }}<input v-model.trim="social[field.key]" :type="field.key === 'email' ? 'email' : 'text'" class="profile-input" :placeholder="field.placeholder" /></label>
            </div>
          </section>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import axios from '@/services/axios'
import { Save as SaveIcon, Upload as UploadIcon } from 'lucide-vue-next'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const user = reactive<any>({ username: '', email: '', contact: '', aboutme: '', photo_url: '', role: null })
const social = reactive<Record<string, string>>({ instagram: '', facebook: '', linkedin: '', email: '', whatsapp: '', twitter: '' })
const socialFields = [
  { key: 'linkedin', label: 'LinkedIn', placeholder: 'https://linkedin.com/in/username' },
  { key: 'instagram', label: 'Instagram', placeholder: 'https://instagram.com/username' },
  { key: 'facebook', label: 'Facebook', placeholder: 'https://facebook.com/username' },
  { key: 'twitter', label: 'X / Twitter', placeholder: 'https://x.com/username' },
  { key: 'email', label: 'Public email', placeholder: 'hello@example.com' },
  { key: 'whatsapp', label: 'WhatsApp', placeholder: '628123456789' },
] as const
const loading = ref(false)
const saving = ref(false)
const notice = ref('')
const noticeType = ref<'success' | 'error'>('success')
const photoFile = ref<File | null>(null)
const photoPreview = ref('')
const initials = computed(() => (user.username || user.email || 'U').split(/[\s@._-]+/).filter(Boolean).slice(0, 2).map((x: string) => x[0]?.toUpperCase()).join(''))
async function reloadProfile() {
  loading.value = true; notice.value = ''
  try { const { data } = await axios.get('/api/profile'); const u = data.user ?? data; Object.assign(user, { username: u.username ?? '', email: u.email ?? '', contact: u.contact ?? '', aboutme: u.aboutme ?? '', photo_url: u.photo_url ?? '', role: u.role ?? null }); const links = typeof u.social_links === 'string' ? JSON.parse(u.social_links || '{}') : (u.social_links ?? {}); for (const key of Object.keys(social)) social[key] = links[key] ?? ''; photoFile.value = null; photoPreview.value = '' }
  catch (e: any) { noticeType.value = 'error'; notice.value = e.response?.data?.message ?? 'Unable to load profile. Check that you are logged in.' }
  finally { loading.value = false }
}
function onPhotoChange(event: Event) { const file = (event.target as HTMLInputElement).files?.[0] ?? null; if (!file) return; if (file.size > 5 * 1024 * 1024) { noticeType.value = 'error'; notice.value = 'Photo must be 5 MB or smaller.'; return } photoFile.value = file; photoPreview.value = URL.createObjectURL(file); notice.value = '' }
async function saveProfile() {
  saving.value = true; notice.value = ''
  try { const payload = new FormData(); payload.append('_method', 'PUT'); payload.append('username', user.username); payload.append('email', user.email); payload.append('contact', user.contact ?? ''); payload.append('aboutme', user.aboutme ?? ''); for (const [key, value] of Object.entries(social)) payload.append(`social_links[${key}]`, value ?? ''); if (photoFile.value) payload.append('photo', photoFile.value); const { data } = await axios.post('/api/profile', payload); const u = data.user ?? data; user.photo_url = u.photo_url ?? user.photo_url; user.role = u.role ?? user.role; photoFile.value = null; photoPreview.value = ''; noticeType.value = 'success'; notice.value = 'Profile updated successfully.'; await reloadProfile() }
  catch (e: any) { noticeType.value = 'error'; const errors = e.response?.data?.errors; notice.value = errors ? Object.values(errors).flat().join(' ') : (e.response?.data?.message ?? 'Unable to save profile.') }
  finally { saving.value = false }
}
onMounted(reloadProfile)
</script>

<style scoped>
.profile-input { display:block; width:100%; margin-top:.375rem; border:1px solid rgb(229 231 235); border-radius:.625rem; padding:.7rem .8rem; font-size:.875rem; font-weight:400; color:inherit; background:transparent; outline:none; }
.profile-input:focus { border-color:rgb(59 130 246); box-shadow:0 0 0 3px rgb(59 130 246 / .12); }
:global(.dark) .profile-input { border-color:rgb(55 65 81); }
</style>
