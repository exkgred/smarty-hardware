import os

base_dir = "/home/admlocal/Documentos/porti/marketplace/frontend"
files = {}

files["src/views/customer/CustomerOrdersView.vue"] = """<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Meus Pedidos</h1>
    <div v-if="ordersStore.loading" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
    <div v-else-if="ordersStore.orders.length === 0" class="text-gray-500">Você ainda não fez nenhum pedido.</div>
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="order in ordersStore.orders" :key="order.id">
          <RouterLink :to="{ name: 'customer-order-detail', params: { id: order.id } }" class="block hover:bg-gray-50">
            <div class="px-4 py-4 sm:px-6 flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-brand truncate">Pedido #{{ order.id }}</p>
                <p class="mt-2 flex items-center text-sm text-gray-500">
                  <span class="truncate">{{ new Date(order.created_at).toLocaleDateString() }}</span>
                </p>
              </div>
              <div class="ml-2 flex-shrink-0 flex flex-col items-end gap-2">
                <AppBadge :variant="order.status === 'PAID' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'info'" :text="order.status" />
                <p class="text-sm text-gray-900 font-medium">R$ {{ order.total.toFixed(2) }}</p>
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

const ordersStore = useOrdersStore()
onMounted(() => {
  ordersStore.fetchMyOrders()
})
</script>
"""

files["src/views/customer/CustomerOrderDetailView.vue"] = """<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
      <RouterLink to="/my-orders" class="text-sm text-brand hover:underline">&larr; Voltar para Meus Pedidos</RouterLink>
    </div>
    <div v-if="ordersStore.loading || !order" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
    <div v-else>
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ order.id }}</h1>
        <AppBadge :variant="order.status === 'PAID' ? 'success' : order.status === 'CANCELLED' ? 'danger' : 'info'" :text="order.status" />
      </div>
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Itens do Pedido</h3>
        </div>
        <div class="border-t border-gray-200">
          <ul class="divide-y divide-gray-200">
            <li v-for="item in order.items" :key="item.id" class="px-4 py-4 sm:px-6 flex justify-between items-center">
              <div class="flex flex-col">
                <span class="text-sm font-medium text-gray-900">{{ item.product_name }}</span>
                <span class="text-sm text-gray-500">Qtd: {{ item.quantity }}</span>
              </div>
              <span class="text-sm text-gray-900">R$ {{ item.subtotal.toFixed(2) }}</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg px-4 py-5 sm:p-6 text-sm">
        <div class="flex justify-between py-2"><span class="text-gray-500">Subtotal</span><span class="font-medium text-gray-900">R$ {{ order.subtotal.toFixed(2) }}</span></div>
        <div class="flex justify-between py-2"><span class="text-gray-500">Frete</span><span class="font-medium text-gray-900">R$ {{ order.shipping_cost.toFixed(2) }}</span></div>
        <div class="flex justify-between py-2 border-t mt-2"><span class="font-bold text-gray-900">Total</span><span class="font-bold text-gray-900 text-lg">R$ {{ order.total.toFixed(2) }}</span></div>
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

const route = useRoute()
const ordersStore = useOrdersStore()
const order = computed(() => ordersStore.currentOrder)

onMounted(() => {
  ordersStore.fetchOrder(Number(route.params.id))
})
</script>
"""

files["src/views/admin/AdminLayout.vue"] = """<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-900 text-white flex flex-col">
      <div class="h-16 flex items-center px-4 font-bold text-xl border-b border-gray-800">Admin Panel</div>
      <nav class="flex-1 px-2 py-4 space-y-1">
        <RouterLink to="/admin" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Dashboard</RouterLink>
        <RouterLink to="/admin/products" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Produtos</RouterLink>
        <RouterLink to="/admin/inventory" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Estoque</RouterLink>
        <RouterLink to="/admin/orders" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Pedidos</RouterLink>
        <RouterLink to="/admin/chatbot" class="block px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800" exact-active-class="bg-gray-800">Chatbot Config</RouterLink>
      </nav>
    </div>
    <!-- Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
        <h1 class="text-lg font-semibold text-gray-900">Painel de Administração</h1>
        <div class="flex items-center gap-4">
          <span class="text-sm text-gray-600">Olá, {{ authStore.user?.name }}</span>
          <button @click="logout" class="text-sm text-red-600 hover:text-red-800 font-medium">Sair</button>
        </div>
      </header>
      <main class="flex-1 overflow-auto p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

function logout() {
  authStore.logout()
  router.push('/')
}
</script>
"""

