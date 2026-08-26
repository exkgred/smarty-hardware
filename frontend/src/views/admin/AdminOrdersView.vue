<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Pedidos</h2>
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ order.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ order.total.toFixed(2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><AppBadge :variant="order.status === 'PAID' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'info'" :text="order.status" /></td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openModal(order)" class="text-brand hover:text-brand-dark">Ver Detalhes</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AppModal v-model="showModal" title="Detalhes do Pedido">
      <div v-if="selectedOrder" class="mt-4 space-y-4">
        <div class="flex justify-between items-center">
          <span class="font-bold">Status Atual:</span>
          <select v-model="selectedOrder.status" @change="updateStatus(selectedOrder.id, selectedOrder.status)" class="rounded border-gray-300 shadow-sm text-sm">
            <option value="PENDING">PENDING</option>
            <option value="PAID">PAID</option>
            <option value="SHIPPED">SHIPPED</option>
            <option value="DELIVERED">DELIVERED</option>
            <option value="CANCELLED">CANCELLED</option>
          </select>
        </div>
        <div class="border-t pt-4">
          <h4 class="font-medium mb-2">Itens:</h4>
          <ul class="text-sm divide-y">
            <li v-for="item in selectedOrder.items" :key="item.id" class="py-2 flex justify-between">
              <span>{{ item.quantity }}x {{ item.product_name }}</span>
              <span>R$ {{ item.subtotal.toFixed(2) }}</span>
            </li>
          </ul>
        </div>
        <div class="flex justify-end pt-4 border-t gap-2">
          <button v-if="selectedOrder.status === 'PAID'" @click="refundOrder(selectedOrder.id)" class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm font-medium hover:bg-red-200">Reembolsar</button>
          <button @click="showModal = false" class="bg-gray-100 text-gray-700 px-3 py-1 rounded text-sm font-medium hover:bg-gray-200">Fechar</button>
        </div>
      </div>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import AppBadge from '@/components/common/AppBadge.vue'
import { useNotification } from '@/composables/useNotification'

const orders = ref<any[]>([])
const showModal = ref(false)
const selectedOrder = ref<any>(null)
const { success, error } = useNotification()

onMounted(loadOrders)

async function loadOrders() {
  try {
    const res = await adminApi.orders.list()
    orders.value = res.data.data
  } catch (e) {
    error('Erro ao carregar pedidos')
  }
}

async function openModal(order: any) {
  try {
    const res = await adminApi.orders.get(order.id)
    selectedOrder.value = res.data
    showModal.value = true
  } catch (e) {
    error('Erro ao carregar detalhes')
  }
}

async function updateStatus(id: number, status: string) {
  try {
    await adminApi.orders.updateStatus(id, status)
    success('Status atualizado')
    loadOrders()
  } catch (e) {
    error('Erro ao atualizar status')
  }
}

async function refundOrder(id: number) {
  if (confirm('Deseja realmente reembolsar este pedido?')) {
    try {
      await adminApi.orders.refund(id)
      success('Pedido reembolsado')
      showModal.value = false
      loadOrders()
    } catch (e) {
      error('Erro ao reembolsar pedido')
    }
  }
}
</script>
