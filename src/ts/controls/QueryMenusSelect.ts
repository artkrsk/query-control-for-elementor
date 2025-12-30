import { registerControlView } from '../utils'
import { CONTROL_NAMES } from '../constants'

/** Menus Select control view extensions (empty - uses base) */
const QueryMenusSelectView = {}

/**
 * Registers the Menus Select control with Elementor
 *
 * Creates and registers the control with Elementor's control system
 * so it can be used in widgets and sections.
 *
 * @returns True if the control was registered successfully
 */
export const registerQueryMenusSelect = (): boolean => {
  return registerControlView(CONTROL_NAMES.MENUS_SELECT, QueryMenusSelectView)
}
