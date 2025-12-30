import type { TSelectOptions, TQueryData } from '../types'

/**
 * Typed context interface for BaseQueryControlView mixin methods
 *
 * This interface represents the 'this' context in BaseQueryControlView methods.
 * It includes properties from Elementor's Select2 control plus our custom additions.
 */
export interface IQueryControlContext {
  /** AJAX action name for fetching data */
  action: string | null

  /** AJAX action name for autocomplete functionality */
  actionAutocomplete: string | null

  /** Current AJAX request promise */
  request: Promise<TSelectOptions> | null

  /** Flag indicating if an AJAX request is in progress */
  isPendingRequest: boolean

  /** Flag indicating if the control has been destroyed */
  isDestroyed: boolean

  /** DOM element reference */
  el: HTMLElement

  /** jQuery wrapped element */
  $el: JQuery

  /** Backbone model unique ID */
  cid: string

  /** UI elements map */
  ui: {
    select?: JQuery
  }

  /** Element container reference */
  container: {
    settings: {
      attributes: Record<string, unknown>
    }
  }

  /** Backbone model */
  model: {
    get(key: string): unknown
    set(key: string, value: unknown): void
  }

  /** Get current control value */
  getControlValue(): unknown

  /** Render the control */
  render(): this

  /** Update AJAX action from model */
  updateAction(): string | null

  /** Update autocomplete action from model */
  updateActionAutocomplete(): string | null

  /** Load data via AJAX */
  loadData(): Promise<TSelectOptions | null>

  /** Fetch data from server */
  fetchData(): Promise<TSelectOptions>

  /** Update available options */
  updateOptions(newOptions: TSelectOptions | null, render?: boolean): boolean

  /** Get control value by name */
  getControlValueByName(controlName: string): unknown

  /** Get query data for AJAX requests */
  getQueryData(): TQueryData

  /** Get normalized IDs array */
  getIDs(): (string | number)[]

  /** Setup sortable functionality */
  setupSortable(): void

  /** Get Select2 default options */
  getSelect2DefaultOptions(): Record<string, unknown>

  /** Get autocomplete configuration */
  getAutoCompletePosts(): Record<string, unknown>

  /** Set loading state */
  setLoading(toggle?: boolean, toggleDisable?: boolean): void
}
