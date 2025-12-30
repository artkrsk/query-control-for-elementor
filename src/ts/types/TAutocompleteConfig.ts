/**
 * Configuration for autocomplete functionality
 */
export type TAutocompleteConfig = {
  ajax: {
    transport: (
      params: { data: { q: string } },
      success: (response: unknown) => void,
      failure: (error: unknown) => void
    ) => Promise<unknown>
    cache: boolean
  }
  closeOnSelect: boolean
  dropdownCssClass: string
}
