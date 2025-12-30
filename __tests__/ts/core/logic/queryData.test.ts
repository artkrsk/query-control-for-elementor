import { describe, it, expect } from 'vitest'
import {
  buildQueryData,
  getQueryFromModel,
  getGroupPostType
} from '../../../../src/ts/core/logic/queryData'

describe('queryData', () => {
  describe('buildQueryData', () => {
    it('returns empty query when modelQuery is null', () => {
      const result = buildQueryData(null, undefined)
      expect(result).toEqual({
        autocomplete: {
          query: {}
        }
      })
    })

    it('returns empty query when modelQuery is undefined', () => {
      const result = buildQueryData(undefined, undefined)
      expect(result).toEqual({
        autocomplete: {
          query: {}
        }
      })
    })

    it('copies modelQuery properties into query', () => {
      const modelQuery = { post_type: 'post', taxonomy: 'category' }
      const result = buildQueryData(modelQuery, undefined)
      expect(result).toEqual({
        autocomplete: {
          query: { post_type: 'post', taxonomy: 'category' }
        }
      })
    })

    it('adds groupPostType to query when provided', () => {
      const result = buildQueryData(null, 'custom_post_type')
      expect(result).toEqual({
        autocomplete: {
          query: { post_type: 'custom_post_type' }
        }
      })
    })

    it('overrides modelQuery post_type with groupPostType', () => {
      const modelQuery = { post_type: 'post' }
      const result = buildQueryData(modelQuery, 'page')
      expect(result).toEqual({
        autocomplete: {
          query: { post_type: 'page' }
        }
      })
    })

    it('returns empty query when modelQuery is an array (not plain object)', () => {
      const result = buildQueryData(['not', 'an', 'object'] as unknown as Record<string, unknown>, undefined)
      expect(result).toEqual({
        autocomplete: {
          query: {}
        }
      })
    })

    it('does not mutate the original modelQuery', () => {
      const modelQuery = { post_type: 'post' }
      const original = { ...modelQuery }
      buildQueryData(modelQuery, 'page')
      expect(modelQuery).toEqual(original)
    })
  })

  describe('getQueryFromModel', () => {
    it('returns query object when model has valid query', () => {
      const model = {
        get: (key: string) => {
          if (key === 'query') {
            return { post_type: 'post' }
          }
          return undefined
        }
      }
      const result = getQueryFromModel(model)
      expect(result).toEqual({ post_type: 'post' })
    })

    it('returns null when model is null', () => {
      expect(getQueryFromModel(null)).toBe(null)
    })

    it('returns null when model is undefined', () => {
      expect(getQueryFromModel(undefined)).toBe(null)
    })

    it('returns null when model.get is not a function', () => {
      const model = { get: 'not a function' } as unknown as { get: (key: string) => unknown }
      expect(getQueryFromModel(model)).toBe(null)
    })

    it('returns null when query is an array', () => {
      const model = {
        get: (key: string) => {
          if (key === 'query') {
            return ['array', 'not', 'object']
          }
          return undefined
        }
      }
      expect(getQueryFromModel(model)).toBe(null)
    })

    it('returns null when query is a primitive', () => {
      const model = {
        get: (key: string) => {
          if (key === 'query') {
            return 'string'
          }
          return undefined
        }
      }
      expect(getQueryFromModel(model)).toBe(null)
    })

    it('returns null when query is null', () => {
      const model = {
        get: () => null
      }
      expect(getQueryFromModel(model)).toBe(null)
    })
  })

  describe('getGroupPostType', () => {
    it('returns post type when container has valid attributes', () => {
      const container = {
        settings: {
          attributes: {
            group_posts_query_post_type: 'custom_type'
          }
        }
      }
      expect(getGroupPostType(container)).toBe('custom_type')
    })

    it('returns undefined when container is null', () => {
      expect(getGroupPostType(null)).toBeUndefined()
    })

    it('returns undefined when container is undefined', () => {
      expect(getGroupPostType(undefined)).toBeUndefined()
    })

    it('returns undefined when settings is missing', () => {
      const container = {} as { settings?: { attributes?: Record<string, unknown> } }
      expect(getGroupPostType(container)).toBeUndefined()
    })

    it('returns undefined when attributes is missing', () => {
      const container = { settings: {} }
      expect(getGroupPostType(container)).toBeUndefined()
    })

    it('returns undefined when post_type is not a string', () => {
      const container = {
        settings: {
          attributes: {
            group_posts_query_post_type: 123
          }
        }
      }
      expect(getGroupPostType(container)).toBeUndefined()
    })

    it('returns undefined when post_type key is missing', () => {
      const container = {
        settings: {
          attributes: {
            other_key: 'value'
          }
        }
      }
      expect(getGroupPostType(container)).toBeUndefined()
    })
  })
})
