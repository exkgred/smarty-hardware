export interface Address {
  zip: string
  street: string
  number: string
  complement: string
  neighborhood: string
  city: string
  state: string
}

export interface SavedCard {
  id: number
  brand: string
  last_four: string
  holder_name: string
  exp_month: number
  exp_year: number
  is_default: boolean
  label: string
}

export interface CardInput {
  number: string
  holder_name: string
  exp_month: number
  exp_year: number
  cvv: string
}

export interface User {
  id: number
  name: string
  email: string
  role: 'customer' | 'admin'
  phone?: string | null
  address?: Address | null
}

export interface Category { id: number; name: string; slug: string; description: string | null }
export interface Product { id: number; category_id: number; category?: Category; name: string; slug: string; description: string | null; price: number; stock_quantity: number; image_url: string | null; is_active: boolean }
export interface CartItem { product: Product; quantity: number }
export interface OrderItem { id: number; product_id: number; product_name: string; unit_price: number; quantity: number; subtotal: number }
export interface Order {
  id: number
  status: string
  subtotal: number
  discount_amount: number
  shipping_cost: number
  total: number
  items: OrderItem[]
  created_at: string
  shipping_name?: string | null
  shipping_address?: string | null
  shipping_details?: Address | null
  payment_method?: string | null
  payment_method_label?: string | null
  channel?: string
  payment_receipt?: Record<string, string> | null
  invoice?: { id: number; number: string; type: string; total: number } | null
  customer_name?: string | null
}
export interface PaginatedResponse<T> { data: T[]; current_page: number; last_page: number; per_page: number; total: number }
export interface ChatMessage { role: 'user' | 'assistant'; content: string }
export interface CheckoutPayload {
  name?: string
  phone?: string
  address?: Address
  save_address?: boolean
  payment_method?: string
  card_id?: number
  save_card?: boolean
  card?: CardInput
}

export function emptyAddress(): Address {
  return { zip: '', street: '', number: '', complement: '', neighborhood: '', city: '', state: '' }
}

export function emptyCard(): CardInput {
  return { number: '', holder_name: '', exp_month: 12, exp_year: new Date().getFullYear() + 3, cvv: '' }
}
