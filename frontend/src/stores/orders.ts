import { defineStore } from 'pinia'
import { ref } from 'vue'
import { ordersApi } from '@/services/api'
import type { Order, CheckoutPayload } from '@/types'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref<Order[]>([])
  const currentOrder = ref<Order | null>(null)
  const loading = ref(false)
  const checkoutLoading = ref(false)

  async function fetchMyOrders() {
    loading.value = true
    try {
      const res = await ordersApi.myOrders()
      orders.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function fetchOrder(id: number) {
    loading.value = true
    try {
      const res = await ordersApi.getOrder(id)
      currentOrder.value = res.data
    } finally {
      loading.value = false
    }
  }

  async function checkout(items: any[], shippingCost: number, extra?: CheckoutPayload) {
    checkoutLoading.value = true
    try {
      const res = await ordersApi.checkout(items, shippingCost, extra)
      return res.data
    } finally {
      checkoutLoading.value = false
    }
  }

  return { orders, currentOrder, loading, checkoutLoading, fetchMyOrders, fetchOrder, checkout }
})
