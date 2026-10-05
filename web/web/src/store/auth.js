import { authApi } from '@/plugins/api'
import { clearToken, getToken, setToken } from '@/helpers/token'

export default {
  namespaced: true,

  state: () => ({ token: getToken(), user: null }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  mutations: {
    SET_TOKEN(state, token) {
      state.token = token
      if (token) setToken(token)
      else clearToken()
    },
    SET_USER(state, user) {
      state.user = user
    },
  },

  actions: {
    async login({ commit }, credentials) {
      const { data } = await authApi.login(credentials)
      commit('SET_TOKEN', data.access_token)
      commit('SET_USER', data.user)
    },

    async register({ commit }, payload) {
      const { data } = await authApi.register(payload)
      commit('SET_TOKEN', data.access_token)
      commit('SET_USER', data.user)
    },

    /** Loads the profile of the stored token; drops the session if the token is no longer valid. */
    async fetchUser({ commit, dispatch, state }) {
      if (!state.token) return
      try {
        const { data } = await authApi.me()
        commit('SET_USER', data.data)
      } catch (error) {
        dispatch('clear')
      }
    },

    async logout({ dispatch }) {
      try {
        await authApi.logout()
      } catch (error) {
        // the token may already be invalid, the local session is cleared anyway
      }
      dispatch('clear')
    },

    clear({ commit }) {
      commit('SET_TOKEN', null)
      commit('SET_USER', null)
    },
  },
}
