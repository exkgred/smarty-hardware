<template>
  <div class="space-y-4">
    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700">CEP</label>
        <div class="mt-1 flex gap-2">
          <input
            :value="modelValue.zip"
            type="text"
            inputmode="numeric"
            maxlength="9"
            placeholder="00000-000"
            :required="required"
            class="block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
            data-cy="address-zip"
            @input="onZipInput"
            @blur="() => lookup()"
          />
          <button type="button" class="btn-ghost shrink-0 px-3" data-cy="address-lookup" :disabled="looking" @click="() => lookup()">
            {{ looking ? '...' : 'Buscar' }}
          </button>
        </div>
        <p v-if="cepError" class="mt-1 text-xs text-rose-600">{{ cepError }}</p>
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Logradouro</label>
        <input
          :value="modelValue.street"
          type="text"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          data-cy="address-street"
          @input="patch({ street: ($event.target as HTMLInputElement).value })"
        />
      </div>
    </div>
    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700">Número</label>
        <input
          :value="modelValue.number"
          type="text"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          data-cy="address-number"
          @input="patch({ number: ($event.target as HTMLInputElement).value })"
        />
      </div>
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700">Complemento</label>
        <input
          :value="modelValue.complement"
          type="text"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          @input="patch({ complement: ($event.target as HTMLInputElement).value })"
        />
      </div>
    </div>
    <div class="grid sm:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700">Bairro</label>
        <input
          :value="modelValue.neighborhood"
          type="text"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          data-cy="address-neighborhood"
          @input="patch({ neighborhood: ($event.target as HTMLInputElement).value })"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Cidade</label>
        <input
          :value="modelValue.city"
          type="text"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          data-cy="address-city"
          @input="patch({ city: ($event.target as HTMLInputElement).value })"
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">UF</label>
        <input
          :value="modelValue.state"
          type="text"
          maxlength="2"
          :required="required"
          class="mt-1 block w-full uppercase rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          data-cy="address-state"
          @input="patch({ state: ($event.target as HTMLInputElement).value.toUpperCase() })"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { cepApi } from '@/services/api'
import type { Address } from '@/types'

const props = defineProps<{
  modelValue: Address
  required?: boolean
}>()

const emit = defineEmits<{
  'update:modelValue': [Address]
}>()

const looking = ref(false)
const cepError = ref('')

function patch(partial: Partial<Address>) {
  emit('update:modelValue', { ...props.modelValue, ...partial })
}

function maskZip(value: string): string {
  const digits = value.replace(/\D/g, '').slice(0, 8)
  return digits.length > 5 ? `${digits.slice(0, 5)}-${digits.slice(5)}` : digits
}

function onZipInput(event: Event) {
  const value = maskZip((event.target as HTMLInputElement).value)
  cepError.value = ''
  patch({ zip: value })
  if (value.replace(/\D/g, '').length === 8) {
    lookup(value)
  }
}

async function lookup(zipOverride?: string) {
  const digits = (zipOverride ?? props.modelValue.zip ?? '').replace(/\D/g, '')
  if (digits.length !== 8 || looking.value) return
  looking.value = true
  cepError.value = ''
  try {
    const { data } = await cepApi.lookup(digits)
    emit('update:modelValue', {
      ...props.modelValue,
      zip: data.zip || zipOverride || props.modelValue.zip,
      street: data.street || props.modelValue.street,
      neighborhood: data.neighborhood || props.modelValue.neighborhood,
      city: data.city || props.modelValue.city,
      state: data.state || props.modelValue.state
    })
  } catch {
    cepError.value = 'CEP não encontrado. Preencha o endereço manualmente.'
  } finally {
    looking.value = false
  }
}
</script>
