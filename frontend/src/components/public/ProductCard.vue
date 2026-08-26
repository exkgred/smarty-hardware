<template>
  <article class="group card-surface flex flex-col overflow-hidden hover:shadow-md hover:border-brand/30 transition">
    <RouterLink :to="{ name: 'product-detail', params: { slug: product.slug } }" class="relative h-52 bg-slate-100 overflow-hidden">
      <img
        :src="product.image_url || '/images/products/cpu-ryzen.jpg'"
        :alt="product.name"
        class="h-full w-full object-cover object-center transition duration-500 group-hover:scale-105"
      />
      <span
        v-if="service"
        class="absolute left-3 top-3 rounded-full bg-ink/85 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-brand"
      >Serviço</span>
      <span
        v-else-if="product.category"
        class="absolute left-3 top-3 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wide text-slate-700"
      >{{ product.category.name }}</span>
      <div v-if="product.stock_quantity === 0" class="absolute inset-0 flex items-center justify-center bg-ink/60">
        <span class="rounded bg-red-600 px-3 py-1 text-xs font-bold text-white">Indisponível</span>
      </div>
    </RouterLink>
    <div class="flex flex-1 flex-col p-4">
      <h3 class="text-sm font-semibold text-slate-900 leading-snug">
        <RouterLink :to="{ name: 'product-detail', params: { slug: product.slug } }">{{ product.name }}</RouterLink>
      </h3>
      <p class="mt-1 line-clamp-2 text-xs text-slate-500" v-html="excerpt"></p>
      <div class="mt-auto pt-4 flex items-end justify-between gap-3">
        <p class="text-lg font-bold text-ink">{{ formatBRL(product.price) }}</p>
        <button
          @click="addToCart"
          :disabled="product.stock_quantity === 0"
          class="rounded-lg bg-ink px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 disabled:bg-slate-300"
        >
          {{ service ? 'Agendar' : 'Adicionar' }}
        </button>
      </div>
    </div>
  </article>
</template>
<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import type { Product } from '@/types'
import { formatBRL, isServiceProduct } from '@/utils/format'

const props = defineProps<{ product: Product }>()
const router = useRouter()
const cartStore = useCartStore()
const service = computed(() => isServiceProduct(props.product))
const excerpt = computed(() => {
  const raw = props.product.description?.replace(/<[^>]+>/g, ' ') ?? ''
  return raw.trim().slice(0, 90)
})

function addToCart() {
  cartStore.addItem(props.product, 1)
  router.push({ name: 'cart' })
}
</script>
