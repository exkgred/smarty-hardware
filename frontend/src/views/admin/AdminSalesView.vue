<template>
  <div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
      <h2 class="text-2xl font-bold text-ink">Venda no balcão</h2>
      <div class="card-surface p-4">
        <input v-model="search" type="search" placeholder="Buscar produto..." class="w-full rounded-lg ring-1 ring-slate-200 px-3 py-2 text-sm" />
        <ul class="mt-3 max-h-72 overflow-auto divide-y">
          <li v-for="p in filteredProducts" :key="p.id" class="py-2 flex items-center justify-between gap-3">
            <div>
              <p class="text-sm font-medium">{{ p.name }}</p>
              <p class="text-xs text-slate-500">{{ formatBRL(p.price) }} · estoque {{ p.stock_quantity }}</p>
            </div>
            <button type="button" class="text-sm font-semibold text-brand-700" :disabled="p.stock_quantity < 1" @click="addLine(p)">Adicionar</button>
          </li>
        </ul>
      </div>
      <div class="card-surface overflow-hidden">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-left text-xs uppercase text-slate-500">
            <tr><th class="px-4 py-2">Item</th><th class="px-4 py-2">Qtd</th><th class="px-4 py-2">Subtotal</th><th></th></tr>
          </thead>
          <tbody>
            <tr v-for="line in lines" :key="line.product.id" class="border-t">
              <td class="px-4 py-2">{{ line.product.name }}</td>
              <td class="px-4 py-2"><input type="number" min="1" :max="line.product.stock_quantity" v-model.number="line.quantity" class="w-16 rounded ring-1 ring-slate-200 px-1 py-1" /></td>
              <td class="px-4 py-2">{{ formatBRL(line.product.price * line.quantity) }}</td>
              <td class="px-4 py-2 text-right"><button class="text-red-600" @click="removeLine(line.product.id)">x</button></td>
            </tr>
          </tbody>
        </table>
        <p v-if="lines.length === 0" class="p-4 text-slate-500 text-sm">Nenhum item na venda.</p>
      </div>
    </div>
    <div class="card-surface p-5 space-y-4 h-fit">
      <h3 class="font-semibold text-ink">Cliente e pagamento</h3>
      <div>
        <label class="text-xs font-semibold text-slate-500">Cliente cadastrado</label>
        <select v-model="customerId" class="mt-1 w-full rounded-lg ring-1 ring-slate-200 py-2 px-2 text-sm">
          <option :value="null">Balcão / avulso</option>
          <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }} — {{ c.email }}</option>
        </select>
      </div>
      <div v-if="!customerId">
        <label class="text-xs font-semibold text-slate-500">Nome no cupom (opcional)</label>
        <input v-model="customerName" class="mt-1 w-full rounded-lg ring-1 ring-slate-200 py-2 px-2 text-sm" />
      </div>
      <div>
        <label class="text-xs font-semibold text-slate-500">Pagamento</label>
        <select v-model="paymentMethod" class="mt-1 w-full rounded-lg ring-1 ring-slate-200 py-2 px-2 text-sm">
          <option v-for="m in PAYMENT_METHODS" :key="m.value" :value="m.value">{{ m.label }}</option>
        </select>
      </div>
      <div class="flex justify-between text-sm"><span>Total</span><span class="font-bold">{{ formatBRL(total) }}</span></div>
      <button type="button" class="btn-primary w-full" :disabled="lines.length === 0 || saving" @click="submit">
        {{ saving ? 'Registrando...' : 'Finalizar venda' }}
      </button>
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { adminApi } from '@/services/api'
import { formatBRL, PAYMENT_METHODS } from '@/utils/format'
import { useNotification } from '@/composables/useNotification'
import type { Product, User } from '@/types'

const products = ref<Product[]>([])
const customers = ref<User[]>([])
const search = ref('')
const lines = ref<{ product: Product; quantity: number }[]>([])
const customerId = ref<number | null>(null)
const customerName = ref('')
const paymentMethod = ref('CASH')
const saving = ref(false)
const { success, error } = useNotification()

const filteredProducts = computed(() => {
  const q = search.value.toLowerCase()
  return products.value.filter(p => !q || p.name.toLowerCase().includes(q)).slice(0, 40)
})
const total = computed(() => lines.value.reduce((s, l) => s + l.product.price * l.quantity, 0))

onMounted(async () => {
  try {
    const [prodRes, custRes] = await Promise.all([
      adminApi.products.list({ per_page: 100 }),
      adminApi.customers()
    ])
    products.value = prodRes.data.data
    customers.value = custRes.data
  } catch {
    error('Não foi possível carregar a venda')
  }
})

function addLine(p: Product) {
  const existing = lines.value.find(l => l.product.id === p.id)
  if (existing) existing.quantity += 1
  else lines.value.push({ product: p, quantity: 1 })
}
function removeLine(id: number) {
  lines.value = lines.value.filter(l => l.product.id !== id)
}

async function submit() {
  saving.value = true
  try {
    const res = await adminApi.sales.create({
      customer_id: customerId.value,
      customer_name: customerName.value || undefined,
      payment_method: paymentMethod.value,
      items: lines.value.map(l => ({ product_id: l.product.id, quantity: l.quantity }))
    })
    success(`Venda #${res.data.id} e nota ${res.data.invoice?.number || ''} registradas`)
    lines.value = []
    customerName.value = ''
  } catch {
    error('Falha ao registrar a venda (estoque ou dados).')
  } finally {
    saving.value = false
  }
}
</script>
