/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { setupElementorMocks, cleanupElementorMocks } from '../../setup'
import { CONTROL_NAMES } from '../../../src/ts/constants'

describe('controls registration', () => {
  let elementorMock: ReturnType<typeof setupElementorMocks>

  beforeEach(() => {
    vi.resetModules()
    elementorMock = setupElementorMocks()
  })

  afterEach(() => {
    cleanupElementorMocks()
  })

  describe('registerQueryPostsSelect', () => {
    it('registers control with correct name', async () => {
      const { registerQueryPostsSelect } = await import('../../../src/ts/controls/QueryPostsSelect')

      const result = registerQueryPostsSelect()

      expect(result).toBe(true)
      expect(elementorMock.addControlView).toHaveBeenCalledWith(
        CONTROL_NAMES.POSTS_SELECT,
        expect.objectContaining({ __extended: true })
      )
    })
  })

  describe('registerQueryTermsSelect', () => {
    it('registers control with correct name', async () => {
      const { registerQueryTermsSelect } = await import('../../../src/ts/controls/QueryTermsSelect')

      const result = registerQueryTermsSelect()

      expect(result).toBe(true)
      expect(elementorMock.addControlView).toHaveBeenCalledWith(
        CONTROL_NAMES.TERMS_SELECT,
        expect.objectContaining({ __extended: true })
      )
    })
  })

  describe('registerQueryPostTypesSelect', () => {
    it('registers control with correct name', async () => {
      const { registerQueryPostTypesSelect } = await import('../../../src/ts/controls/QueryPostTypesSelect')

      const result = registerQueryPostTypesSelect()

      expect(result).toBe(true)
      expect(elementorMock.addControlView).toHaveBeenCalledWith(
        CONTROL_NAMES.POST_TYPES_SELECT,
        expect.objectContaining({ __extended: true })
      )
    })
  })

  describe('registerQueryMenusSelect', () => {
    it('registers control with correct name', async () => {
      const { registerQueryMenusSelect } = await import('../../../src/ts/controls/QueryMenusSelect')

      const result = registerQueryMenusSelect()

      expect(result).toBe(true)
      expect(elementorMock.addControlView).toHaveBeenCalledWith(
        CONTROL_NAMES.MENUS_SELECT,
        expect.objectContaining({ __extended: true })
      )
    })
  })

  describe('when Elementor is not available', () => {
    it('returns false when window.elementor is undefined', async () => {
      cleanupElementorMocks()

      const { registerQueryPostsSelect } = await import('../../../src/ts/controls/QueryPostsSelect')

      const result = registerQueryPostsSelect()

      expect(result).toBe(false)
    })

    it('returns false when addControlView is not a function', async () => {
      ;(window as unknown as Record<string, unknown>).elementor = {
        modules: { controls: { Select2: { extend: vi.fn(() => ({})) } } },
        addControlView: 'not a function'
      }

      const { registerQueryTermsSelect } = await import('../../../src/ts/controls/QueryTermsSelect')

      const result = registerQueryTermsSelect()

      expect(result).toBe(false)
    })
  })
})
