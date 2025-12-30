import type { ILoadObjectsConfig, IFetchConfig, IElementorLoadObjectsConfig } from '../interfaces'
import type { TSelectOptions } from '../types'

/**
 * Load objects via Elementor Common AJAX
 *
 * Wraps the elementorCommon.ajax.loadObjects method in a Promise,
 * making it easier to use with async/await and providing better error handling.
 *
 * @param action - The AJAX action to perform
 * @param config - Configuration object
 * @returns Promise resolving with the AJAX response
 */
export const loadObjectsElementor = (
  action: string,
  config: ILoadObjectsConfig = {}
): Promise<TSelectOptions> => {
  return new Promise((resolve, reject) => {
    const { ids = [], data = {}, before, onSuccess, onError } = config

    if (!action) {
      reject(new Error('Action is required'))
      return
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const ajax = (window.elementorCommon as any)?.ajax as
      | {
          loadObjects: (config: IElementorLoadObjectsConfig) => void
        }
      | undefined

    ajax?.loadObjects({
      action,
      ids,
      data,
      before,
      success: async (response: TSelectOptions) => {
        if (typeof onSuccess === 'function') {
          await onSuccess(response)
        }
        resolve(response)
      },
      error: async (error: unknown) => {
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
 * @param action - The AJAX action name
 * @param config - Configuration object
 * @returns Promise resolving with the AJAX response
 */
export const fetchElementor = <T = unknown>(
  action: string,
  config: IFetchConfig = {}
): Promise<T> => {
  return new Promise((resolve, reject) => {
    const { data = {}, before, after, onSuccess, onError } = config

    if (!action) {
      reject(new Error('Action is required'))
      return
    }

    if (typeof before === 'function') {
      before()
    }

    window.elementor?.ajax?.addRequest(action, {
      data,
      success: async (response: T) => {
        if (typeof onSuccess === 'function') {
          await onSuccess(response)
        }
        if (typeof after === 'function') {
          await after(response)
        }
        resolve(response)
      },
      error: async (error: unknown) => {
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
