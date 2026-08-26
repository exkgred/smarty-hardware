<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
      <RouterLink to="/my-orders" class="text-sm text-brand hover:underline">&larr; Voltar para Meus Pedidos</RouterLink>
    </div>
    <div v-if="ordersStore.loading || !order" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-ink">Pedido #{{ order.id }}</h1>
        <AppBadge :variant="order.status === 'PAID' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'info'" :text="order.status" />
      </div>
      <div class="card-surface overflow-hidden">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg font-medium text-ink">Itens do pedido</h3>
        </div>
        <div class="border-t border-slate-100">
          <ul class="divide-y divide-slate-100">
            <li v-for="item in order.items" :key="item.id" class="px-4 py-4 sm:px-6 flex justify-between items-center">
              <div class="flex flex-col">
                <span class="text-sm font-medium text-ink">{{ item.product_name }}</span>
                <span class="text-sm text-slate-500">Qtd: {{ item.quantity }}</span>
              </div>
              <span class="text-sm text-ink">{{ formatBRL(item.subtotal) }}</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-6 card-surface px-4 py-5 sm:p-6 text-sm">
        <div class="flex justify-between py-2"><span class="text-slate-500">Pagamento</span><span class="font-medium text-ink">{{ paymentLabel(order.payment_method) }}</span></div>
        <div v-if="order.shipping_address || order.shipping_details" class="py-2">
          <span class="text-slate-500">Endereço</span>
          <p class="font-medium text-ink mt-1">{{ order.shipping_address }}</p>
        </div>
        <div v-if="order.invoice" class="flex justify-between py-2"><span class="text-slate-500">Nota</span><span class="font-medium text-ink">{{ order.invoice.number }}</span></div>
        <div class="flex justify-between py-2"><span class="text-slate-500">Subtotal</span><span class="font-medium text-ink">{{ formatBRL(order.subtotal) }}</span></div>
        <div class="flex justify-between py-2"><span class="text-slate-500">Frete</span><span class="font-medium text-ink">{{ formatBRL(order.shipping_cost) }}</span></div>
        <div class="flex justify-between py-2 border-t border-slate-100 mt-2"><span class="font-bold text-ink">Total</span><span class="font-bold text-ink text-lg">{{ formatBRL(order.total) }}</span></div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'
import AppSpinner from '@/components/common/AppSpinner.vue'
import AppBadge from '@/components/common/AppBadge.vue'
import { formatBRL, paymentLabel } from '@/utils/format'

const route = useRoute()
const ordersStore = useOrdersStore()
const order = computed(() => ordersStore.currentOrder)

onMounted(() => {
  ordersStore.fetchOrder(Number(route.params.id))
})
</script>
