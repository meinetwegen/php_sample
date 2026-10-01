/** Formats integer cents as euros, e.g. 1299 -> "€12.99". */
export function formatMoney(cents, locale = 'en') {
  return new Intl.NumberFormat(locale, { style: 'currency', currency: 'EUR' }).format((cents || 0) / 100)
}

export const centsToEuros = (cents) => (cents || 0) / 100
export const eurosToCents = (euros) => Math.round(Number(euros || 0) * 100)
