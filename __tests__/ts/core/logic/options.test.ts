import { describe, it, expect } from 'vitest'
import {
  shouldUpdateOptions,
  mergeOptions,
  filterOptionsByIds,
  getOptionIds
} from '../../../../src/ts/core/logic/options'

describe('options', () => {
  describe('shouldUpdateOptions', () => {
    it('returns false when both options are null', () => {
      expect(shouldUpdateOptions(null, null)).toBe(false)
    })

    it('returns true when currentOptions is null and newOptions is not', () => {
      expect(shouldUpdateOptions(null, { '1': 'Post 1' })).toBe(true)
    })

    it('returns true when newOptions is null and currentOptions is not', () => {
      expect(shouldUpdateOptions({ '1': 'Post 1' }, null)).toBe(true)
    })

    it('returns false when options are identical', () => {
      const options = { '1': 'Post 1', '2': 'Post 2' }
      expect(shouldUpdateOptions(options, { ...options })).toBe(false)
    })

    it('returns true when options have different values', () => {
      const current = { '1': 'Post 1' }
      const updated = { '1': 'Updated Post 1' }
      expect(shouldUpdateOptions(current, updated)).toBe(true)
    })

    it('returns true when options have different keys', () => {
      const current = { '1': 'Post 1' }
      const updated = { '2': 'Post 2' }
      expect(shouldUpdateOptions(current, updated)).toBe(true)
    })

    it('returns true when new options have additional keys', () => {
      const current = { '1': 'Post 1' }
      const updated = { '1': 'Post 1', '2': 'Post 2' }
      expect(shouldUpdateOptions(current, updated)).toBe(true)
    })

    it('returns false for empty objects (both empty)', () => {
      expect(shouldUpdateOptions({}, {})).toBe(false)
    })
  })

  describe('mergeOptions', () => {
    it('returns empty object when both are null', () => {
      expect(mergeOptions(null, null)).toEqual({})
    })

    it('returns newOptions when currentOptions is null', () => {
      const newOptions = { '1': 'Post 1' }
      expect(mergeOptions(null, newOptions)).toEqual(newOptions)
    })

    it('returns currentOptions when newOptions is null', () => {
      const currentOptions = { '1': 'Post 1' }
      expect(mergeOptions(currentOptions, null)).toEqual(currentOptions)
    })

    it('merges both options with newOptions taking precedence', () => {
      const current = { '1': 'Old', '2': 'Post 2' }
      const updated = { '1': 'New', '3': 'Post 3' }
      expect(mergeOptions(current, updated)).toEqual({
        '1': 'New',
        '2': 'Post 2',
        '3': 'Post 3'
      })
    })

    it('does not mutate original objects', () => {
      const current = { '1': 'Post 1' }
      const updated = { '2': 'Post 2' }
      const originalCurrent = { ...current }
      const originalUpdated = { ...updated }
      mergeOptions(current, updated)
      expect(current).toEqual(originalCurrent)
      expect(updated).toEqual(originalUpdated)
    })
  })

  describe('filterOptionsByIds', () => {
    it('returns empty object when options is null', () => {
      expect(filterOptionsByIds(null, [1, 2])).toEqual({})
    })

    it('returns empty object when ids array is empty', () => {
      const options = { '1': 'Post 1', '2': 'Post 2' }
      expect(filterOptionsByIds(options, [])).toEqual({})
    })

    it('filters options to only include specified ids', () => {
      const options = { '1': 'Post 1', '2': 'Post 2', '3': 'Post 3' }
      expect(filterOptionsByIds(options, [1, 3])).toEqual({
        '1': 'Post 1',
        '3': 'Post 3'
      })
    })

    it('handles string ids', () => {
      const options = { '1': 'Post 1', '2': 'Post 2' }
      expect(filterOptionsByIds(options, ['1', '2'])).toEqual({
        '1': 'Post 1',
        '2': 'Post 2'
      })
    })

    it('ignores ids that do not exist in options', () => {
      const options = { '1': 'Post 1' }
      expect(filterOptionsByIds(options, [1, 2, 3])).toEqual({
        '1': 'Post 1'
      })
    })

    it('returns empty object when no ids match', () => {
      const options = { '1': 'Post 1' }
      expect(filterOptionsByIds(options, [99, 100])).toEqual({})
    })
  })

  describe('getOptionIds', () => {
    it('returns array of keys from options', () => {
      const options = { '1': 'Post 1', '2': 'Post 2', '10': 'Post 10' }
      expect(getOptionIds(options)).toEqual(['1', '2', '10'])
    })

    it('returns empty array when options is null', () => {
      expect(getOptionIds(null)).toEqual([])
    })

    it('returns empty array when options is empty', () => {
      expect(getOptionIds({})).toEqual([])
    })

    it('returns keys as strings', () => {
      const options = { '1': 'Post 1' }
      const result = getOptionIds(options)
      expect(result).toEqual(['1'])
      expect(typeof result[0]).toBe('string')
    })
  })
})
