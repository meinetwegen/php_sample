import { http } from './http'

const clean = (params) => Object.fromEntries(Object.entries(params).filter(([, v]) => v !== '' && v != null))

export const categories = () => http.get('/categories')
export const magazines = (params = {}) => http.get('/magazines', { params: clean(params) })
export const magazine = (idOrSlug) => http.get(`/magazines/${idOrSlug}`)
