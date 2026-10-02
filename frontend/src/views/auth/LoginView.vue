<template>
  <div class="flex min-h-[70vh] flex-col justify-center px-6 py-12">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
      <div class="flex justify-center">
        <BrandMark :size="40" tone="onLight" />
      </div>
      <h2 class="mt-3 text-2xl font-bold text-ink">Entrar na conta</h2>
    </div>
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm card-surface p-6">
      <form class="space-y-5" @submit.prevent="onSubmit">
        <div>
          <label class="block text-sm font-medium text-slate-700">Email</label>
          <input id="email" v-model="form.email" type="email" required data-cy="email" class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Senha</label>
          <input id="password" v-model="form.password" type="password" required data-cy="password" class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <button type="submit" :disabled="authStore.isLoading" data-cy="login-submit" class="btn-primary w-full">
          {{ authStore.isLoading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">
        Não tem conta? <RouterLink to="/register" class="font-semibold text-brand-700">Cadastre-se</RouterLink>
      </p>
      <p class="mt-4 rounded-lg bg-slate-50 p-3 text-center text-xs text-slate-500">
        Demo: <span class="font-medium text-ink">cliente@marketplace.test</span> ou
        <span class="font-medium text-ink">admin@marketplace.test</span> · senha <span class="font-medium text-ink">password</span>
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BrandMark from '@/components/common/BrandMark.vue'
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
  } catch (err: unknown) {
    const message = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    error(message || 'Erro ao fazer login')
  }
}
</script>
