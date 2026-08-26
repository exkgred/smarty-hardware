<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h2>
    <div v-if="loading" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
    <div v-else>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-sm font-medium text-gray-500">Total de Pedidos</p>
          <p class="mt-2 text-3xl font-semibold text-gray-900">{{ stats?.totalOrders || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-sm font-medium text-gray-500">Pedidos Pendentes</p>
          <p class="mt-2 text-3xl font-semibold text-gray-900">{{ stats?.pendingOrders || 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-sm font-medium text-gray-500">Total de Vendas</p>
          <p class="mt-2 text-3xl font-semibold text-gray-900">R$ {{ (stats?.totalSales || 0).toFixed(2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
          <p class="text-sm font-medium text-gray-500">Produtos com Estoque Baixo</p>
          <p class="mt-2 text-3xl font-semibold text-red-600">{{ stats?.lowStockProducts || 0 }}</p>
        </div>
      </div>
      
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Últimos Pedidos</h3>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="order in recentOrders" :key="order.id" class="px-6 py-4 flex justify-between">
              <div><p class="text-sm font-medium text-gray-900">Pedido #{{ order.id }}</p><p class="text-xs text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</p></div>
              <div class="text-right"><p class="text-sm font-medium text-gray-900">R$ {{ order.total.toFixed(2) }}</p><p class="text-xs text-gray-500">{{ order.status }}</p></div>
            </li>
          </ul>
        </div>
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Alertas de Estoque Baixo</h3>
          </div>
          <ul class="divide-y divide-gray-200">
            <li v-for="alert in lowStockAlerts" :key="alert.id" class="px-6 py-4 flex justify-between">
              <span class="text-sm font-medium text-gray-900">{{ alert.product_name }}</span>
              <span class="text-sm font-bold text-red-600">Restam {{ alert.stock_quantity }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import AppSpinner from '@/components/common/AppSpinner.vue'

const loading = ref(true)
const stats = ref<any>(null)
const recentOrders = ref<any[]>([])
const lowStockAlerts = ref<any[]>([])

onMounted(async () => {
  try {
    const [dashRes, alertRes] = await Promise.all([
      adminApi.dashboard(),
      adminApi.inventory.alerts()
    ])
    stats.value = dashRes.data
    recentOrders.value = dashRes.data.recent_orders || []
    lowStockAlerts.value = alertRes.data || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})
</script>
