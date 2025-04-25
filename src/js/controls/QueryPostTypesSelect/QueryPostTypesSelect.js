import { registerControlView } from '../../utils'

/**
 * Base implementation for Post Types Select control
 */
const BaseQueryControlPostTypeView = {}

/**
 * Registers the Post Types Select control with Elementor
 *
 * Creates and registers the control with Elementor's control system
 * so it can be used in widgets and sections.
 *
 * @returns {boolean} True if the control was registered successfully, false otherwise
 */
export const register = () => {
  return registerControlView(
    'arts-query-control-for-elementor-post-types-select',
    BaseQueryControlPostTypeView
  )
}
