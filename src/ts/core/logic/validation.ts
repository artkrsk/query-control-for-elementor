/** Type guard for plain objects (not null, not array) */
export const isPlainObject = (value: unknown): value is Record<string, unknown> => {
  return value !== null && typeof value === 'object' && !Array.isArray(value)
}

/**
 * Validates that an action string is valid
 *
 * Checks if the action is a non-empty string that can be used for AJAX requests.
 *
 * @param action - The action string to validate
 * @returns True if the action is valid
 */
export const validateAction = (action: unknown): action is string => {
  return typeof action === 'string' && action.length > 0
}

/**
 * Validates that a response is a valid options object
 *
 * Checks if the response is a non-null object that can be used as options.
 *
 * @param response - The response to validate
 * @returns True if the response is a valid options object
 */
export const isValidResponse = (response: unknown): response is Record<string, string> => {
  return isPlainObject(response)
}

/**
 * Checks if a control has been destroyed
 *
 * Determines if a control is in a destroyed state and should not process further.
 *
 * @param isDestroyed - The destroyed flag
 * @param request - The current request promise
 * @returns True if the control should not proceed
 */
export const shouldSkipProcessing = (
  isDestroyed: boolean,
  request: Promise<unknown> | null
): boolean => {
  return isDestroyed || request !== null
}

/**
 * Validates that IDs array is non-empty and valid
 *
 * @param ids - Array of IDs to validate
 * @returns True if the IDs array is valid and non-empty
 */
export const hasValidIds = (ids: unknown): ids is (string | number)[] => {
  return Array.isArray(ids) && ids.length > 0
}
