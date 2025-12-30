/**
 * Query data structure for autocomplete requests
 */
export type TQueryData = {
  autocomplete: {
    query: Record<string, unknown>
  }
}
