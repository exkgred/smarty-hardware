<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-900">Configuração do Chatbot</h2>
      <button @click="showModal = true" class="bg-brand text-white px-4 py-2 rounded text-sm font-medium">Novo Documento</button>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Título</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="doc in documents" :key="doc.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ doc.title }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ doc.type }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button @click="deleteDoc(doc.id)" class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="documents.length === 0" class="p-6 text-center text-gray-500">Nenhum documento cadastrado.</div>
    </div>
    
    <AppModal v-model="showModal" title="Adicionar Conhecimento">
      <form @submit.prevent="saveDoc" class="space-y-4 mt-4">
        <div><label class="block text-sm font-medium text-gray-700">Título</label><input v-model="form.title" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" /></div>
        <div><label class="block text-sm font-medium text-gray-700">Tipo</label><select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"><option value="faq">FAQ</option><option value="policy">Política</option></select></div>
        <div><label class="block text-sm font-medium text-gray-700">Conteúdo</label><textarea v-model="form.content" required rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea></div>
        <div class="mt-5 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
          <button type="submit" class="inline-flex w-full justify-center rounded-md bg-brand px-3 py-2 text-sm font-semibold text-white shadow-sm sm:col-start-2">Salvar</button>
          <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 sm:col-start-1 sm:mt-0">Cancelar</button>
        </div>
      </form>
    </AppModal>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { adminApi } from '@/services/api'
import AppModal from '@/components/common/AppModal.vue'
import { useNotification } from '@/composables/useNotification'

const documents = ref<any[]>([])
const showModal = ref(false)
const form = ref({ title: '', content: '', type: 'faq' })
const { success, error } = useNotification()

onMounted(loadDocs)

async function loadDocs() {
  try {
    const res = await adminApi.chatbot.listKnowledge()
    documents.value = res.data
  } catch (e) {
    error('Erro ao carregar documentos')
  }
}

async function saveDoc() {
  try {
    await adminApi.chatbot.addKnowledge(form.value)
    success('Documento salvo')
    showModal.value = false
    form.value = { title: '', content: '', type: 'faq' }
    loadDocs()
  } catch (e) {
    error('Erro ao salvar documento')
  }
}

async function deleteDoc(id: number) {
  if (confirm('Tem certeza?')) {
    try {
      await adminApi.chatbot.deleteKnowledge(id)
      success('Documento excluído')
      loadDocs()
    } catch (e) {
      error('Erro ao excluir documento')
    }
  }
}
</script>
