import { describe, it, expect } from 'vitest'
import { normalizeIds } from '../../../src/ts/utils/data'

describe('data', () => {
  describe('normalizeIds', () => {
    it('returns the array as-is when input is an array', () => {
      expect(normalizeIds([1, 2, 3])).toEqual([1, 2, 3])
      expect(normalizeIds(['a', 'b'])).toEqual(['a', 'b'])
      expect(normalizeIds([1, 'mixed', 2])).toEqual([1, 'mixed', 2])
    })

    it('returns empty array when input is an empty array', () => {
      expect(normalizeIds([])).toEqual([])
    })

    it('wraps single value in an array', () => {
      expect(normalizeIds(1)).toEqual([1])
      expect(normalizeIds('single')).toEqual(['single'])
      expect(normalizeIds(42)).toEqual([42])
    })

    it('returns empty array for null', () => {
      expect(normalizeIds(null)).toEqual([])
    })

    it('returns empty array for undefined', () => {
      expect(normalizeIds(undefined)).toEqual([])
    })

    it('returns empty array for empty string', () => {
      expect(normalizeIds('')).toEqual([])
    })

    it('returns empty array for zero', () => {
      expect(normalizeIds(0)).toEqual([])
    })

    it('returns empty array for false', () => {
      expect(normalizeIds(false)).toEqual([])
    })

    it('wraps truthy non-array values', () => {
      expect(normalizeIds(123)).toEqual([123])
      expect(normalizeIds('string')).toEqual(['string'])
      expect(normalizeIds(true)).toEqual([true])
    })
  })
})
