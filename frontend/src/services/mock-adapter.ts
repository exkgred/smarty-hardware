import type { AxiosAdapter, InternalAxiosRequestConfig } from 'axios'
import { demoCategories, demoProducts } from '@/data/demo-catalog'
import type { Order, Product, SavedCard, User } from '@/types'

const STORAGE_KEY = 'smarty-demo-state'

interface DemoUser extends User {
  password: string
}

interface DemoState {
  users: DemoUser[]
  products: Product[]
  orders: Order[]
  cards: SavedCard[]
  knowledge: { id: number; title: string; content: string }[]
  nextOrderId: number
  nextProductId: number
  nextCardId: number
  nextKnowledgeId: number
}

function seedState(): DemoState {
  return {
    users: [
      { id: 1, name: 'Admin Smarty', email: 'admin@marketplace.test', password: 'password', role: 'admin', phone: '11999990000', address: null },
      { id: 2, name: 'Cliente Smarty', email: 'cliente@marketplace.test', password: 'password', role: 'customer', phone: '11988887777', address: null },
      { id: 3, name: 'Cliente balcão', email: 'balcao@smartyhardware.test', password: 'password', role: 'customer', phone: null, address: null },
    ],
    products: demoProducts.map((p) => ({ ...p })),
    orders: [
      {
        id: 1001,
        status: 'paid',
        subtotal: 899,
        discount_amount: 0,
        shipping_cost: 15,
        total: 914,
        items: [{ id: 1, product_id: 1, product_name: 'AMD Ryzen 7 3700X — 8 núcleos / AM4', unit_price: 899, quantity: 1, subtotal: 899 }],
        created_at: new Date(Date.now() - 86400000 * 3).toISOString(),
        shipping_name: 'Cliente Smarty',
        payment_method: 'PIX',
        payment_method_label: 'PIX',
        channel: 'online',
        customer_name: 'Cliente Smarty',
      },
    ],
    cards: [],
    knowledge: [
      { id: 1, title: 'Horário', content: 'Segunda a sexta, 9h às 18h. Sábado até 13h. São Paulo.' },
      { id: 2, title: 'Pagamento', content: 'PIX, crédito, débito, boleto, dinheiro e TED. Sandbox: sem cobrança real.' },
    ],
    nextOrderId: 1002,
    nextProductId: 17,
    nextCardId: 1,
    nextKnowledgeId: 3,
  }
}

function loadState(): DemoState {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (raw) return JSON.parse(raw) as DemoState
  } catch {
    /* ignore */
  }
  return seedState()
}

function saveState(state: DemoState): void {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state))
}

function publicUser(user: DemoUser): User {
  const { password: _password, ...safe } = user
  return safe
}

function currentUser(state: DemoState, config: InternalAxiosRequestConfig): DemoUser | null {
  const header = String(config.headers?.Authorization || '')
  const token = header.replace(/^Bearer\s+/i, '')
  if (!token.startsWith('demo-')) return null
  const id = Number(token.replace('demo-', ''))
  return state.users.find((u) => u.id === id) ?? null
}

function paginate<T>(items: T[], page = 1, perPage = 24) {
  const start = (page - 1) * perPage
  return {
    data: items.slice(start, start + perPage),
    current_page: page,
    last_page: Math.max(1, Math.ceil(items.length / perPage)),
    per_page: perPage,
    total: items.length,
  }
}

function notFound(message: string): never {
  const error = new Error(message) as Error & { status: number }
  error.status = 404
  throw error
}

function unauthorized(message = 'Não autenticado'): never {
  const error = new Error(message) as Error & { status: number }
  error.status = 401
  throw error
}

