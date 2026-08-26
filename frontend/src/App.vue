<template>
  <div class="min-h-screen flex flex-col">
    <AppNavbar v-if="!isAdminRoute" />
    <main class="flex-grow">
      <RouterView />
    </main>
    <AppFooter v-if="!isAdminRoute" />
    <ChatWidget v-if="!isAdminRoute" />
    <NotificationToast />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppNavbar from '@/components/common/AppNavbar.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import ChatWidget from '@/components/chat/ChatWidget.vue'
import NotificationToast from '@/components/common/NotificationToast.vue'

const authStore = useAuthStore()
const route = useRoute()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))

onMounted(() => {
  authStore.initAuth()
})
</script>
