import {
  showControlSpinner,
  hideControlSpinner,
  normalizeIds,
  rearrangeSortable,
  attachSortable,
  getAutocompleteConfig,
  loadObjectsElementor
} from '../utils'

export const BaseQueryControlView = {
  /**
   * AJAX action name for fetching data
   * @type {string|null}
   */
  action: null,

  /**
   * AJAX action name for autocomplete functionality
   * @type {string|null}
   */
  actionAutocomplete: null,

  /**
   * Current AJAX request promise
   * @type {Promise|null}
   */
  request: null,

  /**
   * Flag indicating if an AJAX request is in progress
   * @type {boolean}
   */
  isPendingRequest: false,

  /**
   * Flag indicating if the control has been destroyed
   * @type {boolean}
   */
  isDestroyed: false,

  /**
   * Renders the control and loads initial data
   * Called when Elementor initializes the control view
   * @async
   */
  async onRender() {
    // @ts-expect-error - Type assertion for super access
    this.constructor.__super__.onRender.call(this)

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
  onDestroy() {
    // @ts-expect-error - Type assertion for super access
    this.constructor.__super__.onDestroy.call(this)

    this.isDestroyed = true
  },

  /**
   * Loads data for the control via AJAX
   * Handles pending requests and loading states
   * @async
   * @returns {Promise<Object|null>} The loaded data or null
   */
  async loadData() {
    if (this.isDestroyed) {
      return null
    }

    if (!this.action) {
      return null
    }

    if (this.isPendingRequest) {
      this.setLoading(true)

      this.request.finally(() => {
        this.setLoading(false)
      })

      return this.request
    }

    this.isPendingRequest = true
    this.setLoading(true)
    this.request = await this.fetchData()
    this.isPendingRequest = false
    this.setLoading(false)

    return this.request
  },

  async fetchData() {
    return await loadObjectsElementor(this.action, {
      ids: this.getIDs(),
      data: {
        get_titles: this.getQueryData().autocomplete,
        unique_id: this.cid
      }
    })
  },

  /**
   * Updates the AJAX action from the control model
   * @returns {string|null} The updated action or null
   */
  updateAction() {
    const action = this.model.get('action')

    if (!action) {
      return null
    }

    this.action = action

    return this.action
  },

  /**
   * Updates the autocomplete AJAX action from the control model
   * @returns {string|null} The updated autocomplete action or null
   */
  updateActionAutocomplete() {
    const isAutoCompleteEnabled = !!this.model.get('autocomplete')
    const actionAutocomplete = this.model.get('action_autocomplete')

    if (!isAutoCompleteEnabled || !actionAutocomplete || typeof actionAutocomplete !== 'string') {
      return null
    }

    this.actionAutocomplete = actionAutocomplete

    return this.actionAutocomplete
  },

  /**
   * Sets the loading state of the control
   * Shows or hides the spinner and disables the select element
   * @param {boolean} toggle - Whether to show (true) or hide (false) the loading state
   */
  setLoading(toggle = true, toggleDisable = true) {
    if (this.isDestroyed) {
      return
    }

    // Disable the select element
    if (toggleDisable) {
      this.ui?.select?.prop('disabled', toggle)
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
   * @param {Object} newOptions - The new options to set
   * @param {boolean} render - Whether to re-render the control
   * @returns {boolean} Whether the options were updated
   */
  updateOptions(newOptions, render = true) {
    if (this.isDestroyed) {
      return false
    }

    if (!newOptions || typeof newOptions !== 'object') {
      return false
    }

    const currentOptions = this.model.get('options')

    // No need to re-render if the options are the same
    if (JSON.stringify(currentOptions) === JSON.stringify(newOptions)) {
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
   *
   * @param {string} controlName - The control name
   * @returns {any} The control value
   */
  getControlValueByName(controlName) {
    const name = this.model.get('group') + controlName

    return this.container.settings.attributes[name]
  },

  /**
   * Gets query data for AJAX requests
   *
   * @returns {Object} Query data
   */
  getQueryData() {
    const autocomplete = {
      query: Object.assign({}, this.model.get('query'))
    }

    const groupPostType = this.getControlValueByName('post_type')

    if (groupPostType) {
      Object.assign(autocomplete.query, {
        post_type: groupPostType
      })
    }

    return {
      autocomplete
    }
  },

  /**
   * Converts the control value to an array of IDs
   *
   * @returns {Array} Array of IDs
   */
  getIDs() {
    return normalizeIds(this.getControlValue())
  },

  /**
   * Sets up the sortable functionality if enabled
   * Rearranges items and attaches sortable behavior
   */
  setupSortable() {
    const sortable = this.model.get('sortable')

    if (!sortable) {
      return
    }

    const allPosts = this.model.get('options')
    const sortedPosts = this.getIDs()

    rearrangeSortable(this.$el, { allPosts, sortedPosts })
    attachSortable(this.$el)
  },

  /**
   * Gets the default options for Select2
   * Adds autocomplete functionality if actionAutocomplete is set
   * @returns {Object} Select2 options
   */
  getSelect2DefaultOptions() {
    this.updateActionAutocomplete()

    const defaults = this.model.get('select2options') || {}
    const options = jQuery.extend(
      elementor.modules.controls.Select2.prototype.getSelect2DefaultOptions.apply(this, arguments),
      defaults
    )

    if (!this.actionAutocomplete) {
      return options
    }

    return jQuery.extend(this.getAutoCompletePosts(), options)
  },

  /**
   * Gets the autocomplete configuration for posts
   * @returns {Object} Autocomplete configuration
   */
  getAutoCompletePosts() {
    return getAutocompleteConfig(this.actionAutocomplete, {
      queryData: this.getQueryData.bind(this),
      before: this.setLoading.bind(this, true, false),
      after: this.setLoading.bind(this, false, false)
    })
  }
}
