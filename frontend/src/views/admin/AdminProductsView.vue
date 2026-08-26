<template>
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
        <div><label class="block text-sm font-medium text-gray-700">Nome</label><input v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" @input="onName" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Slug</label><input v-model="form.slug" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Categoria</label>
          <select v-model.number="form.category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
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
import { adminApi, productsApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import { useNotification } from '@/composables/useNotification'
import { slugify } from '@/utils/format'
import type { Category } from '@/types'

const products = ref<any[]>([])
const categories = ref<Category[]>([])
const showModal = ref(false)
const editingId = ref<number | null>(null)
const form = ref({ name: '', slug: '', category_id: 1, description: '', price: 0, stock_quantity: 0, image_url: '', is_active: true })
const { success, error } = useNotification()

onMounted(async () => {
  const cat = await productsApi.categories()
  categories.value = cat.data
  loadProducts()
})

async function loadProducts() {
  try {
    const res = await adminApi.products.list()
    products.value = res.data.data
  } catch (e) {
    error('Erro ao carregar produtos')
  }
}

function onName() {
  if (!editingId.value) form.value.slug = slugify(form.value.name)
}

function openModal(p?: any) {
  if (p) {
    editingId.value = p.id
    form.value = { ...p }
  } else {
    editingId.value = null
    form.value = { name: '', slug: '', category_id: categories.value[0]?.id ?? 1, description: '', price: 0, stock_quantity: 0, image_url: '', is_active: true }
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
