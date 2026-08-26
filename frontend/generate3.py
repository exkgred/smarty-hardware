import os

base_dir = "/home/admlocal/Documentos/porti/marketplace/frontend"
files = {}

files["src/components/common/AppNavbar.vue"] = """<template>
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex">
          <RouterLink to="/" class="flex-shrink-0 flex items-center text-xl font-bold text-brand">
            Marketplace
          </RouterLink>
          <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
            <RouterLink to="/catalog" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
              Catálogo
            </RouterLink>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <RouterLink to="/cart" class="relative text-gray-400 hover:text-gray-500">
            <span class="sr-only">Carrinho</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span v-if="cartStore.totalItems > 0" class="absolute -top-2 -right-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
              {{ cartStore.totalItems }}
            </span>
          </RouterLink>
          
          <template v-if="authStore.isAuthenticated">
            <div class="relative group">
              <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <span>{{ authStore.user?.name }}</span>
              </button>
              <div class="absolute right-0 w-48 mt-2 py-1 bg-white border border-gray-200 rounded-md shadow-lg hidden group-hover:block">
                <RouterLink to="/my-orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meus Pedidos</RouterLink>
                <RouterLink v-if="authStore.isAdmin" to="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Painel Admin</RouterLink>
                <button @click="authStore.logout" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</button>
              </div>
            </div>
          </template>
          <template v-else>
            <RouterLink to="/login" class="text-gray-500 hover:text-gray-700">Login</RouterLink>
            <RouterLink to="/register" class="bg-brand text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-brand-dark">Registrar</RouterLink>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
const authStore = useAuthStore()
const cartStore = useCartStore()
</script>
"""

files["src/components/common/AppFooter.vue"] = """<template>
  <footer class="bg-white mt-12 border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <p class="mt-4 text-center text-sm text-gray-500">&copy; 2026 Marketplace, Inc. Todos os direitos reservados.</p>
    </div>
  </footer>
</template>
<script setup lang="ts"></script>
"""

files["src/components/common/AppPagination.vue"] = """<template>
  <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          Página <span class="font-medium">{{ currentPage }}</span> de <span class="font-medium">{{ lastPage }}</span>
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <button @click="$emit('page-change', currentPage - 1)" :disabled="currentPage === 1" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
            <span class="sr-only">Anterior</span>
            &lt;
          </button>
          <button @click="$emit('page-change', currentPage + 1)" :disabled="currentPage === lastPage" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
            <span class="sr-only">Próximo</span>
            &gt;
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
defineProps<{ currentPage: number, lastPage: number }>()
defineEmits(['page-change'])
</script>
"""

files["src/components/common/AppModal.vue"] = """<template>
  <div v-if="modelValue" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$emit('update:modelValue', false)"></div>
    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
          <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
            <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">{{ title }}</h3>
            <div class="mt-2">
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
defineProps<{ modelValue: boolean; title?: string }>()
defineEmits(['update:modelValue'])
</script>
"""

files["src/components/common/AppBadge.vue"] = """<template>
  <span :class="['inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset', colorClass]">
    {{ text }}
  </span>
</template>
<script setup lang="ts">
import { computed } from 'vue'
const props = defineProps<{ variant: 'success' | 'warning' | 'danger' | 'info'; text: string }>()

const colorClass = computed(() => {
  switch (props.variant) {
    case 'success': return 'bg-green-50 text-green-700 ring-green-600/20'
    case 'warning': return 'bg-yellow-50 text-yellow-800 ring-yellow-600/20'
    case 'danger': return 'bg-red-50 text-red-700 ring-red-600/10'
    case 'info': return 'bg-blue-50 text-blue-700 ring-blue-700/10'
    default: return 'bg-gray-50 text-gray-600 ring-gray-500/10'
  }
})
</script>
"""

files["src/components/common/AppSpinner.vue"] = """<template>
  <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
  </svg>
</template>
"""

files["src/components/common/NotificationToast.vue"] = """<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
    <div v-for="notif in notifications" :key="notif.id" 
         class="pointer-events-auto w-72 p-4 rounded shadow-lg text-white"
         :class="{
           'bg-green-500': notif.type === 'success',
           'bg-red-500': notif.type === 'error',
           'bg-blue-500': notif.type === 'info'
         }">
      {{ notif.message }}
    </div>
  </div>
</template>
<script setup lang="ts">
import { useNotification } from '@/composables/useNotification'
const { notifications } = useNotification()
</script>
"""

