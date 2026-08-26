import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { CartItem, Product } from '@/types'

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>(JSON.parse(localStorage.getItem('cart') || '[]'))

  const totalItems = computed(() => items.value.reduce((acc, item) => acc + item.quantity, 0))
  const totalPrice = computed(() => items.value.reduce((acc, item) => acc + (item.product.price * item.quantity), 0))
  const isEmpty = computed(() => items.value.length === 0)

  function save() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  function addItem(product: Product, quantity: number) {
    const existing = items.value.find(i => i.product.id === product.id)
    if (existing) existing.quantity += quantity
    else items.value.push({ product, quantity })
    save()
  }

  function removeItem(productId: number) {
    items.value = items.value.filter(i => i.product.id !== productId)
    save()
  }

  function updateQuantity(productId: number, quantity: number) {
    const item = items.value.find(i => i.product.id === productId)
    if (item) {
      item.quantity = quantity
      if (item.quantity <= 0) removeItem(productId)
      else save()
    }
  }

  function clearCart() {
    items.value = []
    save()
  }

  return { items, totalItems, totalPrice, isEmpty, addItem, removeItem, updateQuantity, clearCart }
})
