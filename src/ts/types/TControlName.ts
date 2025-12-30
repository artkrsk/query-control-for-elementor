import type { CONTROL_NAMES } from '../constants'

/** Union type of all valid control name strings */
export type TControlName = (typeof CONTROL_NAMES)[keyof typeof CONTROL_NAMES]
