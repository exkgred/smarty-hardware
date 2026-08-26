<template>
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
