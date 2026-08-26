<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div v-if="productsStore.loading" class="flex justify-center py-16"><AppSpinner class="w-12 h-12 text-brand" /></div>
    <div v-else-if="product" class="grid lg:grid-cols-2 gap-10">
      <div class="overflow-hidden rounded-2xl bg-slate-100 ring-1 ring-slate-200">
        <img :src="product.image_url || '/images/products/cpu-ryzen.jpg'" :alt="product.name" class="w-full h-[28rem] object-cover object-center" />
      </div>
      <div>
        <nav class="flex text-sm text-slate-500 gap-2 mb-4">
          <RouterLink to="/" class="hover:text-ink">Início</RouterLink>
          <span>/</span>
          <RouterLink to="/catalog" class="hover:text-ink">Catálogo</RouterLink>
          <span>/</span>
          <span class="text-ink">{{ product.name }}</span>
        </nav>
        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">{{ product.category?.name }}</p>
        <h1 class="mt-2 text-3xl font-bold text-ink">{{ product.name }}</h1>
        <p class="mt-4 text-3xl font-bold text-ink">{{ formatBRL(product.price) }}</p>
        <p class="mt-2 text-sm" :class="product.stock_quantity > 0 ? 'text-emerald-700' : 'text-red-600'">
          {{ service ? (product.stock_quantity > 0 ? `${product.stock_quantity} vagas nesta semana` : 'Agenda lotada') : (product.stock_quantity > 0 ? `${product.stock_quantity} em estoque na bancada` : 'Sem estoque') }}
        </p>
        <div class="mt-6 prose prose-sm text-slate-600 max-w-none" v-html="product.description || 'Sem descrição.'"></div>
        <div class="mt-8 flex gap-3 items-center">
          <input v-if="!service" type="number" v-model.number="quantity" min="1" :max="product.stock_quantity" class="w-20 rounded-lg border-slate-200 py-3 text-center ring-1 ring-slate-200" :disabled="product.stock_quantity === 0" />
          <button @click="addToCart" :disabled="product.stock_quantity === 0" class="btn-primary flex-1 py-3">
            {{ product.stock_quantity === 0 ? 'Indisponível' : (service ? 'Agendar serviço' : 'Adicionar ao carrinho') }}
          </button>
        </div>
        <ul class="mt-8 grid grid-cols-2 gap-3 text-xs text-slate-500">
          <li class="rounded-lg bg-slate-50 p-3">Troca em 7 dias (peça lacrada)</li>
          <li class="rounded-lg bg-slate-50 p-3">Garantia 90 dias (peças) / 30 dias (serviço)</li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '@/stores/products'
import { useCartStore } from '@/stores/cart'
import AppSpinner from '@/components/common/AppSpinner.vue'
import { formatBRL, isServiceProduct } from '@/utils/format'

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()
const cartStore = useCartStore()
const quantity = ref(1)
const product = computed(() => productsStore.currentProduct)
const service = computed(() => product.value ? isServiceProduct(product.value) : false)

onMounted(load)
watch(() => route.params.slug, load)

function load() {
  productsStore.fetchProduct(route.params.slug as string)
}

function addToCart() {
  if (product.value) {
    cartStore.addItem(product.value, service.value ? 1 : quantity.value)
    router.push({ name: 'cart' })
  }
}
</script>
