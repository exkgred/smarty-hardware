<template>
  <div class="space-y-8">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold text-ink">Estoque e entradas</h2>
    </div>

    <section class="card-surface p-5">
      <h3 class="font-semibold text-ink">Nota de entrada</h3>
      <p class="text-xs text-slate-500 mb-4">Dá entrada no estoque e emite a nota de mercadoria (sandbox).</p>
      <form class="grid sm:grid-cols-2 gap-3" @submit.prevent="saveEntry">
        <input v-model="entry.supplier" required placeholder="Fornecedor" class="rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm" />
        <input v-model="entry.document_number" placeholder="Nº da NF (opcional)" class="rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm" />
        <select v-model.number="entry.product_id" required class="rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm">
          <option :value="0" disabled>Produto</option>
          <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} ({{ p.stock_quantity }})</option>
        </select>
        <input v-model.number="entry.quantity" type="number" min="1" required placeholder="Qtd" class="rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm" />
        <input v-model.number="entry.unit_cost" type="number" step="0.01" min="0" placeholder="Custo unitário" class="rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm" />
        <button class="btn-primary sm:col-span-2">Registrar entrada</button>
      </form>
    </section>

    <section class="card-surface overflow-hidden">
      <div class="px-5 py-3 border-b flex justify-between items-center">
        <h3 class="font-semibold text-ink">Movimentações recentes</h3>
        <button class="text-sm text-brand-700" @click="loadMovements">Atualizar</button>
      </div>
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
          <tr>
            <th class="px-4 py-2">Data</th>
            <th class="px-4 py-2">Produto</th>
            <th class="px-4 py-2">Tipo</th>
            <th class="px-4 py-2">Qtd</th>
            <th class="px-4 py-2">NF</th>
            <th class="px-4 py-2">Motivo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="mov in movements" :key="mov.id" class="border-t">
            <td class="px-4 py-2 text-slate-500">{{ mov.created_at ? new Date(mov.created_at).toLocaleString('pt-BR') : '—' }}</td>
            <td class="px-4 py-2">{{ mov.product_name }}</td>
            <td class="px-4 py-2"><AppBadge :variant="mov.type === 'IN' || mov.type === 'RELEASE' ? 'success' : 'danger'" :text="mov.type" /></td>
            <td class="px-4 py-2">{{ mov.quantity }}</td>
            <td class="px-4 py-2">{{ mov.document_number || '—' }}</td>
            <td class="px-4 py-2 text-slate-500">{{ mov.reason }}</td>
          </tr>
        </tbody>
      </table>
    </section>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { adminApi } from '@/services/api'
import AppBadge from '@/components/common/AppBadge.vue'
import { useNotification } from '@/composables/useNotification'
import type { Product } from '@/types'

const products = ref<Product[]>([])
const movements = ref<any[]>([])
const entry = ref({ supplier: '', document_number: '', product_id: 0, quantity: 1, unit_cost: 0 })
const { success, error } = useNotification()

onMounted(async () => {
  await Promise.all([loadProducts(), loadMovements()])
})

async function loadProducts() {
  const res = await adminApi.products.list({ per_page: 100 })
  products.value = res.data.data
}

async function loadMovements() {
  try {
    const res = await adminApi.inventory.recentMovements()
    movements.value = res.data
  } catch {
    error('Erro ao carregar movimentações')
  }
}

async function saveEntry() {
  try {
    await adminApi.inventory.entry({
      supplier: entry.value.supplier,
      document_number: entry.value.document_number || undefined,
      items: [{ product_id: entry.value.product_id, quantity: entry.value.quantity, unit_cost: entry.value.unit_cost || undefined }]
    })
    success('Entrada e nota registradas')
    entry.value = { supplier: '', document_number: '', product_id: 0, quantity: 1, unit_cost: 0 }
    await Promise.all([loadProducts(), loadMovements()])
  } catch {
    error('Não foi possível registrar a entrada')
  }
}
</script>
