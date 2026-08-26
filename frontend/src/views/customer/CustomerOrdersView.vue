<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-ink mb-6">Meus pedidos</h1>
    <div v-if="ordersStore.loading" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
    <div v-else-if="ordersStore.orders.length === 0" class="card-surface p-12 text-center text-slate-500">Você ainda não fez nenhum pedido.</div>
    <div v-else class="card-surface overflow-hidden">
      <ul class="divide-y divide-slate-100">
        <li v-for="order in ordersStore.orders" :key="order.id">
          <RouterLink :to="{ name: 'customer-order-detail', params: { id: order.id } }" class="block hover:bg-slate-50">
            <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-brand-700 truncate">Pedido #{{ order.id }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ new Date(order.created_at).toLocaleDateString('pt-BR') }}</p>
              </div>
              <div class="ml-2 flex-shrink-0 flex flex-col items-end gap-2">
                <AppBadge :variant="order.status === 'PAID' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'info'" :text="order.status" />
                <p class="text-sm text-ink font-medium">{{ formatBRL(order.total) }}</p>
              </div>
            </div>
          </RouterLink>
        </li>
      </ul>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted } from 'vue'
import { useOrdersStore } from '@/stores/orders'
import AppSpinner from '@/components/common/AppSpinner.vue'
import AppBadge from '@/components/common/AppBadge.vue'
import { formatBRL } from '@/utils/format'

const ordersStore = useOrdersStore()
onMounted(() => {
  ordersStore.fetchMyOrders()
})
</script>
