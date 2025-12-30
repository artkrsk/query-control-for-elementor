import { registerAllControls } from './controls'

// Ensure global types are loaded
import './global.d.ts'

/**
 * Register all query controls when Elementor editor is initialized
 *
 * The 'elementor/init' event is dispatched by Elementor when the editor
 * is ready to accept control registrations.
 */
window.addEventListener('elementor/init', () => {
  registerAllControls()
})

// Export types and utilities for external use
export * from './types'
export * from './interfaces'
export * from './constants'
export * from './utils'
export * from './core'
export * from './controls'
