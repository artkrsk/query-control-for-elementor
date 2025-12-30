import { describe, it, expect } from 'vitest'
import {
  isPlainObject,
  validateAction,
  isValidResponse,
  shouldSkipProcessing,
  hasValidIds
} from '../../../../src/ts/core/logic/validation'

describe('validation', () => {
  describe('isPlainObject', () => {
    it('returns true for plain objects', () => {
      expect(isPlainObject({})).toBe(true)
      expect(isPlainObject({ key: 'value' })).toBe(true)
      expect(isPlainObject({ nested: { obj: true } })).toBe(true)
    })

    it('returns false for null', () => {
      expect(isPlainObject(null)).toBe(false)
    })

    it('returns false for undefined', () => {
      expect(isPlainObject(undefined)).toBe(false)
    })

    it('returns false for arrays', () => {
      expect(isPlainObject([])).toBe(false)
      expect(isPlainObject([1, 2, 3])).toBe(false)
      expect(isPlainObject(['a', 'b'])).toBe(false)
    })

    it('returns false for primitives', () => {
      expect(isPlainObject('string')).toBe(false)
      expect(isPlainObject(123)).toBe(false)
      expect(isPlainObject(true)).toBe(false)
    })

    it('returns true for Date objects (they are objects)', () => {
      // Note: Date is technically an object, not a "plain" object
      // but our implementation treats it as such for simplicity
      expect(isPlainObject(new Date())).toBe(true)
    })
  })

  describe('validateAction', () => {
    it('returns true for non-empty strings', () => {
      expect(validateAction('action_name')).toBe(true)
      expect(validateAction('a')).toBe(true)
      expect(validateAction('arts_query_posts')).toBe(true)
    })

    it('returns false for empty string', () => {
      expect(validateAction('')).toBe(false)
    })

    it('returns false for null', () => {
      expect(validateAction(null)).toBe(false)
    })

    it('returns false for undefined', () => {
      expect(validateAction(undefined)).toBe(false)
    })

    it('returns false for non-string types', () => {
      expect(validateAction(123)).toBe(false)
      expect(validateAction({})).toBe(false)
      expect(validateAction([])).toBe(false)
      expect(validateAction(true)).toBe(false)
    })
  })

  describe('isValidResponse', () => {
    it('returns true for plain objects', () => {
      expect(isValidResponse({})).toBe(true)
      expect(isValidResponse({ id: 'title' })).toBe(true)
    })

    it('returns false for arrays', () => {
      expect(isValidResponse([])).toBe(false)
      expect(isValidResponse([1, 2, 3])).toBe(false)
    })

    it('returns false for null', () => {
      expect(isValidResponse(null)).toBe(false)
    })

    it('returns false for primitives', () => {
      expect(isValidResponse('string')).toBe(false)
      expect(isValidResponse(123)).toBe(false)
    })
  })

  describe('shouldSkipProcessing', () => {
    it('returns true when isDestroyed is true', () => {
      expect(shouldSkipProcessing(true, null)).toBe(true)
      expect(shouldSkipProcessing(true, Promise.resolve())).toBe(true)
    })

    it('returns true when request is not null', () => {
      expect(shouldSkipProcessing(false, Promise.resolve())).toBe(true)
    })

    it('returns false only when isDestroyed is false AND request is null', () => {
      expect(shouldSkipProcessing(false, null)).toBe(false)
    })

    it('returns true when both conditions are true', () => {
      expect(shouldSkipProcessing(true, Promise.resolve())).toBe(true)
    })
  })

  describe('hasValidIds', () => {
    it('returns true for non-empty arrays', () => {
      expect(hasValidIds([1])).toBe(true)
      expect(hasValidIds([1, 2, 3])).toBe(true)
      expect(hasValidIds(['a', 'b'])).toBe(true)
      expect(hasValidIds([1, 'mixed'])).toBe(true)
    })

    it('returns false for empty arrays', () => {
      expect(hasValidIds([])).toBe(false)
    })

    it('returns false for non-arrays', () => {
      expect(hasValidIds(null)).toBe(false)
      expect(hasValidIds(undefined)).toBe(false)
      expect(hasValidIds('string')).toBe(false)
      expect(hasValidIds(123)).toBe(false)
      expect(hasValidIds({})).toBe(false)
    })
  })
})
