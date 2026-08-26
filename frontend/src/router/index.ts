import { createRouter, createWebHistory } from 'vue-router'
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
    { path: '/account', name: 'account', component: () => import('@/views/customer/AccountView.vue'), meta: { requiresAuth: true } },
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
        { path: 'sales', name: 'admin-sales', component: () => import('@/views/admin/AdminSalesView.vue') },
        { path: 'inventory', name: 'admin-inventory', component: () => import('@/views/admin/AdminInventoryView.vue') },
        { path: 'invoices', name: 'admin-invoices', component: () => import('@/views/admin/AdminInvoicesView.vue') },
        { path: 'orders', name: 'admin-orders', component: () => import('@/views/admin/AdminOrdersView.vue') },
        { path: 'chatbot', name: 'admin-chatbot', component: () => import('@/views/admin/AdminChatbotView.vue') }
      ]
    }
  ]
})

router.beforeEach(async (to, _from, next) => {
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