function replyChat(message: string): string {
  const text = message.toLowerCase()
  if (text.includes('ryzen') || text.includes('processador')) {
    return 'Temos o Ryzen 7 3700X por R$ 899 e o i9-9900K por R$ 1.099. Os dois estão em estoque nesta demo.'
  }
  if (text.includes('pag') || text.includes('pix') || text.includes('cartão') || text.includes('cartao')) {
    return 'Aceitamos PIX, crédito, débito, boleto, dinheiro e TED. Nesta demo o pagamento é sandbox — sem cobrança real.'
  }
  if (text.includes('limp') || text.includes('notebook') || text.includes('assist')) {
    return 'Fazemos limpeza + pasta térmica (R$ 179), formatação (R$ 199), diagnóstico de placa (R$ 120) e montagem de PC (R$ 250). Serviços não cobram frete.'
  }
  if (text.includes('horár') || text.includes('horario') || text.includes('abre')) {
    return 'A bancada Smarty funciona de segunda a sexta, 9h às 18h, e sábado até 13h. Estamos em São Paulo.'
  }
  if (text.includes('rtx') || text.includes('placa de vídeo') || text.includes('gpu')) {
    return 'Temos a NVIDIA GeForce RTX Founders Edition por R$ 2.499. Recomendamos fonte de 650 W.'
  }
  return 'Sou a Mia, da Smarty Hardware (demo). Posso falar de peças em estoque, assistência da bancada, frete de R$ 15 e formas de pagamento. O que você quer saber?'
}