files["src/views/admin/AdminDashboardView.vue"] = """<template>
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
"""

files["src/views/admin/AdminProductsView.vue"] = """<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Produtos</h2>
      <button @click="openModal()" class="bg-brand text-white px-4 py-2 rounded text-sm font-medium">Novo Produto</button>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Preço</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ product.price.toFixed(2) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.stock_quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="openModal(product)" class="text-brand hover:text-brand-dark mr-3">Editar</button>
              <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <AppModal v-model="showModal" :title="editingId ? 'Editar Produto' : 'Novo Produto'">
      <form @submit.prevent="saveProduct" class="space-y-4 mt-4">
        <div><label class="block text-sm font-medium text-gray-700">Nome</label><input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Slug</label><input v-model="form.slug" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">ID Categoria</label><input v-model.number="form.category_id" type="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Descrição</label><textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea></div>
        <div class="flex gap-4">
          <div class="flex-1"><label class="block text-sm font-medium text-gray-700">Preço</label><input v-model.number="form.price" type="number" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
          <div class="flex-1"><label class="block text-sm font-medium text-gray-700">Estoque</label><input v-model.number="form.stock_quantity" type="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700">URL Imagem</label><input v-model="form.image_url" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        <div class="flex items-center"><input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand" /><label class="ml-2 block text-sm text-gray-900">Ativo</label></div>
        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-dark sm:col-start-2">Salvar</button>
          <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">Cancelar</button>
        </div>
      </form>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import { useNotification } from '@/composables/useNotification'

const products = ref<any[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ name: '', slug: '', category_id: 1, description: '', price: 0, stock_quantity: 0, image_url: '', is_active: true })
const { success, error } = useNotification()

onMounted(loadProducts)

async function loadProducts() {
  try {
    const res = await adminApi.products.list()
    products.value = res.data.data
  } catch (e) {
    error('Erro ao carregar produtos')
  }
}

function openModal(p?: any) {
  if (p) {
    editingId.value = p.id
    form.value = { ...p }
  } else {
    editingId.value = null
    form.value = { name: '', slug: '', category_id: 1, description: '', price: 0, stock_quantity: 0, image_url: '', is_active: true }
  }
  showModal.value = true
}

async function saveProduct() {
  try {
    if (editingId.value) {
      await adminApi.products.update(editingId.value, form.value)
      success('Produto atualizado')
    } else {
      await adminApi.products.create(form.value)
      success('Produto criado')
    }
    showModal.value = false
    loadProducts()
  } catch (e) {
    error('Erro ao salvar produto')
  }
}

async function deleteProduct(id: number) {
  if (confirm('Tem certeza?')) {
    try {
      await adminApi.products.delete(id)
      success('Produto excluído')
      loadProducts()
    } catch (e) {
      error('Erro ao excluir produto')
    }
  }
}
</script>
"""

