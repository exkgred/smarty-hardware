<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-ink mb-8">Checkout</h1>
    <form @submit.prevent="submitOrder" class="space-y-8 card-surface p-6">
      <div>
        <h2 class="text-lg font-semibold text-ink">{{ servicesOnly ? 'Dados para o agendamento' : 'Entrega' }}</h2>
        <div class="mt-4 space-y-4">
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700">Nome</label>
              <input type="text" v-model="form.name" required class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700">Telefone</label>
              <input type="tel" v-model="form.phone" class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand" />
            </div>
          </div>
          <AddressFields v-model="form.address" :required="!servicesOnly" />
          <label class="flex items-center gap-2 text-sm text-slate-600">
            <input v-model="form.save_address" type="checkbox" class="rounded border-slate-300" />
            Salvar este endereço na minha conta
          </label>
        </div>
      </div>
      <div class="border-t border-slate-100 pt-6">
        <h2 class="text-lg font-semibold text-ink">Forma de pagamento</h2>
        <p class="mt-1 text-xs text-slate-500">Ambiente sandbox: o pagamento é confirmado na hora, sem cobrança real.</p>
        <div class="mt-4 grid sm:grid-cols-2 gap-3">
          <label
            v-for="method in PAYMENT_METHODS"
            :key="method.value"
            class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 text-sm"
            :class="form.payment_method === method.value ? 'border-brand bg-brand-50' : 'border-slate-200'"
          >
            <input v-model="form.payment_method" type="radio" :value="method.value" class="mt-1" />
            <span>
              <span class="font-semibold text-ink">{{ method.label }}</span>
              <span class="block text-xs text-slate-500">{{ method.hint }}</span>
            </span>
          </label>
        </div>

        <div v-if="needsCard" class="mt-6 space-y-4 rounded-xl bg-slate-50 p-4">
          <h3 class="text-sm font-semibold text-ink">Cartão</h3>
          <div v-if="savedCards.length" class="space-y-2">
            <label
              v-for="card in savedCards"
              :key="card.id"
              class="flex cursor-pointer items-center gap-3 rounded-lg border bg-white p-3 text-sm"
              :class="selectedCardId === card.id ? 'border-brand' : 'border-slate-200'"
            >
              <input v-model="selectedCardId" type="radio" :value="card.id" />
              <span>
                <span class="font-medium text-ink">{{ card.label }}</span>
                <span class="block text-xs text-slate-500">{{ card.holder_name }}</span>
              </span>
            </label>
            <label class="flex cursor-pointer items-center gap-3 rounded-lg border bg-white p-3 text-sm" :class="selectedCardId === 'new' ? 'border-brand' : 'border-slate-200'">
              <input v-model="selectedCardId" type="radio" value="new" />
              <span class="font-medium text-ink">Usar um cartão novo</span>
            </label>
          </div>
          <CardFields
            v-if="selectedCardId === 'new' || !savedCards.length"
            v-model="form.card"
            required
            hint="Teste: 4242 4242 4242 4242 · CVV 123. O PAN não é armazenado."
          />
          <label v-if="selectedCardId === 'new' || !savedCards.length" class="flex items-center gap-2 text-sm text-slate-600">
            <input v-model="form.save_card" type="checkbox" class="rounded border-slate-300" />
            Salvar cartão para próximas compras
          </label>
        </div>
      </div>
      <div class="border-t border-slate-100 pt-6">
        <h2 class="text-lg font-semibold text-ink">Resumo</h2>
        <div class="mt-4 text-sm space-y-2">
          <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span>{{ formatBRL(cartStore.totalPrice) }}</span></div>
          <div class="flex justify-between"><span class="text-slate-500">Frete</span><span>{{ formatBRL(shippingCost) }}</span></div>
          <div class="flex justify-between border-t border-slate-100 pt-3 text-lg font-bold"><span>Total</span><span>{{ formatBRL(cartStore.totalPrice + shippingCost) }}</span></div>
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" :disabled="ordersStore.checkoutLoading || cartStore.isEmpty" class="btn-primary px-8 py-3">
          {{ ordersStore.checkoutLoading ? 'Processando...' : 'Confirmar pedido' }}
        </button>
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import AddressFields from '@/components/common/AddressFields.vue'
import CardFields from '@/components/common/CardFields.vue'
import { useCartStore } from '@/stores/cart'
import { useOrdersStore } from '@/stores/orders'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import { cardsApi } from '@/services/api'
import { formatBRL, isServiceProduct, PAYMENT_METHODS } from '@/utils/format'
import { emptyAddress, emptyCard, type Address, type CheckoutPayload, type SavedCard } from '@/types'

const router = useRouter()
const cartStore = useCartStore()
const ordersStore = useOrdersStore()
const authStore = useAuthStore()
const { error } = useNotification()
const form = reactive({
  name: '',
  phone: '',
  address: emptyAddress(),
  save_address: true,
  payment_method: 'PIX',
  save_card: true,
  card: emptyCard()
})
const savedCards = ref<SavedCard[]>([])
const selectedCardId = ref<number | 'new'>('new')
const servicesOnly = computed(() => cartStore.items.length > 0 && cartStore.items.every(item => isServiceProduct(item.product)))
const shippingCost = computed(() => servicesOnly.value ? 0 : 15)
const needsCard = computed(() => form.payment_method === 'CREDIT_CARD' || form.payment_method === 'DEBIT_CARD')

onMounted(async () => {
  if (authStore.user) {
    form.name = authStore.user.name
    form.phone = authStore.user.phone || ''
    form.address = { ...emptyAddress(), ...(authStore.user.address || {}) } as Address
    form.card.holder_name = authStore.user.name.toUpperCase()
  }
  try {
    const res = await cardsApi.list()
    savedCards.value = res.data
    const def = savedCards.value.find(card => card.is_default) || savedCards.value[0]
    if (def) selectedCardId.value = def.id
  } catch {
    savedCards.value = []
  }
})

async function submitOrder() {
  if (!authStore.isAuthenticated) {
    error('Entre na conta para concluir o pedido.')
    router.push({ name: 'login' })
    return
  }
  try {
    const items = cartStore.items.map(i => ({ product_id: i.product.id, quantity: i.quantity }))
    const extra: CheckoutPayload = {
      name: form.name,
      phone: form.phone,
      address: form.address,
      save_address: form.save_address,
      payment_method: form.payment_method
    }
    if (needsCard.value) {
      if (selectedCardId.value !== 'new') {
        extra.card_id = Number(selectedCardId.value)
      } else {
        extra.card = form.card
        extra.save_card = form.save_card
      }
    }
    const res = await ordersStore.checkout(items, shippingCost.value, extra)
    cartStore.clearCart()
    router.push({ name: 'order-success', query: { orderId: String(res.id) } })
  } catch (err: unknown) {
    const message = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    error(message || 'Não foi possível processar o pedido. Verifique o estoque e o cartão.')
  }
}
</script>
