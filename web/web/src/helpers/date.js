export function formatDate(value, locale = 'en') {
  if (!value) return ''
  return new Date(value).toLocaleDateString(locale, { year: 'numeric', month: 'short', day: 'numeric' })
}
