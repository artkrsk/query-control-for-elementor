import { BaseQueryControlView, BaseQueryControlViewStatic } from '../core'

/**
 * Creates a new control view by extending Elementor's Select2 control
 *
 * Combines the base query control with custom view methods and static properties
 * to create a complete control view class suitable for registration with Elementor.
 *
 * @param view - Custom view methods to merge with base control
 * @param staticView - Custom static properties to merge with base static properties
 * @returns Extended control view class
 */
export const createControlView = (
  view: Record<string, unknown> = {},
  staticView: Record<string, unknown> = {}
): unknown => {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const controls = window.elementor?.modules?.controls as any
  const Select2 = controls?.Select2

  if (!Select2) {
    console.warn('Elementor Select2 control not found')
    return null
  }

  return Select2.extend(
    {
      ...BaseQueryControlView,
      ...view
    },
    { ...BaseQueryControlViewStatic, ...staticView }
  )
}

/**
 * Registers a custom control view with Elementor
 *
 * Creates and registers a new control view with the specified name and properties,
 * extending the base query control functionality.
 *
 * @param name - The name of the control to register
 * @param view - Custom view methods to merge with base control
 * @param staticView - Custom static properties to merge with base static properties
 * @returns True if registration was successful, false otherwise
 */
export const registerControlView = (
  name: string,
  view: Record<string, unknown> = {},
  staticView: Record<string, unknown> = {}
): boolean => {
  if (!name || typeof name !== 'string') {
    return false
  }

  if (view && typeof view !== 'object') {
    return false
  }

  if (staticView && typeof staticView !== 'object') {
    return false
  }

  if (typeof window.elementor?.addControlView !== 'function') {
    return false
  }

  const controlView = createControlView(view, staticView)

  if (!controlView) {
    return false
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  window.elementor.addControlView(name, controlView as any)

  return true
}
