import { createI18n } from 'vue-i18n'
import { DEFAULT_LOCALE } from '@/config'
import en from '@/assets/translations/en.json'
import ru from '@/assets/translations/ru.json'

export default createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || DEFAULT_LOCALE,
  fallbackLocale: 'en',
  messages: { en, ru },
})
