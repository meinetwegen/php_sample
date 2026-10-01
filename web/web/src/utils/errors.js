/** Human readable message from an axios error. */
export function errorMessage(error) {
  return error?.response?.data?.message || error?.message || 'Unexpected error'
}

/** Laravel validation errors -> { field: 'first message' }. */
export function fieldErrors(error) {
  const errors = error?.response?.data?.errors || {}
  return Object.fromEntries(Object.entries(errors).map(([field, messages]) => [field, messages[0]]))
}
