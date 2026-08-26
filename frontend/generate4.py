import os

base_dir = "/home/admlocal/Documentos/porti/marketplace/frontend"
files = {}

files["src/views/public/HomeView.vue"] = """<template>
  <div>
    <div class="bg-brand">
      <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white sm:text-5xl sm:tracking-tight lg:text-6xl">Sua loja online completa</h1>
        <p class="max-w-xl mt-5 mx-auto text-xl text-brand-100">Encontre os melhores produtos com os melhores preços.</p>
        <div class="mt-10">
          <RouterLink to="/catalog" class="inline-block bg-white border border-transparent rounded-md py-3 px-8 text-base font-medium text-brand hover:bg-gray-50">Ver Catálogo</RouterLink>
        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Produtos em Destaque</h2>
      <div v-if="productsStore.loading" class="flex justify-center"><AppSpinner class="w-8 h-8 text-brand" /></div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard v-for="product in productsStore.products.slice(0, 8)" :key="product.id" :product="product" />
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted } from 'vue'
import { useProductsStore } from '@/stores/products'
import ProductCard from '@/components/public/ProductCard.vue'
import AppSpinner from '@/components/common/AppSpinner.vue'

const productsStore = useProductsStore()
onMounted(() => {
  productsStore.fetchProducts()
})
</script>
"""

files["src/views/public/CatalogView.vue"] = """<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex gap-8">
      <div class="hidden lg:block w-64 flex-shrink-0">
        <FilterSidebar :categories="productsStore.categories" @filter-change="onFilterChange" />
      </div>
      <div class="flex-1">
        <div v-if="productsStore.loading" class="flex justify-center py-12"><AppSpinner class="w-12 h-12 text-brand" /></div>
        <div v-else-if="productsStore.products.length === 0" class="text-center py-12 text-gray-500">Nenhum produto encontrado.</div>
        <div v-else>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <ProductCard v-for="product in productsStore.products" :key="product.id" :product="product" />
          </div>
          <AppPagination class="mt-8" :current-page="productsStore.pagination.current_page" :last-page="productsStore.pagination.last_page" @page-change="onPageChange" />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted } from 'vue'
import { useProductsStore } from '@/stores/products'
import FilterSidebar from '@/components/public/FilterSidebar.vue'
import ProductCard from '@/components/public/ProductCard.vue'
import AppPagination from '@/components/common/AppPagination.vue'
import AppSpinner from '@/components/common/AppSpinner.vue'

const productsStore = useProductsStore()

onMounted(() => {
  productsStore.fetchProducts()
})

function onFilterChange(filters: any) {
  Object.keys(filters).forEach(key => productsStore.setFilter(key, filters[key]))
  productsStore.fetchProducts({ page: 1 })
}

function onPageChange(page: number) {
  productsStore.fetchProducts({ page })
}
</script>
"""

files["src/views/public/ProductDetailView.vue"] = """<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div v-if="productsStore.loading" class="flex justify-center py-12"><AppSpinner class="w-12 h-12 text-brand" /></div>
    <div v-else-if="product" class="lg:grid lg:grid-cols-2 lg:gap-x-8">
      <div class="aspect-h-4 aspect-w-3 rounded-lg overflow-hidden lg:aspect-none lg:h-96">
        <img :src="product.image_url || 'https://via.placeholder.com/800'" :alt="product.name" class="w-full h-full object-cover object-center" />
      </div>
      <div class="mt-10 px-4 sm:px-0 lg:mt-0">
        <nav class="flex text-sm text-gray-500 space-x-2 mb-4">
          <RouterLink to="/" class="hover:text-gray-900">Home</RouterLink>
          <span>&gt;</span>
          <RouterLink to="/catalog" class="hover:text-gray-900">Catálogo</RouterLink>
          <span>&gt;</span>
          <span class="text-gray-900">{{ product.name }}</span>
        </nav>
        <h1 class="text-3xl font-extrabold text-gray-900">{{ product.name }}</h1>
        <div class="mt-3">
          <h2 class="sr-only">Informações do produto</h2>
          <p class="text-3xl text-gray-900">R$ {{ product.price.toFixed(2) }}</p>
        </div>
        <div class="mt-6">
          <h3 class="sr-only">Descrição</h3>
          <div class="text-base text-gray-700" v-html="product.description || 'Sem descrição.'"></div>
        </div>
        <div class="mt-8 flex gap-4 items-center">
          <input type="number" v-model.number="quantity" min="1" :max="product.stock_quantity" class="w-20 rounded-md border-gray-300 py-2 text-center" :disabled="product.stock_quantity === 0" />
          <button @click="addToCart" :disabled="product.stock_quantity === 0" class="flex-1 bg-brand text-white py-3 px-8 rounded-md font-medium hover:bg-brand-dark focus:outline-none disabled:bg-gray-400">
            {{ product.stock_quantity === 0 ? 'Sem Estoque' : 'Adicionar ao Carrinho' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '@/stores/products'
import { useCartStore } from '@/stores/cart'
import AppSpinner from '@/components/common/AppSpinner.vue'

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()
const cartStore = useCartStore()

const quantity = ref(1)
const product = computed(() => productsStore.currentProduct)

onMounted(() => {
  productsStore.fetchProduct(route.params.slug as string)
})

function addToCart() {
  if (product.value) {
    cartStore.addItem(product.value, quantity.value)
    router.push({ name: 'cart' })
  }
}
</script>
"""

