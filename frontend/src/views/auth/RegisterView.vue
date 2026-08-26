<template>
  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
      <p class="text-xs font-bold tracking-[0.25em] text-brand-700">SMARTY HARDWARE</p>
      <h2 class="mt-3 text-2xl font-bold text-ink">Criar conta</h2>
    </div>
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-sm card-surface p-6">
      <form class="space-y-5" @submit.prevent="onSubmit">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nome</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Email</label>
          <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Senha</label>
          <input v-model="form.password" type="password" required class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700">Confirmar senha</label>
          <input v-model="form.password_confirmation" type="password" required class="mt-1 block w-full rounded-lg py-2 px-3 text-sm ring-1 ring-slate-200 focus:ring-2 focus:ring-brand" />
        </div>
        <button type="submit" :disabled="authStore.isLoading" class="btn-primary w-full">
          {{ authStore.isLoading ? 'Registrando...' : 'Criar conta' }}
        </button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-500">
        Já tem conta? <RouterLink to="/login" class="font-semibold text-brand-700">Entrar</RouterLink>
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
const form = ref({ name: '', email: '', password: '', password_confirmation: '' })

async function onSubmit() {
  if (form.value.password !== form.value.password_confirmation) {
    error('As senhas não conferem.')
    return
  }
  try {
    await authStore.register(form.value)
    router.push('/')
  } catch (err: unknown) {
    const message = (err as { response?: { data?: { message?: string } } })?.response?.data?.message
    error(message || 'Erro ao registrar')
  }
}
</script>
