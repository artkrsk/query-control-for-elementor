import { fetchElementor } from './ajax'

/**
 * Creates a transport function for the Select2 AJAX autocomplete
 *
 * This function returns a method that Select2 can use as its AJAX transport,
 * handling the communication with the server and returning properly
 * formatted results.
 *
 * @param {string} action - The AJAX action to perform
 * @param {Object} config - Configuration object
 * @param {Function} [config.before] - Function to call before the request
 * @param {Function} [config.after] - Function to call after the request
 * @param {Object|Function} [config.queryData] - Additional data to send with the request
 *
 * @returns {Function} - Transport function for Select2
 */
export const createTransport = (action, config) => {
  const { before = null, after = null, queryData = {} } = config

  return (params, success, failure) => {
    const data = typeof queryData === 'function' ? queryData() : queryData

    if (typeof data === 'object') {
      // Add search term
      Object.assign(data, {
        search: params.data.q
      })
    }

    // Make the request to Elementor
    return fetchElementor(action, {
      data,
      before,
      after,
      onSuccess: success,
      onError: failure
    })
  }
}

/**
 * Returns autocomplete configuration for Select2
 *
 * Generates a configuration object for Select2 with AJAX autocomplete
 * enabled, using the specified action and context.
 *
 * @param {string} action - The AJAX action to perform
 * @param {Object} config - Configuration object
 * @param {Object|Function} [config.queryData] - Additional data to send with the request
 * @param {Function} [config.before] - Function to call before the request
 * @param {Function} [config.after] - Function to call after the request
 * @param {boolean} [config.closeOnSelect] - Whether to close the dropdown on selection
 *
 * @returns {Object} - Autocomplete configuration
 */
export const getAutocompleteConfig = (action, config) => {
  const { closeOnSelect = false, before = null, after = null, queryData = {} } = config

  return {
    ajax: {
      transport: createTransport(action, {
        queryData,
        before,
        after
      }),
      cache: true
    },
    closeOnSelect,
    dropdownCssClass: 'arts-query-control-for-elementor-dropdown'
  }
}
