import { ref } from 'vue'

export interface Notification {
  id: number
  type: 'success' | 'error' | 'info'
  message: string
}

const notifications = ref<Notification[]>([])
let nextId = 0

export function useNotification() {
  const add = (type: Notification['type'], message: string) => {
    const id = nextId++
    notifications.value.push({ id, type, message })
    setTimeout(() => {
      remove(id)
    }, 3000)
  }

  const remove = (id: number) => {
    notifications.value = notifications.value.filter(n => n.id !== id)
  }

  return {
    notifications,
    success: (msg: string) => add('success', msg),
    error: (msg: string) => add('error', msg),
    info: (msg: string) => add('info', msg),
    remove
  }
}
