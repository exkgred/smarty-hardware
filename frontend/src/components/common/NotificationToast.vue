<template>
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
