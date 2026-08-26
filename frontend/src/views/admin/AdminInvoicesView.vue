<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-ink">Notas</h2>
      <div class="flex gap-2 text-sm">
        <button class="btn-ghost" :class="type === '' && 'ring-2 ring-brand'" @click="type = ''; load()">Todas</button>
        <button class="btn-ghost" @click="type = 'SALE'; load()">Vendas</button>
        <button class="btn-ghost" @click="type = 'ENTRY'; load()">Entradas</button>
      </div>
    </div>
    <div class="card-surface overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-3">Número</th>
            <th class="px-4 py-3">Tipo</th>
            <th class="px-4 py-3">Cliente / fornecedor</th>
            <th class="px-4 py-3">Pagamento</th>
            <th class="px-4 py-3">Total</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="inv in invoices" :key="inv.id" class="border-t">
            <td class="px-4 py-3 font-medium">{{ inv.number }}</td>
            <td class="px-4 py-3">{{ inv.type === 'SALE' ? 'Venda' : 'Entrada' }}</td>
            <td class="px-4 py-3">{{ inv.customer_name }}</td>
            <td class="px-4 py-3">{{ paymentLabel(inv.payment_method) }}</td>
            <td class="px-4 py-3">{{ formatBRL(inv.total) }}</td>
            <td class="px-4 py-3 text-right"><button class="text-brand-700 font-semibold" @click="open(inv.id)">Ver</button></td>
          </tr>
        </tbody>
      </table>
      <p v-if="invoices.length === 0" class="p-6 text-center text-slate-500">Nenhuma nota emitida.</p>
    </div>
    <AppModal v-model="show" :title="selected?.number || 'Nota'">
      <div v-if="selected" class="mt-3 text-sm space-y-2">
        <p>{{ selected.type === 'SALE' ? 'Nota de venda' : 'Nota de entrada' }} · {{ selected.customer_name }}</p>
        <ul class="divide-y">
          <li v-for="(item, idx) in selected.items" :key="idx" class="py-2 flex justify-between">
            <span>{{ item.quantity }}x {{ item.name }}</span>
            <span>{{ formatBRL(item.subtotal) }}</span>
          </li>
        </ul>
        <p class="font-bold text-right">{{ formatBRL(selected.total) }}</p>
        <p class="text-xs text-slate-500">{{ selected.notes }}</p>
      </div>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import { formatBRL, paymentLabel } from '@/utils/format'
import { useNotification } from '@/composables/useNotification'

const invoices = ref<any[]>([])
const selected = ref<any>(null)
const show = ref(false)
const type = ref('')
const { error } = useNotification()

onMounted(load)

async function load() {
  try {
    const res = await adminApi.invoices.list(type.value ? { type: type.value } : {})
    invoices.value = res.data
  } catch {
    error('Erro ao carregar notas')
  }
}

async function open(id: number) {
  try {
    const res = await adminApi.invoices.get(id)
    selected.value = res.data
    show.value = true
  } catch {
    error('Nota não encontrada')
  }
}
</script>
