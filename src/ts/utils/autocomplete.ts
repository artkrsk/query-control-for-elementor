import { fetchElementor } from './ajax'
import type { TAutocompleteConfig } from '../types'
import type { ITransportConfig, IAutocompleteConfigOptions } from '../interfaces'

/**
 * Creates a transport function for the Select2 AJAX autocomplete
 *
 * This function returns a method that Select2 can use as its AJAX transport,
 * handling the communication with the server and returning properly
 * formatted results.
 *
 * @param action - The AJAX action to perform
 * @param config - Configuration object
 * @returns Transport function for Select2
 */
export const createTransport = (action: string, config: ITransportConfig) => {
  const { before, after, queryData = {} } = config

  return (
    params: { data: { q: string } },
    success: (response: unknown) => void,
    failure: (error: unknown) => void
  ): Promise<unknown> => {
    const data = typeof queryData === 'function' ? queryData() : queryData

    if (typeof data === 'object' && data !== null) {
      Object.assign(data, {
        search: params.data.q
      })
    }

    return fetchElementor(action, {
      data: data as Record<string, unknown>,
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
 * @param action - The AJAX action to perform
 * @param config - Configuration object
 * @returns Autocomplete configuration
 */
export const getAutocompleteConfig = (
  action: string,
  config: IAutocompleteConfigOptions
): TAutocompleteConfig => {
  const {
    closeOnSelect = false,
    before,
    after,
    queryData = { autocomplete: { query: {} } }
  } = config

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
