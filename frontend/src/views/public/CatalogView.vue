<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-8">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-700">Catálogo</p>
      <h1 class="mt-1 text-3xl font-bold text-ink">Peças e serviços</h1>
      <p class="mt-2 text-slate-500">Estoque da bancada Smarty — fotos reais dos itens e da oficina.</p>
    </div>
    <div class="flex flex-col lg:flex-row gap-8">
      <div class="lg:hidden">
        <button type="button" class="btn-ghost w-full" @click="showFilters = !showFilters">
          {{ showFilters ? 'Ocultar filtros' : 'Filtrar catálogo' }}
        </button>
        <div v-if="showFilters" class="mt-4">
          <FilterSidebar :categories="productsStore.categories" :initial="productsStore.filters" @filter-change="onFilterChange" />
        </div>
      </div>
      <div class="hidden lg:block w-72 shrink-0">
        <FilterSidebar :categories="productsStore.categories" :initial="productsStore.filters" @filter-change="onFilterChange" />
      </div>
      <div class="flex-1">
        <div v-if="productsStore.loading" class="flex justify-center py-16"><AppSpinner class="w-12 h-12 text-brand" /></div>
        <div v-else-if="productsStore.products.length === 0" class="card-surface p-12 text-center text-slate-500">Nenhum item encontrado com esses filtros.</div>
        <div v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            <ProductCard v-for="product in productsStore.products" :key="product.id" :product="product" />
          </div>
          <AppPagination class="mt-8 rounded-xl overflow-hidden" :current-page="productsStore.pagination.current_page" :last-page="productsStore.pagination.last_page" @page-change="onPageChange" />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useProductsStore } from '@/stores/products'
import FilterSidebar from '@/components/public/FilterSidebar.vue'
import ProductCard from '@/components/public/ProductCard.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import AppSpinner from '@/components/common/AppSpinner.vue'

const productsStore = useProductsStore()
const route = useRoute()
const showFilters = ref(false)

async function applyRouteQuery() {
  await productsStore.fetchCategories()
  const slug = typeof route.query.category === 'string' ? route.query.category : null
  const search = typeof route.query.search === 'string' ? route.query.search : ''
  const category = slug ? productsStore.categories.find(c => c.slug === slug) : null
  productsStore.setFilter('category_id', category?.id ?? null)
  productsStore.setFilter('search', search)
  await productsStore.fetchProducts({ page: 1, per_page: 24 })
}

onMounted(applyRouteQuery)
watch(() => [route.query.category, route.query.search], applyRouteQuery)

function onFilterChange(filters: Record<string, unknown>) {
  Object.keys(filters).forEach(key => productsStore.setFilter(key, filters[key]))
  productsStore.fetchProducts({ page: 1, per_page: 24 })
}

function onPageChange(page: number) {
  productsStore.fetchProducts({ page, per_page: 24 })
}
</script>
