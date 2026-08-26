<template>
  <div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-6">
      <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
      </svg>
    </div>
    <h1 class="text-3xl font-bold text-ink">Pedido confirmado</h1>
    <p class="mt-4 text-lg text-slate-500">
      Obrigado. Pedido <strong class="text-ink">#{{ $route.query.orderId }}</strong>
      <span v-if="order?.invoice"> · Nota {{ order.invoice.number }}</span>.
    </p>
    <div v-if="order?.payment_receipt" class="mt-6 text-left card-surface p-5 text-sm text-slate-600">
      <p class="font-semibold text-ink">{{ order.payment_method_label || order.payment_method }}</p>
      <p class="mt-1">{{ order.payment_receipt.instructions || order.payment_receipt.status }}</p>
      <p v-if="order.payment_receipt.copy_paste" class="mt-2 break-all font-mono text-xs bg-slate-50 p-2 rounded">{{ order.payment_receipt.copy_paste }}</p>
      <p v-if="order.payment_receipt.digitable_line" class="mt-2 font-mono text-xs">{{ order.payment_receipt.digitable_line }}</p>
    </div>
    <p class="mt-4 text-slate-500">Peças seguem para despacho; serviços entram na agenda da bancada Smarty.</p>
    <div class="mt-8 flex justify-center gap-4">
      <RouterLink to="/my-orders" class="btn-ghost">Meus pedidos</RouterLink>
      <RouterLink to="/catalog" class="btn-primary">Continuar na loja</RouterLink>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'
import type { Order } from '@/types'

const route = useRoute()
const ordersStore = useOrdersStore()
const order = ref<Order | null>(null)

onMounted(async () => {
  const id = Number(route.query.orderId)
  if (!id) return
  await ordersStore.fetchOrder(id)
  order.value = ordersStore.currentOrder
})
</script>
