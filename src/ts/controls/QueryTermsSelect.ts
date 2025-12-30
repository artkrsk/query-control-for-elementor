import { registerControlView } from '../utils'
import { CONTROL_NAMES } from '../constants'

/** Terms Select control view extensions (empty - uses base) */
const QueryTermsSelectView = {}

/**
 * Registers the Terms Select control with Elementor
 *
 * Creates and registers the control with Elementor's control system
 * so it can be used in widgets and sections.
 *
 * @returns True if the control was registered successfully
 */
export const registerQueryTermsSelect = (): boolean => {
  return registerControlView(CONTROL_NAMES.TERMS_SELECT, QueryTermsSelectView)
}
