/**
 * Normalizes IDs to an array
 *
 * Ensures that whatever value is passed (single ID, array of IDs, or falsy value)
 * is consistently returned as an array of IDs.
 *
 * @param ids - The IDs to normalize, could be a single ID or an array
 * @returns Array of IDs
 */
export const normalizeIds = (ids: unknown): (string | number)[] => {
  if (!ids) {
    return []
  }

  if (!Array.isArray(ids)) {
    return [ids as string | number]
  }

  return ids as (string | number)[]
}
