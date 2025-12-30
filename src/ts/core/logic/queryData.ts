import type { TQueryData } from '../../types'
import { isPlainObject } from './validation'

/**
 * Builds query data structure for AJAX autocomplete requests
 *
 * Combines model query settings with optional group post type filtering
 * to create the data structure expected by the server.
 *
 * @param modelQuery - Query object from the control model
 * @param groupPostType - Optional post type from group control
 * @returns Formatted query data for AJAX request
 */
export const buildQueryData = (
  modelQuery: Record<string, unknown> | null | undefined,
  groupPostType: string | undefined
): TQueryData => {
  const query: Record<string, unknown> = isPlainObject(modelQuery)
    ? { ...modelQuery }
    : {}

  if (groupPostType) {
    query['post_type'] = groupPostType
  }

  return {
    autocomplete: {
      query
    }
  }
}

/**
 * Extracts query object from a model
 *
 * Safely gets the query configuration from a model's attributes,
 * returning null if not available.
 *
 * @param model - Backbone model with get method
 * @returns Query object or null
 */
export const getQueryFromModel = (
  model: { get: (key: string) => unknown } | null | undefined
): Record<string, unknown> | null => {
  if (!model || typeof model.get !== 'function') {
    return null
  }

  const query = model.get('query')

  if (isPlainObject(query)) {
    return query
  }

  return null
}

/**
 * Extracts group post type from container settings
 *
 * Retrieves the group_posts_query_post_type setting from a container's
 * settings attributes, used for filtering queries.
 *
 * @param container - Container object with settings.attributes
 * @returns Post type string or undefined
 */
export const getGroupPostType = (
  container: { settings?: { attributes?: Record<string, unknown> } } | null | undefined
): string | undefined => {
  if (!container?.settings?.attributes) {
    return undefined
  }

  const postType = container.settings.attributes['group_posts_query_post_type']

  if (typeof postType === 'string') {
    return postType
  }

  return undefined
}
