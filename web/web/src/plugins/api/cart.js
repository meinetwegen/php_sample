import { http } from './http'

export const get = () => http.get('/cart')
export const add = (magazineId, quantity = 1) => http.post('/cart', { magazine_id: magazineId, quantity })
export const update = (magazineId, quantity) => http.put(`/cart/${magazineId}`, { quantity })
export const remove = (magazineId) => http.delete(`/cart/${magazineId}`)
export const clear = () => http.delete('/cart')
