import { vi } from 'vitest'

/**
 * Mock for window.elementor global
 */
export const createElementorMock = () => ({
  modules: {
    controls: {
      Select2: {
        extend: vi.fn((view, staticView) => ({
          ...view,
          ...staticView,
          __extended: true
        }))
      }
    }
  },
  addControlView: vi.fn()
})

/**
 * Reset and setup Elementor mocks before each test
 */
export const setupElementorMocks = () => {
  const elementorMock = createElementorMock()
  ;(window as Record<string, unknown>).elementor = elementorMock
  return elementorMock
}

/**
 * Cleanup Elementor mocks after tests
 */
export const cleanupElementorMocks = () => {
  delete (window as Record<string, unknown>).elementor
}
