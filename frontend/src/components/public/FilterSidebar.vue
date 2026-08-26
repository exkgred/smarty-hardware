<template>
  <aside class="card-surface p-5 sticky top-24">
    <h2 class="text-sm font-bold text-ink">Filtrar catálogo</h2>
    <form @submit.prevent="applyFilters" class="mt-4 space-y-5">
      <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Busca</label>
        <input v-model="filters.search" type="search" placeholder="Ryzen, RTX, limpeza..." class="mt-2 block w-full rounded-lg border-0 py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
      </div>
      <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Categoria</label>
        <select v-model="filters.category_id" class="mt-2 block w-full rounded-lg border-0 py-2 pl-3 pr-8 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand">
          <option :value="null">Todas</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Preço (R$)</label>
        <div class="mt-2 flex items-center gap-2">
          <input v-model.number="filters.min_price" type="number" placeholder="Mín" class="w-full rounded-lg border-0 py-2 px-2 text-sm ring-1 ring-slate-200 focus:ring-brand" />
          <input v-model.number="filters.max_price" type="number" placeholder="Máx" class="w-full rounded-lg border-0 py-2 px-2 text-sm ring-1 ring-slate-200 focus:ring-brand" />
        </div>
      </div>
      <button type="submit" class="btn-primary w-full">Aplicar</button>
    </form>
  </aside>
</template>
<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Category } from '@/types'

const props = defineProps<{ categories: Category[], initial?: Record<string, unknown> }>()
const emit = defineEmits(['filter-change'])

const filters = ref({ category_id: null as number | null, min_price: null as number | null, max_price: null as number | null, search: '' })

watch(
  () => props.initial,
  (value) => {
    if (!value) return
    filters.value = {
      category_id: (value.category_id as number | null) ?? null,
      min_price: (value.min_price as number | null) ?? null,
      max_price: (value.max_price as number | null) ?? null,
      search: (value.search as string) ?? ''
    }
  },
  { immediate: true, deep: true }
)

function applyFilters() {
  emit('filter-change', { ...filters.value })
}
</script>
