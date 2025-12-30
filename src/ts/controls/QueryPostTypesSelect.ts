import { registerControlView } from '../utils'
import { CONTROL_NAMES } from '../constants'

/** Post Types Select control view extensions (empty - uses base) */
const QueryPostTypesSelectView = {}

/**
 * Registers the Post Types Select control with Elementor
 *
 * Creates and registers the control with Elementor's control system
 * so it can be used in widgets and sections.
 *
 * @returns True if the control was registered successfully
 */
export const registerQueryPostTypesSelect = (): boolean => {
  return registerControlView(CONTROL_NAMES.POST_TYPES_SELECT, QueryPostTypesSelectView)
}
