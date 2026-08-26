<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
    <div>
      <h1 class="text-3xl font-bold text-ink">Minha conta</h1>
      <p class="mt-1 text-sm text-slate-500">Endereço com busca por CEP e cartões salvos (sandbox).</p>
    </div>

    <form class="card-surface p-6 space-y-6" @submit.prevent="saveProfile">
      <h2 class="text-lg font-semibold text-ink">Dados e endereço</h2>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nome</label>
          <input v-model="profile.name" type="text" required class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Telefone</label>
          <input v-model="profile.phone" type="tel" class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand" />
        </div>
      </div>
      <AddressFields v-model="profile.address" />
      <div class="flex justify-end">
        <button type="submit" :disabled="savingProfile" class="btn-primary">
          {{ savingProfile ? 'Salvando...' : 'Salvar endereço' }}
        </button>
      </div>
    </form>

    <section class="card-surface p-6 space-y-6">
      <div>
        <h2 class="text-lg font-semibold text-ink">Cartões de crédito</h2>
        <p class="text-xs text-slate-500 mt-1">Sandbox: use <code>4242 4242 4242 4242</code>. O número completo nunca é gravado — só bandeira e os 4 últimos dígitos.</p>
      </div>

      <ul v-if="cards.length" class="divide-y divide-slate-100 rounded-xl border border-slate-100">
        <li v-for="card in cards" :key="card.id" class="flex items-center justify-between px-4 py-3 text-sm">
          <div>
            <p class="font-semibold text-ink">{{ card.label }}</p>
            <p class="text-xs text-slate-500">{{ card.holder_name }} · {{ String(card.exp_month).padStart(2, '0') }}/{{ card.exp_year }}</p>
          </div>
          <button type="button" class="text-rose-600 text-xs font-semibold hover:underline" @click="removeCard(card.id)">Remover</button>
        </li>
      </ul>
      <p v-else class="text-sm text-slate-500">Nenhum cartão cadastrado ainda.</p>

      <form class="space-y-4 border-t border-slate-100 pt-6" @submit.prevent="addCard">
        <h3 class="text-sm font-semibold text-ink">Cadastrar novo cartão</h3>
        <CardFields v-model="newCard" required hint="Ambiente de teste — nenhuma cobrança real é feita." />
        <div class="flex justify-end">
          <button type="submit" :disabled="savingCard" class="btn-primary">
            {{ savingCard ? 'Salvando...' : 'Salvar cartão' }}
          </button>
        </div>
      </form>
    </section>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import AddressFields from '@/components/common/AddressFields.vue'
import CardFields from '@/components/common/CardFields.vue'
import { cardsApi } from '@/services/api'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'
import { emptyAddress, emptyCard, type Address, type SavedCard } from '@/types'

const authStore = useAuthStore()
const { success, error } = useNotification()

const profile = reactive({
  name: '',
  phone: '',
  address: emptyAddress()
})
const cards = ref<SavedCard[]>([])
const newCard = ref(emptyCard())
const savingProfile = ref(false)
const savingCard = ref(false)

function fillFromUser() {
  const user = authStore.user
  if (!user) return
  profile.name = user.name
  profile.phone = user.phone || ''
  profile.address = { ...emptyAddress(), ...(user.address || {}) } as Address
}

async function loadCards() {
  const res = await cardsApi.list()
  cards.value = res.data
}

onMounted(async () => {
  if (!authStore.user) await authStore.fetchMe().catch(() => {})
  fillFromUser()
  await loadCards().catch(() => {})
})

async function saveProfile() {
  savingProfile.value = true
  try {
    await authStore.updateProfile({
      name: profile.name,
      phone: profile.phone,
      address: profile.address
    })
    success('Endereço salvo.')
  } catch {
    error('Não foi possível salvar o perfil.')
  } finally {
    savingProfile.value = false
  }
}

async function addCard() {
  savingCard.value = true
  try {
    await cardsApi.create(newCard.value)
    newCard.value = emptyCard()
    await loadCards()
    success('Cartão cadastrado.')
  } catch (err: unknown) {
    const message = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    error(message || 'Cartão inválido. Confira número, validade e CVV.')
  } finally {
    savingCard.value = false
  }
}

async function removeCard(id: number) {
  if (!confirm('Remover este cartão?')) return
  try {
    await cardsApi.remove(id)
    await loadCards()
    success('Cartão removido.')
  } catch {
    error('Não foi possível remover o cartão.')
  }
}
</script>
