import { createStore } from 'vuex'
import auth from './auth'
const store = createStore({ strict: process.env.NODE_ENV !== 'production' })

store.registerModule('auth', auth)
export default store
