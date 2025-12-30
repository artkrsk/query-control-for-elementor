import type { TSelectOptions } from '../types'

/** AJAX loadObjects configuration for Elementor */
export interface IElementorLoadObjectsConfig {
  action: string
  ids: (string | number)[]
  data: Record<string, unknown>
  before?: (() => void) | undefined
  success: (response: TSelectOptions) => void
  error: (error: unknown) => void
}
