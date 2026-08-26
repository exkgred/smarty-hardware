import { defineStore } from 'pinia'
import { ref } from 'vue'
import { chatApi } from '@/services/api'
import type { ChatMessage } from '@/types'

function newSessionId(): string {
  return crypto.randomUUID()
}

export const useChatStore = defineStore('chat', () => {
  const messages = ref<ChatMessage[]>([])
  const isLoading = ref(false)
  const sessionId = ref(localStorage.getItem('chat_session_id') || '')

  function initSession() {
    if (!sessionId.value) {
      sessionId.value = newSessionId()
      localStorage.setItem('chat_session_id', sessionId.value)
    }
    messages.value = [{ role: 'assistant', content: 'Olá! Sou a Mia, da Smarty Hardware. Posso falar de peças, estoque, assistência da bancada e formas de pagamento (PIX, cartão, boleto, dinheiro ou transferência).' }]
  }

  async function sendMessage(content: string) {
    messages.value.push({ role: 'user', content })
    isLoading.value = true
    try {
      const res = await chatApi.sendMessage(sessionId.value, content)
      messages.value.push({ role: 'assistant', content: res.data.message })
    } catch {
      messages.value.push({ role: 'assistant', content: 'Desculpe, ocorreu um erro ao processar sua mensagem.' })
    } finally {
      isLoading.value = false
    }
  }

  function clearHistory() {
    messages.value = [{ role: 'assistant', content: 'Olá! Sou a Mia, da Smarty Hardware. Posso falar de peças, estoque, assistência da bancada e formas de pagamento (PIX, cartão, boleto, dinheiro ou transferência).' }]
    sessionId.value = newSessionId()
    localStorage.setItem('chat_session_id', sessionId.value)
  }

  return { messages, isLoading, sessionId, initSession, sendMessage, clearHistory }
})
