export const PAYMENT_METHODS = [
  { value: 'PIX', label: 'PIX', hint: 'Aprovação imediata (sandbox)' },
  { value: 'CREDIT_CARD', label: 'Cartão de crédito', hint: '1x no **** 4242' },
  { value: 'DEBIT_CARD', label: 'Cartão de débito', hint: 'Débito sandbox' },
  { value: 'BOLETO', label: 'Boleto bancário', hint: 'Linha digitável simulada' },
  { value: 'CASH', label: 'Dinheiro (balcão)', hint: 'Recebido na loja' },
  { value: 'TRANSFER', label: 'Transferência', hint: 'TED/DOC sandbox' }
] as const

export type PaymentMethodValue = (typeof PAYMENT_METHODS)[number]['value']

export function paymentLabel(value?: string | null): string {
  return PAYMENT_METHODS.find(m => m.value === value)?.label ?? value ?? '—'
}

export function slugify(name: string): string {
  return name
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '')
}

export function formatBRL(value: number): string {
  return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

export function isServiceProduct(product: { category?: { slug?: string } | null }): boolean {
  return product.category?.slug === 'servicos'
}
