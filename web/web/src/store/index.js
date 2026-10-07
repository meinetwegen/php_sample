import { createStore } from 'vuex'
import auth from './auth'
import cart from './cart'

const store = createStore({ strict: process.env.NODE_ENV !== 'production' })

store.registerModule('auth', auth)
store.registerModule('cart', cart)
store.subscribe(({ type, payload }) => type === 'auth/SET_USER' && !payload && store.commit('cart/RESET'))

export default store
