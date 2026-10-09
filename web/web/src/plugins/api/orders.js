import { http } from './http'

export const list = () => http.get('/orders')
export const get = (id) => http.get(`/orders/${id}`)
export const checkout = (payload) => http.post('/orders', payload)
export const pay = (id) => http.post(`/orders/${id}/pay`)
export const cancel = (id) => http.post(`/orders/${id}/cancel`)