files["src/views/admin/AdminInventoryView.vue"] = """<template>
  <div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Controle de Estoque</h2>
    
    <div class="mb-6 flex gap-4">
      <input type="number" v-model="productId" placeholder="ID do Produto" class="rounded-md border-gray-300 shadow-sm" />
      <button @click="loadMovements" class="bg-gray-800 text-white px-4 py-2 rounded">Ver Movimentações</button>
      <button @click="showModal = true" class="bg-brand text-white px-4 py-2 rounded ml-auto">Ajuste Manual</button>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantidade</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="mov in movements" :key="mov.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(mov.created_at).toLocaleString() }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm"><AppBadge :variant="mov.type === 'IN' ? 'success' : 'danger'" :text="mov.type" /></td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ mov.quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ mov.reason }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="movements.length === 0" class="p-6 text-center text-gray-500">Nenhuma movimentação encontrada.</div>
    </div>
    
    <AppModal v-model="showModal" title="Ajuste de Estoque">
      <form @submit.prevent="saveAdjustment" class="space-y-4 mt-4">
        <div><label class="block text-sm font-medium text-gray-700">ID Produto</label><input v-model.number="form.product_id" type="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Tipo</label><select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="IN">Entrada</option><option value="OUT">Saída</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700">Quantidade</label><input v-model.number="form.quantity" type="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Motivo</label><input v-model="form.reason" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" /></div>
        <div class="mt-5 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm sm:col-start-2">Salvar</button>
          <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 sm:col-start-1 sm:mt-0">Cancelar</button>
        </div>
      </form>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import AppBadge from '@/components/common/AppBadge.vue'
import { useNotification } from '@/composables/useNotification'

const productId = ref<number>()
const movements = ref<any[]>([])
const showModal = ref(false)
const form = ref({ product_id: 0, type: 'IN', quantity: 0, reason: '' })
const { success, error } = useNotification()

async function loadMovements() {
  if (!productId.value) return
  try {
    const res = await adminApi.inventory.movements(productId.value)
    movements.value = res.data
  } catch (e) {
    error('Erro ao carregar movimentações')
  }
}

async function saveAdjustment() {
  try {
    await adminApi.inventory.adjust(form.value)
    success('Ajuste realizado')
    showModal.value = false
    if (productId.value === form.value.product_id) loadMovements()
  } catch (e) {
    error('Erro ao ajustar estoque')
  }
}
</script>
"""

files["src/views/admin/AdminOrdersView.vue"] = """<template>
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
"""

files["src/views/admin/AdminChatbotView.vue"] = """<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Configuração do Chatbot</h2>
      <button @click="showModal = true" class="bg-brand text-white px-4 py-2 rounded text-sm font-medium">Novo Documento</button>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="doc in documents" :key="doc.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ doc.title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doc.type }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="deleteDoc(doc.id)" class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="documents.length === 0" class="p-6 text-center text-gray-500">Nenhum documento cadastrado.</div>
    </div>
    
    <AppModal v-model="showModal" title="Adicionar Conhecimento">
      <form @submit.prevent="saveDoc" class="space-y-4 mt-4">
        <div><label class="block text-sm font-medium text-gray-700">Título</label><input v-model="form.title" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Tipo</label><select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="faq">FAQ</option><option value="policy">Política</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700">Conteúdo</label><textarea v-model="form.content" required rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea></div>
        <div class="mt-5 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm sm:col-start-2">Salvar</button>
          <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 sm:col-start-1 sm:mt-0">Cancelar</button>
        </div>
      </form>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import { useNotification } from '@/composables/useNotification'

const documents = ref<any[]>([])
const showModal = ref(false)
const form = ref({ title: '', content: '', type: 'faq' })
const { success, error } = useNotification()

onMounted(loadDocs)

async function loadDocs() {
  try {
    const res = await adminApi.chatbot.listKnowledge()
    documents.value = res.data
  } catch (e) {
    error('Erro ao carregar documentos')
  }
}

async function saveDoc() {
  try {
    await adminApi.chatbot.addKnowledge(form.value)
    success('Documento salvo')
    showModal.value = false
    form.value = { title: '', content: '', type: 'faq' }
    loadDocs()
  } catch (e) {
    error('Erro ao salvar documento')
  }
}

async function deleteDoc(id: number) {
  if (confirm('Tem certeza?')) {
    try {
      await adminApi.chatbot.deleteKnowledge(id)
      success('Documento excluído')
      loadDocs()
    } catch (e) {
      error('Erro ao excluir documento')
    }
  }
}
</script>
"""

for path, content in files.items():
    full_path = os.path.join(base_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)

print("Part 5 done. All frontend files created.")