files["src/views/public/CartView.vue"] = """<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Carrinho de Compras</h1>
    <div v-if="cartStore.isEmpty" class="text-center py-12 bg-white rounded-lg shadow">
      <p class="text-lg text-gray-500 mb-4">Seu carrinho está vazio.</p>
      <RouterLink to="/catalog" class="text-brand font-medium hover:text-brand-dark">Continuar Comprando</RouterLink>
    </div>
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <ul class="divide-y divide-gray-200">
        <li v-for="item in cartStore.items" :key="item.product.id" class="p-6 flex py-6">
          <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
            <img :src="item.product.image_url || 'https://via.placeholder.com/150'" class="w-full h-full object-center object-cover" />
          </div>
          <div class="ml-4 flex-1 flex flex-col">
            <div>
              <div class="flex justify-between text-base font-medium text-gray-900">
                <h3><RouterLink :to="{ name: 'product-detail', params: { slug: item.product.slug } }">{{ item.product.name }}</RouterLink></h3>
                <p class="ml-4">R$ {{ (item.product.price * item.quantity).toFixed(2) }}</p>
              </div>
            </div>
            <div class="flex-1 flex items-end justify-between text-sm">
              <div class="flex items-center">
                <label class="mr-2">Qtd</label>
                <input type="number" :value="item.quantity" @change="e => updateQty(item.product.id, e)" min="1" :max="item.product.stock_quantity" class="w-16 rounded-md border-gray-300 py-1 text-center" />
              </div>
              <button type="button" @click="cartStore.removeItem(item.product.id)" class="font-medium text-red-600 hover:text-red-500">Remover</button>
            </div>
          </div>
        </li>
      </ul>
      <div class="border-t border-gray-200 p-6">
        <div class="flex justify-between text-base font-medium text-gray-900 mb-4">
          <p>Subtotal</p>
          <p>R$ {{ cartStore.totalPrice.toFixed(2) }}</p>
        </div>
        <div class="mt-6 flex justify-between gap-4">
          <RouterLink to="/catalog" class="flex-1 flex justify-center items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50">Continuar Comprando</RouterLink>
          <RouterLink to="/checkout" class="flex-1 flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-brand hover:bg-brand-dark">Finalizar Compra</RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useCartStore } from '@/stores/cart'
const cartStore = useCartStore()

function updateQty(id: number, e: Event) {
  const target = e.target as HTMLInputElement
  cartStore.updateQuantity(id, parseInt(target.value))
}
</script>
"""

files["src/views/public/CheckoutView.vue"] = """<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Finalizar Pedido</h1>
    <form @submit.prevent="submitOrder" class="space-y-8 bg-white p-6 rounded-lg shadow">
      <div>
        <h2 class="text-lg font-medium text-gray-900">Informações de Entrega</h2>
        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Nome</label>
            <input type="text" v-model="form.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand sm:text-sm" />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Endereço Completo</label>
            <input type="text" v-model="form.address" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand focus:ring-brand sm:text-sm" />
          </div>
        </div>
      </div>
      <div class="border-t border-gray-200 pt-8">
        <h2 class="text-lg font-medium text-gray-900">Resumo do Pedido</h2>
        <div class="mt-4">
          <div class="flex justify-between py-2">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-medium">R$ {{ cartStore.totalPrice.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="text-gray-600">Frete</span>
            <span class="font-medium">R$ {{ shippingCost.toFixed(2) }}</span>
          </div>
          <div class="flex justify-between py-2 border-t border-gray-200 mt-2 text-lg font-bold">
            <span>Total</span>
            <span>R$ {{ (cartStore.totalPrice + shippingCost).toFixed(2) }}</span>
          </div>
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" :disabled="ordersStore.checkoutLoading || cartStore.isEmpty" class="bg-brand text-white px-8 py-3 rounded-md font-medium hover:bg-brand-dark disabled:bg-gray-400">
          {{ ordersStore.checkoutLoading ? 'Processando...' : 'Confirmar Pedido' }}
        </button>
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useOrdersStore } from '@/stores/orders'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'

const router = useRouter()
const cartStore = useCartStore()
const ordersStore = useOrdersStore()
const authStore = useAuthStore()
const { error } = useNotification()

const form = ref({ name: '', address: '' })
const shippingCost = 15.00

onMounted(() => {
  if (authStore.user) {
    form.value.name = authStore.user.name
  }
})

async function submitOrder() {
  if (!authStore.isAuthenticated) {
    error('Você precisa estar logado para finalizar a compra.')
    router.push({ name: 'login' })
    return
  }
  try {
    const items = cartStore.items.map(i => ({ product_id: i.product.id, quantity: i.quantity }))
    const res = await ordersStore.checkout(items, shippingCost)
    cartStore.clearCart()
    router.push({ name: 'order-success', query: { orderId: res.id } })
  } catch (err) {
    error('Erro ao processar pedido.')
  }
}
</script>
"""

