<template>
  <div>
    <section class="relative overflow-hidden bg-ink text-white">
      <div class="absolute inset-0">
        <img src="/images/products/psu.jpg" alt="" class="h-full w-full object-cover opacity-35" />
        <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/40" />
      </div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <p class="text-brand-200 text-xs font-semibold uppercase tracking-[0.25em]">Peças · Upgrades · Bancada</p>
        <h1 class="mt-4 max-w-2xl text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
          Hardware certo, assistência de quem monta PC todos os dias.
        </h1>
        <p class="mt-5 max-w-xl text-lg text-slate-300">
          Processadores, placas de vídeo, memória e HD com estoque real. Também limpamos, formatamos, diagnosticamos e montamos o seu setup.
        </p>
        <div class="mt-8 flex flex-wrap gap-3">
          <RouterLink to="/catalog" class="btn-primary" data-cy="cta-catalog">Ver peças</RouterLink>
          <RouterLink to="/catalog?category=servicos" class="btn-ghost !border-slate-600 !bg-white/5 !text-white hover:!bg-white/10">Agendar assistência</RouterLink>
        </div>
        <dl class="mt-12 grid grid-cols-3 max-w-lg gap-6 text-sm">
          <div>
            <dt class="text-slate-400">Garantia peças</dt>
            <dd class="text-xl font-semibold text-white">90 dias</dd>
          </div>
          <div>
            <dt class="text-slate-400">Bancada</dt>
            <dd class="text-xl font-semibold text-white">30 dias</dd>
          </div>
          <div>
            <dt class="text-slate-400">Frete peças</dt>
            <dd class="text-xl font-semibold text-white">R$ 15</dd>
          </div>
        </dl>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
        <RouterLink
          v-for="cat in categoryCards"
          :key="cat.slug"
          :to="{ name: 'catalog', query: { category: cat.slug } }"
          class="card-surface p-4 hover:border-brand/40 hover:shadow-glow transition"
        >
          <p class="text-xs uppercase tracking-wide text-brand-700 font-semibold">{{ cat.kicker }}</p>
          <p class="mt-1 font-semibold text-slate-900">{{ cat.name }}</p>
          <p class="mt-1 text-xs text-slate-500">{{ cat.hint }}</p>
        </RouterLink>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <div class="flex items-end justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-ink">Peças em destaque</h2>
          <p class="text-sm text-slate-500">Fotos reais dos produtos que estão na loja agora.</p>
        </div>
        <RouterLink to="/catalog" class="text-sm font-semibold text-brand-700 hover:text-brand-dark">Ver tudo</RouterLink>
      </div>
      <div v-if="productsStore.loading" class="flex justify-center py-12"><AppSpinner class="w-10 h-10 text-brand" /></div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <ProductCard v-for="product in parts" :key="product.id" :product="product" />
      </div>
    </section>

    <section class="bg-slate-100/80">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h2 class="text-2xl font-bold text-ink">Assistência técnica</h2>
        <p class="mt-1 text-sm text-slate-500 mb-6">Leve o equipamento ou agende no checkout. Serviços não cobram frete.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
          <ProductCard v-for="product in services" :key="product.id" :product="product" />
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
      <h2 class="text-2xl font-bold text-ink">Como funciona</h2>
      <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="card-surface p-6">
          <p class="text-xs font-bold uppercase tracking-wide text-brand-700">01</p>
          <h3 class="mt-2 font-semibold text-ink">Escolha peças ou serviço</h3>
          <p class="mt-2 text-sm text-slate-500">Catálogo com fotos reais da loja: Ryzen, i9, RTX, TUF Z390, RAM, HD e periféricos — ou agenda da bancada Smarty.</p>
        </div>
        <div class="card-surface p-6">
          <p class="text-xs font-bold uppercase tracking-wide text-brand-700">02</p>
          <h3 class="mt-2 font-semibold text-ink">Checkout em minutos</h3>
          <p class="mt-2 text-sm text-slate-500">Frete de R$ 15 para peças. Serviços não cobram frete: traga o equipamento ou retire na loja em São Paulo. Pague com PIX, cartão, boleto, dinheiro ou transferência.</p>
        </div>
        <div class="card-surface p-6">
          <p class="text-xs font-bold uppercase tracking-wide text-brand-700">03</p>
          <h3 class="mt-2 font-semibold text-ink">Garantia da bancada</h3>
          <p class="mt-2 text-sm text-slate-500">90 dias em peças, 30 dias no serviço executado, 7 dias para arrependimento em item lacrado (CDC).</p>
        </div>
      </div>
    </section>
  </div>
</template>
<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useProductsStore } from '@/stores/products'
import ProductCard from '@/components/public/ProductCard.vue'
import AppSpinner from '@/components/common/AppSpinner.vue'
import { isServiceProduct } from '@/utils/format'

const productsStore = useProductsStore()
const parts = computed(() => productsStore.products.filter(p => !isServiceProduct(p)).slice(0, 8))
const services = computed(() => productsStore.products.filter(p => isServiceProduct(p)))

const categoryCards = [
  { slug: 'processadores', name: 'Processadores', kicker: 'CPU', hint: 'Ryzen 7 e i9-9900K' },
  { slug: 'placas-de-video', name: 'Placas de vídeo', kicker: 'GPU', hint: 'RTX Founders Edition' },
  { slug: 'placas-mae', name: 'Placas-mãe', kicker: 'MB', hint: 'ASUS TUF Z390' },
  { slug: 'memoria-e-storage', name: 'Memória e HD', kicker: 'RAM', hint: 'Kingston e G.SKILL' },
  { slug: 'gabinetes-cooling', name: 'Gabinete', kicker: 'CASE', hint: 'RGB e Corsair AIO' },
  { slug: 'perifericos', name: 'Periféricos', kicker: 'I/O', hint: 'Teclado e mouse' },
  { slug: 'servicos', name: 'Assistência', kicker: 'LAB', hint: 'Limpeza e montagem' }
]

onMounted(() => {
  productsStore.fetchCategories()
  productsStore.fetchProducts({ per_page: 30 })
})
</script>
