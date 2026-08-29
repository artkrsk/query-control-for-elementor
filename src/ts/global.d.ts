import type { ElementorEditor, ElementorModules, ElementorCommon, $e } from '@artemsemkin/elementor-types'

declare global {
  interface Window {
    $e?: $e
    elementor?: ElementorEditor
    elementorCommon?: ElementorCommon
    elementorModules?: ElementorModules
  }
}

export {}