files["src/views/public/OrderSuccessView.vue"] = """<template>
  <div class="max-w-2xl mx-auto px-4 py-16 text-center">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-6">
      <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
      </svg>
    </div>
    <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">Pedido Confirmado!</h1>
    <p class="mt-4 text-lg text-gray-500">Obrigado pela sua compra. Seu número de pedido é <strong>#{{ $route.query.orderId }}</strong>.</p>
    <div class="mt-8 flex justify-center gap-4">
      <RouterLink to="/my-orders" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
        Ver Meus Pedidos
      </RouterLink>
      <RouterLink to="/catalog" class="inline-flex justify-center rounded-md border border-transparent bg-brand px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-dark">
        Continuar Comprando
      </RouterLink>
    </div>
  </div>
</template>
"""

files["src/views/auth/LoginView.vue"] = """<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
      <h2 class="mt-10 text-2xl font-bold leading-9 tracking-tight text-gray-900">Entre na sua conta</h2>
    </div>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="onSubmit">
        <div>
          <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
          <div class="mt-2">
            <input id="email" v-model="form.email" type="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Senha</label>
          <div class="mt-2">
            <input id="password" v-model="form.password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <button type="submit" :disabled="authStore.isLoading" class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-dark focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:opacity-50">
            {{ authStore.isLoading ? 'Entrando...' : 'Entrar' }}
          </button>
        </div>
      </form>
      <p class="mt-10 text-center text-sm text-gray-500">
        Não tem uma conta? <RouterLink to="/register" class="font-semibold leading-6 text-brand hover:text-brand-dark">Registre-se</RouterLink>
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'

const authStore = useAuthStore()
const router = useRouter()
const { error } = useNotification()
const form = ref({ email: '', password: '' })

async function onSubmit() {
  try {
    await authStore.login(form.value)
    router.push('/')
  } catch (err: any) {
    error(err.response?.data?.message || 'Erro ao fazer login')
  }
}
</script>
"""

files["src/views/auth/RegisterView.vue"] = """<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
      <h2 class="mt-10 text-2xl font-bold leading-9 tracking-tight text-gray-900">Crie sua conta</h2>
    </div>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form class="space-y-6" @submit.prevent="onSubmit">
        <div>
          <label class="block text-sm font-medium leading-6 text-gray-900">Nome</label>
          <div class="mt-2">
            <input v-model="form.name" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium leading-6 text-gray-900">Email</label>
          <div class="mt-2">
            <input v-model="form.email" type="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium leading-6 text-gray-900">Senha</label>
          <div class="mt-2">
            <input v-model="form.password" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium leading-6 text-gray-900">Confirmar Senha</label>
          <div class="mt-2">
            <input v-model="form.password_confirmation" type="password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6" />
          </div>
        </div>
        <div>
          <button type="submit" :disabled="authStore.isLoading" class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-dark focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:opacity-50">
            {{ authStore.isLoading ? 'Registrando...' : 'Registrar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useNotification } from '@/composables/useNotification'

const authStore = useAuthStore()
const router = useRouter()
const { error } = useNotification()
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })

async function onSubmit() {
  if (form.value.password !== form.value.password_confirmation) {
    error('As senhas não conferem.')
    return
  }
  try {
    await authStore.register(form.value)
    router.push('/')
  } catch (err: any) {
    error(err.response?.data?.message || 'Erro ao registrar')
  }
}
</script>
"""

for path, content in files.items():
    full_path = os.path.join(base_dir, path)
    os.makedirs(os.path.dirname(full_path), exist_ok=True)
    with open(full_path, 'w') as f:
        f.write(content)

print("Part 4 done.")