async function handleDemoRequest(config: InternalAxiosRequestConfig): Promise<unknown> {
  const state = loadState()
  const method = (config.method || 'get').toUpperCase()
  const url = (config.url || '').replace(/\/$/, '')
  const params = (config.params || {}) as Record<string, unknown>
  const body = (typeof config.data === 'string' ? JSON.parse(config.data || '{}') : config.data || {}) as Record<string, unknown>
  const user = currentUser(state, config)

  const withCategory = (product: Product): Product => ({
    ...product,
    category: demoCategories.find((c) => c.id === product.category_id) || product.category,
  })

  if (url === '/categories' && method === 'GET') return demoCategories

  if (url === '/products' && method === 'GET') {
    let list = state.products.filter((p) => p.is_active).map(withCategory)
    const categoryId = params.category_id ? Number(params.category_id) : null
    const search = String(params.search || '').toLowerCase()
    const min = params.min_price != null ? Number(params.min_price) : null
    const max = params.max_price != null ? Number(params.max_price) : null
    if (categoryId) list = list.filter((p) => p.category_id === categoryId)
    if (search) list = list.filter((p) => p.name.toLowerCase().includes(search))
    if (min != null) list = list.filter((p) => p.price >= min)
    if (max != null) list = list.filter((p) => p.price <= max)
    return paginate(list, Number(params.page || 1), Number(params.per_page || 24))
  }

  const productMatch = url.match(/^\/products\/([^/]+)$/)
  if (productMatch && method === 'GET') {
    const product = state.products.find((p) => p.slug === productMatch[1])
    if (!product) notFound('Produto não encontrado')
    return withCategory(product)
  }

  if (url === '/login' && method === 'POST') {
    const email = String(body.email || '').toLowerCase()
    const password = String(body.password || '')
    const found = state.users.find((u) => u.email === email && u.password === password)
      || (email.includes('@') && password.length >= 4
        ? { id: 2, name: email.split('@')[0], email, password, role: 'customer' as const, phone: null, address: null }
        : null)
    if (!found) {
      const error = new Error('Credenciais inválidas') as Error & { status: number }
      error.status = 422
      throw error
    }
    if (!state.users.some((u) => u.id === found.id)) {
      found.id = state.users.length + 1
      state.users.push(found)
      saveState(state)
    }
    return { token: `demo-${found.id}`, user: publicUser(found) }
  }

  if (url === '/register' && method === 'POST') {
    const created: DemoUser = {
      id: state.users.length + 1,
      name: String(body.name || 'Visitante'),
      email: String(body.email || `user${Date.now()}@demo.test`),
      password: String(body.password || 'password'),
      role: 'customer',
      phone: null,
      address: null,
    }
    state.users.push(created)
    saveState(state)
    return { token: `demo-${created.id}`, user: publicUser(created) }
  }

  if (url === '/logout' && method === 'POST') return { ok: true }

  if (url === '/me' && method === 'GET') {
    if (!user) unauthorized()
    return publicUser(user)
  }

  if (url === '/me' && method === 'PUT') {
    if (!user) unauthorized()
    Object.assign(user, {
      name: body.name ?? user.name,
      phone: body.phone ?? user.phone,
      address: body.address ?? user.address,
    })
    saveState(state)
    return publicUser(user)
  }

  if (url.startsWith('/cep/') && method === 'GET') {
    const cep = url.replace('/cep/', '')
    if (cep === '01310100' || cep === '01001000') {
      return { zip: cep, street: 'Avenida Paulista', neighborhood: 'Bela Vista', city: 'São Paulo', state: 'SP' }
    }
    try {
      const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
      const data = await res.json() as { erro?: boolean; logradouro?: string; bairro?: string; localidade?: string; uf?: string }
      if (data.erro) notFound('CEP não encontrado')
      return { zip: cep, street: data.logradouro, neighborhood: data.bairro, city: data.localidade, state: data.uf }
    } catch {
      return { zip: cep, street: 'Rua da Demo', neighborhood: 'Centro', city: 'São Paulo', state: 'SP' }
    }
  }

  if (url === '/me/cards' && method === 'GET') return state.cards
  if (url === '/me/cards' && method === 'POST') {
    const number = String(body.number || '').replace(/\D/g, '')
    const card: SavedCard = {
      id: state.nextCardId++,
      brand: number.startsWith('4') ? 'Visa' : 'Mastercard',
      last_four: number.slice(-4) || '4242',
      holder_name: String(body.holder_name || 'CLIENTE DEMO'),
      exp_month: Number(body.exp_month || 12),
      exp_year: Number(body.exp_year || 2030),
      is_default: Boolean(body.is_default) || state.cards.length === 0,
      label: `${number.startsWith('4') ? 'Visa' : 'Mastercard'} •••• ${number.slice(-4) || '4242'}`,
    }
    state.cards.push(card)
    saveState(state)
    return card
  }
  const cardDelete = url.match(/^\/me\/cards\/(\d+)$/)
  if (cardDelete && method === 'DELETE') {
    state.cards = state.cards.filter((c) => c.id !== Number(cardDelete[1]))
    saveState(state)
    return { ok: true }
  }

  if (url === '/orders/my' && method === 'GET') return state.orders
  const orderGet = url.match(/^\/orders\/(\d+)$/)
  if (orderGet && method === 'GET') {
    const order = state.orders.find((o) => o.id === Number(orderGet[1]))
    if (!order) notFound('Pedido não encontrado')
    return order
  }

  if (url === '/orders/checkout' && method === 'POST') {
    if (!user) unauthorized()
    const items = (body.items as { product_id: number; quantity: number }[]) || []
    const orderItems = items.map((item, index) => {
      const product = state.products.find((p) => p.id === item.product_id)
      const price = product?.price ?? 0
      return {
        id: index + 1,
        product_id: item.product_id,
        product_name: product?.name || 'Item',
        unit_price: price,
        quantity: item.quantity,
        subtotal: price * item.quantity,
      }
    })
    const subtotal = orderItems.reduce((sum, item) => sum + item.subtotal, 0)
    const shipping = Number(body.shippingCost || 0)
    const methodLabel: Record<string, string> = {
      PIX: 'PIX', CREDIT_CARD: 'Cartão de crédito', DEBIT_CARD: 'Cartão de débito',
      BOLETO: 'Boleto', CASH: 'Dinheiro', TED: 'TED',
    }
    const pay = String(body.payment_method || 'PIX')
    const order: Order = {
      id: state.nextOrderId++,
      status: 'paid',
      subtotal,
      discount_amount: 0,
      shipping_cost: shipping,
      total: subtotal + shipping,
      items: orderItems,
      created_at: new Date().toISOString(),
      shipping_name: String(body.name || user.name),
      payment_method: pay,
      payment_method_label: methodLabel[pay] || pay,
      channel: 'online',
      customer_name: user.name,
    }
    state.orders.unshift(order)
    if (body.save_card && body.card && typeof body.card === 'object') {
      const cardInput = body.card as { number?: string; holder_name?: string; exp_month?: number; exp_year?: number }
      const number = String(cardInput.number || '4242424242424242').replace(/\D/g, '')
      state.cards.push({
        id: state.nextCardId++,
        brand: 'Visa',
        last_four: number.slice(-4),
        holder_name: cardInput.holder_name || user.name,
        exp_month: cardInput.exp_month || 12,
        exp_year: cardInput.exp_year || 2030,
        is_default: state.cards.length === 0,
        label: `Visa •••• ${number.slice(-4)}`,
      })
    }
    saveState(state)
    return order
  }

  if (url === '/payment/methods' && method === 'GET') {
    return [
      { value: 'PIX', label: 'PIX' },
      { value: 'CREDIT_CARD', label: 'Crédito' },
      { value: 'DEBIT_CARD', label: 'Débito' },
    ]
  }
  if (url === '/payment/intent' && method === 'POST') {
    return { status: 'approved', orderId: body.orderId }
  }

  if (url === '/chat/send' && method === 'POST') {
    return { message: replyChat(String(body.message || '')) }
  }
  if (url.startsWith('/chat/history/') && method === 'GET') return []

  if (url === '/admin/dashboard' && method === 'GET') {
    return {
      totalOrders: state.orders.length,
      pendingOrders: state.orders.filter((o) => o.status === 'pending').length,
      totalSales: state.orders.reduce((sum, o) => sum + o.total, 0),
      lowStockProducts: state.products.filter((p) => p.stock_quantity < 6).length,
      recent_orders: state.orders.slice(0, 5),
    }
  }

  if (url === '/admin/products' && method === 'GET') {
    return paginate(state.products.map(withCategory), Number(params.page || 1), Number(params.per_page || 50))
  }
  if (url === '/admin/products' && method === 'POST') {
    const created: Product = {
      id: state.nextProductId++,
      category_id: Number(body.category_id || 1),
      name: String(body.name || 'Novo produto'),
      slug: String(body.slug || `produto-${Date.now()}`),
      description: String(body.description || ''),
      price: Number(body.price || 0),
      stock_quantity: Number(body.stock_quantity || 0),
      image_url: body.image_url ? String(body.image_url) : demoProducts[0].image_url,
      is_active: body.is_active !== false,
    }
    state.products.push(created)
    saveState(state)
    return created
  }
  const adminProduct = url.match(/^\/admin\/products\/(\d+)$/)
  if (adminProduct && method === 'PUT') {
    const product = state.products.find((p) => p.id === Number(adminProduct[1]))
    if (!product) notFound('Produto não encontrado')
    Object.assign(product, body)
    saveState(state)
    return product
  }
  if (adminProduct && method === 'DELETE') {
    state.products = state.products.filter((p) => p.id !== Number(adminProduct[1]))
    saveState(state)
    return { ok: true }
  }

  if (url === '/admin/inventory/alerts' && method === 'GET') {
    return state.products
      .filter((p) => p.stock_quantity < 6)
      .map((p) => ({ id: p.id, product_name: p.name, stock_quantity: p.stock_quantity }))
  }
  if (url === '/admin/inventory/movements' && method === 'GET') return []
  if (url.startsWith('/admin/inventory/') && method === 'GET') return []
  if (url === '/admin/inventory/adjust' && method === 'POST') return { ok: true }
  if (url === '/admin/inventory/entries' && method === 'POST') {
    const product = state.products.find((p) => p.id === Number(body.product_id))
    if (product) product.stock_quantity += Number(body.quantity || 0)
    saveState(state)
    return { ok: true }
  }

  if (url === '/admin/customers' && method === 'GET') {
    return state.users.filter((u) => u.role === 'customer').map(publicUser)
  }
  if (url === '/admin/sales' && method === 'POST') {
    const order: Order = {
      id: state.nextOrderId++,
      status: 'paid',
      subtotal: Number(body.total || 0),
      discount_amount: 0,
      shipping_cost: 0,
      total: Number(body.total || 0),
      items: [],
      created_at: new Date().toISOString(),
      payment_method: String(body.payment_method || 'CASH'),
      payment_method_label: 'Balcão',
      channel: 'counter',
      invoice: { id: state.nextOrderId, number: `NF-${state.nextOrderId}`, type: 'NFCe', total: Number(body.total || 0) },
      customer_name: String(body.customer_name || 'Balcão'),
    }
    state.orders.unshift(order)
    saveState(state)
    return order
  }

  if (url === '/admin/invoices' && method === 'GET') {
    return state.orders
      .filter((o) => o.invoice)
      .map((o) => ({ id: o.invoice!.id, number: o.invoice!.number, type: o.invoice!.type, total: o.total, payment_method: o.payment_method, created_at: o.created_at }))
  }
  const invoiceGet = url.match(/^\/admin\/invoices\/(\d+)$/)
  if (invoiceGet && method === 'GET') {
    const order = state.orders.find((o) => o.invoice?.id === Number(invoiceGet[1]))
    if (!order?.invoice) notFound('Nota não encontrada')
    return { ...order.invoice, order }
  }
  const issueInvoice = url.match(/^\/admin\/orders\/(\d+)\/invoice$/)
  if (issueInvoice && method === 'POST') {
    const order = state.orders.find((o) => o.id === Number(issueInvoice[1]))
    if (!order) notFound('Pedido não encontrado')
    order.invoice = { id: order.id + 5000, number: `NF-${order.id}`, type: 'NFCe', total: order.total }
    saveState(state)
    return order.invoice
  }

  if (url === '/admin/orders' && method === 'GET') {
    return paginate(state.orders, Number(params.page || 1), Number(params.per_page || 20))
  }
  const adminOrder = url.match(/^\/admin\/orders\/(\d+)$/)
  if (adminOrder && method === 'GET') {
    const order = state.orders.find((o) => o.id === Number(adminOrder[1]))
    if (!order) notFound('Pedido não encontrado')
    return order
  }
  const orderStatus = url.match(/^\/admin\/orders\/(\d+)\/status$/)
  if (orderStatus && method === 'PUT') {
    const order = state.orders.find((o) => o.id === Number(orderStatus[1]))
    if (!order) notFound('Pedido não encontrado')
    order.status = String(body.status || order.status)
    saveState(state)
    return order
  }
  const refund = url.match(/^\/admin\/orders\/(\d+)\/refund$/)
  if (refund && method === 'POST') {
    const order = state.orders.find((o) => o.id === Number(refund[1]))
    if (!order) notFound('Pedido não encontrado')
    order.status = 'refunded'
    saveState(state)
    return order
  }

  if (url === '/admin/chatbot/knowledge' && method === 'GET') return state.knowledge
  if (url === '/admin/chatbot/knowledge' && method === 'POST') {
    const doc = { id: state.nextKnowledgeId++, title: String(body.title || 'Nota'), content: String(body.content || '') }
    state.knowledge.push(doc)
    saveState(state)
    return doc
  }
  const knowledgeDel = url.match(/^\/admin\/chatbot\/knowledge\/(\d+)$/)
  if (knowledgeDel && method === 'DELETE') {
    state.knowledge = state.knowledge.filter((k) => k.id !== Number(knowledgeDel[1]))
    saveState(state)
    return { ok: true }
  }

  return { ok: true }
}

export const demoAdapter: AxiosAdapter = async (config) => {
  try {
    const data = await handleDemoRequest(config)
    return { data, status: 200, statusText: 'OK', headers: {}, config }
  } catch (err) {
    const status = (err as { status?: number }).status ?? 400
    const message = err instanceof Error ? err.message : 'Erro na demo'
    const error = new Error(message) as Error & {
      response: { data: { message: string }; status: number; config: InternalAxiosRequestConfig }
      config: InternalAxiosRequestConfig
      isAxiosError: boolean
    }
    error.response = { data: { message }, status, config }
    error.config = config
    error.isAxiosError = true
    return Promise.reject(error)
  }
}
