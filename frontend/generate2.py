import os

base_dir = "/home/admlocal/Documentos/porti/marketplace/frontend"
files = {}

files["src/stores/products.ts"] = """import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productsApi } from '@/services/api'
import type { Product, Category } from '@/types'

export const useProductsStore = defineStore('products', () => {
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const categories = ref<Category[]>([])
  const pagination = ref({ current_page: 1, last_page: 1 })
  const loading = ref(false)
  const filters = ref({ category_id: null, min_price: null, max_price: null, search: '' })

  async function fetchProducts(params: any = {}) {
    loading.value = true
    try {
      const res = await productsApi.list({ ...filters.value, ...params })
      products.value = res.data.data
      pagination.value = { current_page: res.data.current_page, last_page: res.data.last_page }
    } finally {
      loading.value = false
    }
  }

  async function fetchProduct(slug: string) {
    loading.value = true
    try {
      const res = await productsApi.show(slug)
      currentProduct.value = res.data
    } finally {
      loading.value = false
    }
  }

  function setFilter(key: string, value: any) {
    (filters.value as any)[key] = value
  }

  function resetFilters() {
    filters.value = { category_id: null, min_price: null, max_price: null, search: '' }
  }

  return { products, currentProduct, categories, pagination, loading, filters, fetchProducts, fetchProduct, setFilter, resetFilters }
})
"""

files["src/stores/orders.ts"] = """import { defineStore } from 'pinia'
import { ref } from 'vue'
import { ordersApi } from '@/services/api'
import type { Order } from '@/types'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref<Order[]>([])
  const currentOrder = ref<Order | null>(null)
  const loading = ref(false)
  const checkoutLoading = ref(false)

  async function fetchMyOrders() {
    loading.value = true
    try {
      const res = await ordersApi.myOrders()
      orders.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function fetchOrder(id: number) {
    loading.value = true
    try {
      const res = await ordersApi.getOrder(id)
      currentOrder.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function checkout(items: any[], shippingCost: number) {
    checkoutLoading.value = true
    try {
      const res = await ordersApi.checkout(items, shippingCost)
      return res.data
    } finally {
      checkoutLoading.value = false
    }
  }

  return { orders, currentOrder, loading, checkoutLoading, fetchMyOrders, fetchOrder, checkout }
})
"""

files["src/stores/chat.ts"] = """import { defineStore } from 'pinia'
import { ref } from 'vue'
import { chatApi } from '@/services/api'
import type { ChatMessage } from '@/types'
import { v4 as uuidv4 } from 'uuid'

export const useChatStore = defineStore('chat', () => {
  const messages = ref<ChatMessage[]>([])
  const isLoading = ref(false)
  const sessionId = ref(localStorage.getItem('chat_session_id') || '')

  function initSession() {
    if (!sessionId.value) {
      sessionId.value = uuidv4()
      localStorage.setItem('chat_session_id', sessionId.value)
    }
    messages.value = [{ role: 'assistant', content: 'Olá! Como posso ajudá-lo hoje?' }]
  }

  async function sendMessage(content: string) {
    messages.value.push({ role: 'user', content })
    isLoading.value = true
    try {
      const res = await chatApi.sendMessage(sessionId.value, content)
      messages.value.push({ role: 'assistant', content: res.data.message })
    } catch {
      messages.value.push({ role: 'assistant', content: 'Desculpe, ocorreu um erro ao processar sua mensagem.' })
    } finally {
      isLoading.value = false
    }
  }

  function clearHistory() {
    messages.value = [{ role: 'assistant', content: 'Olá! Como posso ajudá-lo hoje?' }]
    sessionId.value = uuidv4()
    localStorage.setItem('chat_session_id', sessionId.value)
  }

  return { messages, isLoading, sessionId, initSession, sendMessage, clearHistory }
})
"""

files["src/router/index.ts"] = """import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'home', component: () => import('@/views/public/HomeView.vue') },
    { path: '/catalog', name: 'catalog', component: () => import('@/views/public/CatalogView.vue') },
    { path: '/product/:slug', name: 'product-detail', component: () => import('@/views/public/ProductDetailView.vue') },
    { path: '/cart', name: 'cart', component: () => import('@/views/public/CartView.vue') },
    { path: '/checkout', name: 'checkout', component: () => import('@/views/public/CheckoutView.vue') },
    { path: '/order-success', name: 'order-success', component: () => import('@/views/public/OrderSuccessView.vue') },
    { path: '/login', name: 'login', component: () => import('@/views/auth/LoginView.vue') },
    { path: '/register', name: 'register', component: () => import('@/views/auth/RegisterView.vue') },
    {
      path: '/my-orders',
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'customer-orders', component: () => import('@/views/customer/CustomerOrdersView.vue') },
        { path: ':id', name: 'customer-order-detail', component: () => import('@/views/customer/CustomerOrderDetailView.vue') }
      ]
    },
    {
      path: '/admin',
      component: () => import('@/views/admin/AdminLayout.vue'),
      meta: { requiresAdmin: true },
      children: [
        { path: '', name: 'admin-dashboard', component: () => import('@/views/admin/AdminDashboardView.vue') },
        { path: 'products', name: 'admin-products', component: () => import('@/views/admin/AdminProductsView.vue') },
        { path: 'inventory', name: 'admin-inventory', component: () => import('@/views/admin/AdminInventoryView.vue') },
        { path: 'orders', name: 'admin-orders', component: () => import('@/views/admin/AdminOrdersView.vue') },
        { path: 'chatbot', name: 'admin-chatbot', component: () => import('@/views/admin/AdminChatbotView.vue') }
      ]
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  if (!authStore.user && authStore.token) {
    await authStore.fetchMe().catch(() => {})
  }

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'login' })
  } else if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  } else {
    next()
  }
})

export default router
"""

files["src/composables/useNotification.ts"] = """import { ref } from 'vue'

export interface Notification {
  id: number
  type: 'success' | 'error' | 'info'
  message: string
}

const notifications = ref<Notification[]>([])
let nextId = 0

export function useNotification() {
  const add = (type: Notification['type'], message: string) => {
    const id = nextId++
    notifications.value.push({ id, type, message })
    setTimeout(() => {
      remove(id)
    }, 3000)
  }

  const remove = (id: number) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  return {
    notifications,
    success: (msg: string) => add('success', msg),
    error: (msg: string) => add('error', msg),
    info: (msg: string) => add('info', msg),
    remove
  }
}
"""

files["src/App.vue"] = """<template>
  <div class="min-h-screen flex flex-col">
    <AppNavbar v-if="!isAdminRoute" />
    <main class="flex-grow">
      <RouterView />
    </main>
    <AppFooter v-if="!isAdminRoute" />
    <ChatWidget v-if="!isAdminRoute" />
    <NotificationToast />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppNavbar from '@/components/common/AppNavbar.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import ChatWidget from '@/components/chat/ChatWidget.vue'
import NotificationToast from '@/components/common/NotificationToast.vue'

const authStore = useAuthStore()
const route = useRoute()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

onMounted(() => {
  authStore.initAuth()
})
</script>
"""

for path, content in files.items():
    full_path = os.path.join(base_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)

print("Part 2 done.")
