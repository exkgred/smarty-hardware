import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productsApi } from '@/services/api'
import type { Product, Category } from '@/types'

export const useProductsStore = defineStore('products', () => {
  const products = ref<Product[]>([])
  const currentProduct = ref<Product | null>(null)
  const categories = ref<Category[]>([])
  const pagination = ref({ current_page: 1, last_page: 1 })
  const loading = ref(false)
  const filters = ref({ category_id: null, min_price: null, max_price: null, search: '' })

  async function fetchProducts(params: any = {}) {
    loading.value = true
    try {
      const res = await productsApi.list({ ...filters.value, ...params })
      products.value = res.data.data
      pagination.value = { current_page: res.data.current_page, last_page: res.data.last_page }
    } finally {
      loading.value = false
    }
  }

  async function fetchProduct(slug: string) {
    loading.value = true
    try {
      const res = await productsApi.show(slug)
      currentProduct.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    const res = await productsApi.categories()
    categories.value = res.data
  }

  function setFilter(key: string, value: any) {
    (filters.value as any)[key] = value
  }

  function resetFilters() {
    filters.value = { category_id: null, min_price: null, max_price: null, search: '' }
  }

  return { products, currentProduct, categories, pagination, loading, filters, fetchProducts, fetchProduct, fetchCategories, setFilter, resetFilters }
})
