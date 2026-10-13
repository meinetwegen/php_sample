import { http } from './http'

export const stats = () => http.get('/admin/stats')

export const magazines = (params = {}) => http.get('/admin/magazines', { params })
export const createMagazine = (payload) => http.post('/admin/magazines', payload)
export const updateMagazine = (id, payload) => http.put(`/admin/magazines/${id}`, payload)
export const deleteMagazine = (id) => http.delete(`/admin/magazines/${id}`)

export const createCategory = (payload) => http.post('/admin/categories', payload)
export const updateCategory = (id, payload) => http.put(`/admin/categories/${id}`, payload)
export const deleteCategory = (id) => http.delete(`/admin/categories/${id}`)

export const orders = (params = {}) => http.get('/admin/orders', { params })
export const setOrderStatus = (id, status) => http.patch(`/admin/orders/${id}/status`, { status })