files["src/components/public/ProductCard.vue"] = """<template>
  <div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow-md transition">
    <RouterLink :to="{ name: 'product-detail', params: { slug: product.slug } }" class="aspect-h-4 aspect-w-3 bg-gray-200 sm:aspect-none sm:h-64 cursor-pointer relative">
      <img :src="product.image_url || 'https://via.placeholder.com/400'" alt="Imagem do produto" class="h-full w-full object-cover object-center sm:h-full sm:w-full" />
      <div v-if="product.stock_quantity === 0" class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <span class="text-white font-bold bg-red-600 px-3 py-1 rounded">Sem estoque</span>
      </div>
    </RouterLink>
    <div class="flex flex-1 flex-col space-y-2 p-4">
      <h3 class="text-sm font-medium text-gray-900">
        <RouterLink :to="{ name: 'product-detail', params: { slug: product.slug } }">{{ product.name }}</RouterLink>
      </h3>
      <p class="text-sm text-gray-500">{{ product.category?.name }}</p>
      <div class="flex flex-1 flex-col justify-end">
        <p class="text-base font-medium text-gray-900">R$ {{ product.price.toFixed(2) }}</p>
        <button @click="addToCart" :disabled="product.stock_quantity === 0" class="mt-4 w-full rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-dark disabled:bg-gray-400">
          Adicionar ao Carrinho
        </button>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import type { Product } from '@/types'

const props = defineProps<{ product: Product }>()
const router = useRouter()
const cartStore = useCartStore()

function addToCart() {
  cartStore.addItem(props.product, 1)
  router.push({ name: 'cart' })
}
</script>
"""

files["src/components/public/FilterSidebar.vue"] = """<template>
  <form @submit.prevent="applyFilters" class="space-y-6">
    <div>
      <h3 class="text-sm font-medium text-gray-900">Categoria</h3>
      <select v-model="filters.category_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6">
        <option :value="null">Todas</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
    </div>
    <div>
      <h3 class="text-sm font-medium text-gray-900">Preço</h3>
      <div class="mt-2 flex items-center space-x-2">
        <input v-model.number="filters.min_price" type="number" placeholder="Min" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6" />
        <span>-</span>
        <input v-model.number="filters.max_price" type="number" placeholder="Max" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-brand sm:text-sm sm:leading-6" />
      </div>
    </div>
    <button type="submit" class="w-full rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
      Aplicar Filtros
    </button>
  </form>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import type { Category } from '@/types'

defineProps<{ categories: Category[] }>()
const emit = defineEmits(['filter-change'])

const filters = ref({ category_id: null, min_price: null, max_price: null })

function applyFilters() {
  emit('filter-change', { ...filters.value })
}
</script>
"""

files["src/components/chat/ChatWidget.vue"] = """<template>
  <div class="fixed bottom-4 right-4 z-40">
    <button v-if="!isOpen" @click="toggleChat" class="bg-brand text-white p-4 rounded-full shadow-lg hover:bg-brand-dark transition">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
    </button>
    <div v-else class="w-80 h-96 bg-white rounded-lg shadow-xl flex flex-col border border-gray-200">
      <div class="bg-brand text-white p-3 rounded-t-lg flex justify-between items-center">
        <h3 class="font-medium">Assistente Virtual</h3>
        <button @click="toggleChat" class="text-white hover:text-gray-200">&times;</button>
      </div>
      <div class="flex-1 p-4 overflow-y-auto flex flex-col space-y-3" ref="messagesContainer">
        <div v-for="(msg, idx) in chatStore.messages" :key="idx" :class="['max-w-[85%] rounded-lg p-2 text-sm', msg.role === 'user' ? 'bg-blue-100 self-end text-blue-900' : 'bg-gray-100 self-start text-gray-800']">
          {{ msg.content }}
        </div>
        <div v-if="chatStore.isLoading" class="bg-gray-100 self-start rounded-lg p-2 flex space-x-1">
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
      </div>
      <div class="p-3 border-t">
        <form @submit.prevent="sendMessage" class="flex gap-2">
          <input v-model="input" type="text" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand text-sm p-2 border" placeholder="Digite sua mensagem..." :disabled="chatStore.isLoading" />
          <button type="submit" class="bg-brand text-white px-3 py-1 rounded-md text-sm hover:bg-brand-dark disabled:opacity-50" :disabled="!input.trim() || chatStore.isLoading">Enviar</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import { useChatStore } from '@/stores/chat'

const isOpen = ref(false)
const chatStore = useChatStore()
const input = ref('')
const messagesContainer = ref<HTMLElement | null>(null)

function toggleChat() {
  isOpen.value = !isOpen.value
  if (isOpen.value && chatStore.messages.length === 0) {
    chatStore.initSession()
  }
}

async function sendMessage() {
  if (!input.value.trim()) return
  const msg = input.value
  input.value = ''
  await chatStore.sendMessage(msg)
}

watch(() => chatStore.messages.length, () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
})
</script>
"""

for path, content in files.items():
    full_path = os.path.join(base_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)

print("Part 3 done.")
