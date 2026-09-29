export const API_BASE_URL = process.env.VUE_APP_API_URL || '/api'

export const LOCALES = ['en', 'ru']
export const DEFAULT_LOCALE = 'en'

export const FORMATS = ['print', 'digital', 'both']
export const SORTS = ['newest', 'price_asc', 'price_desc', 'title']
export const PER_PAGE = 12

export const ORDER_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']
// Mirrors App\States\OrderStates on the backend: which status an order may move to next.
export const ORDER_TRANSITIONS = {
  pending: ['processing', 'cancelled'],
  processing: ['shipped', 'cancelled'],
  shipped: ['delivered'],
  delivered: [],
  cancelled: [],
}
