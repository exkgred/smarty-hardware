<template>
  <div class="space-y-4">
    <p v-if="hint" class="text-xs text-slate-500">{{ hint }}</p>
    <div>
      <label class="block text-sm font-medium text-slate-700">Número do cartão</label>
      <input
        :value="modelValue.number"
        type="text"
        inputmode="numeric"
        autocomplete="cc-number"
        placeholder="ACCT-000015"
        :required="required"
        class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
        @input="onNumber"
      />
    </div>
    <div>
      <label class="block text-sm font-medium text-slate-700">Nome impresso</label>
      <input
        :value="modelValue.holder_name"
        type="text"
        autocomplete="cc-name"
        :required="required"
        class="mt-1 block w-full uppercase rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
        @input="patch({ holder_name: ($event.target as HTMLInputElement).value.toUpperCase() })"
      />
    </div>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-slate-700">Mês</label>
        <select
          :value="modelValue.exp_month"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          @change="patch({ exp_month: Number(($event.target as HTMLSelectElement).value) })"
        >
          <option v-for="month in 12" :key="month" :value="month">{{ String(month).padStart(2, '0') }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">Ano</label>
        <select
          :value="modelValue.exp_year"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          @change="patch({ exp_year: Number(($event.target as HTMLSelectElement).value) })"
        >
          <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700">CVV</label>
        <input
          :value="modelValue.cvv"
          type="password"
          inputmode="numeric"
          maxlength="4"
          autocomplete="cc-csc"
          :required="required"
          class="mt-1 block w-full rounded-lg ring-1 ring-slate-200 py-2 px-3 text-sm focus:ring-brand"
          @input="patch({ cvv: ($event.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 4) })"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { CardInput } from '@/types'

const props = defineProps<{
  modelValue: CardInput
  required?: boolean
  hint?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [CardInput]
}>()

const years = computed(() => {
  const start = new Date().getFullYear()
  return Array.from({ length: 12 }, (_, i) => start + i)
})

function patch(partial: Partial<CardInput>) {
  emit('update:modelValue', { ...props.modelValue, ...partial })
}

function onNumber(event: Event) {
  const digits = (event.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 19)
  const grouped = digits.replace(/(\d{4})(?=\d)/g, '$1 ').trim()
  patch({ number: grouped })
}
</script>
