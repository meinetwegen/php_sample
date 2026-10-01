import { http } from './http'

export const login = (payload) => http.post('/auth/login', payload)
export const register = (payload) => http.post('/auth/register', payload)
export const refresh = () => http.post('/auth/refresh')
export const logout = () => http.post('/auth/logout')
export const me = () => http.get('/auth/me')
export const updateProfile = (payload) => http.put('/auth/me', payload)
export const changePassword = (payload) => http.put('/auth/password', payload)
