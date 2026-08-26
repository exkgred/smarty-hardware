<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-900 text-white flex flex-col">
      <div class="h-16 flex items-center px-4 font-bold text-sm tracking-wide border-b border-slate-800">SMARTY ADMIN</div>
      <nav class="flex-1 px-2 py-4 space-y-1">
        <RouterLink to="/admin" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Dashboard</RouterLink>
        <RouterLink to="/admin/products" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Produtos</RouterLink>
        <RouterLink to="/admin/sales" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Venda no balcão</RouterLink>
        <RouterLink to="/admin/inventory" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Estoque / entradas</RouterLink>
        <RouterLink to="/admin/invoices" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Notas</RouterLink>
        <RouterLink to="/admin/orders" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Pedidos</RouterLink>
        <RouterLink to="/admin/chatbot" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Chatbot / Mia</RouterLink>
      </nav>
    </div>
    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
        <h1 class="text-lg font-semibold text-gray-900">Painel de Administração</h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Olá, {{ authStore.user?.name }}</span>
          <button @click="logout" class="text-sm text-red-600 hover:text-red-800 font-medium">Sair</button>
        </div>
      </header>
      <main class="flex-1 overflow-auto p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

function logout() {
  authStore.logout()
  router.push('/')
}
</script>
