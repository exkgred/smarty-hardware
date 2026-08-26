<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-ink mb-8">Carrinho</h1>
    <div v-if="cartStore.isEmpty" class="card-surface p-12 text-center">
      <p class="text-lg text-slate-500 mb-4">Seu carrinho está vazio.</p>
      <RouterLink to="/catalog" class="text-brand-700 font-semibold hover:text-brand-dark">Ir para o catálogo</RouterLink>
    </div>
    <div v-else class="card-surface overflow-hidden">
      <ul class="divide-y divide-slate-100">
        <li v-for="item in cartStore.items" :key="item.product.id" class="p-6 flex gap-4">
          <img :src="item.product.image_url || '/images/products/cpu-ryzen.jpg'" class="w-24 h-24 rounded-lg object-cover ring-1 ring-slate-200" :alt="item.product.name" />
          <div class="flex-1 flex flex-col">
            <div class="flex justify-between gap-4">
              <h3 class="font-semibold text-ink">
                <RouterLink :to="{ name: 'product-detail', params: { slug: item.product.slug } }">{{ item.product.name }}</RouterLink>
              </h3>
              <p class="font-semibold">{{ formatBRL(item.product.price * item.quantity) }}</p>
            </div>
            <p class="text-xs text-slate-500">{{ item.product.category?.name }}</p>
            <div class="mt-auto flex items-center justify-between text-sm">
              <div class="flex items-center gap-2">
                <label>Qtd</label>
                <input type="number" :value="item.quantity" @change="e => updateQty(item.product.id, e)" min="1" :max="item.product.stock_quantity" class="w-16 rounded-md border-slate-200 py-1 text-center ring-1 ring-slate-200" />
              </div>
              <button type="button" @click="cartStore.removeItem(item.product.id)" class="font-medium text-red-600 hover:text-red-500">Remover</button>
            </div>
          </div>
        </li>
      </ul>
      <div class="border-t border-slate-100 p-6">
        <div class="flex justify-between text-slate-600">
          <span>Subtotal</span>
          <span>{{ formatBRL(cartStore.totalPrice) }}</span>
        </div>
        <p class="mt-2 text-xs text-slate-400">Frete calculado no checkout (R$ 15 para peças · serviços sem frete).</p>
        <div class="mt-6 flex gap-3">
          <RouterLink to="/catalog" class="btn-ghost flex-1">Continuar comprando</RouterLink>
          <RouterLink to="/checkout" class="btn-primary flex-1">Finalizar</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useCartStore } from '@/stores/cart'
import { formatBRL } from '@/utils/format'
const cartStore = useCartStore()

function updateQty(id: number, e: Event) {
  const target = e.target as HTMLInputElement
  cartStore.updateQuantity(id, parseInt(target.value))
}
</script>
