import { ref } from 'vue'
import {
  getMe,
  logout as logoutApi,
} from '@/services/auth'

const user = ref<any>(null)
const loading = ref(false)
const initialized = ref(false)

export function useAuth() {
  const fetchUser = async () => {
    try {
      loading.value = true

      const response = await getMe()

      user.value = response.user

      return user.value
    } catch (error: any) {
      user.value = null

      return null
    } finally {
      loading.value = false
      initialized.value = true
    }
  }

  const logout = async () => {
    await logoutApi()

    user.value = null
  }

  const isAuthenticated = () => {
    return user.value !== null
  }

  const isAdmin = () => {
    return user.value?.role?.name === 'Admin'
  }

  const isSuperAdmin = () => {
    return user.value?.role?.name === 'Super Admin'
  }

  return {
    user,
    loading,
    initialized,
    fetchUser,
    logout,
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
  }
}