<template>
  <div class="fixed bottom-4 right-4 z-40">
    <button v-if="!isOpen" @click="toggleChat" class="bg-brand text-ink p-4 rounded-full shadow-glow hover:bg-brand-dark transition" aria-label="Abrir chat">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
    </button>
    <div v-else class="w-80 h-[28rem] bg-white rounded-2xl shadow-xl flex flex-col border border-slate-200 overflow-hidden">
      <div class="bg-ink text-white p-3 flex justify-between items-center">
        <div>
          <h3 class="font-semibold text-sm">Mia · Smarty</h3>
          <p class="text-[11px] text-slate-400">Peças, estoque e assistência</p>
        </div>
        <button @click="toggleChat" class="text-slate-300 hover:text-white text-xl leading-none">&times;</button>
      </div>
      <div class="flex-1 p-3 overflow-y-auto flex flex-col space-y-2" ref="messagesContainer">
        <div v-if="chatStore.messages.length <= 1" class="flex flex-wrap gap-1.5 mb-2">
          <button v-for="prompt in prompts" :key="prompt" type="button" class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] text-slate-700 hover:bg-brand-50" @click="ask(prompt)">{{ prompt }}</button>
        </div>
        <div v-for="(msg, idx) in chatStore.messages" :key="idx" :class="['max-w-[85%] rounded-xl p-2 text-sm whitespace-pre-wrap', msg.role === 'user' ? 'bg-ink text-white self-end' : 'bg-slate-100 self-start text-slate-800']">
          {{ msg.content }}
        </div>
        <div v-if="chatStore.isLoading" class="bg-slate-100 self-start rounded-xl p-2 flex space-x-1">
          <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
          <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
      </div>
      <form @submit.prevent="sendMessage" class="p-3 border-t flex gap-2">
        <input v-model="input" type="text" class="flex-1 rounded-lg border-slate-200 text-sm p-2 ring-1 ring-slate-200 focus:ring-brand" placeholder="Ex.: tem Ryzen 7 em estoque?" :disabled="chatStore.isLoading" />
        <button type="submit" class="bg-brand text-ink px-3 py-1 rounded-lg text-sm font-semibold disabled:opacity-50" :disabled="!input.trim() || chatStore.isLoading">Enviar</button>
      </form>
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
const prompts = ['Tem Ryzen 7?', 'Quais formas de pagamento?', 'Fazem limpeza de notebook?', 'Qual o horário?']

function toggleChat() {
  isOpen.value = !isOpen.value
  if (isOpen.value && chatStore.messages.length === 0) {
    chatStore.initSession()
  }
}

async function ask(text: string) {
  await chatStore.sendMessage(text)
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
