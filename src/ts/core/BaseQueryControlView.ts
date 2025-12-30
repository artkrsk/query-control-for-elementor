import type { IQueryControlContext } from '../interfaces'
import type { TSelectOptions, TQueryData } from '../types'
import {
  showControlSpinner,
  hideControlSpinner,
  normalizeIds,
  rearrangeSortable,
  attachSortable,
  getAutocompleteConfig,
  loadObjectsElementor
} from '../utils'
import { buildQueryData, getQueryFromModel, shouldUpdateOptions, validateAction } from './logic'

/**
 * Base Query Control View
 *
 * Provides the core functionality for all query-based Elementor controls.
 * This is an object literal that gets merged with Elementor's Select2 control
 * via Backbone's extend mechanism.
 */
export const BaseQueryControlView = {
  /** AJAX action name for fetching data */
  action: null as string | null,

  /** AJAX action name for autocomplete functionality */
  actionAutocomplete: null as string | null,

  /** Current AJAX request promise */
  request: null as Promise<TSelectOptions> | null,

  /** Flag indicating if an AJAX request is in progress */
  isPendingRequest: false,

  /** Flag indicating if the control has been destroyed */
  isDestroyed: false,

  /**
   * Renders the control and loads initial data
   * Called when Elementor initializes the control view
   */
  async onRender(this: IQueryControlContext): Promise<void> {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    ;(this.constructor as any).__super__.onRender.call(this)

    this.updateAction()
    this.updateActionAutocomplete()

    const data = await this.loadData()
    this.updateOptions(data)
    this.setupSortable()
  },

  /**
   * Cleanup when the control is destroyed
   * Sets the destroyed flag to prevent async operations from continuing
   */
  onDestroy(this: IQueryControlContext): void {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    ;(this.constructor as any).__super__.onDestroy.call(this)

    this.isDestroyed = true
  },

  /**
   * Loads data for the control via AJAX
   * Handles pending requests and loading states
   */
  async loadData(this: IQueryControlContext): Promise<TSelectOptions | null> {
    if (this.isDestroyed) {
      return null
    }

    if (!this.action) {
      return null
    }

    if (this.isPendingRequest && this.request) {
      this.setLoading(true)

      this.request.finally(() => {
        this.setLoading(false)
      })

      return this.request
    }

    this.isPendingRequest = true
    this.setLoading(true)
    const result = await this.fetchData()
    this.request = Promise.resolve(result)
    this.isPendingRequest = false
    this.setLoading(false)

    return result
  },

  /** Fetches data from the server */
  async fetchData(this: IQueryControlContext): Promise<TSelectOptions> {
    return await loadObjectsElementor(this.action!, {
      ids: this.getIDs(),
      data: {
        get_titles: this.getQueryData().autocomplete,
        unique_id: this.cid
      }
    })
  },

  /**
   * Updates the AJAX action from the control model
   */
  updateAction(this: IQueryControlContext): string | null {
    const action = this.model.get('action')

    if (!validateAction(action)) {
      return null
    }

    this.action = action

    return this.action
  },

  /**
   * Updates the autocomplete AJAX action from the control model
   */
  updateActionAutocomplete(this: IQueryControlContext): string | null {
    const isAutoCompleteEnabled = !!this.model.get('autocomplete')
    const actionAutocomplete = this.model.get('action_autocomplete')

    if (!isAutoCompleteEnabled || !validateAction(actionAutocomplete)) {
      return null
    }

    this.actionAutocomplete = actionAutocomplete

    return this.actionAutocomplete
  },

  /**
   * Sets the loading state of the control
   * Shows or hides the spinner and disables the select element
   */
  setLoading(this: IQueryControlContext, toggle = true, toggleDisable = true): void {
    if (this.isDestroyed) {
      return
    }

    // Disable the select element
    if (toggleDisable && this.ui?.select) {
      this.ui.select.prop('disabled', toggle)
    }

    if (toggle) {
      showControlSpinner(this.el)
    } else {
      hideControlSpinner(this.el)
    }
  },

  /**
   * Updates the available options in the control
   * Re-renders the control if needed
   */
  updateOptions(
    this: IQueryControlContext,
    newOptions: TSelectOptions | null,
    render = true
  ): boolean {
    if (this.isDestroyed) {
      return false
    }

    if (!newOptions || typeof newOptions !== 'object') {
      return false
    }

    const currentOptions = this.model.get('options') as TSelectOptions | null

    // No need to re-render if the options are the same
    if (!shouldUpdateOptions(currentOptions, newOptions)) {
      return false
    }

    this.model.set('options', newOptions)

    if (render) {
      this.render()
    }

    return true
  },

  /**
   * Gets the value of a control by name, taking group into account
   */
  getControlValueByName(this: IQueryControlContext, controlName: string): unknown {
    const group = this.model.get('group') as string | undefined
    const name = (group || '') + controlName

    return this.container.settings.attributes[name]
  },

  /**
   * Gets query data for AJAX requests
   */
  getQueryData(this: IQueryControlContext): TQueryData {
    const modelQuery = getQueryFromModel(this.model)
    const groupPostType = this.getControlValueByName('post_type') as string | undefined

    return buildQueryData(modelQuery, groupPostType)
  },

  /**
   * Converts the control value to an array of IDs
   */
  getIDs(this: IQueryControlContext): (string | number)[] {
    return normalizeIds(this.getControlValue())
  },

  /**
   * Sets up the sortable functionality if enabled
   * Rearranges items and attaches sortable behavior
   */
  setupSortable(this: IQueryControlContext): void {
    const sortable = this.model.get('sortable')

    if (!sortable) {
      return
    }

    const allPosts = this.model.get('options') as TSelectOptions | undefined
    const sortedPosts = this.getIDs()

    if (allPosts) {
      rearrangeSortable(this.$el, { allPosts, sortedPosts })
    }
    attachSortable(this.$el)
  },

  /**
   * Gets the default options for Select2
   * Adds autocomplete functionality if actionAutocomplete is set
   */
  getSelect2DefaultOptions(this: IQueryControlContext): Record<string, unknown> {
    this.updateActionAutocomplete()

    const defaults = (this.model.get('select2options') as Record<string, unknown>) || {}

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const controls = window.elementor?.modules?.controls as any
    const Select2Proto = controls?.Select2?.prototype

    const parentOptions = Select2Proto?.getSelect2DefaultOptions?.apply(this, []) || {}

    const options = jQuery.extend(parentOptions, defaults) as Record<string, unknown>

    if (!this.actionAutocomplete) {
      return options
    }

    return jQuery.extend(this.getAutoCompletePosts(), options) as Record<string, unknown>
  },

  /**
   * Gets the autocomplete configuration for posts
   */
  getAutoCompletePosts(this: IQueryControlContext): Record<string, unknown> {
    return getAutocompleteConfig(this.actionAutocomplete!, {
      queryData: this.getQueryData.bind(this),
      before: this.setLoading.bind(this, true, false),
      after: this.setLoading.bind(this, false, false)
    })
  }
}
