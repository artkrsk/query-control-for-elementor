/**
 * Load objects via Elementor Common AJAX
 *
 * Wraps the elementorCommon.ajax.loadObjects method in a Promise,
 * making it easier to use with async/await and providing better error handling.
 *
 * @param {string} action - The AJAX action to perform
 * @param {Object} config - Configuration object
 * @param {Array} [config.ids] - The IDs to load
 * @param {Object} [config.data] - Additional data for the request
 * @param {Function} [config.before] - Function to call before the request
 * @param {Function} [config.onSuccess] - Function to call on success
 * @param {Function} [config.onError] - Function to call on error
 *
 * @returns {Promise<Object>} - Promise resolving with the AJAX response
 */
export const loadObjectsElementor = (action, config = {}) => {
  return new Promise((resolve, reject) => {
    const { ids = [], data = {}, before = null, onSuccess = null, onError = null } = config

    if (!action) {
      reject(new Error('Action is required'))
    }

    window.elementorCommon.ajax.loadObjects({
      action,
      ids,
      data,
      before,
      success: async (response) => {
        if (typeof onSuccess === 'function') {
          await onSuccess(response)
        }

        resolve(response)
      },
      error: async (error) => {
        if (typeof onError === 'function') {
          await onError(error)
        }

        reject(error)
      }
    })
  })
}

/**
 * Make a direct Elementor AJAX request
 *
 * Wraps the elementor.ajax.addRequest method in a Promise,
 * making it easier to use with async/await and providing better error handling.
 *
 * @param {string} action - The AJAX action name
 * @param {Object} config - Configuration object
 * @param {Object} [config.data] - Data to send with the request
 * @param {Function} [config.before] - Function to call before the request
 * @param {Function} [config.after] - Function to call after the request
 * @param {Function} [config.onSuccess] - Function to call on success
 * @param {Function} [config.onError] - Function to call on error
 *
 * @returns {Promise<Object>} - Promise resolving with the AJAX response
 */
export const fetchElementor = (action, config = {}) => {
  return new Promise((resolve, reject) => {
    const { data = {}, before = null, after = null, onSuccess = null, onError = null } = config

    if (!action) {
      reject(new Error('Action is required'))
    }

    if (typeof before === 'function') {
      before()
    }

    window.elementor.ajax.addRequest(action, {
      data,
      success: async (response) => {
        if (typeof onSuccess === 'function') {
          await onSuccess(response)
        }

        if (typeof after === 'function') {
          await after(response)
        }

        resolve(response)
      },
      error: async (error) => {
        if (typeof onError === 'function') {
          await onError(error)
        }

        if (typeof after === 'function') {
          await after(error)
        }

        reject(error)
      }
    })
  })
}
