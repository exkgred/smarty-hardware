<script setup lang="ts">
withDefaults(
  defineProps<{
    size?: number
    tone?: 'onDark' | 'onLight'
  }>(),
  { size: 32, tone: 'onDark' },
)
</script>

<template>
  <span class="inline-flex items-center gap-2">
    <svg :width="size" :height="size" viewBox="0 0 32 32" aria-hidden="true">
      <rect width="32" height="32" rx="8" fill="#0b1220" />
      <path
        d="M10.2 11.2h11.6v9.6H10.2z"
        fill="none"
        stroke="#22d3ee"
        stroke-width="1.6"
      />
      <path d="M12.2 11.2V9.6h7.6v1.6M12.2 20.8v1.6h7.6v-1.6" stroke="#22d3ee" stroke-width="1.6" />
      <path d="M16 8.8 12.6 16h3.1l-1.1 6.2L20 13.6h-3.2L16 8.8Z" fill="#06b6d4" />
    </svg>
    <span class="leading-tight">
      <span
        class="block text-sm font-bold tracking-wide"
        :class="tone === 'onLight' ? 'text-ink' : 'text-white'"
      >SMARTY</span>
      <span
        class="block text-[10px] uppercase tracking-[0.2em]"
        :class="tone === 'onLight' ? 'text-brand-700' : 'text-brand-200'"
      >Hardware</span>
    </span>
  </span>
</template>
