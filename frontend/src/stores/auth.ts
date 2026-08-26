import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@/services/api'
import type { User } from '@/types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))
  const isLoading = ref(false)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  async function initAuth() {
    if (token.value) {
      try {
        await fetchMe()
      } catch {
        logout()
      }
    }
  }

  async function fetchMe() {
    isLoading.value = true
    try {
      const res = await authApi.me()
      user.value = res.data
    } finally {
      isLoading.value = false
    }
  }

  async function login(data: any) {
    isLoading.value = true
    try {
      const res = await authApi.login(data)
      token.value = res.data.token
      localStorage.setItem('token', res.data.token)
      await fetchMe()
    } finally {
      isLoading.value = false
    }
  }

  async function register(data: any) {
    isLoading.value = true
    try {
      const res = await authApi.register(data)
      token.value = res.data.token
      localStorage.setItem('token', res.data.token)
      await fetchMe()
    } finally {
      isLoading.value = false
    }
  }

  async function updateProfile(data: { name?: string; phone?: string | null; address?: User['address'] }) {
    const res = await authApi.updateProfile(data)
    user.value = res.data
    return res.data
  }

  function logout() {
    authApi.logout().catch(() => {})
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isLoading, isAuthenticated, isAdmin, login, register, logout, fetchMe, initAuth, updateProfile }
})
