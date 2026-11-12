import type {
  ElementorEditor,
  ElementorModules,
  ElementorCommon,
  $e as ElementorCommands
} from '@artemsemkin/elementor-types'

declare global {
  interface Window {
    $e?: ElementorCommands
    elementor?: ElementorEditor
    elementorCommon?: ElementorCommon
    elementorModules?: ElementorModules
  }
}

export {}
