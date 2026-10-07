import { cartApi } from '@/plugins/api'

const empty = () => ({ items: [], count: 0, subtotal_cents: 0, shipping_cents: 0, total_cents: 0 })

export default {
  namespaced: true,

  state: () => ({ cart: empty() }),

  getters: {
    count: (state) => state.cart.count,
  },

  mutations: {
    SET_CART(state, cart) {
      state.cart = cart
    },
    RESET(state) {
      state.cart = empty()
    },
  },

  actions: {
    async fetch({ commit }) {
      commit('SET_CART', (await cartApi.get()).data.data)
    },
    async add({ commit }, { magazineId, quantity = 1 }) {
      commit('SET_CART', (await cartApi.add(magazineId, quantity)).data.data)
    },
    async update({ commit }, { magazineId, quantity }) {
      commit('SET_CART', (await cartApi.update(magazineId, quantity)).data.data)
    },
    async remove({ commit }, magazineId) {
      commit('SET_CART', (await cartApi.remove(magazineId)).data.data)
    },
    async clear({ commit }) {
      commit('SET_CART', (await cartApi.clear()).data.data)
    },
  },
}
