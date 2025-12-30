import type { TSelectOptions } from '../../types'

/**
 * Checks if options should be updated based on comparison
 *
 * Compares two option objects to determine if an update is needed.
 * Returns true if the options are different.
 *
 * @param currentOptions - Current options object
 * @param newOptions - New options object to compare
 * @returns True if options should be updated
 */
export const shouldUpdateOptions = (
  currentOptions: TSelectOptions | null,
  newOptions: TSelectOptions | null
): boolean => {
  // If both are null/undefined, no update needed
  if (!currentOptions && !newOptions) {
    return false
  }

  // If one is null and other isn't, update is needed
  if (!currentOptions || !newOptions) {
    return true
  }

  // Compare serialized versions for deep equality
  return JSON.stringify(currentOptions) !== JSON.stringify(newOptions)
}

/**
 * Merges new options into existing options
 *
 * Creates a new object combining existing and new options,
 * with new options taking precedence.
 *
 * @param currentOptions - Current options object
 * @param newOptions - New options to merge
 * @returns Merged options object
 */
export const mergeOptions = (
  currentOptions: TSelectOptions | null,
  newOptions: TSelectOptions | null
): TSelectOptions => {
  return {
    ...(currentOptions || {}),
    ...(newOptions || {})
  }
}

/**
 * Filters options to only include specified IDs
 *
 * Creates a new options object containing only the key-value pairs
 * where the key is present in the ids array.
 *
 * @param options - Options object to filter
 * @param ids - Array of IDs to keep
 * @returns Filtered options object
 */
export const filterOptionsByIds = (
  options: TSelectOptions | null,
  ids: (string | number)[]
): TSelectOptions => {
  if (!options || !ids.length) {
    return {}
  }

  const result: TSelectOptions = {}

  ids.forEach((id) => {
    const key = String(id)
    if (key in options) {
      result[key] = options[key]!
    }
  })

  return result
}

/**
 * Extracts IDs array from options object
 *
 * Returns all keys from the options object as an array.
 *
 * @param options - Options object
 * @returns Array of option IDs
 */
export const getOptionIds = (options: TSelectOptions | null): string[] => {
  if (!options) {
    return []
  }

  return Object.keys(options)
}
