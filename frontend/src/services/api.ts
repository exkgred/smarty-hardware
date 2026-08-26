import axios from 'axios'
import type { Address, CheckoutPayload } from '@/types'

const api = axios.create({
  baseURL: '/api',
  headers: { Accept: 'application/json' }
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token && config.headers) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  res => res,
  err => {
    const status = err.response?.status
    const path = window.location.pathname
    if (status === 401 && !path.startsWith('/login') && !path.startsWith('/register')) {
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
  me: () => api.get('/me'),
  updateProfile: (data: { name?: string; phone?: string | null; address?: Address | null }) =>
    api.put('/me', data)
}
export const cepApi = {
  lookup: (cep: string) => api.get(`/cep/${cep.replace(/\D/g, '')}`)
}
export const cardsApi = {
  list: () => api.get('/me/cards'),
  create: (data: { number: string; holder_name: string; exp_month: number; exp_year: number; cvv: string; is_default?: boolean }) =>
    api.post('/me/cards', data),
  remove: (id: number) => api.delete(`/me/cards/${id}`)
}
export const productsApi = {
  list: (params?: any) => api.get('/products', { params }),
  show: (slug: string) => api.get(`/products/${slug}`),
  categories: () => api.get('/categories')
}
export const ordersApi = {
  myOrders: () => api.get('/orders/my'),
  getOrder: (id: number) => api.get(`/orders/${id}`),
  checkout: (items: any[], shippingCost: number, extra?: CheckoutPayload) =>
    api.post('/orders/checkout', { items, shippingCost, ...extra })
}
export const paymentApi = {
  methods: () => api.get('/payment/methods'),
  createIntent: (orderId: number, amount: number) => api.post('/payment/intent', { orderId, amount })
}
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
    entry: (data: any) => api.post('/admin/inventory/entries', data),
    recentMovements: () => api.get('/admin/inventory/movements'),
    alerts: () => api.get('/admin/inventory/alerts')
  },
  customers: () => api.get('/admin/customers'),
  sales: {
    create: (data: any) => api.post('/admin/sales', data)
  },
  invoices: {
    list: (params?: any) => api.get('/admin/invoices', { params }),
    get: (id: number) => api.get(`/admin/invoices/${id}`),
    issue: (orderId: number) => api.post(`/admin/orders/${orderId}/invoice`)
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
