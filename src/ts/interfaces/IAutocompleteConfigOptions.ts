import type { ITransportConfig } from './ITransportConfig'

/** Extended configuration for autocomplete with Select2 options */
export interface IAutocompleteConfigOptions extends ITransportConfig {
  closeOnSelect?: boolean | undefined
}
