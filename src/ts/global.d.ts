import type { ElementorMain, ElementorModules, ElementorCommon, $e } from '@arts/elementor-types'

declare global {
  interface Window {
    $e?: $e
    elementor?: ElementorMain
    elementorCommon?: ElementorCommon
    elementorModules?: ElementorModules
  }
}

export {}
