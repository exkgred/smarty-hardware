<template>
  <header class="sticky top-0 z-30 border-b border-slate-800/60 bg-ink/95 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center gap-6">
        <RouterLink to="/" class="flex items-center gap-2 shrink-0">
          <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand/15 ring-1 ring-brand/40">
            <svg class="h-5 w-5 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </span>
          <span class="leading-tight">
            <span class="block text-sm font-bold tracking-wide text-white">SMARTY</span>
            <span class="block text-[10px] uppercase tracking-[0.2em] text-brand-200">Hardware</span>
          </span>
        </RouterLink>

        <nav class="flex items-center gap-4 text-sm font-medium text-slate-300">
          <RouterLink to="/catalog" class="hover:text-white">Peças</RouterLink>
          <RouterLink to="/catalog?category=servicos" class="hover:text-white">Assistência</RouterLink>
        </nav>

        <form class="hidden sm:flex flex-1 max-w-md ml-auto" @submit.prevent="goSearch">
          <input
            v-model="search"
            type="search"
            placeholder="Buscar Ryzen, RTX, SSD, limpeza..."
            class="w-full rounded-l-lg border-0 bg-slate-800/80 px-3 py-2 text-sm text-white placeholder:text-slate-400 ring-1 ring-slate-700 focus:ring-2 focus:ring-brand"
          />
          <button type="submit" class="rounded-r-lg bg-brand px-3 text-ink font-semibold text-sm hover:bg-brand-dark">Buscar</button>
        </form>

        <div class="flex items-center gap-3 ml-auto sm:ml-0">
          <RouterLink to="/cart" class="relative text-slate-300 hover:text-white">
            <span class="sr-only">Carrinho</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="cartStore.totalItems > 0" class="absolute -top-2 -right-2 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1 text-[10px] font-bold text-ink">
              {{ cartStore.totalItems }}
            </span>
          </RouterLink>

          <template v-if="authStore.isAuthenticated">
            <div class="relative" @mouseenter="openMenu" @mouseleave="scheduleClose">
              <button
                type="button"
                class="flex items-center gap-1 px-2 py-2 text-sm font-medium text-slate-200 hover:text-white"
                aria-haspopup="true"
                :aria-expanded="menuOpen"
                @click="toggleMenu"
              >
                {{ authStore.user?.name }}
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
              </button>
              <div v-show="menuOpen" class="absolute right-0 top-full z-50 w-48 pt-1">
                <div class="rounded-lg border border-slate-700 bg-ink-800 py-1 shadow-xl">
                  <RouterLink to="/account" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-800" @click="closeMenu">Minha conta</RouterLink>
                  <RouterLink to="/my-orders" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-800" @click="closeMenu">Meus pedidos</RouterLink>
                  <RouterLink v-if="authStore.isAdmin" to="/admin" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-800" @click="closeMenu">Painel admin</RouterLink>
                  <button type="button" class="block w-full text-left px-4 py-2 text-sm text-slate-200 hover:bg-slate-800" @click="logout">Sair</button>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <RouterLink to="/login" class="text-sm text-slate-300 hover:text-white">Entrar</RouterLink>
            <RouterLink to="/register" class="hidden sm:inline-flex rounded-lg bg-brand px-3 py-1.5 text-sm font-semibold text-ink hover:bg-brand-dark">Criar conta</RouterLink>
          </template>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()
const search = ref('')
const menuOpen = ref(false)
let closeTimer: ReturnType<typeof setTimeout> | null = null

function openMenu() {
  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }
  menuOpen.value = true
}

function closeMenu() {
  if (closeTimer) {
    clearTimeout(closeTimer)
    closeTimer = null
  }
  menuOpen.value = false
}

function scheduleClose() {
  if (closeTimer) clearTimeout(closeTimer)
  closeTimer = setTimeout(() => {
    menuOpen.value = false
    closeTimer = null
  }, 200)
}

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}

function logout() {
  closeMenu()
  authStore.logout()
}

function goSearch() {
  router.push({ name: 'catalog', query: search.value ? { search: search.value } : {} })
}
</script>
