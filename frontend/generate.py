import os

base_dir = "/home/admlocal/Documentos/porti/marketplace/frontend"

files = {}

files["tailwind.config.js"] = """/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 900: '#1e3a8a' },
        brand: { DEFAULT: '#2563eb', dark: '#1d4ed8' }
      }
    }
  },
  plugins: []
}
"""

files["src/main.ts"] = """import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.mount('#app')
"""

files["src/assets/main.css"] = """@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  body { @apply bg-gray-50 text-gray-900; }
}
"""

files["vite.config.ts"] = """import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: { alias: { '@': path.resolve(__dirname, './src') } },
  server: { proxy: { '/api': { target: 'http://localhost:8000', changeOrigin: true } } }
})
"""

files["index.html"] = """<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Marketplace - Sua loja online" />
    <title>Marketplace - Sua loja online</title>
  </head>
  <body>
    <div id="app"></div>
    <script type="module" src="/src/main.ts"></script>
  </body>
</html>
"""

files["src/types/index.ts"] = """export interface User { id: number; name: string; email: string; role: 'customer' | 'admin' }
export interface Category { id: number; name: string; slug: string; description: string | null }
export interface Product { id: number; category_id: number; category?: Category; name: string; slug: string; description: string | null; price: number; stock_quantity: number; image_url: string | null; is_active: boolean }
export interface CartItem { product: Product; quantity: number }
export interface OrderItem { id: number; product_id: number; product_name: string; unit_price: number; quantity: number; subtotal: number }
export interface Order { id: number; status: string; subtotal: number; discount_amount: number; shipping_cost: number; total: number; items: OrderItem[]; created_at: string }
export interface PaginatedResponse<T> { data: T[]; current_page: number; last_page: number; per_page: number; total: number }
export interface ChatMessage { role: 'user' | 'assistant'; content: string }
"""

files["src/services/api.ts"] = """import axios from 'axios'

const api = axios.create({ baseURL: '/api' })

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token && config.headers) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  res => res,
  err => {
    if (err.response?.status === 401) {
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(err)
  }
)

export const authApi = {
  register: (data: any) => api.post('/register', data),
  login: (data: any) => api.post('/login', data),
  logout: () => api.post('/logout'),
  me: () => api.get('/me')
}
export const productsApi = {
  list: (params?: any) => api.get('/products', { params }),
  show: (slug: string) => api.get(`/products/${slug}`)
}
export const ordersApi = {
  myOrders: () => api.get('/orders/my'),
  getOrder: (id: number) => api.get(`/orders/${id}`),
  checkout: (items: any[], shippingCost: number) => api.post('/orders/checkout', { items, shippingCost })
}
export const paymentApi = { createIntent: (orderId: number, amount: number) => api.post('/payment/intent', { orderId, amount }) }
export const chatApi = {
  sendMessage: (sessionId: string, message: string) => api.post('/chat/send', { sessionId, message }),
  getHistory: (sessionId: string) => api.get(`/chat/history/${sessionId}`)
}
export const adminApi = {
  dashboard: () => api.get('/admin/dashboard'),
  products: {
    list: (params?: any) => api.get('/admin/products', { params }),
    create: (data: any) => api.post('/admin/products', data),
    update: (id: number, data: any) => api.put(`/admin/products/${id}`, data),
    delete: (id: number) => api.delete(`/admin/products/${id}`)
  },
  inventory: {
    movements: (productId: number) => api.get(`/admin/inventory/${productId}/movements`),
    adjust: (data: any) => api.post('/admin/inventory/adjust', data),
    alerts: () => api.get('/admin/inventory/alerts')
  },
  orders: {
    list: (params?: any) => api.get('/admin/orders', { params }),
    get: (id: number) => api.get(`/admin/orders/${id}`),
    updateStatus: (id: number, status: string) => api.put(`/admin/orders/${id}/status`, { status }),
    refund: (id: number) => api.post(`/admin/orders/${id}/refund`)
  },
  chatbot: {
    listKnowledge: () => api.get('/admin/chatbot/knowledge'),
    addKnowledge: (data: any) => api.post('/admin/chatbot/knowledge', data),
    deleteKnowledge: (id: number) => api.delete(`/admin/chatbot/knowledge/${id}`)
  }
}
"""

files["src/stores/auth.ts"] = """import { defineStore } from 'pinia'
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

  function logout() {
    authApi.logout().catch(() => {})
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { user, token, isLoading, isAuthenticated, isAdmin, login, register, logout, fetchMe, initAuth }
})
"""

files["src/stores/cart.ts"] = """import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { CartItem, Product } from '@/types'

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>(JSON.parse(localStorage.getItem('cart') || '[]'))

  const totalItems = computed(() => items.value.reduce((acc, item) => acc + item.quantity, 0))
  const totalPrice = computed(() => items.value.reduce((acc, item) => acc + (item.product.price * item.quantity), 0))
  const isEmpty = computed(() => items.value.length === 0)

  function save() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  function addItem(product: Product, quantity: number) {
    const existing = items.value.find(i => i.product.id === product.id)
    if (existing) existing.quantity += quantity
    else items.value.push({ product, quantity })
    save()
  }

  function removeItem(productId: number) {
    items.value = items.value.filter(i => i.product.id !== productId)
    save()
  }

  function updateQuantity(productId: number, quantity: number) {
    const item = items.value.find(i => i.product.id === productId)
    if (item) {
      item.quantity = quantity
      if (item.quantity <= 0) removeItem(productId)
      else save()
    }
  }

  function clearCart() {
    items.value = []
    save()
  }

  return { items, totalItems, totalPrice, isEmpty, addItem, removeItem, updateQuantity, clearCart }
})
"""

for path, content in files.items():
    full_path = os.path.join(base_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)
print("Part 1 done.")
