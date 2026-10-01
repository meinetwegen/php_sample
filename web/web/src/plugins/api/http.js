import axios from 'axios'
import { API_BASE_URL } from '@/config'
import { clearToken, getToken, setToken } from '@/helpers/token'

export const http = axios.create({
  baseURL: API_BASE_URL,
  headers: { Accept: 'application/json' },
})

http.interceptors.request.use((config) => {
  const token = getToken()
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

const SKIP_REFRESH = /^\/auth\/(login|register|refresh)/
let refreshing = null

// On 401 try to exchange the token once, then replay the request. If that fails the session is over.
http.interceptors.response.use(
  (response) => response,
  async (error) => {
    const { response, config } = error

    if (response?.status === 401 && config && !config._retried && !SKIP_REFRESH.test(config.url) && getToken()) {
      config._retried = true
      try {
        refreshing = refreshing || http.post('/auth/refresh').finally(() => { refreshing = null })
        const { data } = await refreshing
        setToken(data.access_token)
        return http(config)
      } catch (refreshError) {
        clearToken()
        window.dispatchEvent(new Event('auth:logout'))
      }
    }

    return Promise.reject(error)
  },
)
