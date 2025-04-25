import { BaseQueryControlView, BaseQueryControlViewStatic } from '../base'

/**
 * Creates a new control view by extending Elementor's Select2 control
 *
 * Combines the base query control with custom view methods and static properties
 * to create a complete control view class suitable for registration with Elementor.
 *
 * @param {Object} view - Custom view methods to merge with base control
 * @param {Object} staticView - Custom static properties to merge with base static properties
 *
 * @returns {Object} - Extended control view class
 */
const createControlView = (view, staticView) => {
  return window.elementor.modules.controls.Select2.extend(
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
 * @param {string} name - The name of the control to register
 * @param {Object} view - Custom view methods to merge with base control
 * @param {Object} staticView - Custom static properties to merge with base static properties
 *
 * @returns {boolean} - True if registration was successful, false otherwise
 */
export function registerControlView(name, view = {}, staticView = {}) {
  if (!name || typeof name !== 'string') {
    return false
  }

  if (view && typeof view !== 'object') {
    return false
  }

  if (staticView && typeof staticView !== 'object') {
    return false
  }

  if (typeof window.elementor.addControlView !== 'function') {
    return false
  }

  const controlView = createControlView(view, staticView)

  window.elementor.addControlView(name, controlView)

  return true
}
