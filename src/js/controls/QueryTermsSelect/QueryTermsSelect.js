import { registerControlView } from '../../utils'

/**
 * Base implementation for Terms Select control
 */
const BaseQueryControlTermsSelectView = {}

/**
 * Registers the Terms Select control with Elementor
 *
 * Creates and registers the control with Elementor's control system
 * so it can be used in widgets and sections.
 *
 * @returns {boolean} True if the control was registered successfully, false otherwise
 */
export const register = () => {
  return registerControlView(
    'arts-query-control-for-elementor-terms-select',
    BaseQueryControlTermsSelectView
  )
}
